<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $webPath;

    public function __construct(UuidInterface $id, string $name, string $path, string $webPath)
    {
        $this->id      = $id->toString();
        $this->name    = $name;
        $this->path    = $path;
        $this->webPath = $webPath;
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getWebPath(): string
    {
        return $this->webPath;
    }

    public function setWebPath(string $webPath): self
    {
        $this->webPath = $webPath;

        return $this;
    }
}
