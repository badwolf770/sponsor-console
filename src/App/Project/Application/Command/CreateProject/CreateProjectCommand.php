<?php
declare(strict_types=1);

namespace App\Project\Application\Command\CreateProject;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

class CreateProjectCommand implements CommandInterface
{

    /**
     * @OA\Property(description="Название проекта", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    public string $name;
    /**
     * @OA\Property(description="Название клиента", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    public string $client;
    /**
     * @OA\Property(description="Название бренда", example="metro")
     * @Assert\Type("string")
     * @Assert\NotBlank()
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
     */
    public array $reaches;
}
