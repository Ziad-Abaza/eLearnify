<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('role_user')->truncate();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            [
                'role_id' => Str::uuid()->toString(),
                'name' => 'Admin',
                'description' => 'Administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => Str::uuid()->toString(),
                'name' => 'Instructor',
                'description' => 'User who can create and manage courses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => Str::uuid()->toString(),
                'name' => 'Student',
                'description' => 'User who can enroll in courses and learn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Role::insert($roles);
    }
}
