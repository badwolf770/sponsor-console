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

class EverestTemplateImportStrategy implements TemplateImportStrategyInterface
{
    public const EMPTY_PROGRAM_NAME = 'интернет';

    public function import(Spreadsheet $spreadsheet, TemplateImport $templateImport): ImportedData
    {
        $array = $spreadsheet->getActiveSheet()->toArray();

        $packageName = $spreadsheet->getActiveSheet()->getTitle();
        $tax = 0;
        $channelName = str_replace(['Спонсорское предложение на телеканале', ' для'], ['', ''], $array[0][0]);

        $spotsArray = [];

        foreach ($array as $rowIndex => $row) {
            $titleRowIndexed = false;
            if ($row[0] === 'Название программы') {
                $programIndex = null;
                $monthIndex = null;
                $weekDayIndex = null;
                $timeStartIndex = null;
                $timeFinishIndex = null;
                $sponsorTypeIndex = null;
                $outsPerMonthIndex = null;
                $timingIndex = null;
                $costIndex = null;
                if (!$titleRowIndexed) {
                    foreach ($row as $cellIndex => $cellValue) {
                        if (empty($cellValue)) {
                            continue;
                        }
                        if (str_contains($cellValue, 'Название программы')) {
                            $programIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Месяц')) {
                            $monthIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'День недели')) {
                            $weekDayIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Начало эфира')) {
                            $timeStartIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Окончание эфира')) {
                            $timeFinishIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Опция') || str_contains($cellValue, 'ОПЦИЯ')) {
                            $sponsorTypeIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'в месяц')) {
                            $outsPerMonthIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Продолжи')) {
                            $timingIndex = $cellIndex;
                        }
                        if (str_contains($cellValue, 'Сумма, руб')) {
                            $costIndex = $cellIndex;
                        }
                    }
                    $titleRowIndexed = true;
                }

                $programArray = array_slice($array, $rowIndex + 1);
                $month = null;
                $weekDay = null;
                $timeStart = null;
                $timeFinish = null;
                foreach ($programArray as $programRow) {
                    if (empty($programRow[$sponsorTypeIndex])) {
                        break;
                    }

                    $program = $programRow[$programIndex];
                    $month = $programRow[$monthIndex] ?: $month;
                    $weekDay = $programRow[$weekDayIndex] ?: $weekDay;
                    $timeStart = $programRow[$timeStartIndex] ?: $timeStart;
                    $timeFinish = $programRow[$timeFinishIndex] ?: $timeFinish;

                    $spotsArray[] = [
                        'program' => trim((string)$program) ?: self::EMPTY_PROGRAM_NAME,
                        'month' => mb_strtolower(trim((string)$month)),
                        'weekDay' => mb_strtolower(trim((string)$weekDay)),
                        'timeStart' => (int)str_replace(':', '', $timeStart),
                        'timeFinish' => (int)str_replace(':', '', $timeFinish),
                        'sponsorType' => trim((string)$programRow[$sponsorTypeIndex]),
                        'outsPerMonth' => (int)$programRow[$outsPerMonthIndex],
                        'timing' => (int)$programRow[$timingIndex],
                        'cost' => (float)str_replace('р.', '', trim((string)$programRow[$costIndex])),
                    ];
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
}
