<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Project\Application\Command\CreateProject\CreateProjectCommand;
use App\Project\Application\Command\DeleteProject\DeleteProjectCommand;
use App\Project\Application\Command\Export\ExportCommand;
use App\Project\Application\Command\UploadTemplate\UploadTemplateCommand;
use App\Project\Application\Query\GetProjectById\GetProjectByIdQuery;
use App\Project\Application\Query\GetProjects\GetProjectsQuery;
use App\Project\Application\Query\GetUploadStatus\UploadStatusQuery;
use App\Project\Application\Query\GetUploadStatus\UploadStatusReadModel;
use App\Shared\Application\ReadModel\EntityCreatedModel;
use App\Shared\Domain\Error\ErrorModel;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Infrastructure\Bus\AsyncEvent\MessengerAsyncEventBus;
use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use App\Project\Application\Query\GetProjects\ProjectsReadModel;
use App\Project\Application\Query\ReadModel\ProjectReadModel;
use App\Project\Application\Command\UpdateProject\UpdateProjectCommand;
use App\Project\Application\TemplateImport\TemplateType;

class ProjectController extends ApiController
{
    /**
     * Создание проекта
     * @Route("/api/project", methods={"POST"})
     * @OA\Tag(name="project")
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=CreateProjectCommand::class)
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Проект создан",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=EntityCreatedModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("command", class="App\Project\Application\Command\CreateProject\CreateProjectCommand" ,converter="custom.param_converter")
     * @param CreateProjectCommand             $command
     * @param MessengerCommandBus              $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function create(
        CreateProjectCommand             $command,
        MessengerCommandBus              $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $entityCreatedModel = $bus->handle($command);
        return $this->respondCreated($entityCreatedModel);
    }

    /**
     * Обновление проекта
     * @Route("/api/project/{projectId}", methods={"PUT"})
     * @OA\Tag(name="project")
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UpdateProjectCommand::class, groups={"default"})
     *     )
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Проект обновлен"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("command", class="App\Project\Application\Command\UpdateProject\UpdateProjectCommand" ,converter="custom.param_converter")
     * @param UpdateProjectCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function update(
        UpdateProjectCommand             $command,
        MessengerCommandBus              $bus,
        ConstraintViolationListInterface $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $bus->handle($command);
        return $this->respondNoContent();
    }

    /**
     * Загрузка шаблона проекта
     * @Route("/api/project/{projectId}/template/{templateType}", methods={"POST"})
     * @OA\Tag(name="project")
     * @OA\RequestBody(
     *  required=true,
     *  @OA\MediaType(
     *      mediaType="multipart/form-data",
     *      @OA\Schema(
     *          @OA\Property(
     *              property="file",
     *              description="Файл шаблона, xlsx",
     *              type="file"
     *          ),
     *      ),
     *  ),
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="Тип шаблона",
     *     example="Выберите один из: everest, gpm, utv, vgtrk, first",
     *     @OA\Schema(
     *      type="string",
     *      enum={TemplateType::EVEREST,TemplateType::FIRST_CHANNEL,TemplateType::GPM,TemplateType::UTV,TemplateType::VGTRK},
     *      example=TemplateType::EVEREST
     *     ),
     *     name="templateType"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Шаблон загружен"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("command", class="App\Project\Application\Command\UploadTemplate\UploadTemplateCommand" ,converter="custom.param_converter")
     * @param UploadTemplateCommand            $command
     * @param MessengerCommandBus              $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function uploadTemplate(
        UploadTemplateCommand            $command,
        MessengerCommandBus              $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $bus->handle($command);
        return $this->respondNoContent();
    }

    /**
     * Статус загрузки файлов проекта
     * @Route("/api/project/{projectId}/upload-status", methods={"GET"})
     * @OA\Tag(name="project")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Статус загрузки файлов проекта",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=UploadStatusReadModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("query", class="App\Project\Application\Query\GetUploadStatus\UploadStatusQuery" ,converter="custom.param_converter")
     * @param UploadStatusQuery                $query
     * @param MessengerQueryBus                $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getUploadStatus(
        UploadStatusQuery                $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $model = $bus->ask($query);

        return $this->respond($model);
    }

    /**
     * Экспорт проекта
     * @Route("/api/project/{projectId}/export", methods={"POST"})
     * @OA\Tag(name="project")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Задача по экспорту проекта поставлена в очередь"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("command", class="App\Project\Application\Command\Export\ExportCommand" ,converter="custom.param_converter")
     * @param ExportCommand                    $command
     * @param MessengerAsyncEventBus           $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function export(
        ExportCommand                    $command,
        MessengerAsyncEventBus           $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $bus->handle($command);

        return $this->respondNoContent();
    }

    /**
     * Получение списка проектов
     * @Route("/api/project", methods={"GET"})
     * @OA\Tag(name="project")
     * @OA\Response(
     *     response=200,
     *     description="Список проектов",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ProjectsReadModel::class, groups={"default"})
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("query", class="App\Project\Application\Query\GetProjects\GetProjectsQuery" ,converter="custom.param_converter")
     * @param GetProjectsQuery                 $query
     * @param MessengerQueryBus                $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getProjects(
        GetProjectsQuery                 $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $model = $bus->ask($query);

        return $this->respond($model);
    }

    /**
     * Получение проекта по id
     * @Route("/api/project/{projectId}", methods={"GET"})
     * @OA\Tag(name="project")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Проект",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ProjectReadModel::class, groups={"getById"})
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("query", class="App\Project\Application\Query\GetProjectById\GetProjectByIdQuery" ,converter="custom.param_converter")
     * @param GetProjectByIdQuery              $query
     * @param MessengerQueryBus                $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getById(
        GetProjectByIdQuery              $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $model = $bus->ask($query);

        return $this->respond($model, ['getById']);
    }

    /**
     * удаление проекта
     * @Route("/api/project/{projectId}", methods={"DELETE"})
     * @OA\Tag(name="project")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Проект удален"
     * )
     * @OA\Response(
     *     response=400,
     *     description="Ошибки валидации",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ValidationErrorModel::class)
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Ошибка сервера",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=ErrorModel::class)
     *     )
     * )
     * @ParamConverter("command", class="App\Project\Application\Command\DeleteProject\DeleteProjectCommand" ,converter="custom.param_converter")
     * @param DeleteProjectCommand            $command
     * @param MessengerCommandBus              $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function deleteProject(
        DeleteProjectCommand            $command,
        MessengerCommandBus              $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $bus->handle($command);
        return $this->respondNoContent();
    }
}
