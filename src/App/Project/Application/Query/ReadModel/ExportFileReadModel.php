<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use OpenApi\Annotations as OA;
use JMS\Serializer\Annotation as Serializer;

class ExportFileReadModel
{
    /**
     * @OA\Property (
     *     property="id",
     *     description="Id файла",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     property="client",
     *     description="Имя файла",
     *     type="string",
     *     example="metro_metro0.51367900 1648481442.xlsx"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $name;
    /**
     * @OA\Property (
     *     property="webPath",
     *     description="Путь до файла",
     *     type="string",
     *     example="/upload/export/metro_metro0.51367900 1648481442.xlsx"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $webPath;
}
