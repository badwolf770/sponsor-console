<?php
declare(strict_types=1);

namespace App\Project\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Month
{
    public const JANUARY = 'январь';
    public const FEBRUARY = 'февраль';
    public const MARCH = 'март';
    public const APRIL = 'апрель';
    public const MAY = 'май';
    public const JUNE = 'июнь';
    public const JULY = 'июль';
    public const AUGUST = 'август';
    public const SEPTEMBER = 'сентябрь';
    public const OCTOBER = 'октябрь';
    public const NOVEMBER = 'ноябрь';
    public const DECEMBER = 'декабрь';

    private string $month;

    public function __construct(string $month)
    {
        Assert::inArray($month, $this->getMonths(), "Месяц {$month} не найден");
        $this->month = $month;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    public function __toString(): string
    {
        return $this->month;
    }


    private function getMonths(): array
    {
        return [
            self::JANUARY,
            self::FEBRUARY,
            self::MARCH,
            self::APRIL,
            self::MAY,
            self::JUNE,
            self::JULY,
            self::AUGUST,
            self::SEPTEMBER,
            self::OCTOBER,
            self::NOVEMBER,
            self::DECEMBER,
        ];
    }

    public function getMonthOrder(): int
    {
        $orders = [
            self::JANUARY => 1,
            self::FEBRUARY => 2,
            self::MARCH => 3,
            self::APRIL => 4,
            self::MAY => 5,
            self::JUNE => 6,
            self::JULY => 7,
            self::AUGUST => 8,
            self::SEPTEMBER => 9,
            self::OCTOBER => 10,
            self::NOVEMBER => 11,
            self::DECEMBER => 12,
        ];
        return $orders[$this->month];
    }
}
