<?php

namespace App\Enums\Tasks;

enum TaskPriority: string
{
    case ESSENTIAL = 'essential';
    case DESIRED = 'desired';
    case DEFERRABLE = 'deferrable';
}
