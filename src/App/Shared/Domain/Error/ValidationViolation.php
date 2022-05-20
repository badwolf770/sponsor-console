<?php
declare(strict_types=1);

namespace App\Shared\Domain\Error;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class ValidationViolation
{
    /**
     * @var string
     * @OA\Property (
     *     property="propertyPath",
     *     description="Наименование проверенного поля",
     *     type="string",
     *     example="flightId"
     * ),
     * @Serializer\Groups({"default"})
     */
    public string $propertyPath;

    /**
     * @var string
     * @OA\Property (
     *   property="message",
     *   description="Сообщение",
     *   type="string",
     *   example="The record in the 'Flight' table with identity 4111 doesn't exist"
     * ),
     * @Serializer\Groups({"default"})
     */
    public string $message;

    /**
     * @var array
     * @OA\Property (
     *   property="parameters",
     *   description="Переданные для проверки данные",
     *   type="array",
     *   @OA\Items(
     *      description="Произвольный список параметров",
     *      @OA\Property (property="{{id}}", example="4111"),
     *      @OA\Property (property="{{entity}}", example="Flight")
     *   )
     * )
     * @Serializer\Groups({"default"})
     */
    public array $parameters;


    public function __construct(string $propertyPath, string $message, array $parameters = [])
    {
        $this->propertyPath = $propertyPath;
        $this->message        = $message;
        $this->parameters   = $parameters;
    }
}