<?php
declare(strict_types=1);

namespace App\Project\Domain\Repository;

use App\Project\Domain\Project;
use Ramsey\Uuid\UuidInterface;

interface ProjectRepositoryInterface
{
    public function save(Project $project): void;

    public function findById(UuidInterface $id): Project;

    /* @return Project[] */
    public function findAll(): array;
}
