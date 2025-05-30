<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseProgressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user_course_id' => $this->user_course_id,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'completion_percentage' => $this->completion_percentage,
            'last_accessed' => $this->last_accessed,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'user_id' => $this->user->user_id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'course' =>$this->whenLoaded('course', function() {
                return [
                    'course_id' => $this->course->course_id,
                    'title' => $this->course->title,
                    'description' => $this->course->description,
                    'image_url' => $this->course->getImage(),
                    'icon_url' => $this->course->getIcon(),
                    'instructor_id' => $this->course->instructor_id,
                    'instructor_name' => $this->course->instructor->name,
                ];
            }),
        ];
    }
}
