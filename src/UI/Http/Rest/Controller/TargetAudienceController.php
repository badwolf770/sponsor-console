<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use App\Project\Application\Query\FindTargetAudiences\FindTargetAudiencesQuery;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use FOS\RestBundle\View\View;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Error\ValidationErrorModel;
use App\Shared\Domain\Error\ErrorModel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Project\Application\Query\FindTargetAudiences\FindTargetAudiencesReadModel;

class TargetAudienceController extends ApiController
{
    /**
     * Поиск целевых аудитории по названию
     * @Route("/api/target-audience/{name}", methods={"GET"})
     * @OA\Tag(name="target-audience")
     * @OA\Parameter (
     *     in="path",
     *     description="название",
     *     example="All 18+",
     *     name="name"
     * )
     * @OA\Response(
     *     response=200,
     *     description="Список найденных аудиторий",
     *     @OA\JsonContent(
     *          type="object",
     *          ref=@Model(type=FindTargetAudiencesReadModel::class)
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
     * @ParamConverter("query", class="App\Project\Application\Query\FindTargetAudiences\FindTargetAudiencesQuery", converter="custom.param_converter")
     * @param FindTargetAudiencesQuery         $query
     * @param MessengerQueryBus                $bus
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     * @throws \Throwable
     */
    public function findByName(
        FindTargetAudiencesQuery         $query,
        MessengerQueryBus                $bus,
        ConstraintViolationListInterface $validationErrors
    ): View {
        if (count($validationErrors) > 0) {
            return $this->handleValidationErrors($validationErrors);
        }
        $result = $bus->ask($query);

        return $this->respond($result);
    }
}
