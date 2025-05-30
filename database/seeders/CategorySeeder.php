<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('courses')->truncate();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'category_id' => Str::uuid()->toString(),
                'category_name' => 'Web Development',
                'description' => 'Courses related to web design and development, including frontend and backend technologies.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => Str::uuid()->toString(),
                'category_name' => 'Mobile Development',
                'description' => 'Courses on building applications for mobile platforms like iOS and Android.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => Str::uuid()->toString(),
                'category_name' => 'Data Science',
                'description' => 'Learn data analysis, machine learning, and big data technologies.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => Str::uuid()->toString(),
                'category_name' => 'Cloud Computing',
                'description' => 'Courses on cloud platforms like AWS, Azure, and Google Cloud.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Category::insert($categories);
    }
}
