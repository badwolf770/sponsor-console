<?php
declare(strict_types=1);

namespace App\Project\Domain\ValueObject;

use Webmozart\Assert\Assert;

class WeekDay
{
    public const MONDAY = 'понельник';
    public const TUESDAY = 'вторник';
    public const WEDNESDAY = 'среда';
    public const THURSDAY = 'четверг';
    public const FRIDAY = 'пятница';
    public const SATURDAY = 'суббота';
    public const SUNDAY = 'воскресенье';
    public const WEEKDAY = 'будни';
    public const DAY_OFF = 'выходные';
    public const WEEK = 'неделя';

    private string $weekDay;

    public function __construct(string $weekDay)
    {
        Assert::inArray($weekDay, $this->getWeekDays(), "День недели {$weekDay} не найден");
        $this->weekDay = $weekDay;
    }

    /**
     * @return string
     */
    public function getWeekDay(): string
    {
        return $this->weekDay;
    }

    public function __toString(): string
    {
        return $this->weekDay;
    }


    private function getWeekDays(): array
    {
        return [
            self::MONDAY,
            self::TUESDAY,
            self::WEDNESDAY,
            self::THURSDAY,
            self::FRIDAY,
            self::SATURDAY,
            self::SUNDAY,
            self::WEEKDAY,
            self::DAY_OFF,
            self::WEEK,
        ];
    }

    public function getWeekDayOrder(): int
    {
        $orders = [
            self::MONDAY => 1,
            self::TUESDAY => 2,
            self::WEDNESDAY => 3,
            self::THURSDAY => 4,
            self::FRIDAY => 5,
            self::SATURDAY => 6,
            self::SUNDAY => 7,
            self::WEEKDAY => 8,
            self::DAY_OFF => 9,
            self::WEEK => 10,
        ];

        return $orders[$this->weekDay];
    }
}
