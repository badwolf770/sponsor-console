<?php
declare(strict_types=1);

namespace App\Project\Application\Query\ReadModel;

use App\Project\Application\Query\GetUploadStatus\UploadStatusReadModel;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use JMS\Serializer\Annotation as Serializer;

class ProjectReadModel
{
    /**
     * @OA\Property (
     *     property="id",
     *     description="Id проекта",
     *     type="string",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $id;
    /**
     * @OA\Property (
     *     property="name",
     *     description="Название проекта",
     *     type="string",
     *     example="Metro"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $name;
    /**
     * @OA\Property (
     *     property="client",
     *     description="Имя клиента",
     *     type="string",
     *     example="Metro"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $client;
    /**
     * @OA\Property (
     *     property="brand",
     *     description="Название бренда",
     *     type="string",
     *     example="Metro шеф"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $brand;
    /**
     * @OA\Property (
     *     property="exportFile",
     *     description="Файл экспорта",
     *     type="object",
     *     ref=@Model(type=ExportFileReadModel::class),
     *     nullable=true
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public ?ExportFileReadModel $exportFile = null;
    /**
     * @OA\Property (
     *     property="uploadStatuses",
     *     description="Cтатус загрузки файлов",
     *     type="object",
     *     ref=@Model(type=UploadStatusReadModel::class)
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public UploadStatusReadModel $uploadStatuses;

    /**
     * @OA\Property (
     *     property="packages",
     *     description="Пакеты",
     *     type="array",
     *     @OA\Items(
     *       ref=@Model(type=PackageReadModel::class, groups={"getById"})
     *     )
     *  )
     * @Serializer\Groups({"getById"})
     */
    public array $packages = [];

    /**
     * @OA\Property (
     *     property="createdAt",
     *     description="дата создания",
     *     type="string",
     *     example="2019-05-17"
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $createdAt;

    /**
     * @OA\Property(description="Охваты", type="array",
     *      @OA\Items(
     *          type="integer",
     *          example=1
     *      )
     * )
     * @Serializer\Groups({"default", "getById"})
     */
    public array $reaches;

    /**
     * @OA\Property (
     *     description="статус экспорта проекта в файл",
     *     type="string",
     *     enum={App\Project\Domain\ValueObject\ExportStatus::NotExported,App\Project\Domain\ValueObject\ExportStatus::InProgress,App\Project\Domain\ValueObject\ExportStatus::Completed},
     *     example=App\Project\Domain\ValueObject\ExportStatus::NotExported
     *  )
     * @Serializer\Groups({"default", "getById"})
     */
    public string $exportStatus;
}
