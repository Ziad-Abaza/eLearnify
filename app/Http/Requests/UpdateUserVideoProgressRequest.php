<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserVideoProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $progressId = $this->route('userVideoProgress')->progress_id;

        return [
            'user_id' => ['sometimes','required','uuid',
                Rule::exists('users', 'user_id'),
                Rule::unique('user_video_progress')
                    ->ignore($progressId, 'progress_id')
                    ->where(function ($query) {
                        return $query->where('video_id', $this->video_id);
                    }),
            ],
            'video_id' => ['sometimes','required','uuid',
                Rule::exists('videos', 'video_id'),
                Rule::unique('user_video_progress')
                    ->ignore($progressId, 'progress_id')
                    ->where(function ($query) {
                        return $query->where('user_id', $this->user_id);
                    }),
            ],
            'is_completed' => ['sometimes', 'required', 'boolean'],
            'last_watched_time' => ['nullable', 'date'],
        ];
    }
}
