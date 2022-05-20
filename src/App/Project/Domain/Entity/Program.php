<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use App\Project\Domain\Spot;
use App\Shared\Domain\Collection\Collection;
use Ramsey\Uuid\UuidInterface;

class Program
{
    /**
     * @var Collection<Spot>
     */
    private Collection $spots;

    public function __construct(
        private UuidInterface $id,
        private string        $name
    ) {
        $this->spots = new Collection();
    }

    /**
     * @return Collection<Spot>
     */
    public function getSpots(): Collection
    {
        return $this->spots;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function addSpot(Spot $spot): self
    {
        if (!$this->spots->contains($spot)) {
            $this->spots->add($spot);
        }
        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->contains($spot)) {
            $this->spots->removeElement($spot);
        }
        return $this;
    }
}
