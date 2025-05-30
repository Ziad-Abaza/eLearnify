<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Str;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Certificate::truncate();

        $student1 = User::where('email', 'student1@example.com')->first();
        $laravelCourse = Course::where('title', 'Laravel for Beginners')->first();

        if ($student1 && $laravelCourse) {
            Certificate::create([
                'certificate_id' => Str::uuid()->toString(),
                'user_id' => $student1->user_id,
                'course_id' => $laravelCourse->course_id,
                'issue_date' => now(),
                // 'expiry_date' => now()->addYear(), // Optional: if certificates expire
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Add more certificates as needed
    }
}
