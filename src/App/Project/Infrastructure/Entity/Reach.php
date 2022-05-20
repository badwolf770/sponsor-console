<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\ReachRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=ReachRepository::class)
 * @ORM\Table(name="reach",uniqueConstraints={@UniqueConstraint(name="unique_reach",
 *     columns={"flight_id", "name"})})
 */
class Reach
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $name;

    /**
     * @ORM\Column(type="float")
     */
    private float $percent;

    /**
     * @ORM\ManyToOne(targetEntity=Flight::class, inversedBy="reaches")
     * @ORM\JoinColumn(nullable=false)
     */
    private Flight $flight;

    public function __construct(UuidInterface $id, int $name, float $percent)
    {
        $this->id = $id->toString();
        $this->name = $name;
        $this->percent = $percent;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): int
    {
        return $this->name;
    }


    public function getPercent(): float
    {
        return $this->percent;
    }

    public function getFlight(): Flight
    {
        return $this->flight;
    }

    public function setFlight(Flight $flight): void
    {
        $this->flight = $flight;
    }

    /**
     * @param float $percent
     */
    public function setPercent(float $percent): void
    {
        $this->percent = $percent;
    }
}
