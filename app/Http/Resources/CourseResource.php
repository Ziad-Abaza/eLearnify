<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'instructor_id' => $this->instructor_id,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'image_url' => $this->getImage(),
            'icon_url' => $this->getIcon(),

            'instructor' => $this->whenLoaded('instructor', function(){
                return [
                    'instructor_id' => $this->instructor->user_id,
                    'name' => $this->instructor->name,
                    'email' => $this->instructor->email,
                                    ];
            }),
            'category' => $this->whenLoaded('category', function(){
                return [
                    'category_id' => $this->category->category_id,
                    'name' => $this->category->category_name,
                ];
            }),
        ];
    }
}
