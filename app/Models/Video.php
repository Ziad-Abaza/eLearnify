<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Video extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $primaryKey = 'video_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'course_id',
        'title',
        'duration',
        's3_url',
        'order_in_course',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'video_id');
    }

    public function userVideoProgress()
    {
        return $this->hasMany(UserVideoProgress::class, 'video_id');
    }

    // Media Collection
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('video_file')->singleFile();
    }

    public function getThumbnail()
    {
        return $this->getFirstMediaUrl('thumbnail');
    }

    public function getVideo()
    {
        return $this->getFirstMediaUrl('video_file');
    }

    public function setThumbnail($file)
    {
        $this->clearMediaCollection('thumbnail');
        $this->addMedia($file)->toMediaCollection('thumbnail');
    }

    public function setVideoFile($file)
    {
        $this->clearMediaCollection('video_file');
        $this->addMedia($file)->toMediaCollection('video_file');
    }
}
