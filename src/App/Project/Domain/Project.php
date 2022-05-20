<?php
declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\Entity\ExportFile;
use App\Project\Domain\ValueObject\ExportStatus;
use App\Shared\Domain\Collection\Collection;
use Ramsey\Uuid\UuidInterface;

class Project
{
    /**
     * @var Collection<UuidInterface>
     */
    private Collection $packageIds;

    private ?ExportFile $exportFile = null;
    private \DateTimeImmutable $createdAt;

    public function __construct(
        private UuidInterface $id,
        private string        $name,
        private string        $client,
        private string        $brand,
        private array         $reaches,
        ?\DateTimeImmutable $createdAt = null,
        private ExportStatus $exportStatus = ExportStatus::NotExported,
    )
    {
        $this->packageIds = new Collection();
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }

    public function addPackageId(UuidInterface $packageId): self
    {
        if (!$this->packageIds->contains($packageId)) {
            $this->packageIds->add($packageId);
        }
        return $this;
    }

    /**
     * @return Collection<UuidInterface>
     */
    public function getPackageIds(): Collection
    {
        return $this->packageIds;
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
    public function getClient(): string
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @return ExportFile
     */
    public function getExportFile(): ?ExportFile
    {
        return $this->exportFile;
    }

    /**
     * @param ExportFile $exportFile
     */
    public function addExportFile(ExportFile $exportFile): void
    {
        $this->exportFile = $exportFile;
    }

    public function getReaches(): array
    {
        return $this->reaches;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExportStatus(): ExportStatus
    {
        return $this->exportStatus;
    }
}
