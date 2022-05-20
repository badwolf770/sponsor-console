<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;

class FlightReadModel
{
    /**
     * @OA\Property (
     *     description="Id",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $id = null;
    /**
     * @OA\Property (
     *     description="Название флайта",
     *     type="string",
     *     example="F7 - All 25-55 BC",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $name = null;
    /**
     * @OA\Property (
     *     description="Программы",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=ProgramReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $programs = [];

    /**
     * @OA\Property (
     *     description="список ричей",
     *     type="array",
     *     @OA\Items(
     *          ref=@Model(type=ReachReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $reaches = [];
    /**
     * @OA\Property (
     *     description="ots",
     *     type="float",
     *     example=10.2
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?float $ots = null;
}