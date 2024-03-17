<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Construction\ObjectConstructor;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use PewPew\Hydrator\Exception\MappingExceptionInterface;
use PewPew\Hydrator\JMS\Exception\MappingException;
use PewPew\Hydrator\JMS\Exception\MappingException\Context;
use PewPew\Hydrator\JMS\Exception\MultipleMappingException;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal PewPew\Hydrator\JMS\Construction
 */
final class InvariantValidator
{
    public function validate(
        ClassMetadata $metadata,
        mixed $data,
        DeserializationContext $context
    ): ?MappingExceptionInterface {
        // Process only array and object data
        if (!\is_array($data) && !\is_object($data)) {
            return null;
        }

        $errors = $this->collectErrors($metadata, $data, $context);

        if ($errors === []) {
            return null;
        }

        if (\count($errors) === 1) {
            return \reset($errors);
        }

        return new MultipleMappingException($errors);
    }

    /**
     * @throws MappingExceptionInterface
     */
    public function validateOrFail(ClassMetadata $metadata, mixed $data, DeserializationContext $context): void
    {
        $error = $this->validate($metadata, $data, $context);

        if ($error !== null) {
            throw $error;
        }
    }

    /**
     * @return list<MappingException>
     */
    private function collectErrors(ClassMetadata $class, array|object $data, DeserializationContext $ctx): array
    {
        $errors = [];

        foreach ($class->propertyMetadata as $property) {
            if ($this->isValid($property, $data)) {
                continue;
            }

            $context = $this->createErrorContext($property, $ctx);
            $name = $property->serializedName ?? $property->name ?: '<unknown>';

            $errors[] = MappingException::fromContextOfField($context, $name);
        }

        return $errors;
    }

    private function createErrorContext(PropertyMetadata $meta, DeserializationContext $ctx): Context
    {
        /** @var list<non-empty-string> $actualPath */
        $actualPath = $ctx->getCurrentPath();

        $expectedTypeExists = \is_array($meta->type)
            && isset($meta->type['name'])
            && \is_string($meta->type['name'])
            && $meta->type['name'] !== '';

        $expectedType = $expectedTypeExists ? $meta->type['name'] : 'mixed';

        return new Context($expectedType, null, $actualPath);
    }

    private function isValid(PropertyMetadata $meta, array|object $data): bool
    {
        return $this->hasBeenPassed($meta, $data)
            || !$this->isRequired($meta);
    }

    private function hasBeenPassed(PropertyMetadata $meta, array|object $data): bool
    {
        /** @var string|null|false $name */
        $name = $meta->serializedName;

        if ($name === null || $name === false || $name === '') {
            $name = $meta->name;
        }

        if (\is_array($data)) {
            return isset($data[$name]) && \array_key_exists($name, $data);
        }

        return \property_exists($data, $name);
    }

    private function isRequired(PropertyMetadata $meta): bool
    {
        return !$meta->skipWhenEmpty
            && $meta->hasDefault === false;
    }
}
