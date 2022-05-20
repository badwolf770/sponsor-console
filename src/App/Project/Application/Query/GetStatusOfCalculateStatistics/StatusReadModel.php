<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetStatusOfCalculateStatistics;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class StatusReadModel
{
    /**
     * @OA\Property (
     *     description="Статус расчета статистик по пакету(охваты, рейтинги)",
     *     type="string",
     *     enum={App\Project\Domain\ValueObject\CalculationStatus::NotCalculated,App\Project\Domain\ValueObject\CalculationStatus::InProgress,App\Project\Domain\ValueObject\CalculationStatus::Completed},
     *     example=App\Project\Domain\ValueObject\CalculationStatus::NotCalculated
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }
}