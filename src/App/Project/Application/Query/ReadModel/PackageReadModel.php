<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class PackageReadModel
{
    /**
     * @OA\Property (
     *     description="Id пакета",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     description="Название пакета",
     *     type="string",
     *     example="Metro"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $name;
    /**
     * @OA\Property(
     *     description="Ндс",
     *     type="float",
     *     example=25.0
     * )
     * @Serializer\Groups({"getById"})
     */
    public float $tax;
    /**
     * @OA\Property(
     *     description="Статус активности пакета",
     *     type="boolean",
     *     example=true
     * )
     * @Serializer\Groups({"getById"})
     */
    public bool $active;
    /**
     * @OA\Property (
     *     description="Каналы",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=ChannelReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getPackageById"})
     */
    public array $channels = [];

    /**
     * @OA\Property (
     *     description="Статус расчета статистик по пакету(охваты, рейтинги)",
     *     type="string",
     *     enum={App\Project\Domain\ValueObject\CalculationStatus::NotCalculated,App\Project\Domain\ValueObject\CalculationStatus::InProgress,App\Project\Domain\ValueObject\CalculationStatus::Completed},
     *     example=App\Project\Domain\ValueObject\CalculationStatus::NotCalculated
     *  )
     * @Serializer\Groups({"getPackageById"})
     */
    public string $calculationStatus;
}
