<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Construction;

use Doctrine\Instantiator\Instantiator;
use Doctrine\Instantiator\InstantiatorInterface;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use PewPew\Hydrator\Exception\MappingExceptionInterface;

/**
 * Advanced constructor for JMS v1.0 ... v3.20+ compatibility
 */
final readonly class AdvancedConstructor implements ObjectConstructorInterface
{
    private ObjectConstructor $objects;

    private EnumConstructor $enums;

    public function __construct(
        InstantiatorInterface $instantiator = new Instantiator(),
    ) {
        $this->objects = new ObjectConstructor($instantiator);
        $this->enums = new EnumConstructor();
    }

    /**
     * @param VisitorInterface $visitor NOTE: The {@see VisitorInterface}
     *        required instead of {@see DeserializationVisitorInterface} for
     *        JMS [1.0 ... 3.0] compatibility.
     * @param mixed $data
     *
     * @throws MappingExceptionInterface
     */
    public function construct(
        VisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context,
    ): ?object {
        if (\enum_exists($metadata->name)) {
            return $this->enums->construct($metadata, $data, $context);
        }

        return $this->objects->construct($metadata, $data, $context);
    }
}
