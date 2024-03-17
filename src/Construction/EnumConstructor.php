<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Construction;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use PewPew\Hydrator\JMS\Exception\MappingException;
use PewPew\Hydrator\JMS\Exception\MappingException\Context;

/**
 * @internal This is an internal library class, please do not use it in your code.
 * @psalm-internal PewPew\Hydrator\JMS\Construction
 */
final readonly class EnumConstructor
{
    public function construct(ClassMetadata $metadata, mixed $data, DeserializationContext $context): ?\BackedEnum
    {
        /** @var class-string<\UnitEnum> $enum */
        $enum = $metadata->name;

        if (\is_string($data) || \is_int($data)) {
            // In case of \BackedEnum
            if (\is_a($enum, \BackedEnum::class, true)) {
                /** @var string|int|null $result */
                $result = $enum::tryFrom($data);

                if ($result === null) {
                    $context = $this->getErrorContextForEnum($enum, $data, $context);

                    throw MappingException::fromContext($context);
                }
            }
            // Otherwise TODO
        }

        throw MappingException::fromContext(
            $this->getErrorContextForEnum($enum, $data, $context),
        );
    }

    /**
     * @param class-string<\UnitEnum> $enum
     */
    private function getErrorContextForEnum(string $enum, mixed $data, DeserializationContext $context): Context
    {
        $expected = $this->getExpectedTypeForEnum($enum);

        /** @var non-empty-string $actual */
        $actual = \is_object($data) ? 'object' : \get_debug_type($data);

        /** @var list<non-empty-string> $path */
        $path = $context->getCurrentPath();

        return new Context($expected, $actual, $path);
    }

    /**
     * @param class-string<\UnitEnum> $enum
     *
     * @return non-empty-string
     */
    private function getExpectedTypeForEnum(string $enum): string
    {
        $result = [];

        foreach ($enum::cases() as $case) {
            if ($case instanceof \BackedEnum) {
                $result[] = \is_string($case->value)
                    ? \addcslashes($case->value, '"\\')
                    : $case->value;
            } else {
                $result[] = $case->name;
            }
        }

        /** @var non-empty-string */
        return \implode('|', $result);
    }
}
