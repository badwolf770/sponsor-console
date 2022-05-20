<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Validator\FlightsLinksToSpotsByPackageValidator;

use Symfony\Component\Validator\Constraint;

/**
 * Проверка того, что флайты пакета закреплены за спотами
 * @Annotation
 */
class FlightsLinksToSpotsByPackage extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return static::class . 'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
    public string $packageIdField;
}
