<?php
declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\ValueObject\CalculationStatus;
use App\Shared\Domain\Collection\Collection;
use Ramsey\Uuid\UuidInterface;

class Package
{
    /**
     * @var Collection<UuidInterface>
     */
    private Collection $spotIds;

    public function __construct(
        private UuidInterface $id,
        private UuidInterface $projectId,
        private string        $name,
        private float         $tax,
        private bool          $active = true,
        private CalculationStatus $calculationStatus = CalculationStatus::NotCalculated
    )
    {
        $this->spotIds = new Collection();
    }

    public function getSpotIds(): Collection
    {
        return $this->spotIds;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProjectId(): UuidInterface
    {
        return $this->projectId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function addSpotId(UuidInterface $spotId): self
    {
        if (!$this->spotIds->contains($spotId)) {
            $this->spotIds->add($spotId);
        }
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function changeActivity(bool $active): void
    {
        $this->active = $active;
    }

    public function getCalculationStatus(): CalculationStatus
    {
        return $this->calculationStatus;
    }
}
