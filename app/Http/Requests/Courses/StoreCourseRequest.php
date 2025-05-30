<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200|unique:courses,title',
            'description' => 'nullable|string',
            'instructor_id' => 'required|uuid|exists:users,user_id',
            'category_id' => 'required|uuid|exists:categories,category_id',

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
