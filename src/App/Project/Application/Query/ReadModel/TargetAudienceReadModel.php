<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class TargetAudienceReadModel
{
    /**
     * @OA\Property (
     *     description="Id целевой",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     description="Название",
     *     type="string",
     *     example="All 18+"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $name;
}
