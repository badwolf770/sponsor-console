<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=FlightRepository::class)
 * @ORM\Table(name="flight",uniqueConstraints={@UniqueConstraint(name="unique_flight",
 *     columns={"name", "package_id"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Flight
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
     * @ORM\ManyToOne(targetEntity=TargetAudience::class)
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"default"})
     */
    private TargetAudience $targetAudience;

    /**
     * @ORM\ManyToOne(targetEntity=Package::class, inversedBy="flights")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Groups({"default"})
     */
    private Package $package;

    /**
     * @ORM\OneToMany(targetEntity=Spot::class, mappedBy="flight")
     */
    private Collection $spots;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private \DateTime $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Reach::class, mappedBy="flight")
     */
    private Collection $reaches;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Groups({"default"})
     */
    private ?float $universe = null;

    public function __construct(UuidInterface $id, string $name, TargetAudience $audience, Package $package)
    {
        $this->id             = $id->toString();
        $this->name           = $name;
        $this->targetAudience = $audience;
        $this->package        = $package;
        $this->spots          = new ArrayCollection();
        $this->reaches        = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTargetAudience(): TargetAudience
    {
        return $this->targetAudience;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function setPackage(Package $package): void
    {
        $this->package = $package;
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
            $spot->setFlight($this);
        }

        return $this;
    }

    public function removeSpot(Spot $spot): self
    {
        if ($this->spots->removeElement($spot)) {
            // set the owning side to null (unless already changed)
            if ($spot->getFlight() === $this) {
                $spot->setFlight(null);
            }
        }

        return $this;
    }

    public function setTargetAudience(TargetAudience $targetAudience): void
    {
        $this->targetAudience = $targetAudience;
    }

    /**
     * @return Collection<int, Reach>
     */
    public function getReaches(): Collection
    {
        return $this->reaches;
    }

    public function addReach(Reach $reach): self
    {
        if (!$this->reaches->contains($reach)) {
            $this->reaches[] = $reach;
            $reach->setFlight($this);
        }

        return $this;
    }

    public function removeReach(Reach $reach): self
    {
        if ($this->reaches->removeElement($reach)) {
            // set the owning side to null (unless already changed)
            if ($reach->getFlight() === $this) {
                $reach->setFlight(null);
            }
        }

        return $this;
    }

    public function getUniverse(): ?float
    {
        return $this->universe;
    }

    public function setUniverse(?float $universe): void
    {
        $this->universe = $universe;
    }
}
