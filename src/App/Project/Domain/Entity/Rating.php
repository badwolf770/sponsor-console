<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class Rating
{
    public function __construct(
        private UuidInterface $id,
        private float         $tvr,
        private float         $grps20,
        private float         $affinity
    ) {
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getTvr(): float
    {
        return $this->tvr;
    }

    /**
     * @return float
     */
    public function getGrps20(): float
    {
        return $this->grps20;
    }

    /**
     * @return float
     */
    public function getAffinity(): float
    {
        return $this->affinity;
    }

    public function changeTvr(float $tvr): void
    {
        $this->tvr = $tvr;
    }

    public function changeGrps20(float $grps20): void
    {
        $this->grps20 = $grps20;
    }

    public function changeAffinity(float $affinity): void
    {
        $this->affinity = $affinity;
    }
}
