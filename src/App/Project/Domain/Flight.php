<?php
declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\Entity\Reach;
use App\Project\Domain\Entity\TargetAudience;
use App\Shared\Domain\Collection\Collection;
use Ramsey\Uuid\UuidInterface;

class Flight
{
    /**
     * @var Collection<Reach>
     */
    private Collection $reaches;
    private ?float $universe = null;

    public function __construct(
        private UuidInterface  $id,
        private string         $name,
        private TargetAudience $targetAudience,
        private UuidInterface  $packageId
    )
    {
        $this->reaches = new Collection;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addReach(Reach $reach): self
    {
        if (!$this->reaches->contains($reach)) {
            $this->reaches->add($reach);
        }
        return $this;
    }

    public function removeReach(Reach $reach): self
    {
        if ($this->reaches->contains($reach)) {
            $this->reaches->removeElement($reach);
        }
        return $this;
    }

    /**
     * @return Collection<Reach>
     */
    public function getReaches(): Collection
    {
        return $this->reaches;
    }

    public function getUniverse(): ?float
    {
        return $this->universe;
    }

    public function setUniverse(?float $universe): void
    {
        $this->universe = $universe;
    }

    public function getTargetAudience(): TargetAudience
    {
        return $this->targetAudience;
    }

    public function getPackageId(): UuidInterface
    {
        return $this->packageId;
    }
}
