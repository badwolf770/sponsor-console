<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\WeekDayRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=WeekDayRepository::class)
 */
class WeekDay
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $name;


    public function __construct(UuidInterface $id, string $name)
    {
        $this->id = $id->toString();

        $this->name = $name;
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
}
