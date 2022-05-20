<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Validator\Existence;

use Doctrine\DBAL\Connection;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistenceValidator extends ConstraintValidator
{
    /**
     *
     * @var Connection
     */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value The value that should be validated
     * @param Constraint $constraint
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!empty($value)) {
            if (!$constraint instanceof Existence) {
                throw new UnexpectedTypeException($constraint, Existence::class);
            }
            $tableName  = strtolower(preg_replace('/\B([A-Z])/', '_$1', $constraint->entity));
            $columnName = strtolower(preg_replace('/\B([A-Z])/', '_$1', $constraint->key));

            $qb        = $this->connection->createQueryBuilder();
            $predicate = '"' . $columnName . '" = :value';
            $table     = '"' . $tableName . '"';
            $qb->select(['*'])
                ->from($table)
                ->where($predicate)->setParameter(':value', $value);
            $data           = $qb->execute()->fetchOne();
            $checkCondition = $constraint->checkPositive ? !empty($data) : empty($data);
            if ($checkCondition) {
                $this->context->buildViolation($constraint->message)->setParameter('{{id}}',
                    (string)$value)->setParameter('{{entity}}', (string)$constraint->entity)->addViolation();
            }
        }
    }
}
