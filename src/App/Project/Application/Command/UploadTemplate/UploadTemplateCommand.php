<?php
declare(strict_types=1);

namespace App\Project\Application\Command\UploadTemplate;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Project\Application\TemplateImport\TemplateType;
use App\Shared\Infrastructure\Validator\Existence\Existence;

class UploadTemplateCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
    /**
     * @Assert\Choice(choices=TemplateType::TYPES, message="Выберите шаблон для загрузки")
     * @Existence(entity="TemplateType", key="name", checkPositive=false, message="Тип шаблона {{id}} не существует!")
     * @Assert\NotBlank()
     */
    public string $templateType;

    /**
     * @Assert\NotBlank()
     * @Assert\File(
     *     maxSize = "128m",
     *     mimeTypes = {"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"},
     *     mimeTypesMessage = "Для загрузки принимаются только xlsx"
     * )
     */
    public UploadedFile $file;
}
