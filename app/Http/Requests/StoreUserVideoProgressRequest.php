<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserVideoProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكنك تخصيص الصلاحيات لاحقًا
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required','uuid',Rule::exists('users', 'user_id'),],
            'video_id' => ['required','uuid',Rule::exists('videos', 'video_id'),],
            'is_completed' => ['required', 'boolean'],
            'last_watched_time' => ['nullable', 'date'],
        ];
    }
}
