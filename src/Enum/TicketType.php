<?php

namespace App\Enum;

enum TicketType: string
{
    case BUG = 'Bug/Error';
    case FEATURE_REQUEST = 'Feature Request';
    case TASK = 'Task';
    case IMPROVEMENT = 'Improvement';
    case QUESTION = 'Question';
    case DOCUMENTATION = 'Documentation';
    case ENHANCEMENT = 'Enhancement';
    case SECURITY = 'Security';
    case PERFORMANCE = 'Performance';
    case MAINTENANCE = 'Maintenance';
}
