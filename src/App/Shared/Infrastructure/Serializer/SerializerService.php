<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Serializer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class SerializerService
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function toArray(mixed $data, array $groups = ['default']): array
    {
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->setSerializeNull(true);

        return $this->serializer->toArray($data, $context);
    }
}
