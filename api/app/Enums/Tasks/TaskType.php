<?php

namespace App\Enums\Tasks;

enum TaskType: string
{
    case FEATURE = 'feature';
    case ISSUE = 'issue';
    case ADMIN = 'admin';
}
