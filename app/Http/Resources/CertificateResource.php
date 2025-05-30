<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'certificate_id' => $this->certificate_id,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'user_id' => $this->user->user_id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'course' => $this->whenLoaded('course', function() {
                return [
                    'course_id' => $this->course->course_id,
                    'title' => $this->course->title,
                    'description' => $this->course->description,
                ];
            }),
            'template_url' => $this->getTemplate(),
            'generated_certificate_url' => $this->getGeneratedCertificate(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
