<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Validator\UniqueConstraintValidator;

use Symfony\Component\Validator\Constraint;

/**
 * Проверка существования уникальной записи в entity по списку полей
 * @Annotation
 */
class UniqueConstraint extends Constraint
{
    /* entity */
    public string $entity;
    /* список полей entity по которым проверить уникальность поле ентити => поле dto которую валидируем */
    public array $uniqueFields = [];
    public ?string $message = 'Такая сущность уже существует';
    /**
     * если true, то проверяется наличие записи, если false то отсутствие
     */
    public bool $checkPositive = true;

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
}
