<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangePackageActiveStatus;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;
use App\Project\Infrastructure\Entity\Package;

use App\Shared\Infrastructure\Validator\UniqueConstraintValidator\UniqueConstraint;

/**
 * @Assert\GroupSequence({"ChangePackageActiveStatusCommand", "strict"})
 * @UniqueConstraint(entity=Package::class, checkPositive=false, uniqueFields={"id":"packageId","project":"projectId"}, message="пакет не существует в проекте!", groups={"strict"})
 */
class ChangePackageActiveStatusCommand implements CommandInterface
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
    /**
     * @Assert\Type("bool")
     */
    public bool $status;
}
