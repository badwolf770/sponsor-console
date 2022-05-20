<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class ChannelReadModel
{
    /**
     * @OA\Property (
     *     description="Id канала",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     description="Название канала",
     *     type="string",
     *     example="Первый"
     *  )
     * @Serializer\Groups({"getById"})
     */
    public string $name;
    /**
     * @OA\Property (
     *     description="Месяцы",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=MonthReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $months = [];
}
