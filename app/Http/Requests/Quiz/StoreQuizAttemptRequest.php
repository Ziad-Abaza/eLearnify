<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizAttemptRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => ['required', 'uuid', Rule::exists('users', 'user_id')],
            'question_id' => ['required', 'uuid', Rule::exists('questions', 'question_id')],
            'selected_option_id' => ['required', 'uuid', Rule::exists('question_options', 'option_id')],
            'is_correct' => 'nullable|boolean',
            'attempt_time' => 'nullable|date',
        ];
    }
}
