<?php
declare(strict_types=1);

namespace App\Project\Application\Service;

use App\Project\Application\Dto\SpotDto;
use App\Project\Application\TemplateImport\Strategy\EverestTemplateImportStrategy;

class SpotService
{
    public function prepareSpot(SpotDto $dto): void
    {
        if ($dto->program === EverestTemplateImportStrategy::EMPTY_PROGRAM_NAME) {
            $dto->program         = null;
            $dto->month           = null;
            $dto->monthOrder      = 99;
            $dto->weekDay         = null;
            $dto->broadcastStart  = null;
            $dto->broadcastFinish = null;
            $dto->outsPerMonths   = null;
            $dto->timing          = null;
            $dto->timingCount     = null;
        }
    }

    public function prepareBroadcastTime(int $time): string
    {
        $timeString = (string)$time;
        if (strlen($timeString) === 3) {
            $timeString = '0' . $timeString;
        }

        $array  = str_split($timeString);
        $result = '';
        foreach ($array as $index => $character) {
            if ($index === 2) {
                $result .= ':';
            }
            $result .= $character;
        }

        return $result;
    }
}
