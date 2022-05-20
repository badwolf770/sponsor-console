<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Repository;

use App\Project\Domain\Repository\SpotRepositoryInterface;
use App\Project\Domain\Spot;
use App\Project\Infrastructure\Entity\Channel;
use App\Project\Infrastructure\Entity\Flight;
use App\Project\Infrastructure\Entity\Month;
use App\Project\Infrastructure\Entity\Package;
use App\Project\Infrastructure\Entity\Program;
use App\Project\Infrastructure\Entity\Rating;
use App\Project\Infrastructure\Entity\SponsorType;
use App\Project\Infrastructure\Entity\WeekDay;
use App\Project\Infrastructure\Hydrator\SpotHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class SpotDomainRepository implements SpotRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private SpotHydrator $spotHydrator;
    private ObjectRepository $packageRepository;
    private ObjectRepository $channelRepository;
    private ObjectRepository $programRepository;
    private ObjectRepository $sponsorTypeRepository;
    private ObjectRepository $monthRepository;
    private ObjectRepository $weekDayRepository;
    private ObjectRepository $spotRepository;
    private ObjectRepository $flightRepository;
    private ObjectRepository $ratingRepository;

    public function __construct(EntityManagerInterface $entityManager, SpotHydrator $spotHydrator)
    {
        $this->entityManager = $entityManager;
        $this->spotHydrator = $spotHydrator;
        $this->packageRepository = $this->entityManager->getRepository(Package::class);
        $this->channelRepository = $this->entityManager->getRepository(Channel::class);
        $this->programRepository = $this->entityManager->getRepository(Program::class);
        $this->sponsorTypeRepository = $this->entityManager->getRepository(SponsorType::class);
        $this->monthRepository = $this->entityManager->getRepository(Month::class);
        $this->weekDayRepository = $this->entityManager->getRepository(WeekDay::class);
        $this->spotRepository = $this->entityManager->getRepository(\App\Project\Infrastructure\Entity\Spot::class);
        $this->flightRepository = $this->entityManager->getRepository(Flight::class);
        $this->ratingRepository = $this->entityManager->getRepository(Rating::class);
    }

    public function save(Spot $spot): void
    {
        $packageEntity = $this->packageRepository->find($spot->getPackageId()->toString());
        $channelEntity = $this->channelRepository->findByNameInsensitive($spot->getChannel()->getName());
        if (!$channelEntity) {
            $channelEntity = new Channel($spot->getChannel()->getId(), $spot->getChannel()->getName());
            $this->entityManager->persist($channelEntity);
            $this->entityManager->flush();
        }
        $programEntity = $this->programRepository->findByNameInsensitive($spot->getProgram()->getName());
        if (!$programEntity) {
            $programEntity = new Program($spot->getProgram()->getId(), $spot->getProgram()->getName());
            $this->entityManager->persist($programEntity);
            $this->entityManager->flush();
        }
        $sponsorTypeEntity = $this->sponsorTypeRepository->findByNameInsensitive($spot->getSponsorType()->getName());
        if (!$sponsorTypeEntity) {
            $sponsorTypeEntity = new SponsorType($spot->getSponsorType()->getId(),
                $spot->getSponsorType()->getName());
            $this->entityManager->persist($sponsorTypeEntity);
            $this->entityManager->flush();
        }
        $monthEntity = $this->monthRepository->findByNameInsensitive($spot->getMonth()->getMonth());
        if (!$monthEntity) {
            $monthEntity = new Month(Uuid::uuid4(), $spot->getMonth()->getMonth());
            $this->entityManager->persist($monthEntity);
            $this->entityManager->flush();
        }

        $weekDayEntity = $this->weekDayRepository->findByNameInsensitive($spot->getWeekDay()->getWeekDay());
        if (!$weekDayEntity) {
            $weekDayEntity = new WeekDay(Uuid::uuid4(), $spot->getWeekDay()->getWeekDay());
            $this->entityManager->persist($weekDayEntity);
            $this->entityManager->flush();
        }

        $spotEntity =
            $this->spotRepository->findOneBy([
                'program' => $programEntity->getId(),
                'package' => $packageEntity->getId(),
                'channel' => $channelEntity->getId(),
                'sponsorType' => $sponsorTypeEntity->getId(),
                'month' => $monthEntity->getId(),
                'weekDay' => $weekDayEntity->getId(),
                'broadcastStart' => $spot->getBroadcastStart(),
                'broadcastFinish' => $spot->getBroadcastFinish()
            ]) ?: new \App\Project\Infrastructure\Entity\Spot(
                $spot->getId(),
                $programEntity,
                $sponsorTypeEntity,
                $monthEntity,
                $weekDayEntity,
                $spot->getBroadcastStart(),
                $spot->getBroadcastFinish(),
                $spot->getTimingInSec(),
                $spot->getOutsPerMonth(),
                $spot->getCost(),
                $packageEntity,
                $channelEntity);
        if ($spot->getFlight()) {
            $flightEntity = $this->flightRepository->find($spot->getFlight()->getId()->toString());
            $spotEntity->setFlight($flightEntity);
        }
        if ($spot->getRating()) {
            $ratingEntity = $this->ratingRepository->find($spot->getRating()->getId()->toString())
                ?: new Rating(
                    $spot->getRating()->getId(),
                    $spot->getRating()->getTvr(),
                    $spot->getRating()->getGrps20(),
                    $spot->getRating()->getAffinity()
                );
            $ratingEntity->setTvr($spot->getRating()->getTvr());
            $ratingEntity->setGrps20($spot->getRating()->getGrps20());
            $ratingEntity->setAffinity($spot->getRating()->getAffinity());

            $spotEntity->setRating($ratingEntity);
        }
        $this->entityManager->persist($spotEntity);
        $this->entityManager->flush();
    }

    public function findById(UuidInterface $id): Spot
    {
        $entity = $this->spotRepository->find($id->toString());
        return $this->spotHydrator->hydrateEntity($entity);
    }

    /* @return  Spot[] */
    public function findByPackageId(UuidInterface $packageId): array
    {
        $spotEntities = $this->spotRepository->findBy(['package' => $packageId->toString()]);
        $result = [];
        foreach ($spotEntities as $spotEntity) {
            $result[] = $this->spotHydrator->hydrateEntity($spotEntity);
        }
        return $result;
    }
}