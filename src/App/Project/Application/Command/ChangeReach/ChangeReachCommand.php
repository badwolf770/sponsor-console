<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangeReach;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;
use App\Project\Infrastructure\Entity\Reach;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Assert\GroupSequence({"ChangeReachCommand", "strict"})
 * @UniqueConstraint(entity=Reach::class, checkPositive=false, uniqueFields={"id":"reachId","flight":"flightId"}, message="охват не существует во флайте!", groups={"strict"})
 */
class ChangeReachCommand implements CommandInterface
{
    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Flight", key="id", checkPositive=false, message="Флайт {{id}} не существует!")
     * })
     */
    public string $flightId;

    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Reach", key="id", checkPositive=false, message="Охват {{id}} не существует!")
     * })
     */
    public string $reachId;

    /**
     * @OA\Property(description="процент", type="float", example=5.5)
     * @Assert\NotBlank
     * @Assert\Type("float")
     * @Serializer\Groups({"default"})
     */
    public float $percent;
}