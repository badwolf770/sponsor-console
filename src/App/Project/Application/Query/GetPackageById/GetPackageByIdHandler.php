<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetPackageById;

use App\Project\Application\Dto\SpotDto;
use App\Project\Application\Query\ReadModel\ChannelReadModel;
use App\Project\Application\Query\ReadModel\FlightReadModel;
use App\Project\Application\Query\ReadModel\MonthReadModel;
use App\Project\Application\Query\ReadModel\PackageReadModel;
use App\Project\Application\Query\ReadModel\ProgramReadModel;
use App\Project\Application\Query\ReadModel\RatingReadModel;
use App\Project\Application\Query\ReadModel\ReachReadModel;
use App\Project\Application\Query\ReadModel\SpotReadModel;
use App\Project\Application\Service\ReachService;
use App\Project\Application\Service\SpotService;
use App\Project\Application\Service\StatisticService;
use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Application\Service\SortService;
use Ramsey\Uuid\Uuid;

class GetPackageByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProjectDomainRepository $projectDomainRepository,
        private PackageDomainRepository $packageDomainRepository,
        private SpotDomainRepository    $spotDomainRepository,
        private SpotService             $spotService,
        private SortService             $sortService,
        private ReachService            $reachService,
        private StatisticService        $statisticService
    )
    {
    }

    public function __invoke(GetPackageByIdQuery $query): PackageReadModel
    {
        $project = $this->projectDomainRepository->findById(Uuid::fromString($query->projectId));
        $package = $this->packageDomainRepository->findById(Uuid::fromString($query->packageId));

        $packageReadModel = new PackageReadModel();
        $packageReadModel->id = $package->getId()->toString();
        $packageReadModel->name = $package->getName();
        $packageReadModel->tax = $package->getTax();
        $packageReadModel->active = $package->isActive();
        $packageReadModel->calculationStatus = $package->getCalculationStatus()->value;
        $spotsByGroups = [];
        $flightsByName = [];
        $programsByName = [];
        $monthsByOrder = [];
        $channelsByName = [];
        $universesByFlight = [];
        $spotsById = [];

        $spots = $this->spotDomainRepository->findByPackageId(Uuid::fromString($query->packageId));
        foreach ($spots as $spot) {
            $spotsById[$spot->getId()->toString()] = $spot;
            $channelsByName[$spot->getChannel()->getName()] = $spot->getChannel();

            $spotDto = new SpotDto(
                $spot->getProgram()->getName(),
                $spot->getMonth()->getMonth(),
                $spot->getMonth()->getMonthOrder(),
                $spot->getWeekDay()->getWeekDay(),
                $spot->getBroadcastStart(),
                $spot->getBroadcastFinish(),
                $spot->getSponsorType()->getName(),
                $spot->getOutsPerMonth(),
                $spot->getTimingInSec(),
                0,
                $spot->getCost(),
                $spot->getFlight()?->getName()
            );
            $this->spotService->prepareSpot($spotDto);
            $monthsByOrder[$spotDto->monthOrder] = $spotDto->month;
            $programsByName[$spotDto->program] = $spot->getProgram();

            $spotReadModel = new SpotReadModel();
            $spotReadModel->id = $spot->getId()->toString();
            $spotReadModel->sponsorType = $spotDto->sponsorType;
            $spotReadModel->weekDay = $spotDto->weekDay;
            $spotReadModel->timingInSec = $spotDto->timing;
            $spotReadModel->outsPerMonth = $spotDto->outsPerMonths;
            $spotReadModel->cost = $spotDto->cost;
            $spotReadModel->broadcastStart = $spotDto->broadcastStart ? $this->spotService->prepareBroadcastTime($spotDto->broadcastStart) : null;
            $spotReadModel->broadcastFinish = $spotDto->broadcastFinish ? $this->spotService->prepareBroadcastTime($spotDto->broadcastFinish) : null;
            $spotReadModel->totalTiming = $spotDto->timing ? $spotDto->timing * $spotDto->outsPerMonths : null;
            $flightReadModel = new FlightReadModel();
            if ($spot->getFlight()) {
                $flightReadModel->id = $spot->getFlight()->getId()->toString();
                $flightReadModel->name = $spot->getFlight()->getName();
            }
            $flightsByName[$spotDto->monthOrder . $flightReadModel->name] = $flightReadModel;

            $ratingReadModel = new RatingReadModel();
            if ($spot->getRating() && (int)$spotDto->outsPerMonths > 0) {
                $calculatedStatistics = $this->statisticService->calculateStatistics($spot);
                $universesByFlight[$spot->getFlight()?->getName()] = $spot->getFlight()?->getUniverse();
                $ratingReadModel->id = $spot->getRating()->getId()->toString();
                $ratingReadModel->tvr = $spot->getRating()->getTvr();
                $ratingReadModel->grps20 = $spot->getRating()->getGrps20();
                $ratingReadModel->avTvr = $calculatedStatistics->avTvr;
                $ratingReadModel->trps = $calculatedStatistics->trps;
                $ratingReadModel->trps20 = $calculatedStatistics->trps20;
                $ratingReadModel->cpp = $calculatedStatistics->cpp;
                $ratingReadModel->affinity = $spot->getRating()->getAffinity();
            }

            $spotReadModel->rating = $ratingReadModel;

            $reaches = [];
            $reachesByAudience = [];

            $reachesByName = [];
            if ($spot->getOutsPerMonth() > 0) {
                if ($spot->getFlight()?->getReaches()->count() > 0) {
                    foreach ($spot->getFlight()->getReaches() as $reach) {
                        $reachesByName[$reach->getName()] = $reach;
                    }
                }
                foreach ($project->getReaches() as $reach) {
                    $reachReadModel = new ReachReadModel();
                    if (array_key_exists($reach, $reachesByName)) {
                        $reachEntity = $reachesByName[$reach];
                        $reachReadModel->id = $reachEntity->getId()->toString();
                        $reachReadModel->name = $this->reachService->generatePercentName($reach);
                        $reachReadModel->value = round($reachEntity->getPercent() * 100, 2);
                        $reaches[] = $reachReadModel;

                        $reachByAudienceReadModel = new ReachReadModel();
                        $reachByAudienceReadModel->name = $this->reachService->generateThousandName($reach);
                        $reachByAudienceReadModel->value = $this->statisticService->calculateReachThousand($spot->getFlight()->getUniverse(), round($reachEntity->getPercent() * 100, 2));
                    } else {
                        $reachReadModel->name = $this->reachService->generatePercentName($reach);
                        $reaches[] = $reachReadModel;

                        $reachByAudienceReadModel = new ReachReadModel();
                        $reachByAudienceReadModel->name = $this->reachService->generateThousandName($reach);
                    }
                    $reachesByAudience[] = $reachByAudienceReadModel;
                }

                $reaches = array_merge($reachesByAudience, $reaches);
                $flightReadModel->reaches = $reaches;
            }

            $spotsByGroups
            [$spot->getChannel()->getName()]
            [$spotDto->monthOrder]
            [$spotDto->flight]
            [$spotDto->program]
            [$spot->getWeekDay()->getWeekDayOrder()]
            [$spot->getBroadcastStart()]
            [$spot->getBroadcastFinish()]
            [$spot->getTimingInSec()]
            [$spot->getOutsPerMonth()][]
            [] = $spotReadModel;
        }
        $this->sortService->recursiveKsort($spotsByGroups);

        foreach ($spotsByGroups as $channel => $months) {
            $channel = $channelsByName[$channel];
            $channelReadModel = new ChannelReadModel();
            $channelReadModel->id = $channel->getId()->toString();
            $channelReadModel->name = $channel->getName();
            $packageReadModel->channels[] = $channelReadModel;
            foreach ($months as $month => $flights) {
                $monthReadModel = new MonthReadModel();
                $monthReadModel->name = $monthsByOrder[$month];
                $channelReadModel->months[] = $monthReadModel;
                foreach ($flights as $flight => $programs) {
                    $flightReadModel = $flightsByName[$month . $flight];
                    $monthReadModel->flights[] = $flightReadModel;
                    if ($flightReadModel->name) {
                        $universe = $universesByFlight[$flight] ?? null;
                        if ($universe) {
                            $tvrSum = 0;
                            foreach ($programs as $weekdays) {
                                foreach ($weekdays as $broadcastStarts) {
                                    foreach ($broadcastStarts as $broadcastFinished) {
                                        foreach ($broadcastFinished as $timings) {
                                            foreach ($timings as $outsPerMonths) {
                                                foreach ($outsPerMonths as $outsPerMonth) {
                                                    foreach ($outsPerMonth as $spots) {
                                                        foreach ($spots as $spot) {
                                                            $tvrSum += (float)$spotsById[$spot->id]->getRating()?->getTvr();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $flightReadModel->ots = round(($tvrSum * $universe) / 100, 2);
                        }
                    }
                    foreach ($programs as $program => $weekdays) {
                        $programEntity = $programsByName[$program];
                        $programReadModel = new ProgramReadModel();
                        $programReadModel->id = $program ? $programEntity->getId()->toString() : null;
                        $programReadModel->name = $program ? $programEntity->getName() : null;
                        $flightReadModel->programs[] = $programReadModel;
                        foreach ($weekdays as $broadcastStarts) {
                            foreach ($broadcastStarts as $broadcastFinished) {
                                foreach ($broadcastFinished as $timings) {
                                    foreach ($timings as $outsPerMonths) {
                                        foreach ($outsPerMonths as $outsPerMonth) {
                                            foreach ($outsPerMonth as $spots) {
                                                foreach ($spots as $spot) {
                                                    $programReadModel->spots[] = $spot;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $packageReadModel;
    }
}