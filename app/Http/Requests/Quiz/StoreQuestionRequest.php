<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
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
            'video_id' => ['required', 'uuid', Rule::exists('videos', 'video_id')],
            'question_text' => 'required|string',
            'question_type' => ['required', 'string', Rule::in(['multiple_choice', 'single_choice', 'true_false'])],
            'points' => 'nullable|integer|min:0',
        ];
    }
}
