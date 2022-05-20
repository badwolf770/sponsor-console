<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;

class SpotReadModel
{
    /**
     * @OA\Property (
     *     description="Id спота",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     description="Опция",
     *     type="string",
     *     example="Заставка"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $sponsorType;
    /**
     * @OA\Property (
     *     description="День недели",
     *     type="string",
     *     example="понедельник",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $weekDay = null;
    /**
     * @OA\Property (
     *     description="Время размещения",
     *     type="integer",
     *     example=15,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?int $timingInSec = null;
    /**
     * @OA\Property (
     *     description="Кол-во выходов в месяц",
     *     type="integer",
     *     example=5,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?int $outsPerMonth = null;
    /**
     * @OA\Property (
     *     description="Стоимость всех размещений",
     *     type="float",
     *     example=14.88
     *  )
     * @Serializer\Groups({"getById"})
     */
    public float $cost;
    /**
     * @OA\Property (
     *     description="Начало программы",
     *     type="string",
     *     example="14:00",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $broadcastStart = null;
    /**
     * @OA\Property (
     *     description="Окончание программы",
     *     type="string",
     *     example="15:00",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $broadcastFinish = null;

    /**
     * @OA\Property (
     *     description="Рейтинги",
     *     type="object",
     *     ref=@Model(type=RatingReadModel::class)
     *  )
     * @Serializer\Groups({"getById"})
     */
    public RatingReadModel $rating;

    /**
     * @OA\Property (
     *     description="Итого секунд",
     *     type="float",
     *     example=14.88,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $totalTiming;
}
