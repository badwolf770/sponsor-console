<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Validator\UniqueConstraintValidator;

use App\Project\Application\Command\LinkFlightToSpot\LinkFlightToSpotCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param LinkFlightToSpotCommand $value The value that should be validated
     * @param Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!empty($value)) {
            if (!$constraint instanceof UniqueConstraint) {
                throw new UnexpectedTypeException($constraint, UniqueConstraint::class);
            }
            $query = $this->entityManager->createQueryBuilder()
                ->select('t')
                ->from($constraint->entity, 't');
            foreach ($constraint->uniqueFields as $field => $dtoField) {
                if (property_exists($value, $dtoField)) {
                    $query->andWhere("t.$field = :$field")->setParameter($field, $value->$dtoField);
                }
            }

            $result = $query->setMaxResults(1)->getQuery()->getOneOrNullResult();
            $checkCondition = $constraint->checkPositive ? !empty($result) : empty($result);
            if ($checkCondition) {
                $violation = $this->context->buildViolation($constraint->message)
                    //->setParameter('spotId', $value->spotId)
                    //->setParameter('flightId', $value->flightId)
                    ->atPath($constraint->uniqueFields[array_key_first($constraint->uniqueFields)]);
                foreach ($constraint->uniqueFields as $dtoField) {
                    if (property_exists($value, $dtoField)) {
                        $violation->setParameter($dtoField, $value->$dtoField);
                    }
                }
                $violation->addViolation();
            }
        }
    }
}
