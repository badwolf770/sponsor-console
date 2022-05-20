<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangeRating;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;
use App\Project\Infrastructure\Entity\Spot;
use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;

/**
 * @Assert\GroupSequence({"ChangeRatingCommand", "strict"})
 * @UniqueConstraint(entity=Spot::class, checkPositive=false, uniqueFields={"id":"spotId","rating":"ratingId"}, message="Рейтинг ratingId не существует в споте!", groups={"strict"})
 */
class ChangeRatingCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank(),
     * @Assert\Uuid(),
     * @Existence(entity="Spot", key="id", checkPositive=false, message="Спот {{id}} не существует!")
     */
    public string $spotId;
    /**
     * @Assert\NotBlank(),
     * @Assert\Uuid(),
     * @Existence(entity="Rating", key="id", checkPositive=false, message="Рейтинг {{id}} не существует!")
     */
    public string $ratingId;
    /**
     * @OA\Property(description="tvr", type="float", example=0.2, nullable=true)
     * @Serializer\Groups({"default"})
     */
    public ?float $tvr = null;
    /**
     * @OA\Property(description="grps20", type="float", example=0.2, nullable=true)
     * @Serializer\Groups({"default"})
     */
    public ?float $grps20 = null;
    /**
     * @OA\Property(description="affinity", type="float", example=0.2, nullable=true)
     * @Serializer\Groups({"default"})
     */
    public ?float $affinity = null;
}