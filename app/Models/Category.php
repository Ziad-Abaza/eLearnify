<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'category_name',
        'description',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_image')->singleFile();
    }

    public function getImage(){
        return $this->getFirstMediaUrl('category_image');
    }

    public function setImage($image)
    {
        $this->deleteImage();
        $this->addMedia($image)->toMediaCollection('category_image');
    }

    public function deleteImage(){
        $this->clearMediaCollection('category_image');
    }
}
