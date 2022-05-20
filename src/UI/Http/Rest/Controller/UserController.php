<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Shared\Application\Query\getCurrentUser\GetCurrentUserQuery;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use App\Shared\Application\Query\getCurrentUser\UserReadModel;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Domain\Error\ErrorModel;
use Nelmio\ApiDocBundle\Annotation\Model;

class UserController extends ApiController
{
    /**
     * Получение текущего пользователя
     * @Route("/api/user/current", methods={"GET"})
     * @OA\Tag(name="user")
     * @OA\Response(
     *     response=200,
     *     description="Пользователь",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=UserReadModel::class, groups={"default"})
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
     * @ParamConverter("query", class="App\Shared\Application\Query\getCurrentUser\GetCurrentUserQuery" ,converter="custom.param_converter")
     * @param GetCurrentUserQuery              $query
     * @param MessengerQueryBus                $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function getCurrent(
        GetCurrentUserQuery              $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $model = $bus->ask($query);

        return $this->respond($model);
    }
}