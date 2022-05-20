<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Validator\FlightsLinksToSpotsByPackageValidator;

use App\Project\Application\Command\LinkFlightToSpot\LinkFlightToSpotCommand;
use App\Project\Infrastructure\Repository\FlightRepository;
use App\Project\Infrastructure\Repository\SpotRepository;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FlightsLinksToSpotsByPackageValidator extends ConstraintValidator
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
            if (!$constraint instanceof FlightsLinksToSpotsByPackage) {
                throw new UnexpectedTypeException($constraint, FlightsLinksToSpotsByPackage::class);
            }
            $packageId = $value->{$constraint->packageIdField};
            $flights = $this->flightRepository->findBy(['package' => $packageId]);
            $flightIds = [];
            foreach ($flights as $flight){
                $flightIds[] = $flight->getId();
            }
            $spots = $this->spotRepository->count(['flight' => $flightIds]);
            if($spots === 0){
                $this->context->buildViolation("Необходимо закрепить флайты за спотами!")
                    ->setParameter($constraint->packageIdField, $packageId)
                    ->atPath($constraint->packageIdField)
                    ->addViolation();
            }
        }
    }
}
