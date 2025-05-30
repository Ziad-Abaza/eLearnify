<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $primaryKey = 'coupon_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'course_id',
        'max_uses',
        'used_count',
        'expires_at'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function isValid()
    {
        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }
        if ($this->used_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

}
