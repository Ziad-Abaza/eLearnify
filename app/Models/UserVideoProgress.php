<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserVideoProgress extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'progress_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'user_video_progress'; // Explicitly define table name

    protected $fillable = [
        'user_id',
        'video_id',
        'is_completed',
        'last_watched_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
}
