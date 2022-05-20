<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport;

use App\Project\Application\TemplateImport\Strategy\EverestTemplateImportStrategy;

class TemplateType
{
    public const EVEREST       = 'everest';
    public const GPM           = 'gpm';
    public const UTV           = 'utv';
    public const VGTRK         = 'vgtrk';
    public const FIRST_CHANNEL = 'first';
    public const TYPES         = [
        self::EVEREST,
        self::GPM,
        self::UTV,
        self::VGTRK,
        self::FIRST_CHANNEL
    ];

    public static function getStrategies(): array
    {
        return [
            self::EVEREST => EverestTemplateImportStrategy::class
        ];
    }
}
