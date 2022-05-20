<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetFlightsByPackage;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class FlightsByPackageReadModel
{
    /**
     * @var FlightByPackageReadModel[]
     * @OA\Property (
     *     description="Список флайтов",
     *     type="array",
     *     @OA\Items(
     *          type="object",
     *          ref=@Model(type=FlightByPackageReadModel::class, groups={"default"})
     *     )
     * )
     * @Serializer\Groups({"default"})
     */
    public array $flights = [];

}