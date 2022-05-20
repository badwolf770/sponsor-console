<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use FOS\RestBundle\View\View;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Domain\Error\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Project\Application\Command\LinkFlightToSpot\LinkFlightToSpotCommand;
use App\Project\Application\Command\ChangeRating\ChangeRatingCommand;

class SpotController extends ApiController
{
    /**
     * Привязка флайта к споту
     * @Route("/api/spot/{spotId}/flight/{flightId}/link", methods={"PATCH"})
     * @OA\Tag(name="spot")
     * @OA\Parameter (
     *     in="path",
     *     description="id спота",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="spotId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id флайта",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="flightId"
     * )
     * @OA\Response(
     *     response=204,
     *     description="Флайт привязан к споту"
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
     * @ParamConverter("command", class="App\Project\Application\Command\LinkFlightToSpot\LinkFlightToSpotCommand", converter="custom.param_converter")
     * @param LinkFlightToSpotCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function link(
        LinkFlightToSpotCommand          $command,
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
     * Изменение рейтинга спота
     * @Route("/api/spot/{spotId}/rating/{ratingId}", methods={"PUT"})
     * @OA\Tag(name="spot")
     * @OA\Parameter (
     *     in="path",
     *     description="id спота",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="spotId"
     * )
     * @OA\Parameter (
     *     in="path",
     *     description="id рейтинга",
     *     example="e35b0dc5-5e32-476e-afe6-fe2fe2d6f3b5",
     *     name="ratingId"
     * )
     * @OA\RequestBody (
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=ChangeRatingCommand::class, groups={"default"})
     *     )
     * )
     * @OA\Response(
     *     response=204,
     *     description="Рейтинг успешно изменен"
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
     * @ParamConverter("command", class="App\Project\Application\Command\ChangeRating\ChangeRatingCommand", converter="custom.param_converter")
     * @param ChangeRatingCommand $command
     * @param MessengerCommandBus $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function changeRating(
        ChangeRatingCommand              $command,
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
}
