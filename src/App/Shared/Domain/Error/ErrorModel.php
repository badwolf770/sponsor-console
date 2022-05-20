<?php
declare(strict_types=1);

namespace App\Shared\Domain\Error;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class ErrorModel
{
    /**
     * @var string
     * @OA\Property (
     *     property="message",
     *     description="Ошибка",
     *     type="string",
     *     example="Ошибка сервера"
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
     *     example="что-то случилось :("
     * )
     * @Serializer\Groups({"default"})
     */
    public string $detail;
}
