<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserCourseProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'uuid', Rule::exists('users', 'user_id')],
            'course_id' => ['required', 'uuid', Rule::exists('courses', 'course_id')],
            'completion_percentage' => 'required|numeric|min:0|max:100',
            'last_accessed' => 'nullable|date',
        ];
    }
}
