<?php

namespace App\Support;

final class QueueNames
{
    public static function default(): string
    {
        return (string) config('observability.queues.default', 'default');
    }

    public static function pdf(): string
    {
        return (string) config('observability.queues.pdf', 'pdf');
    }

    public static function ai(): string
    {
        return (string) config('observability.queues.ai', 'ai');
    }

    public static function webhooks(): string
    {
        return (string) config('observability.queues.webhooks', 'webhooks');
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_values(array_unique([
            self::default(),
            self::pdf(),
            self::ai(),
            self::webhooks(),
        ]));
    }
}
