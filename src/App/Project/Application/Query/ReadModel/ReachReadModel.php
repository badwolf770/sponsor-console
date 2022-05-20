<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class ReachReadModel
{
    /**
     * @OA\Property (
     *     description="Id рича, может быть пустое если рич высчитан из оригинала",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $id = null;
    /**
     * @OA\Property (
     *     description="Название",
     *     type="string",
     *     example="Reach 1+,%"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $name = null;

    /**
     * @OA\Property (
     *     description="значение",
     *     type="float",
     *     example=10.2
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $value = null;
}