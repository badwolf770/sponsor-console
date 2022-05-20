<?php
declare(strict_types=1);

namespace App\Project\Domain\ValueObject;

enum CalculationStatus: string
{
    case NotCalculated = 'notCalculated';
    case InProgress = 'inProgress';
    case Completed = 'completed';
}