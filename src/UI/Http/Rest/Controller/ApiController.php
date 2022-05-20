<?php
declare(strict_types=1);

namespace UI\Http\Rest\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractFOSRestController
{

    public function __construct(protected SerializerInterface $serializer, protected ValidatorInterface $validator)
    {
    }

    public function respond(mixed $data, array $groups = ['default']): View
    {
        return $this->serialize($data, Response::HTTP_OK, $groups);
    }

    /**
     * Returns a 201 Created
     *
     * @param mixed $data
     * @param array $groups
     * @return View
     */
    public function respondCreated(mixed $data = [], array $groups = ['default']): View
    {
        return $this->serialize($data, Response::HTTP_CREATED, $groups);
    }

    /**
     * Returns a 204 no content
     *
     * @return View
     */
    public function respondNoContent(): View
    {
        return View::create(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize(mixed $data, int $statusCode, array $groups = ['default']): mixed
    {
        $view    = View::create($data, $statusCode);
        $context = new Context();
        $context->setGroups($groups);
        $context->setSerializeNull(true);
        $view->setContext($context);

        return $view;
    }

    protected function toArray($records, array $groups = ['default']): array
    {
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->setSerializeNull(true);

        return $this->serializer->toArray($records, $context);
    }

    protected function handleValidationErrors(ConstraintViolationListInterface $validationErrors): View
    {
        return View::create($validationErrors, Response::HTTP_BAD_REQUEST);
    }
}