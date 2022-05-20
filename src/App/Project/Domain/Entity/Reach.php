<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class Reach
{
    public function __construct(
        private UuidInterface $id,
        private int           $name,
        private float         $percent
    )
    {
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): int
    {
        return $this->name;
    }

    /**
     * @return float example=0.5
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    public function changePercent(float $percent): void
    {
        $this->percent = $percent;
    }
}
