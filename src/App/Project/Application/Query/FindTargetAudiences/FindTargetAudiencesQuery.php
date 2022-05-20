<?php
declare(strict_types=1);

namespace App\Project\Application\Query\FindTargetAudiences;

use App\Shared\Application\Query\QueryInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FindTargetAudiencesQuery implements QueryInterface
{
    /**
     * @OA\Property(description="Название целевой", example="all 18+")
     * @Assert\NotBlank()
     * @Assert\Length(min=1 ,max=255)
     */
    public string $name;
}
