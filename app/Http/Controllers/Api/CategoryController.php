<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Throwable;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => $categories->map(function ($category) {
                    return new \App\Http\Resources\CategoryResource($category);
                })
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve categories',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            $category = Category::create([
                'category_name' => $request->category_name,
                'description' => $request->description,
            ]);

            if ($request->hasFile('image')) {
                $category->setImage($request->file('image'));
            }

            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'Category created successfully',
                'data' => new \App\Http\Resources\CategoryResource($category->refresh()),
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
                'message' => 'Failed to create category',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => new \App\Http\Resources\CategoryResource($category),
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to retrieve category',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $validated = $request->validated();

            $category->update($request->only(['category_name', 'description']));

            if ($request->hasFile('image')) {
                $category->setImage($request->file('image'));
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Category updated successfully',
                'data' => new \App\Http\Resources\CategoryResource($category),
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
                'message' => 'Failed to update category',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->deleteImage();
            $category->delete();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'Category deleted successfully',
            ], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Failed to delete category',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
