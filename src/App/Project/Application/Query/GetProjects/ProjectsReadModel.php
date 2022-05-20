<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetProjects;

use App\Project\Application\Query\ReadModel\ProjectReadModel;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class ProjectsReadModel
{
    /**
     * @var ProjectReadModel[]
     * @OA\Property (
     *     description="Список проектов",
     *     type="array",
     *     @OA\Items(
     *          type="object",
     *          ref=@Model(type=ProjectReadModel::class, groups={"default"})
     *     )
     * )
     * @Serializer\Groups({"default"})
     */
    public array $projects;

    public function __construct(array $projects)
    {
        $this->projects = $projects;
    }
}
