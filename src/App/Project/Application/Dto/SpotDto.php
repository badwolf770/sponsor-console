<?php
declare(strict_types=1);

namespace App\Project\Application\Dto;

class SpotDto
{
    public function __construct(
        public ?string $program,
        public ?string $month,
        public ?int    $monthOrder,
        public ?string $weekDay,
        public ?int    $broadcastStart,
        public ?int    $broadcastFinish,
        public ?string $sponsorType,
        public ?int    $outsPerMonths,
        public ?int    $timing,
        public ?int    $timingCount,
        public ?float  $cost,
        public ?string $flight
    ) {
    }
}
