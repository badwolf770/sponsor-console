<?php
declare(strict_types=1);

namespace App\Project\Application\Command\CalculateStatisticsByPackage;

use App\Project\Domain\Entity\Rating;
use App\Project\Domain\Entity\Reach;
use App\Project\Domain\Project;
use App\Project\Domain\Spot;
use App\Project\Infrastructure\Repository\FlightDomainRepository;
use App\Project\Infrastructure\Repository\PackageRepository;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Shared\Infrastructure\Bus\AsyncEvent\AsyncEventHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

class CalculateStatisticsByPackageHandler implements AsyncEventHandlerInterface
{
    public function __construct(
        private SpotDomainRepository    $spotDomainRepository,
        private ProjectDomainRepository $projectDomainRepository,
        private FlightDomainRepository  $flightDomainRepository,
        private EntityManagerInterface  $entityManager,
        private LoggerInterface         $logger,
        private PackageRepository       $packageRepository
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(CalculateStatisticsByPackageCommand $command)
    {
        try {
            $project = $this->projectDomainRepository->findById(Uuid::fromString($command->projectId));
            $spots = $this->spotDomainRepository->findByPackageId(Uuid::fromString($command->packageId));
            $packageEntity = $this->packageRepository->find($command->packageId);
            $packageEntity->setCalculationStartedAt(new \DateTime());
            $this->entityManager->flush($packageEntity);

            $spotsByFlight = [];

            foreach ($spots as $spot) {
                if ($spot->getFlight()) {
                    $spotsByFlight[$spot->getFlight()->getId()->toString()][] = $spot;
                }
            }

            foreach ($spotsByFlight as $flightSpots) {
                $this->calculateByFlightSpots($project, $flightSpots);
            }
            $packageEntity->setCalculationFinishedAt(new \DateTime());
            $this->entityManager->flush($packageEntity);
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage(), ['class' => self::class, 'data' => (array)$command]);
            throw new RecoverableMessageHandlingException();
        }
    }

    /**
     * @param Project $project
     * @param Spot[] $spots
     * @return void
     * @throws \Throwable
     */
    private function calculateByFlightSpots(Project $project, array $spots): void
    {
        $this->entityManager->beginTransaction();
        try {
            $flight = $spots[0]->getFlight();
            foreach ($project->getReaches() as $reachValue) {
                $reach = new Reach(Uuid::uuid4(), $reachValue, 1.1);
                $flight->addReach($reach);
            }
            $flight->setUniverse(1000);
            $this->flightDomainRepository->save($flight);

            foreach ($spots as $spot) {
                $rating = new Rating(Uuid::uuid4(), 1, 10, 100);
                $spot->addRating($rating);
                $this->spotDomainRepository->save($spot);
            }
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}