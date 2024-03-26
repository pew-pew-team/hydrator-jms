<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Metadata\Cache\PsrCacheAdapter as MetadataPsrCacheAdapter;
use PewPew\Hydrator\ExtractorInterface;
use PewPew\Hydrator\HydratorInterface;
use PewPew\Hydrator\JMS\Construction\AdvancedConstructor;
use Psr\Cache\CacheItemPoolInterface;

final readonly class Builder
{
    private Serializer $serializer;

    public function __construct(SerializerBuilder $builder)
    {
        $this->serializer = $builder
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->setObjectConstructor(new AdvancedConstructor())
            ->enableEnumSupport()
            ->build();
    }

    /**
     * @param array<class-string, non-empty-string> $configs Sets a map of
     *        namespace prefixes to directories.
     * @param iterable<array-key, SubscribingHandlerInterface> $handlers List
     *        of subscribing handlers.
     * @param iterable<array-key, EventSubscriberInterface> $subscribers List
     *        of event subscribers.
     *
     * @psalm-suppress InternalClass
     * @psalm-suppress TooManyArguments
     */
    public static function create(
        array $configs = [],
        bool $debug = false,
        iterable $handlers = [],
        iterable $subscribers = [],
        CacheItemPoolInterface $cache = null,
    ): self {
        $builder = SerializerBuilder::create()
            ->setDebug($debug)
            ->addDefaultHandlers()
            ->setMetadataDirs($configs)
            ->configureHandlers(static function (HandlerRegistry $registry) use ($handlers): void {
                foreach ($handlers as $handler) {
                    $registry->registerSubscribingHandler($handler);
                }
            })
            ->configureListeners(static function (EventDispatcher $dispatcher) use ($subscribers): void {
                foreach ($subscribers as $subscriber) {
                    $dispatcher->addSubscriber($subscriber);
                }
            });

        if ($cache !== null) {
            $builder->setMetadataCache(new MetadataPsrCacheAdapter('jms_hydrator', $cache));
        }

        return new self($builder);
    }

    /**
     * @api
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * @api
     */
    public function createExtractor(): ExtractorInterface
    {
        return new Extractor($this->serializer);
    }

    /**
     * @api
     */
    public function createHydrator(): HydratorInterface
    {
        return new Hydrator($this->serializer);
    }
}
