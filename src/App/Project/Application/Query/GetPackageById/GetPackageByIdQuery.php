<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetPackageById;

use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use App\Project\Infrastructure\Entity\Package;
use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;

/**
 * @Assert\GroupSequence({"GetPackageByIdQuery", "strict"})
 * @UniqueConstraint(entity=Package::class, checkPositive=false, uniqueFields={"id":"packageId","project":"projectId"}, message="пакет не существует в проекте!", groups={"strict"})
 */
class GetPackageByIdQuery implements QueryInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Package", key="id", checkPositive=false, message="Пакет {{id}} не существует!")
     */
    public string $packageId;
}