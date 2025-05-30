<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'video_id' => $this->video_id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'duration' => $this->duration,
            's3_url' => $this->s3_url ?? $this->getVideo(),
            'thumbnail_url' => $this->getThumbnail(),
            'order_in_course' => $this->order_in_course,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
