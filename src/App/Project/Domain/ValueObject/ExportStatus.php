<?php
declare(strict_types=1);

namespace App\Project\Domain\ValueObject;

enum ExportStatus: string
{
    case NotExported = 'notExported';
    case InProgress = 'inProgress';
    case Completed = 'completed';
}