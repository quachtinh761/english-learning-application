<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'quiz_category_id',
        'title',
        'description',
        'instruction',
        'start_time',
        'end_time',
        'duration_in_minutes',
        'number_of_questions',
        'maximum_point',
        'is_anonymous',
        'is_randomize_question',
        'number_of_submissions',
        'lowest_point',
        'highest_point',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_anonymous' => 'boolean',
        'is_randomize_question' => 'boolean',
    ];

    public function quizCategory()
    {
        return $this->belongsTo(QuizCategory::class);
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function quizSubmissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }
}
