<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetFlightsByPackage;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Project\Application\Query\ReadModel\TargetAudienceReadModel;

class FlightByPackageReadModel
{
    /**
     * @OA\Property (
     *     description="Id",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     description="Название флайта",
     *     type="string",
     *     example="F7 - All 25-55 BC"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $name;

    /**
     * @OA\Property (
     *     description="Целевая аудитория",
     *     type="object",
     *     ref=@Model(type=TargetAudienceReadModel::class)
     *  )
     * @Serializer\Groups({"default"})
     */
    public TargetAudienceReadModel $targetAudience;
}