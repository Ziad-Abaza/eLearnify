<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'option_id' => $this->option_id,
            'question_id' => $this->question_id,
            'option_text' => $this->option_text,
            'is_correct' => $this->is_correct,
            'question' => $this->whenLoaded('question', function(){
                return [
                    'question_id' => $this->question->question_id,
                    'question_text' => $this->question->question_text,
                    'question_type' => $this->question->question_type,
                    'points' => $this->question->points,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
