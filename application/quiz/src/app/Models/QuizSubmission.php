<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'submission_code',
        'user_id',
        'user_name',
        'user_email',
        'number_of_corrections',
        'total_points',
        'detail',
        'rank',
    ];

    protected $casts = [
        'detail' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
