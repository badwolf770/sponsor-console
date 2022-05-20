<?php
declare(strict_types=1);

namespace App\Project\Domain\Repository;

use App\Project\Domain\Spot;
use Ramsey\Uuid\UuidInterface;

interface SpotRepositoryInterface
{
    public function save(Spot $spot): void;

    public function findById(UuidInterface $id): Spot;

    /* @return  Spot[] */
    public function findByPackageId(UuidInterface $packageId): array;
}