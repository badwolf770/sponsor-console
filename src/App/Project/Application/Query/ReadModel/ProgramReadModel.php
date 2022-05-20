<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class ProgramReadModel
{
    /**
     * @OA\Property (
     *     description="Id программы",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $id = null;
    /**
     * @OA\Property (
     *     description="Название программы",
     *     type="string",
     *     example="Дачный вопрос",
     *     nullable=true
     *  )
     * @Serializer\Groups({"getById"})
     */
    public ?string $name = null;
    /**
     * @OA\Property (
     *     description="Споты",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=SpotReadModel::class)
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $spots = [];
}
