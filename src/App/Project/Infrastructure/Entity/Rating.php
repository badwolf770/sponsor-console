<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=RatingRepository::class)
 */
class Rating
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @Serializer\Groups({"default"})
     */
    private string $id;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"default"})
     */
    private float $tvr;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"default"})
     */
    private float $grps20;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"default"})
     */
    private float $affinity;

    public function __construct(
        UuidInterface $id,
        float         $tvr,
        float         $grps20,
        float         $affinity
    ) {
        $this->id       = $id->toString();
        $this->tvr      = $tvr;
        $this->grps20   = $grps20;
        $this->affinity = $affinity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTvr(): float
    {
        return $this->tvr;
    }

    public function getGrps20(): float
    {
        return $this->grps20;
    }

    public function getAffinity(): float
    {
        return $this->affinity;
    }

    public function setTvr(float $tvr): void
    {
        $this->tvr = $tvr;
    }

    public function setGrps20(float $grps20): void
    {
        $this->grps20 = $grps20;
    }

    public function setAffinity(float $affinity): void
    {
        $this->affinity = $affinity;
    }
}
