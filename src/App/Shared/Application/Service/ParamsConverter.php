<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ParamsConverter implements ParamConverterInterface
{
    private ValidatorInterface $validator;
    private string $validationErrorsArgument;

    public function __construct(
        ValidatorInterface      $validator,
        string                  $validationErrorsArgument = 'validationErrors'
    )
    {
        $this->validator = $validator;
        $this->validationErrorsArgument = $validationErrorsArgument;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $data = $request->query->all();
        $routeParams = $request->attributes->get('_route_params');
        $bodyParams = $request->request->all();

        if ($request->files->count() > 0) {
            $files = $request->files->all();
            $data = array_merge($data, $files);
        }
        $data = array_merge($data, $routeParams, $bodyParams);
        if ($object = $request->attributes->get($configuration->getName())) {
            $data = array_merge($data, (array)$object);
        } else {
            $object = (new \ReflectionClass($configuration->getClass()))->newInstanceWithoutConstructor();
        }
        $reflectionClass = new \ReflectionClass($object);
        foreach ($reflectionClass->getProperties() as $property) {
            if (!array_key_exists($property->getName(), $data)) {
                continue;
            }
            $property->setAccessible(true);
            $property->setValue($object, $this->convertValue($property, $data[$property->getName()]));
        }
        $request->attributes->set($configuration->getName(), $object);

        $errors = $this->validator->validate($object);

        $request->attributes->set(
            $this->validationErrorsArgument,
            $errors
        );
        return true;
    }

    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() !== null && $configuration->getConverter() === 'custom.param_converter';

    }

    private function convertValue(\ReflectionProperty $property, mixed $value): mixed
    {
        $type = $property->getType()->getName();
        return match ($type) {
            'string' => (string)$value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'int' => filter_var($value, FILTER_VALIDATE_INT),
            'float' => filter_var($value, FILTER_VALIDATE_FLOAT),
            'DateTimeImmutable' => new \DateTimeImmutable($value),
            'Symfony\Component\HttpFoundation\File\UploadedFile' => $value,
            'array' => $value,
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
