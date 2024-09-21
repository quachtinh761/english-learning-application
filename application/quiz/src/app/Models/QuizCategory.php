<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'parent_id',
        'number_of_quizzes',
        'order',
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
