<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Project\Application\Command\DeleteFlight\DeleteFlightCommand;
use App\Project\Application\Command\LinkFlightToSpots\LinkFlightToSpotsCommand;
use App\Project\Application\Query\GetFlightsByPackage\GetFlightsByPackageQuery;
use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use FOS\RestBundle\View\View;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Application\ReadModel\EntityCreatedModel;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Domain\Error\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Project\Application\Command\CreateFlight\CreateFlightCommand;
use App\Project\Application\Query\GetFlightsByPackage\FlightsByPackageReadModel;
use App\Project\Application\Command\ChangeReach\ChangeReachCommand;

class FlightController extends ApiController
{
    /**
     * Создание флайта
     * @Route("/api/package/{packageId}/flight", methods={"POST"})
     * @OA\Tag(name="flight")
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=CreateFlightCommand::class, groups={"default"})
     *     )
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Response(
     *     response=201,
     *     description="Флайт создан",
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
     * @ParamConverter("command", class="App\Project\Application\Command\CreateFlight\CreateFlightCommand", converter="custom.param_converter")
     * @param CreateFlightCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function create(
        CreateFlightCommand              $command,
        MessengerCommandBus              $bus,
        ConstraintViolationListInterface $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $entityCreatedModel = $bus->handle($command);
        return $this->respondCreated($entityCreatedModel);
    }

    /**
     * Получение флайтов по id пакета
     * @Route("/api/package/{packageId}/flight", methods={"GET"})
     * @OA\Tag(name="flight")
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Флайты",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=FlightsByPackageReadModel::class, groups={"default"})
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
     * @ParamConverter("query", class="App\Project\Application\Query\GetFlightsByPackage\GetFlightsByPackageQuery" ,converter="custom.param_converter")
     * @param GetFlightsByPackageQuery $query
     * @param MessengerQueryBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getAllByPackage(
        GetFlightsByPackageQuery         $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View
    {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $result = $bus->ask($query);

        return $this->respond($result);
    }

    /**
     * удаление флайта
     * @Route("/api/package/{packageId}/flight/{flightId}", methods={"DELETE"})
     * @OA\Tag(name="flight")
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id флайта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="flightId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Флайт удален"
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
     * @ParamConverter("command", class="App\Project\Application\Command\DeleteFlight\DeleteFlightCommand" ,converter="custom.param_converter")
     * @param DeleteFlightCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function delete(
        DeleteFlightCommand              $command,
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
     * Привязка флайта к спотам
     * @Route("/api/package/{packageId}/flight/{flightId}/spots", methods={"PATCH"})
     * @OA\Tag(name="flight")
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id флайта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="flightId"
     * )
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=LinkFlightToSpotsCommand::class, groups={"default"})
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Флайт привязан к спотам"
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
     * @ParamConverter("command", class="App\Project\Application\Command\LinkFlightToSpots\LinkFlightToSpotsCommand", converter="custom.param_converter")
     * @param LinkFlightToSpotsCommand          $command
     * @param MessengerCommandBus              $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function link(
        LinkFlightToSpotsCommand          $command,
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
     * Изменение охвата
     * @Route("/api/package/{packageId}/flight/{flightId}/reach/{reachId}", methods={"PATCH"})
     * @OA\Tag(name="flight")
     * @OA\Parameter (
     *     in="path",
     *     description="id пакета",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="packageId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id флайта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="flightId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id охвата",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="reachId"
     * )
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=ChangeReachCommand::class, groups={"default"})
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Охват изменен"
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
     * @ParamConverter("command", class="App\Project\Application\Command\ChangeReach\ChangeReachCommand", converter="custom.param_converter")
     * @param ChangeReachCommand          $command
     * @param MessengerCommandBus              $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function changeReach(
        ChangeReachCommand          $command,
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
