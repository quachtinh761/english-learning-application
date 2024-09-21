<?php

namespace App\Enums;

enum QuizStatusEnum: string {
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Ongoing = 'ongoing';
    case Ended = 'ended';
    case Archived = 'archived';
}
