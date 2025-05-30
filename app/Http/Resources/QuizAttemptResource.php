<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'attempt_id' => $this->attempt_id,
            'user' => [
                'id' => $this->user->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'question' => [
                'id' => $this->question->question_id,
                'video_id' => $this->question->video_id,
                'question_text' => $this->question->question_text,
                'question_type' => $this->question->question_type,
                'points' => $this->question->points,
            ],
            'selected_option' => [
                'id' => $this->selectedOption->option_id,
                'text' => $this->selectedOption->option_text,
                'is_correct' => $this->selectedOption->is_correct,
            ],
            'attempt_time' => $this->attempt_time,
            'is_correct' => $this->is_correct,
        ];
    }
}
