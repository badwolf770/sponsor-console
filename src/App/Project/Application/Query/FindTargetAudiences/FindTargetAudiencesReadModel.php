<?php
declare(strict_types=1);

namespace App\Project\Application\Query\FindTargetAudiences;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Project\Application\Query\ReadModel\TargetAudienceReadModel;
use JMS\Serializer\Annotation as Serializer;

class FindTargetAudiencesReadModel
{
    /**
     * @OA\Property (
     *     description="Целевые аудитории",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=TargetAudienceReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"default"})
     */
    public array $targetAudiences = [];
}
