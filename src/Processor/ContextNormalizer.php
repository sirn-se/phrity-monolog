<?php

namespace Phrity\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Phrity\Monolog\Tools\DefaultSerializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ContextNormalizer implements ProcessorInterface
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface|null $normalizer = null)
    {
        $this->normalizer = $normalizer ?? new DefaultSerializer();
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        return $record->with(context: $this->normalizer->normalize($record->context));
    }
}
