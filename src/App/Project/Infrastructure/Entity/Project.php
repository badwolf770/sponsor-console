<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @Serializer\Groups({"default"})
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"default"})
     */
    private string $name;
    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"default"})
     */
    private string $client;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"default"})
     */
    private string $brand;

    /**
     * @ORM\OneToMany(targetEntity=Package::class, mappedBy="project", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private Collection $packages;

    /**
     * @ORM\OneToMany(targetEntity=TemplateImport::class, mappedBy="project")
     */
    private $templateImports;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Groups({"default"})
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private ?\DateTime $exportStartedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private ?\DateTime $exportFinishedAt = null;

    /**
     * @ORM\OneToOne(targetEntity=File::class, cascade={"persist", "remove"})
     */
    private ?File $file = null;

    /**
     * @ORM\Column(type="json", options={"jsonb"=true}, nullable=true)
     * @Serializer\Groups({"default"})
     */
    private array $reaches;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTime $deletedAt;

    public function __construct(UuidInterface $id, string $name, string $client, string $brand, array $reaches)
    {
        $this->id              = $id->toString();
        $this->name            = $name;
        $this->client          = $client;
        $this->brand           = $brand;
        $this->reaches         = $reaches;
        $this->packages        = new ArrayCollection();
        $this->templateImports = new ArrayCollection();
        $this->createdAt       = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, Package>
     */
    public function getPackages(): Collection
    {
        return $this->packages;
    }

    public function addPackage(Package $package): self
    {
        if (!$this->packages->contains($package)) {
            $this->packages[] = $package;
            $package->setProject($this);
        }

        return $this;
    }

    public function removePackage(Package $package): self
    {
        if ($this->packages->removeElement($package)) {
            // set the owning side to null (unless already changed)
            if ($package->getProject() === $this) {
                $package->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TemplateImport>
     */
    public function getTemplateImports(): Collection
    {
        return $this->templateImports;
    }

    public function addTemplateImport(TemplateImport $templateImport): self
    {
        if (!$this->templateImports->contains($templateImport)) {
            $this->templateImports[] = $templateImport;
            $templateImport->setProject($this);
        }

        return $this;
    }

    public function removeTemplateImport(TemplateImport $templateImport): self
    {
        if ($this->templateImports->removeElement($templateImport)) {
            // set the owning side to null (unless already changed)
            if ($templateImport->getProject() === $this) {
                $templateImport->setProject(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getReaches(): ?array
    {
        return $this->reaches;
    }

    public function setReaches(?array $reaches): self
    {
        $this->reaches = $reaches;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getExportStartedAt(): ?\DateTime
    {
        return $this->exportStartedAt;
    }

    public function setExportStartedAt(?\DateTime $exportStartedAt): void
    {
        $this->exportStartedAt = $exportStartedAt;
    }

    public function getExportFinishedAt(): ?\DateTime
    {
        return $this->exportFinishedAt;
    }

    public function setExportFinishedAt(?\DateTime $exportFinishedAt): void
    {
        $this->exportFinishedAt = $exportFinishedAt;
    }
}
