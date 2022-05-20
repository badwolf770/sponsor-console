<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Validator\Existence;

use Symfony\Component\Validator\Constraint;

/**
 * Ограничение для проверки наличия записи в выбранной таблице
 * @Annotation
 */
class Existence extends Constraint
{
    /**
     * Наименование таблицы в которой проверяется существование записи
     * В аннотации объявляется запись формата @Ex\Existence(entity="Campaign", key="id")
     * где, Ex указывает на папку размещения ограничения и валидатора
     * @var string
     */
    public string $entity;

    /**
     * Ключ по которому идет проверка
     * @var string
     */
    public string $key;


    /**
     * если true, то проверяется наличие записи, если false то отсутствие
     * @var bool
     */
    public bool $checkPositive = true;

    /**
     * @var string
     */
    public string $message = "Кампания {{id}} уже существует!";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return static::class . 'Validator';
    }

    public function __construct($options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);
    }
}
