<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\SpotRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=SpotRepository::class)
 * @ORM\Table(name="spot",uniqueConstraints={@UniqueConstraint(name="unique_idx",
 *     columns={"program_id", "channel_id", "package_id", "sponsor_type_id", "month_id", "week_day_id", "broadcast_start","broadcast_finish", "timing_in_sec", "outs_per_month"})})
 */
class Spot
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Program::class, inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private Program $program;

    /**
     * @ORM\ManyToOne(targetEntity=SponsorType::class, inversedBy="spots", cascade={"persist"})
     */
    private SponsorType $sponsorType;

    /**
     * @ORM\ManyToOne(targetEntity=Month::class, inversedBy="spots", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private Month $month;

    /**
     * @ORM\ManyToOne(targetEntity=WeekDay::class, cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private WeekDay $weekDay;

    /**
     * @ORM\Column(type="integer",options={"comment":"время начала, без разделителя :, пример 800 = 08:00"})
     */
    private int $broadcastStart;
    /**
     * @ORM\Column(type="integer",options={"comment":"время окончания, без разделителя :, пример 1500 = 15:00"})
     */
    private int $broadcastFinish;

    /**
     * @ORM\Column(type="integer")
     */
    private int $timingInSec;

    /**
     * @ORM\Column(type="integer")
     */
    private int $outsPerMonth;

    /**
     * @ORM\Column(type="float")
     */
    private float $cost;

    /**
     * @ORM\ManyToOne(targetEntity=Package::class, inversedBy="spots")
     * @ORM\JoinColumn(nullable=false)
     */
    private Package $package;

    /**
     * @ORM\ManyToOne(targetEntity=Channel::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Channel $channel;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Flight::class, inversedBy="spots")
     */
    private ?Flight $flight = null;

    /**
     * @ORM\OneToOne(targetEntity=Rating::class, cascade={"persist", "remove"})
     */
    private ?Rating $rating = null;

    public function __construct(
        UuidInterface $id,
        Program       $program,
        SponsorType   $sponsorType,
        Month         $month,
        WeekDay       $weekDay,
        int           $broadcastStart,
        int           $broadcastFinish,
        int           $timingInSec,
        int           $outsPerMonth,
        float         $cost,
        Package       $package,
        Channel       $channel
    )
    {
        $this->id = $id->toString();
        $this->program = $program;
        $this->sponsorType = $sponsorType;
        $this->month = $month;
        $this->weekDay = $weekDay;
        $this->broadcastStart = $broadcastStart;
        $this->broadcastFinish = $broadcastFinish;
        $this->timingInSec = $timingInSec;
        $this->outsPerMonth = $outsPerMonth;
        $this->cost = $cost;
        $this->package = $package;
        $this->channel = $channel;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function setProgram(Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getSponsorType(): SponsorType
    {
        return $this->sponsorType;
    }

    public function setSponsorType(SponsorType $sponsorType): self
    {
        $this->sponsorType = $sponsorType;

        return $this;
    }

    public function getMonth(): Month
    {
        return $this->month;
    }

    public function setMonth(Month $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getWeekDay(): WeekDay
    {
        return $this->weekDay;
    }

    public function setWeekDay(WeekDay $weekDay): self
    {
        $this->weekDay = $weekDay;

        return $this;
    }

    public function getTimingInSec(): int
    {
        return $this->timingInSec;
    }

    public function setTimingInSec(int $timingInSec): self
    {
        $this->timingInSec = $timingInSec;

        return $this;
    }

    public function getOutsPerMonth(): int
    {
        return $this->outsPerMonth;
    }

    public function setOutsPerMonth(int $outsPerMonth): self
    {
        $this->outsPerMonth = $outsPerMonth;

        return $this;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getBroadcastStart(): int
    {
        return $this->broadcastStart;
    }

    public function setBroadcastStart(int $broadcastStart): void
    {
        $this->broadcastStart = $broadcastStart;
    }

    public function getBroadcastFinish(): int
    {
        return $this->broadcastFinish;
    }

    public function setBroadcastFinish(int $broadcastFinish): void
    {
        $this->broadcastFinish = $broadcastFinish;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }

    public function setPackage(Package $package): self
    {
        $this->package = $package;

        return $this;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): self
    {
        $this->flight = $flight;

        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function setRating(?Rating $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
