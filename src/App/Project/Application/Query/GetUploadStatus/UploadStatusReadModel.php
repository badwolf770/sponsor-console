<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetUploadStatus;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class UploadStatusReadModel
{
    /**
     * @OA\Property(description="статус загрузки файлов", type="array",
     *  @OA\Items(
     *      @OA\Property(
     *          property="fileName",
     *          type="string",
     *          example="еверест.xlsx"
     *      ),
     *      @OA\Property(
     *          property="percent",
     *          type="float",
     *          example=25.1
     *      ),
     * ))
     * @Serializer\Groups({"default", "getById"})
     */
    public array $statuses;

    public function __construct(array $statuses)
    {
        $this->statuses = $statuses;
    }
}
