<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport\Strategy;

use App\Project\Application\TemplateImport\TemplateType;
use App\Project\Infrastructure\Entity\TemplateImport;
use Webmozart\Assert\Assert;

class TemplateImportFactory
{
    public function create(TemplateImport $templateImport): TemplateImportStrategyInterface
    {
        $templateName = $templateImport->getTemplateType()->getName();
        $strategies   = TemplateType::getStrategies();
        Assert::keyExists($strategies, $templateName, "Не найден импортер для шаблона {$templateName}");

        $strategy = $strategies[$templateName];
        Assert::notInstanceOf($strategy, TemplateImportStrategyInterface::class,
            "Шаблон {$templateName} должен имплементировать " . TemplateImportStrategyInterface::class);

        return new $strategy();
    }
}
