<?php
declare(strict_types=1);

namespace App\Project\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class ExportFile
{
    public function __construct(
        private UuidInterface $id,
        private string        $name,
        private string        $path,
        private string        $webPath
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

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getWebPath(): string
    {
        return $this->webPath;
    }
}
