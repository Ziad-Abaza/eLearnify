<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $primaryKey = 'course_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'description',
        'instructor_id',
        'category_id',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'course_id');
    }

    public function userCourseProgress()
    {
        return $this->hasMany(UserCourseProgress::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }

    // Media Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course_image')->singleFile();
        $this->addMediaCollection('course_icon')->singleFile();
    }

    public function getImage()
    {
        return $this->getFirstMediaUrl('course_image');
    }

    public function getIcon()
    {
        return $this->getFirstMediaUrl('course_icon');
    }

    public function setImage($file)
    {
        $this->clearMediaCollection('course_image');
        $this->addMedia($file)->toMediaCollection('course_image');
    }

    public function setIcon($file)
    {
        $this->clearMediaCollection('course_icon');
        $this->addMedia($file)->toMediaCollection('course_icon');
    }

    public function deleteImage()
    {
        $this->clearMediaCollection('course_image');
    }

    public function deleteIcon()
    {
        $this->clearMediaCollection('course_icon');
    }
}
