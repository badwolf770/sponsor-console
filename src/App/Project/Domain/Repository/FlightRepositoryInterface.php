<?php
declare(strict_types=1);

namespace App\Project\Domain\Repository;

use App\Project\Domain\Flight;
use Ramsey\Uuid\UuidInterface;

interface FlightRepositoryInterface
{
    public function save(Flight $flight):void;
    public function findById(UuidInterface $id): Flight;
}