<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserCourseProgress extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'user_course_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'user_course_progress'; // Explicitly define table name

    protected $fillable = [
        'user_id',
        'course_id',
        'completion_percentage',
        'last_accessed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
