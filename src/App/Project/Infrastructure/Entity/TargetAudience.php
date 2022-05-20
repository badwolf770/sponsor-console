<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\TargetAudienceRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=TargetAudienceRepository::class)
 */
class TargetAudience
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
     * @ORM\Column(type="json",options={"jsonb"=true})
     */
    private array $conditions;

    public function __construct(UuidInterface $id, string $name, array $conditions)
    {
        $this->id         = $id->toString();
        $this->name       = $name;
        $this->conditions = $conditions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
}
