<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\TemplateImportRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=TemplateImportRepository::class)
 */
class TemplateImport
{

    public const STATUS_NEW        = 1;
    public const STATUS_IN_PROCESS = 2;
    public const STATUS_COMPLETE   = 5;
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="templateImports")
     * @ORM\JoinColumn(nullable=false)
     */
    private Project $project;

    /**
     * @ORM\ManyToOne(targetEntity=TemplateType::class, inversedBy="templateImports")
     * @ORM\JoinColumn(nullable=false)
     */
    private TemplateType $templateType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $filePath;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status = self::STATUS_NEW;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $startedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $finishedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $originalFileName;

    public function __construct(
        UuidInterface $id,
        Project       $project,
        TemplateType  $templateType,
        string        $filePath,
        string        $originalFileName
    ) {
        $this->id           = $id->toString();
        $this->project      = $project;
        $this->templateType = $templateType;
        $this->filePath         = $filePath;
        $this->createdAt        = new \DateTimeImmutable();
        $this->originalFileName = $originalFileName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getTemplateType(): TemplateType
    {
        return $this->templateType;
    }

    public function setTemplateType(TemplateType $templateType): self
    {
        $this->templateType = $templateType;

        return $this;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }
}
