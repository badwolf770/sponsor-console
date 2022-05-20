<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport\Strategy;

use App\Project\Domain\Package;
use App\Project\Domain\Spot;
use App\Shared\Domain\Collection\Collection;

class ImportedData
{
    /**
     * @param Package $package
     * @param Collection<Spot> $spots
     */
    public function __construct(private Package $package, private Collection $spots)
    {
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    /**
     * @return Collection<Spot>
     */
    public function getSpots(): Collection
    {
        return $this->spots;
    }
}