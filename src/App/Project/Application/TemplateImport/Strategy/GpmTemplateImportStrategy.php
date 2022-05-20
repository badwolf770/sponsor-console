<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport\Strategy;

use App\Project\Domain\Entity\Channel;
use App\Project\Domain\Entity\Program;
use App\Project\Domain\Entity\SponsorType;
use App\Project\Domain\Package;
use App\Project\Domain\Spot;
use App\Project\Domain\ValueObject\Month;
use App\Project\Domain\ValueObject\WeekDay;
use App\Project\Infrastructure\Entity\TemplateImport;
use App\Shared\Domain\Collection\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Ramsey\Uuid\Uuid;

class GpmTemplateImportStrategy implements TemplateImportStrategyInterface
{
    public function import(Spreadsheet $spreadsheet, TemplateImport $templateImport): ImportedData
    {
        $array = $spreadsheet->getActiveSheet()->toArray();

        $packageName = $spreadsheet->getActiveSheet()->getTitle();
        $tax = 0;
        $channelName = null;

        $spotsArray = [];

        $months = [
            'янв',
            'фев',
            'март',
            'апр',
            'май',
            'июнь',
            'июль',
            'август',
            'сент',
            'окт',
            'нояб',
            'декаб',
        ];
        $monthsMap = [
            'янв' => Month::JANUARY,
            'фев' => Month::FEBRUARY,
            'март' => Month::MARCH,
            'апр' => Month::APRIL,
            'май' => Month::MAY,
            'июнь' => Month::JUNE,
            'июль' => Month::JULY,
            'август' => Month::AUGUST,
            'сент' => Month::SEPTEMBER,
            'окт' => Month::OCTOBER,
            'нояб' => Month::NOVEMBER,
            'декаб' => Month::DECEMBER,
        ];
        $weekDays = [
            'пн' => WeekDay::MONDAY,
            'вт' => WeekDay::TUESDAY,
            'ср' => WeekDay::WEDNESDAY,
            'чт' => WeekDay::THURSDAY,
            'пт' => WeekDay::FRIDAY,
            'сб' => WeekDay::SATURDAY,
            'вс' => WeekDay::SUNDAY,
        ];
        $monthsIndexes = [];
        foreach ($array as $rowIndex => $row) {
            $titleRowIndexed = false;
            if ($row[0] === 'Программа') {
                $channelName = trim($array[$rowIndex + 1][0]);
                $programIndex = null;
                $monthIndex = null;
                $weekDayIndex = null;
                $timeStartIndex = null;
                $timeFinishIndex = null;
                $sponsorTypeIndex = null;
                $outsPerMonthIndex = null;
                $timingIndex = null;
                $costPerOutIndex = null;
                //$outsIndex = null;
                $discountIndex = null;
                $marginIndex = null;
                if (!$titleRowIndexed) {
                    foreach ($row as $cellIndex => $cellValue) {
                        if (empty($cellValue)) {
                            continue;
                        }
                        if (str_contains($cellValue, 'Программа')) {
                            $programIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Спонсорское обозначение')) {
                            $sponsorTypeIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Хр-ж')) {
                            $timingIndex = $cellIndex;
                        }
//                        if (str_contains($cellValue, 'Кол-во выходов в')) {
//                            $outsIndex = $cellIndex;
//                        }
                        if ($this->findColumnTitleByRowsRange($array, $rowIndex, $rowIndex + 2, 'янв')) {
                            //$monthIndex = $cellIndex;
                            foreach ($months as $index => $name) {
                                $monthsIndexes[$name] = $cellIndex + $index;
                            }
                        }
                        if (str_contains($cellValue, 'Стоимость спонс. обозн. в 1-й программе')) {
                            $costPerOutIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Скидка, %')) {
                            $costPerOutIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Наценка')) {
                            $marginIndex = $cellIndex;
                        }
                    }
                    $titleRowIndexed = true;
                }

                $programArray = array_slice($array, $rowIndex + 1);
                $program = null;
                $month = null;
                $weekDay = null;
                $timeStart = null;
                $timeFinish = null;
                foreach ($programArray as $programRow) {
                    foreach ($monthsIndexes as $month => $columnIndex) {
                        if ((int)$programRow[$columnIndex] > 0) {
                            $month = $programRow[$monthsMap[$month]];
                            $programParts = explode(';', $programRow[$programIndex]);
                            if ($programParts > 0) {
                                $program = trim($programParts[0]);
                                $weekDay = isset($programParts[1]) ? $weekDays[trim($programParts[1])] : $weekDay;
                                $timeStart = isset($programParts[2]) ? trim($programParts[2]) : $timeStart;
                                $timeFinish = isset($programParts[2]) ? trim($programParts[2]) : $timeFinish;
                            }

                            $cost = (float)str_replace('р.', '', trim((string)$programRow[$costPerOutIndex]));
                            $cost = $cost * (int)$programRow[$columnIndex] * (1 + (float)$programRow[$marginIndex]) * (1 - (float)$programRow[$discountIndex]);
                            $spotsArray[] = [
                                'program' => $program,
                                'month' => mb_strtolower(trim((string)$month)),
                                'weekDay' => mb_strtolower(trim((string)$weekDay)),
                                'timeStart' => (int)str_replace(':', '', $timeStart),
                                'timeFinish' => (int)str_replace(':', '', $timeFinish),
                                'sponsorType' => trim((string)$programRow[$sponsorTypeIndex]),
                                'outsPerMonth' => (int)$programRow[$columnIndex],
                                'timing' => (int)$programRow[$timingIndex],
                                'cost' => $cost,
                            ];
                        }
                    }
                }
            }
        }

        $package = new Package(Uuid::uuid4(), Uuid::fromString($templateImport->getProject()->getId()), $packageName, $tax);
        $channel = new Channel(Uuid::uuid4(), $channelName);
        $programsByName = [];
        $spots = new Collection();
        foreach ($spotsArray as $spotArray) {
            if (!array_key_exists($spotArray['program'], $programsByName)) {
                $program = new Program(Uuid::uuid4(), $spotArray['program']);
                $programsByName[$spotArray['program']] = $program;
            } else {
                $program = $programsByName[$spotArray['program']];
            }
            $sponsorType = new SponsorType(Uuid::uuid4(), $spotArray['sponsorType']);
            $month = new Month($spotArray['month']);
            $weekDay = new WeekDay($spotArray['weekDay']);
            $spot = new Spot(
                Uuid::uuid4(),
                $channel,
                $program,
                $package->getId(),
                $sponsorType,
                $month,
                $weekDay,
                $spotArray['timing'],
                $spotArray['outsPerMonth'],
                $spotArray['cost'],
                $spotArray['timeStart'],
                $spotArray['timeFinish'],
            );
            $spots->add($spot);
            $package->addSpotId($spot->getId());
        }

        return new ImportedData($package, $spots);
    }

    private function findColumnTitleByRowsRange(array $rows, int $from, int $to, string $lookingForColumn): ?int
    {
        $columnIndex = null;
        $slicedRows = array_slice($rows, $from, $to);
        foreach ($slicedRows as $row) {
            foreach ($row as $index => $cell) {
                $columnIndex = $cell === $lookingForColumn ? $index : null;
            }
        }

        return $columnIndex;
    }
}
