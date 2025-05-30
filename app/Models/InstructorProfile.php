<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'instructor_profile_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'bio',
        'specialization',
        'experience',
        'linkedin_url',
        'github_url',
        'website_url',
        'skills'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSkillsAttribute($value)
    {
        return json_decode($value, true);
    }
}
