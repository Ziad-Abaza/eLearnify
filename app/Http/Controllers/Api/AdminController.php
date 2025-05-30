<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    // User Management

    public function getUsers()
    {
        try {
            $users = User::with('roles')->get();

            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => $users
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve users',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => "required|string|email|max:255|unique:users,email," . $user->user_id . ',user_id',
                'roles' => 'required|array'
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email']
            ]);

            $roleIds = collect($validated['roles'])->pluck('role_id')->toArray();
            $user->roles()->sync($roleIds);

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'User updated successfully',
                'data' => $user->load('roles')
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to update user',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteUser(User $user)
    {
        try {
            if ($user->roles()->where('role_name', 'admin')->exists()) {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => 'Cannot delete admin users'
                ], Response::HTTP_FORBIDDEN);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'User deleted successfully'
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to delete user',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Role Management

    public function getRoles()
    {
        try {
            $roles = Role::withCount('users')->get();

            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => $roles
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve roles',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createRole(Request $request)
    {
        try {
            $validated = $request->validate([
                'role_name' => 'required|string|max:255|unique:roles,role_name',
                'description' => 'required|string'
            ]);

            $role = Role::create($validated);

            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'Role created successfully',
                'data' => $role
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to create role',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateRole(Request $request, Role $role)
    {
        try {
            if (in_array($role->role_name, ['admin', 'student'])) {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => 'Cannot modify core roles'
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'role_name' => "required|string|max:255|unique:roles,role_name," . $role->role_id . ',role_id',
                'description' => 'required|string'
            ]);

            $role->update($validated);

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Role updated successfully',
                'data' => $role
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to update role',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRole(Role $role)
    {
        try {
            if (in_array($role->role_name, ['admin', 'student'])) {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => 'Cannot delete core roles'
                ], Response::HTTP_FORBIDDEN);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Role deleted successfully'
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to delete role',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
