<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport\Strategy;

use App\Project\Domain\Project;
use App\Project\Infrastructure\Entity\TemplateImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface TemplateImportStrategyInterface
{
    public function import(Spreadsheet $spreadsheet, TemplateImport $templateImport): ImportedData;
}
