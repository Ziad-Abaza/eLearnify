<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class QuestionOption extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'option_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'selected_option_id');
    }
}
