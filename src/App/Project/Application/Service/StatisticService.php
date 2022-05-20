<?php
declare(strict_types=1);

namespace App\Project\Application\Service;

use App\Project\Application\Dto\StatisticsDto;
use App\Project\Domain\Entity\Rating;
use App\Project\Domain\Spot;
use Webmozart\Assert\Assert;

class StatisticService
{
    public function calculateStatistics(Spot $spot): StatisticsDto
    {
        Assert::notEmpty($spot->getRating(), "Нельзя расчитать статистики для спота без объекта " . Rating::class);
        $dto = new StatisticsDto;
        $dto->avTvr = $spot->getRating()->getTvr() * $spot->getRating()->getAffinity();
        $dto->trps = $dto->avTvr * $spot->getOutsPerMonth();
        $dto->trps20 = (($spot->getOutsPerMonth() * $spot->getTimingInSec()) * $dto->avTvr) / 20;
        $dto->cpp = round($spot->getCost() / $dto->trps20, 2);

        return $dto;
    }

    public function calculateReachThousand(float $universe, float $percent): float
    {
        return $universe * $percent;
    }
}