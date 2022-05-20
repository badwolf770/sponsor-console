<?php
declare(strict_types=1);

namespace App\Project\Application\Command\LinkFlightToSpot;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use App\Project\Infrastructure\Validator\LinkFlightToSpotValidator\LinkFlightToSpot;

/**
 * @Assert\GroupSequence({"LinkFlightToSpotCommand", "strict"})
 * @LinkFlightToSpot(spotField="spotId", groups={"strict"})
 */
class LinkFlightToSpotCommand implements CommandInterface
{
    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Spot", key="id", checkPositive=false, message="Спот {{id}} не существует!")
     * })
     */
    public string $spotId;

    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Flight", key="id", checkPositive=false, message="Флайт {{id}} не существует!")
     * })
     */
    public string $flightId;
}
