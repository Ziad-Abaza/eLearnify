<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        DB::table('role_user')->truncate();
        Schema::enableForeignKeyConstraints();

        $adminRole = Role::where('name', 'Admin')->first();
        $instructorRole = Role::where('name', 'Instructor')->first();
        $studentRole = Role::where('name', 'Student')->first();

        if (!$adminRole || !$instructorRole || !$studentRole) {
            $this->command->error('Admin, Instructor, or Student role not found. Please run RoleSeeder first.');
            return;
        }

        $adminUser = User::create([
            'user_id' => Str::uuid()->toString(),
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $adminUser->roles()->attach($adminRole->role_id);

        $instructorUser = User::create([
            'user_id' => Str::uuid()->toString(),
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $instructorUser->roles()->attach($instructorRole->role_id);

        $studentUser = User::create([
            'user_id' => Str::uuid()->toString(),
            'name' => 'Student User',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $studentUser->roles()->attach($studentRole->role_id);

        // Create a few more students
        for ($i = 1; $i <= 5; $i++) {
            $student = User::create([
                'user_id' => Str::uuid()->toString(),
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $student->roles()->attach($studentRole->role_id);
        }
    }
}
