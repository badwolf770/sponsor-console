<?php
declare(strict_types=1);

namespace App\Project\Domain;

use App\Project\Domain\Entity\Channel;
use App\Project\Domain\Entity\Program;
use App\Project\Domain\Entity\Rating;
use App\Project\Domain\Entity\SponsorType;
use App\Project\Domain\ValueObject\Month;
use App\Project\Domain\ValueObject\WeekDay;
use Ramsey\Uuid\UuidInterface;

class Spot
{
    private ?Flight $flight = null;
    private ?Rating $rating = null;

    public function __construct(
        private UuidInterface $id,
        private Channel       $channel,
        private Program       $program,
        private UuidInterface $packageId,
        private SponsorType   $sponsorType,
        private Month         $month,
        private WeekDay       $weekDay,
        private int           $timingInSec,
        private int           $outsPerMonth,
        private float         $cost,
        private int           $broadcastStart,
        private int           $broadcastFinish
    )
    {
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function getPackageId(): UuidInterface
    {
        return $this->packageId;
    }

    public function getSponsorType(): SponsorType
    {
        return $this->sponsorType;
    }

    public function getMonth(): Month
    {
        return $this->month;
    }

    public function getWeekDay(): WeekDay
    {
        return $this->weekDay;
    }

    public function getTimingInSec(): int
    {
        return $this->timingInSec;
    }

    public function getOutsPerMonth(): int
    {
        return $this->outsPerMonth;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function addFlight(Flight $flight): self
    {
        $this->flight = $flight;

        return $this;
    }

    public function getBroadcastStart(): int
    {
        return $this->broadcastStart;
    }

    public function getBroadcastFinish(): int
    {
        return $this->broadcastFinish;
    }

    public function addRating(Rating $rating): self
    {
        $this->rating = $rating;


        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }
}
