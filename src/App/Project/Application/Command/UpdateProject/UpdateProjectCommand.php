<?php
declare(strict_types=1);

namespace App\Project\Application\Command\UpdateProject;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class UpdateProjectCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
    /**
     * @OA\Property(description="Название проекта", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Serializer\Groups({"default"})
     */
    public string $name;
    /**
     * @OA\Property(description="Название клиента", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Serializer\Groups({"default"})
     */
    public string $client;
    /**
     * @OA\Property(description="Название бренда", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Serializer\Groups({"default"})
     */
    public string $brand;

    /**
     * @OA\Property(description="Охваты", type="array",
     *      @OA\Items(
     *          type="integer",
     *          example=1
     *      )
     * )
     * @Assert\All({
     *     @Assert\Type("int")
     * })
     * @Assert\NotBlank()
     * @Serializer\Groups({"default"})
     */
    public array $reaches;
}