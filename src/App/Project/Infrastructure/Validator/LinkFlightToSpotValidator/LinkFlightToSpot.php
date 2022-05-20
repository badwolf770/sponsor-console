<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Validator\LinkFlightToSpotValidator;

use Symfony\Component\Validator\Constraint;

/**
 * Проверка того, что флайт и спот находятся в одном пакете
 * @Annotation
 */
class LinkFlightToSpot extends Constraint
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
    /* свойство команды с id спотов */
    public string $spotField = 'spotId';
}
