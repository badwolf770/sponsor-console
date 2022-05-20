<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Validator\LinkFlightToSpotValidator;

use App\Project\Application\Command\LinkFlightToSpot\LinkFlightToSpotCommand;
use App\Project\Infrastructure\Repository\FlightRepository;
use App\Project\Infrastructure\Repository\SpotRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LinkFlightToSpotValidator extends ConstraintValidator
{
    public function __construct(
        private SpotRepository   $spotRepository,
        private FlightRepository $flightRepository
    )
    {
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param LinkFlightToSpotCommand $value The value that should be validated
     * @param Constraint $constraint
     * @throws \ReflectionException
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!empty($value)) {
            if (!$constraint instanceof LinkFlightToSpot) {
                throw new UnexpectedTypeException($constraint, LinkFlightToSpot::class);
            }
            $reflectionClass = new \ReflectionClass($value);
            $property = $reflectionClass->getProperty($constraint->spotField);
            $propertyType = $property->getType()->getName();
            if ($propertyType === 'string') {
                $this->check($value->{$constraint->spotField}, $value->flightId, $constraint->spotField);
            }
            if ($propertyType === 'array') {
                foreach ($value->{$constraint->spotField} as $spotId) {
                    $this->check($spotId, $value->flightId, $constraint->spotField);
                }
            }
        }
    }

    private function check(string $spotId, string $flightId, string $field): void
    {
        $spot = $this->spotRepository->find($spotId);
        $flight = $this->flightRepository->find($flightId);

        if ($spot && $flight
            && $spot->getPackage()->getId() !== $flight->getPackage()->getId()) {
            $this->context->buildViolation("Спот $spotId и флайт $flightId не относятся к одному пакету")
                ->setParameter($field, $spotId)
                ->setParameter('flightId', $flightId)
                ->atPath($field)
                ->addViolation();
        }
    }
}
