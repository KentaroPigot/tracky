<?php

namespace App\Enum;

enum TicketPriority: string
{
    case HIGH = 'High';
    case MEDIUM = 'Medium';
    case LOW = 'Low';
}
