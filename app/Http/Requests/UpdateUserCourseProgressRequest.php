<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserCourseProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'required', 'uuid', Rule::exists('users', 'user_id')],
            'course_id' => ['sometimes', 'required', 'uuid', Rule::exists('courses', 'course_id')],
            'completion_percentage' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'last_accessed' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
