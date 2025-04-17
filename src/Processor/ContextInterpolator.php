<?php

namespace Phrity\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Phrity\Util\DataAccessor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Stringable;

class ContextInterpolator implements ProcessorInterface
{
    private NormalizerInterface|null $normalizer;

    public function __construct(NormalizerInterface|null $normalizer = null)
    {
        $this->normalizer = $normalizer;
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $original = new DataAccessor($record->context, '.');
        $normalized = $this->normalizer ? new DataAccessor($this->normalizer->normalize($record->context), '.') : null;

        $message = preg_replace_callback('/{([^}]*)}/', function (array $matches) use ($original, $normalized): string {
            // Check if normilized is usable
            if ($this->normalizer) {
                $content = $normalized->get($matches[1], $matches[0]);
                if (is_scalar($content) || $content instanceof Stringable) {
                    return (string)$content;
                }
            }
            // Check if original is usable
            $content = $original->get($matches[1], $matches[0]);
            if (is_scalar($content) || $content instanceof Stringable) {
                return (string)$content;
            }
            // Default to type
            return get_debug_type($content);
        }, $record->message);
        return $record->with(message: $message);
    }
}
