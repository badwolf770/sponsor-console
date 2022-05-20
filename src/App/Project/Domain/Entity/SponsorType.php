<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class SponsorType
{

    public function __construct(
        private UuidInterface $id,
        private string $name
    ) {
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
    public function getName(): string
    {
        return $this->name;
    }

}
