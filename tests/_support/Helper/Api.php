<?php

namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Tests\ApiTester;
use Codeception\Example;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Psr\Container\ContainerInterface;

class Api extends \Codeception\Module
{
    public function getContainer(): ContainerInterface
    {
        return $this->getModule('Symfony')->_getContainer();
    }


    public function getEntityManager(): EntityManagerInterface
    {
        return $this->getModule('Doctrine2')->_getEntityManager();
    }

    public function getExampleData(Example $example): array
    {
        $reflectionProperty = (new \ReflectionProperty($example, 'data'));
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($example);
    }

    public function normalize(object $object, array $groups = ['default']): array
    {
        $serializer = $this->getContainer()->get('jms_serializer');
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->setSerializeNull(true);

        return $serializer->toArray($object, $context);
    }
}
