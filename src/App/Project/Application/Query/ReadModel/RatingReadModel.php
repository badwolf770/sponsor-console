<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class RatingReadModel
{
    /**
     * @OA\Property (
     *     description="Id райтинга",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $id = null;
    /**
     * @OA\Property (
     *     description="tvr",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $tvr = null;

    /**
     * @OA\Property (
     *     description="grps20",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $grps20 = null;

    /**
     * @OA\Property (
     *     description="avTvr",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $avTvr = null;

    /**
     * @OA\Property (
     *     description="trps",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $trps = null;

    /**
     * @OA\Property (
     *     description="trps",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $trps20 = null;

    /**
     * @OA\Property (
     *     description="cpp",
     *     type="float",
     *     example=10.2,
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $cpp = null;

    /**
     * @OA\Property (
     *     description="affinity",
     *     type="float",
     *     example=10.2
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $affinity = null;
}
