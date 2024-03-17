<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\DeserializationContext;
use PewPew\Hydrator\HydratorInterface;
use PewPew\Hydrator\JMS\Exception\MappingException;
use PewPew\Hydrator\JMS\Exception\MappingException\Context;

final readonly class Hydrator implements HydratorInterface
{
    public function __construct(
        private ArrayTransformerInterface $transformer,
    ) {}

    /**
     * @api
     */
    public function getArrayTransformer(): ArrayTransformerInterface
    {
        return $this->transformer;
    }

    /**
     * @psalm-suppress MixedReturnStatement : Hydrator may return any value
     */
    public function hydrate(string $type, mixed $data): mixed
    {
        $context = DeserializationContext::create();

        if (\is_array($data) || \is_object($data)) {
            return $this->transformer->fromArray((array) $data, $type, $context);
        }

        $context = new Context('object', Context::getPublicTypeName($data));

        throw MappingException::fromContext($context);
    }
}
