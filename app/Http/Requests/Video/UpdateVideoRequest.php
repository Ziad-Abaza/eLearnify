<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends FormRequest
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
            'course_id' => ['sometimes', 'required', 'uuid', Rule::exists('courses', 'course_id')],
            'title' => 'sometimes|required|string|max:200',
            'duration' => 'nullable|integer|min:0',
            's3_url' => 'nullable|string|max:500',
            'order_in_course' => 'nullable|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'video_file' => 'nullable|mimetypes:video/mp4,video/quicktime,video/x-msvideo|max:102400',
        ];
    }
}
