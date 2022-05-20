<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class TargetAudience
{
    public function __construct(
        private UuidInterface $id, private string $name)
    {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}