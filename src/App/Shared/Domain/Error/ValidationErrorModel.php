<?php
declare(strict_types=1);

namespace App\Shared\Domain\Error;

use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ValidationErrorModel Схема ошибки валидации
 * @package App\Common\Error
 */
class ValidationErrorModel
{
    /**
     * @var string
     * @OA\Property (
     *     property="message",
     *     description="Результат валидации",
     *     type="string",
     *     example="Validation Failed"
     *  )
     * @Serializer\Groups({"default"})
     */
    public string $message;

    /**
     * @var string
     * @OA\Property (
     *     property="detail",
     *     description="Детализация ошибки",
     *     type="string",
     *     example="flightId: The record in the 'Flight' table with identity 4111 doesn't exist"
     * )
     * @Serializer\Groups({"default"})
     */
    public string $detail;

    /**
     * @var ValidationViolation[]
     * @OA\Property (
     *     property="violations",
     *     type="array",
     *     @OA\Items(
     *          type="object",
     *          ref=@Model(type=ValidationViolation::class)
     *     )
     * )
     * @Serializer\Groups({"default"})
     */
    public array $violations;

    public function __construct(string $message, string $detail, array $violations)
    {
        $this->message      = $message;
        $this->detail     = $detail;
        $this->violations = $violations;
    }
}