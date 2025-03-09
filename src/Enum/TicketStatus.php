<?php

namespace App\Enum;

enum TicketStatus: string
{
    case OPEN = 'Open';
    case IN_PROGRESS = 'In Progress';
    case RESOLVED = 'Resolved';
    case CLOSED = 'Closed';
    case REOPENED = 'Reopened';
    case ON_HOLD = 'On Hold';
}
