<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetFlightsByPackage;

use App\Shared\Application\Query\QueryInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\Existence\Existence;

class GetFlightsByPackageQuery implements QueryInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Package", key="id", checkPositive=false, message="Пакет {{id}} не существует!")
     */
    public string $packageId;
}