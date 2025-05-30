<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
    public function rules()
    {
        return [
            'video_id' => ['sometimes', 'required', 'uuid', Rule::exists('videos', 'video_id')],
            'question_text' => 'sometimes|required|string',
            'question_type' => ['sometimes', 'required', 'string', Rule::in(['mcq', 'single_choice', 'true_false'])],
            'points' => 'nullable|integer|min:0',
        ];
    }
}
