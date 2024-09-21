<?php

namespace App\Enums;

enum QuizQuestionTypeEnum: string {
    case MultipleChoice = 'multiple_choice';
    case SingleChoice = 'single_choice';
    case Text = 'text';
}
