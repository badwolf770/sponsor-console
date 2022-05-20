<?php
declare(strict_types=1);

namespace App\Project\Domain\Service;

use App\Project\Domain\Project;

interface ExportToFileInterface
{
    public function exportToFile(Project $project): void;
}
