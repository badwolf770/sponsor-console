<?php
declare(strict_types=1);

namespace App\Shared\Application\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class EntityCreatedModel
{
    /**
     * @OA\Property (type="string",description="id созданной сущности", example=1)
     * @Serializer\Groups({"default"})
     */
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
