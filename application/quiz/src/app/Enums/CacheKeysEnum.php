<?php

namespace App\Enums;

enum CacheKeysEnum: string {
    case TopQuizSubmission = 'quiz_top_submissions_%s_%s';
}
