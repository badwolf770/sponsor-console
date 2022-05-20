<?php
declare(strict_types=1);

namespace App\Project\Domain\Repository;

use App\Project\Domain\Package;
use Ramsey\Uuid\UuidInterface;

interface PackageRepositoryInterface
{
    public function findById(UuidInterface $id): Package;

    public function save(Package $package):void;
}