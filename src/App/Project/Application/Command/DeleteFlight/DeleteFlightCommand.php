<?php
declare(strict_types=1);

namespace App\Project\Application\Command\DeleteFlight;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use App\Project\Infrastructure\Entity\Spot;

/**
 * @UniqueConstraint(entity=Spot::class, uniqueFields={"flight":"flightId"}, message="Отвяжите флайт от спотов перед удалением")
 */
class DeleteFlightCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="flight", key="id", checkPositive=false, message="Флайт {{id}} не существует!")
     */
    public string $flightId;
}