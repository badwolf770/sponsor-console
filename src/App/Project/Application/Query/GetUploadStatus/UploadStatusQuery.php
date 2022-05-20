<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetUploadStatus;

use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\Existence\Existence;

class UploadStatusQuery implements QueryInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
}
