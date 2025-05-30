<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courseId = $this->route('course')?->course_id ?? null;

        return [
            'title' => "sometimes|required|string|max:200|unique:courses,title,$courseId,course_id",
            'description' => 'nullable|string',
            'instructor_id' => "sometimes|required|uuid|exists:users,user_id",
            'category_id' => "sometimes|required|uuid|exists:categories,category_id",

            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
