<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserVideoProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'progress_id' => $this->progress_id,
            'user_id' => $this->user_id,
            'video_id' => $this->video_id,
            'is_completed' => $this->is_completed,
            'last_watched_time' => $this->last_watched_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'user_id' => $this->user->user_id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'video' => $this->whenLoaded('video', function() {
                return [
                    'video_id' => $this->video->video_id,
                    'title' => $this->video->title,
                    'duration' => $this->video->duration,
                    'thumbnail_url' => $this->video->getThumbnail(),
                    'course_id' => $this->video->course_id,
                ];
            }),
        ];
    }
}
