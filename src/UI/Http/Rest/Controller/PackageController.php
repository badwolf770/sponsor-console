<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Project\Application\Command\CalculateStatisticsByPackage\CalculateStatisticsByPackageCommand;
use App\Project\Application\Command\ChangePackageActiveStatus\ChangePackageActiveStatusCommand;
use App\Project\Application\Query\GetPackageById\GetPackageByIdQuery;
use App\Project\Application\Query\GetStatusOfCalculateStatistics\GetStatusOfCalculateStatisticsQuery;
use App\Shared\Infrastructure\Bus\AsyncEvent\MessengerAsyncEventBus;
use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use FOS\RestBundle\View\View;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Domain\Error\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Project\Application\Query\ReadModel\PackageReadModel;
use App\Project\Application\Query\GetStatusOfCalculateStatistics\StatusReadModel;

class PackageController extends ApiController
{
    /**
     * Изменение активности пакета
     * @Route("/api/project/{projectId}/package/{packageId}/active/{status}", methods={"PATCH"})
     * @OA\Tag(name="package")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="статус активности пакета, true или false",
     *     example=true,
     *     name="status",
     *     @OA\Schema(
     *      type="boolean"
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="активность изменена"
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
     * @ParamConverter("command", class="App\Project\Application\Command\ChangePackageActiveStatus\ChangePackageActiveStatusCommand", converter="custom.param_converter")
     * @param ChangePackageActiveStatusCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function changeActive(
        ChangePackageActiveStatusCommand $command,
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
     * Получение пакета по id
     * @Route("/api/project/{projectId}/package/{packageId}", methods={"GET"})
     * @OA\Tag(name="package")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Пакет",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=PackageReadModel::class, groups={"getById","getPackageById"})
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
     * @ParamConverter("query", class="App\Project\Application\Query\GetPackageById\GetPackageByIdQuery" ,converter="custom.param_converter")
     * @param GetPackageByIdQuery $query
     * @param MessengerQueryBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getPackageById(
        GetPackageByIdQuery              $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $model = $bus->ask($query);

        return $this->respond($model, ['getById', 'getPackageById']);
    }

    /**
     * Расчет статистик по пакету(охваты, рейтинги)
     * @Route("/api/project/{projectId}/package/{packageId}/calculate-statistics", methods={"POST"})
     * @OA\Tag(name="package")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Статистики расчитываются"
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
     * @ParamConverter("command", class="App\Project\Application\Command\CalculateStatisticsByPackage\CalculateStatisticsByPackageCommand", converter="custom.param_converter")
     * @param CalculateStatisticsByPackageCommand $command
     * @param MessengerAsyncEventBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function calculateStatistics(
        CalculateStatisticsByPackageCommand $command,
        MessengerAsyncEventBus              $bus,
        ConstraintViolationListInterface    $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $bus->handle($command);

        return $this->respondNoContent();
    }

    /**
     * Статус расчета статистик по пакету(охваты, рейтинги)
     * @Route("/api/project/{projectId}/package/{packageId}/calculate-statistics", methods={"GET"})
     * @OA\Tag(name="package")
     * @OA\Parameter (
     *     in="path",
     *     description="id проекта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="projectId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Статус",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=StatusReadModel::class)
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
     * @ParamConverter("query", class="App\Project\Application\Query\GetStatusOfCalculateStatistics\GetStatusOfCalculateStatisticsQuery", converter="custom.param_converter")
     * @param GetStatusOfCalculateStatisticsQuery $query
     * @param MessengerQueryBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getStatusOfCalculateStatistics(
        GetStatusOfCalculateStatisticsQuery $query,
        MessengerQueryBus                   $bus,
        ConstraintViolationListInterface    $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $status = $bus->ask($query);

        return $this->respond($status);
    }
}
