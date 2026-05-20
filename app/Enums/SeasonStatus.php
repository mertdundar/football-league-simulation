<?php

namespace App\Enums;

enum SeasonStatus: string
{
    case Setup = 'setup';
    case InProgress = 'in_progress';
    case Complete = 'complete';
}
