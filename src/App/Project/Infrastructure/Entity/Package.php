<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\PackageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PackageRepository::class)
 */
class Package
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
     * @ORM\Column(type="float")
     * @Serializer\Groups({"default"})
     */
    private float $tax;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="packages")
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;

    /**
     * @ORM\OneToMany(targetEntity=Spot::class, mappedBy="package", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $spots;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Flight::class, mappedBy="package")
     */
    private Collection $flights;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $calculationStartedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $calculationFinishedAt = null;

    public function __construct(UuidInterface $id, string $name, float $tax, Project $project, bool $active)
    {
        $this->id        = $id->toString();
        $this->name      = $name;
        $this->tax       = $tax;
        $this->project   = $project;
        $this->active    = $active;
        $this->spots     = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->flights   = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection<int, Spot>
     */
    public function getSpots(): Collection
    {
        return $this->spots;
    }

    public function addSpot(Spot $spot): self
    {
        if (!$this->spots->contains($spot)) {
            $this->spots[] = $spot;
            $spot->setPackage($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getPackage() === $this) {
                $spot->setPackage(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Flight>
     */
    public function getFlights(): Collection
    {
        return $this->flights;
    }

    public function addFlight(Flight $flight): self
    {
        if (!$this->flights->contains($flight)) {
            $this->flights[] = $flight;
            $flight->setPackage($this);
        }

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCalculationStartedAt(): ?\DateTime
    {
        return $this->calculationStartedAt;
    }

    public function setCalculationStartedAt(?\DateTime $calculationStartedAt): void
    {
        $this->calculationStartedAt = $calculationStartedAt;
    }

    public function getCalculationFinishedAt(): ?\DateTime
    {
        return $this->calculationFinishedAt;
    }

    public function setCalculationFinishedAt(?\DateTime $calculationFinishedAt): void
    {
        $this->calculationFinishedAt = $calculationFinishedAt;
    }
}
