<?php

namespace App\Enums\Tasks;

enum TaskLocation: string
{
    case API = 'api';
    case CLIENT = 'client';
    case INFRASTRUCTURE = 'infrastructure';
}
