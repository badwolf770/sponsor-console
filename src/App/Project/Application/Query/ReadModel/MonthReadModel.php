<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;

class MonthReadModel
{
    /**
     * @OA\Property (
     *     description="Название месяц",
     *     type="string",
     *     example="Май",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $name = null;
    /**
     * @OA\Property (
     *     description="Флайты",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=FlightReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $flights = [];
}