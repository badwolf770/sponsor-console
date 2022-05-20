<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ImportTemplatePackage;

use App\Shared\Application\Command\CommandInterface;

class ImportTemplatePackageCommand implements CommandInterface
{
    public string $templateImportId;

    public function __construct(string $templateImportId)
    {
        $this->templateImportId = $templateImportId;
    }
}
