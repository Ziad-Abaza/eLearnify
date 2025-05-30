<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $primaryKey = 'certificate_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'course_id',
        'issue_date',
        'expiry_date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Media collection for certificate image
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('template')->singleFile();
        $this->addMediaCollection('generated_certificate')->singleFile();
    }

    public function getTemplate()
    {
        return $this->getFirstMediaUrl('template');
    }

    public function getGeneratedCertificate()
    {
        return $this->getFirstMediaUrl('generated_certificate');
    }

    public function setTemplate($file)
    {
        $this->clearMediaCollection('template');
        $this->addMedia($file)->toMediaCollection('template');
    }

    public function setGeneratedCertificate($file)
    {
        $this->clearMediaCollection('generated_certificate');
        $this->addMedia($file)->toMediaCollection('generated_certificate');
    }
}
