<?php
declare(strict_types=1);

namespace App\Project\Application\Command\LinkFlightToSpots;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use App\Project\Infrastructure\Validator\LinkFlightToSpotValidator\LinkFlightToSpot;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Assert\GroupSequence({"LinkFlightToSpotsCommand", "strict"})
 * @LinkFlightToSpot(spotField="spotIds", groups={"strict"})
 */
class LinkFlightToSpotsCommand implements CommandInterface
{
    /**
     * @OA\Property(description="Id спотов", type="array",
     *      @OA\Items(
     *          type="string",
     *          example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *      )
     * )
     * @Assert\All({
     *     @Assert\Sequentially({
     *         @Assert\NotBlank(),
     *         @Assert\Uuid(),
     *         @Existence(entity="Spot", key="id", checkPositive=false, message="Спот {{id}} не существует!")
     *     })
     * })
     * @Serializer\Groups({"default"})
     */
    public array $spotIds;

    /**
     * @Assert\Sequentially({
     *     @Assert\NotBlank(),
     *     @Assert\Uuid(),
     *     @Existence(entity="Flight", key="id", checkPositive=false, message="Флайт {{id}} не существует!")
     * })
     */
    public string $flightId;
}
