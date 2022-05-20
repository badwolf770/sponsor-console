<?php
declare(strict_types=1);

namespace App\Project\Application\Export;

use App\Project\Application\Dto\SpotDto;
use App\Project\Application\Service\ReachService;
use App\Project\Application\Service\SpotService;
use App\Project\Application\Service\StatisticService;
use App\Project\Domain\Entity\ExportFile;
use App\Project\Domain\Entity\Reach;
use App\Project\Domain\Package;
use App\Project\Domain\Project;
use App\Project\Domain\Service\ExportToFileInterface;
use App\Project\Domain\Spot;
use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Shared\Application\Service\SortService;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;

class ExportService implements ExportToFileInterface
{
    private const EXPORT_DIR = '/upload/export/';
    private const PUBLIC_DIR = '/public';

    public function __construct(
        private KernelInterface         $kernel,
        private ProjectDomainRepository $projectDomainRepository,
        private PackageDomainRepository $packageDomainRepository,
        private SpotDomainRepository    $spotDomainRepository,
        private SpotService             $spotService,
        private SortService             $sortService,
        private StatisticService        $statisticService,
        private ReachService            $reachService
    )
    {
    }

    public function exportToFile(Project $project): void
    {
        $spreadsheet = new Spreadsheet();

        foreach ($project->getPackageIds() as $packageId) {
            $package = $this->packageDomainRepository->findById($packageId);
            if ($package->isActive()) {
                $worksheet = $spreadsheet->createSheet();
                $worksheet->setTitle($package->getName());
                $this->generatePackage($worksheet, $package, $project);
            }
        }

        $spreadsheet->removeSheetByIndex(0);
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $fileName = $this->generateTemplateImportFilePath($project);
        $path = $this->generateTemplateImportDir() . $fileName;
        $writer->save($path);

        $webPath = self::EXPORT_DIR . $fileName;
        $exportFile = new ExportFile(Uuid::uuid4(), $fileName, $path, $webPath);
        $project->addExportFile($exportFile);
        $this->projectDomainRepository->save($project);
    }

    private function generatePackage(Worksheet $worksheet, Package $package, Project $project): void
    {
        $startTableRow = 9;
        $startTableColumn = 1;
        $costsPerChanel = [];
        $sumColumn = null;

        $worksheet->setCellValueByColumnAndRow(1, 1, 'Бренд:');
        $worksheet->setCellValueByColumnAndRow(2, 1, $project->getBrand());

        $worksheet->setCellValueByColumnAndRow(1, 2, 'Клиент:');
        $worksheet->setCellValueByColumnAndRow(2, 2, $project->getClient());

        $worksheet->getStyleByColumnAndRow(1, 1, 2, 2)->getFont()->setBold(true);
        $worksheet->getStyleByColumnAndRow(1, 1, 1, 2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);


        $packageByGroups = [];

        $spots = $this->spotDomainRepository->findByPackageId($package->getId());

        $spotsByGroups = [];
        foreach ($spots as $spot) {
            $spotsByGroups
            [$spot->getChannel()->getName()]
            [$spot->getMonth()->getMonthOrder()]
            [$spot->getFlight()?->getName()]
            [$spot->getProgram()->getName()]
            [$spot->getWeekDay()->getWeekDayOrder()]
            [$spot->getBroadcastStart()]
            [$spot->getBroadcastFinish()]
            [$spot->getTimingInSec()]
            [$spot->getOutsPerMonth()][]
            [] = $spot;
        }
        $this->sortService->recursiveKsort($spotsByGroups);

        $spotsByChannels = [];
        foreach ($spotsByGroups as $channel => $months) {
            foreach ($months as $flights) {
                foreach ($flights as $programs) {
                    foreach ($programs as $program => $weekdays) {
                        foreach ($weekdays as $broadcastStarts) {
                            foreach ($broadcastStarts as $broadcastFinished) {
                                foreach ($broadcastFinished as $timings) {
                                    foreach ($timings as $outsPerMonths) {
                                        foreach ($outsPerMonths as $outsPerMonth) {
                                            foreach ($outsPerMonth as $spots) {
                                                foreach ($spots as $spot) {
                                                    $packageByGroups[$channel][$program][] = $spot;
                                                    $spotsByChannels[$channel][] = $spot;
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

        $flightsByRows = [];
        foreach ($packageByGroups as $channel => $programs) {
            $startTableColumn = 1;
            $channelCostsCoordinates = [];
            $startTableRow++;
            $startChannelTableRow = $startTableRow;
            $worksheet->setCellValueByColumnAndRow($startTableColumn, $startTableRow, $channel);
            $startTableRow++;

            $worksheet->getRowDimension($startTableRow)->setRowHeight(40);
            $programColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($programColumn, $startTableRow, 'Название программы');
            $worksheet->getColumnDimensionByColumn($programColumn)->setWidth(25);
            $startTableColumn++;
            $monthColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($monthColumn, $startTableRow, 'Месяц');
            $worksheet->getColumnDimensionByColumn($monthColumn)->setWidth(11);
            $startTableColumn++;
            $weekDayColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($weekDayColumn, $startTableRow, 'День недели');
            $worksheet->getColumnDimensionByColumn($weekDayColumn)->setWidth(13);
            $startTableColumn++;
            $broadcastStartColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($broadcastStartColumn, $startTableRow, 'Начало эфира');
            $worksheet->getColumnDimensionByColumn($broadcastStartColumn)->setWidth(10);
            $startTableColumn++;
            $broadcastFinishColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($broadcastFinishColumn, $startTableRow, 'Окончание эфира');
            $worksheet->getColumnDimensionByColumn($broadcastFinishColumn)->setWidth(10);
            $startTableColumn++;
            $sponsorTypeColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($sponsorTypeColumn, $startTableRow, 'Опция');
            $worksheet->getColumnDimensionByColumn($sponsorTypeColumn)->setWidth(100);
            $startTableColumn++;
            $outsPerMonthsColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($outsPerMonthsColumn, $startTableRow, 'Выходов в месяц');
            $worksheet->getColumnDimensionByColumn($outsPerMonthsColumn)->setWidth(10);
            $startTableColumn++;
            $timingColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($timingColumn, $startTableRow, 'Продолжи-тельность, сек.');
            $worksheet->getColumnDimensionByColumn($timingColumn)->setWidth(14);
            $startTableColumn++;
            $timingCountColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($timingCountColumn, $startTableRow, 'Итого секунд');
            $worksheet->getColumnDimensionByColumn($timingCountColumn)->setWidth(13);
            $startTableColumn++;
            $sumColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($sumColumn, $startTableRow, 'Сумма, руб. без НДС');
            $worksheet->getColumnDimensionByColumn($sumColumn)->setWidth(20);

            $startTableColumn++;
            $tvrColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($tvrColumn, $startTableRow - 1, 'Прогноз канала	');
            $mergeFrom = $worksheet->getCellByColumnAndRow($tvrColumn, $startTableRow - 1)->getCoordinate();
            $mergeTo = $worksheet->getCellByColumnAndRow($tvrColumn + 1, $startTableRow - 1)->getCoordinate();
            $worksheet->mergeCells("{$mergeFrom}:{$mergeTo}");
            $worksheet->setCellValueByColumnAndRow($tvrColumn, $startTableRow, 'TVR');
            $worksheet->getColumnDimensionByColumn($tvrColumn)->setWidth(9);

            $startTableColumn++;
            $grpColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($grpColumn, $startTableRow, 'GRPs20');
            $worksheet->getColumnDimensionByColumn($grpColumn)->setWidth(9);

            $startTableColumn++;
            $flightColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($flightColumn, $startTableRow, 'TA');
            $worksheet->getColumnDimensionByColumn($flightColumn)->setWidth(20);

            $startTableColumn++;
            $avTvrColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($avTvrColumn, $startTableRow, 'AV TVR');
            $worksheet->getColumnDimensionByColumn($avTvrColumn)->setWidth(9);

            $startTableColumn++;
            $trpsColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($trpsColumn, $startTableRow, 'TRPs');
            $worksheet->getColumnDimensionByColumn($trpsColumn)->setWidth(9);

            $startTableColumn++;
            $trps20Column = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($trps20Column, $startTableRow, 'TRPs20');
            $worksheet->getColumnDimensionByColumn($trps20Column)->setWidth(9);

            $startTableColumn++;
            $cppColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($cppColumn, $startTableRow, 'CPP');
            $worksheet->getColumnDimensionByColumn($cppColumn)->setWidth(20);

            $reachColumns = [];

            $reachesCount = count($project->getReaches());
            foreach ($project->getReaches() as $reach) {
                $reachGroup = [];
                $startTableColumn++;
                $reachColumn = $startTableColumn;
                $worksheet->setCellValueByColumnAndRow($reachColumn, $startTableRow, $this->reachService->generateThousandName($reach));
                $worksheet->getColumnDimensionByColumn($reachColumn)->setWidth(9);
                $reachGroup[] = $reachColumn;

                $reachThousandColumn = $reachColumn + $reachesCount;
                $worksheet->setCellValueByColumnAndRow($reachThousandColumn, $startTableRow, $this->reachService->generatePercentName($reach));
                $worksheet->getColumnDimensionByColumn($reachThousandColumn)->setWidth(9);
                $reachGroup[] = $reachThousandColumn;
                $reachColumns[$reach] = $reachGroup;
            }
            $startTableColumn += $reachesCount;

            $startTableColumn++;
            $otsColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($otsColumn, $startTableRow, 'OTS');
            $worksheet->getColumnDimensionByColumn($otsColumn)->setWidth(9);

            $startTableColumn++;
            $affinityColumn = $startTableColumn;
            $worksheet->setCellValueByColumnAndRow($affinityColumn, $startTableRow, 'Aff');
            $worksheet->getColumnDimensionByColumn($affinityColumn)->setWidth(9);

            $worksheet->getStyleByColumnAndRow($programColumn, $startTableRow, $affinityColumn, $startTableRow)
                ->applyFromArray($this->getStyles()['tableTitle']);
            $firstSpotRow = $startTableRow + 1;
            foreach ($programs as $program => $spots) {
                /* @var Spot $spot */
                foreach ($spots as $spot) {
                    $startTableRow++;
                    $rowDto = new SpotDto(
                        $program,
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
                    $this->spotService->prepareSpot($rowDto);
                    $worksheet->setCellValueByColumnAndRow($programColumn, $startTableRow, $rowDto->program);
                    $worksheet->setCellValueByColumnAndRow($monthColumn, $startTableRow, $rowDto->month);
                    $worksheet->setCellValueByColumnAndRow($weekDayColumn, $startTableRow,
                        $rowDto->weekDay);
                    $worksheet->setCellValueByColumnAndRow(
                        $broadcastStartColumn,
                        $startTableRow,
                        $rowDto->broadcastStart ? $this->spotService->prepareBroadcastTime($rowDto->broadcastStart) : null);
                    $worksheet->setCellValueByColumnAndRow(
                        $broadcastFinishColumn,
                        $startTableRow,
                        $rowDto->broadcastFinish ? $this->spotService->prepareBroadcastTime($rowDto->broadcastFinish) : null,
                    );
                    $worksheet->setCellValueByColumnAndRow(
                        $sponsorTypeColumn,
                        $startTableRow,
                        $rowDto->sponsorType);
                    $worksheet->setCellValueByColumnAndRow($outsPerMonthsColumn, $startTableRow,
                        $rowDto->outsPerMonths);
                    $outsPerMonthsCoordinates = $worksheet->getCellByColumnAndRow($outsPerMonthsColumn,
                        $startTableRow)->getCoordinate();
                    $worksheet->setCellValueByColumnAndRow($timingColumn, $startTableRow, $rowDto->timing);
                    $timingInSecCoordinates = $worksheet->getCellByColumnAndRow($timingColumn,
                        $startTableRow)->getCoordinate();

                    $worksheet->setCellValueByColumnAndRow(
                        $timingCountColumn,
                        $startTableRow,
                        $rowDto->outsPerMonths ? "={$outsPerMonthsCoordinates}*{$timingInSecCoordinates}" : null);

                    $worksheet->setCellValueByColumnAndRow(
                        $sumColumn,
                        $startTableRow,
                        $rowDto->cost);
                    $sumColumnCoordinates = $worksheet->getCellByColumnAndRow($sumColumn,
                        $startTableRow)->getCoordinate();
                    $channelCostsCoordinates[] = $sumColumnCoordinates;

                    if ($spot->getRating() && (int)$rowDto->outsPerMonths > 0) {
                        $calculatedStatistics = $this->statisticService->calculateStatistics($spot);
                        $worksheet->setCellValueByColumnAndRow(
                            $tvrColumn,
                            $startTableRow,
                            $spot->getRating()->getTvr());
                        $worksheet->setCellValueByColumnAndRow(
                            $grpColumn,
                            $startTableRow,
                            $spot->getRating()->getGrps20());
                        $worksheet->setCellValueByColumnAndRow(
                            $avTvrColumn,
                            $startTableRow,
                            $calculatedStatistics->avTvr);
                        $worksheet->setCellValueByColumnAndRow(
                            $trpsColumn,
                            $startTableRow,
                            $calculatedStatistics->trps);
                        $worksheet->setCellValueByColumnAndRow(
                            $trps20Column,
                            $startTableRow,
                            $calculatedStatistics->trps20);
                        $worksheet->setCellValueByColumnAndRow(
                            $cppColumn,
                            $startTableRow,
                            $calculatedStatistics->cpp);

                        foreach ($reachColumns as $reachName => $columns) {
                            /* @var Reach $foundReach */
                            $foundReach = $spot->getFlight()->getReaches()->filter(fn(Reach $reach) => $reachName === $reach->getName())->getIterator()->current();
                            $worksheet->setCellValueByColumnAndRow(
                                $columns[0],
                                $startTableRow,
                                round($foundReach->getPercent() * 100, 2)
                            );
                            $worksheet->setCellValueByColumnAndRow(
                                $columns[1],
                                $startTableRow,
                                $this->statisticService->calculateReachThousand($spot->getFlight()->getUniverse(), round($foundReach->getPercent() * 100, 2))
                            );
                        }
                        $spotsByChannel = count($spotsByChannels[$channel]);
                        $destinationRow = $startChannelTableRow + $spotsByChannel;
                        $tvrColumnLetter = Coordinate::stringFromColumnIndex($tvrColumn);
                        $flightColumnLetter = Coordinate::stringFromColumnIndex($flightColumn);
                        $worksheet->setCellValueByColumnAndRow(
                            $otsColumn,
                            $startTableRow,
                            "=SUMIF({$flightColumnLetter}{$startChannelTableRow}:{$flightColumnLetter}{$destinationRow},\"{$spot->getFlight()->getName()}\",{$tvrColumnLetter}{$startChannelTableRow}:{$tvrColumnLetter}{$destinationRow})*{$spot->getFlight()->getUniverse()}");

                        $worksheet->setCellValueByColumnAndRow(
                            $affinityColumn,
                            $startTableRow,
                            $spot->getRating()->getAffinity());
                    }

                    $worksheet->setCellValueByColumnAndRow(
                        $flightColumn,
                        $startTableRow,
                        $rowDto->flight);

                    $flightsByRows[$rowDto->flight][] = $startTableRow;
                }
            }
            $flightRanges = [];
            foreach ($flightsByRows as $flightName => $flightByRows) {
                $firstRangeRow = null;
                $previousRow = null;
                foreach ($flightByRows as $index => $row) {
                    if ($index === 0) {
                        $firstRangeRow = $row;
                        $previousRow = $row - 1;
                    }
                    if ($row - $previousRow === 1) {
                        $previousRow = $row;
                        if (count($flightByRows) - 1 === $index) {
                            $flightRanges[] = [$firstRangeRow, $previousRow];
                            $firstRangeRow = $row;
                            $previousRow = $row;
                        }
                    } else {
                        $flightRanges[] = [$firstRangeRow, $previousRow];
                        $firstRangeRow = $row;
                        $previousRow = $row;
                    }
                }
            }
            $flightColumnCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($flightColumn);
            $otsColumnCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($otsColumn);
            foreach ($flightRanges as $range) {
                [$from, $to] = $range;
                $worksheet->mergeCells("{$flightColumnCoordinate}{$from}:{$flightColumnCoordinate}{$to}");
                $worksheet->getStyleByColumnAndRow($flightColumn, $from, $flightColumn, $to)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $worksheet->mergeCells("{$otsColumnCoordinate}{$from}:{$otsColumnCoordinate}{$to}");
                $worksheet->getStyleByColumnAndRow($otsColumn, $from, $otsColumn, $to)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                foreach ($reachColumns as $reachName => $columns) {
                    $reachColumnCoordinateOne = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columns[0]);
                    $reachColumnCoordinateTwo = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columns[1]);
                    $worksheet->mergeCells("{$reachColumnCoordinateOne}{$from}:{$reachColumnCoordinateOne}{$to}");
                    $worksheet->getStyleByColumnAndRow($columns[0], $from, $columns[0], $to)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);

                    $worksheet->mergeCells("{$reachColumnCoordinateTwo}{$from}:{$reachColumnCoordinateTwo}{$to}");
                    $worksheet->getStyleByColumnAndRow($columns[1], $from, $columns[1], $to)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                        ->setVertical(Alignment::VERTICAL_CENTER);
                }
            }

            $worksheet->getStyleByColumnAndRow($sumColumn, $firstSpotRow, $sumColumn, $startTableRow)
                ->applyFromArray($this->getStyles()['costColumn']);
            $worksheet->getStyleByColumnAndRow($cppColumn, $firstSpotRow, $cppColumn, $startTableRow)
                ->applyFromArray($this->getStyles()['costColumn']);

            $worksheet->getStyleByColumnAndRow($programColumn, $firstSpotRow, $startTableColumn, $startTableRow)
                ->applyFromArray($this->getStyles()['borderTable']);
            $startTableRow++;
            $firstCosts = array_shift($channelCostsCoordinates);
            $lastCosts = count($channelCostsCoordinates) > 1 ? array_pop($channelCostsCoordinates) : $firstCosts;
            $worksheet->setCellValueByColumnAndRow($sumColumn, $startTableRow, "=SUM({$firstCosts}:{$lastCosts})");
            $costsPerChanel[] = $worksheet->getCellByColumnAndRow($sumColumn, $startTableRow)->getCoordinate();

            $worksheet->getStyleByColumnAndRow($sumColumn, $startTableRow, $sumColumn, $startTableRow)
                ->applyFromArray($this->getStyles()['sumCostColumn']);
        }
        $startTableRow++;
        $startTableRow++;
        $firstChannelCosts = array_shift($costsPerChanel);
        $lastChannelCosts = count($costsPerChanel) > 1 ? array_pop($costsPerChanel) : $firstChannelCosts;

        $worksheet->setCellValueByColumnAndRow($sumColumn - 1, $startTableRow,
            'Бюджет 2022 без НДС');
        $worksheet->getStyleByColumnAndRow($sumColumn - 1, $startTableRow)->getAlignment()->setWrapText(true);

        $worksheet->setCellValueByColumnAndRow($sumColumn, $startTableRow,
            "=SUM({$firstChannelCosts}:{$lastChannelCosts})");

        $worksheet->getStyleByColumnAndRow($sumColumn - 1, $startTableRow, $sumColumn, $startTableRow)
            ->applyFromArray($this->getStyles()['costColumn']);
        $worksheet->getStyleByColumnAndRow($sumColumn, $startTableRow)
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THICK);
    }

    private function generateTemplateImportFilePath(
        Project $project,
        string  $extension = 'xlsx'
    ): string
    {
        return "{$project->getClient()}_{$project->getBrand()}" . microtime() . ".{$extension}";
    }

    private function generateTemplateImportDir(): string
    {
        $dir = $this->kernel->getProjectDir() . self::PUBLIC_DIR . self::EXPORT_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        return $dir;
    }

    private function getStyles(): array
    {
        return [
            'tableTitle' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_DOTTED,
                        'color' => [
                            'rgb' => '3f3f3f'
                        ]
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_GRADIENT_LINEAR,
                    'color' => ['rgb' => '696969']
                ],
                'font' => [
                    'size' => 10,
                    'bold' => true,
                    'color' => [
                        'rgb' => 'ffffff'
                    ],
                    'name' => 'Times New Roman'
                ],
                'alignment' => [
                    'wrapText' => true,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
            'costColumn' => [
                'font' => [
                    'bold' => true
                ],
                'numberFormat' => [
                    'formatCode' => '#,##0.00_-"₽"'
                ]
            ],
            'sumCostColumn' => [
                'font' => [
                    'bold' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_GRADIENT_LINEAR,
                    'color' => ['rgb' => '92D050']
                ],
                'numberFormat' => [
                    'formatCode' => '#,##0.00_-"₽"'
                ]
            ],
            'borderTable' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_DOTTED,
                        'color' => [
                            'rgb' => '3f3f3f'
                        ]
                    ],
                ],
            ]
        ];
    }
}
