<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'point',
        'question',
        'type',
        'options',
        'answer',
    ];

    protected $casts = [
        'options' => 'array',
        'answer' => 'array',
    ];
}
