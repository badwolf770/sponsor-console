<?php
declare(strict_types=1);

namespace App\Shared\Domain\Error;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationErrorNormalizer implements SubscribingHandlerInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @param ConstraintViolationListInterface $constraintViolationList
     * @return array
     */
    private function getMessagesAndViolations(ConstraintViolationListInterface $constraintViolationList): array
    {
        $violations = $messages = [];
        /** @var ConstraintViolation $violation */
        foreach ($constraintViolationList as $violation) {
            $violations[] = new ValidationViolation($violation->getPropertyPath(), $violation->getMessage());
            $propertyPath = $violation->getPropertyPath();
            $messages[]   = ($propertyPath ? $propertyPath . ': ' : '') . $violation->getMessage();
        }
        return [$messages, $violations];
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format'    => 'json',
                'type'      => ConstraintViolationList::class,
                'method'    => 'serializeList',
                'priority'  => -915
            ],
            //            [
            //                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
            //                'format'    => 'json',
            //                'type'      => ConstraintViolation::class,
            //                'method'    => 'serializeViolation',
            //                'priority'  => -915
            //            ],
        ];
    }

    public function serializeList(
        JsonSerializationVisitor $visitor,
        ConstraintViolationListInterface $violationList,
        array $type,
        Context $context
    ): array {
        [$titles, $violations] = $this->getMessagesAndViolations($violationList);

        $model =  new ValidationErrorModel(
            'Ошибки валидации',
            $titles ? implode("\n", $titles) : '',
            $violations
        );

        $serializeContext = new SerializationContext();
        $serializeContext->setGroups(['default']);
        $serializeContext->setSerializeNull(true);

        return $this->serializer->toArray($model, $serializeContext);
    }

    public function serializeViolation(
        JsonSerializationVisitor $visitor,
        \DateTime $date,
        array $type,
        Context $context
    ) {
        $q = 1;
    }
}