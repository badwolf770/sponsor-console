<?php
declare(strict_types=1);

namespace App\Project\Application\Command\CreateFlight;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use App\Project\Infrastructure\Entity\Flight;

/**
 * @Assert\GroupSequence({"CreateFlightCommand", "strict"})
 * @UniqueConstraint(entity=Flight::class, uniqueFields={"name":"name","package":"packageId"}, message="Флайт с таким именем уже существует", groups={"strict"})
 */
class CreateFlightCommand implements CommandInterface
{
    /**
     * @OA\Property(description="Id пакета", example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5")
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Package", key="id", checkPositive=false, message="Пакет {{id}} не существует!")
     * })
     */
    public string $packageId;

    /**
     * @OA\Property(description="Название флайта", example="JOLION - All 25-45 BC")
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Length(min=1 ,max=255)
     * })
     * @Serializer\Groups({"default"})
     */
    public string $name;

    /**
     * @OA\Property(description="Id целевой аудитории", example="ed8c4248-65c5-5138-9037-a3c8f688c9cf")
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="TargetAudience", key="id", checkPositive=false, message="Целевая аудитория {{id}} не существует!")
     * })
     * @Serializer\Groups({"default"})
     */
    public string $targetAudienceId;
}
