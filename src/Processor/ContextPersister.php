<?php

namespace Phrity\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class ContextPersister implements ProcessorInterface
{
    /** @var array<string, mixed> */
    private array $context;

    /** @param array<string, mixed> $context */
    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    /** @param array<string, mixed> $context */
    public function set(array $context): void
    {
        $this->context = $context;
    }

    /** @param array<string, mixed> $context */
    public function add(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }

    public function reset(): void
    {
        $this->context = [];
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(context: array_merge($this->context, $record->context));
    }
}
