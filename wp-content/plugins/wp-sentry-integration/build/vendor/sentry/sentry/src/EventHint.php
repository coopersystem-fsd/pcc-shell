<?php

declare (strict_types=1);
namespace Sentry;

/**
 * This class represents hints on how to process an event.
 */
final class EventHint
{
    /**
     * The original exception to add to the event.
     *
     * @var \Throwable|null
     */
    public $exception;
    /**
     * The stacktrace to set on the event.
     *
     * @var Stacktrace|null
     */
    public $stacktrace;
    /**
     * Any extra data that might be needed to process the event.
     *
     * @var array<string, mixed>
     */
    public $extra = [];
    /**
     * Create a EventHint instance from an array of values.
     *
     * @psalm-param array{
     *     exception?: \Throwable|null,
     *     stacktrace?: Stacktrace|null,
     *     extra?: array<string, mixed>
     * } $hintData
     */
    public static function fromArray(array $hintData) : self
    {
        $hint = new self();
        $exception = $hintData['exception'] ?? null;
        $stacktrace = $hintData['stacktrace'] ?? null;
        $extra = $hintData['extra'] ?? [];
        if (null !== $exception && !$exception instanceof \Throwable) {
            throw new \InvalidArgumentException(\sprintf('The value of the "exception" field must be an instance of a class implementing the "%s" interface. Got: "%s".', \Throwable::class, \get_debug_type($exception)));
        }
        if (null !== $stacktrace && !$stacktrace instanceof \Sentry\Stacktrace) {
            throw new \InvalidArgumentException(\sprintf('The value of the "stacktrace" field must be an instance of the "%s" class. Got: "%s".', \Sentry\Stacktrace::class, \get_debug_type($stacktrace)));
        }
        if (!\is_array($extra)) {
            throw new \InvalidArgumentException(\sprintf('The value of the "extra" field must be an array. Got: "%s".', \get_debug_type($extra)));
        }
        $hint->exception = $exception;
        $hint->stacktrace = $stacktrace;
        $hint->extra = $extra;
        return $hint;
    }
}
