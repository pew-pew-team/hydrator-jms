<?php

declare(strict_types=1);

namespace PewPew\Hydrator\JMS\Exception;

use PewPew\Hydrator\Exception\MappingExceptionInterface;

/**
 * @template-implements \IteratorAggregate<array-key, MappingExceptionInterface>
 */
class MultipleMappingException extends \RuntimeException implements
    MappingExceptionInterface,
    \IteratorAggregate,
    \Countable
{
    private const string DEFAULT_MESSAGE = 'An error occurred while processing DTO';

    /**
     * @var non-empty-list<MappingExceptionInterface>
     */
    private array $exceptions;

    /**
     * @param non-empty-list<MappingExceptionInterface> $exceptions
     */
    public function __construct(array $exceptions)
    {
        /** @psalm-suppress RedundantCondition : Additional assert */
        assert($exceptions !== [], 'Exceptions list must not be empty');

        $this->exceptions = $exceptions;

        $message = self::DEFAULT_MESSAGE . ':';

        foreach ($exceptions as $exception) {
            $message .= "\n - " . $exception->getMessage();
        }

        parent::__construct($message);
    }

    public function getPath(): array
    {
        $first = \reset($this->exceptions);

        return $first->getPath();
    }

    public function getExpectedType(): string
    {
        $first = \reset($this->exceptions);

        return $first->getExpectedType();
    }

    public function getActualType(): ?string
    {
        $first = \reset($this->exceptions);

        return $first->getActualType();
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->exceptions);
    }

    /**
     * @return int<1, max>
     */
    public function count(): int
    {
        return \count($this->exceptions);
    }
}
