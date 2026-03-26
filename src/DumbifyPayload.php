<?php

namespace Spatie\OurRay;

class DumbifyPayload
{
    /** @var array<int, string> */
    protected const SUPPORTED_TYPES = [
        'log',
        'custom',
        'color',
        'size',
        'label',
        'separator',
        'confetti',
    ];

    public static function dumbify(array $payload): ?array
    {
        try {
            $type = $payload['type'] ?? null;

            if (in_array($type, static::SUPPORTED_TYPES, true)) {
                return $payload;
            }

            $result = static::convert($type, $payload['content'] ?? []);

            if ($result === null) {
                return null;
            }

            $payload['type'] = $result['type'];
            $payload['content'] = $result['content'];

            return $payload;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected static function convert(?string $type, array $content): ?array
    {
        if ($type === 'table') {
            return static::convertTable($content);
        }

        if ($type === 'exception') {
            return static::convertException($content);
        }

        if ($type === 'trace') {
            return static::convertTrace($content);
        }

        if ($type === 'caller') {
            return static::convertCaller($content);
        }

        if ($type === 'carbon') {
            return static::convertCarbon($content);
        }

        if ($type === 'measure') {
            return static::convertMeasure($content);
        }

        if ($type === 'json_string') {
            return static::convertJsonString($content);
        }

        if ($type === 'notify') {
            return static::convertNotify($content);
        }

        if ($type === 'application_log') {
            return static::convertApplicationLog($content);
        }

        if ($type === 'executed_query') {
            return static::convertExecutedQuery($content);
        }

        if ($type === 'event') {
            return static::convertEvent($content);
        }

        if ($type === 'job_event') {
            return static::convertJobEvent($content);
        }

        if ($type === 'mailable') {
            return static::convertMailable($content);
        }

        if ($type === 'eloquent_model') {
            return static::convertEloquentModel($content);
        }

        if ($type === 'view') {
            return static::convertView($content);
        }

        if ($type === 'response') {
            return static::convertResponse($content);
        }

        return null;
    }

    protected static function convertTable(array $content): array
    {
        $values = $content['values'] ?? [];
        $label = $content['label'] ?? 'Table';

        $lines = [];

        foreach ($values as $key => $value) {
            $lines[] = "{$key}: ".static::stripHtml($value);
        }

        return static::toCustom(implode("\n", $lines), $label);
    }

    protected static function convertException(array $content): array
    {
        $class = $content['class'] ?? 'Exception';
        $message = $content['message'] ?? '';
        $frames = $content['frames'] ?? [];

        $lines = ["{$class}: {$message}"];

        foreach (array_slice($frames, 0, 10) as $frame) {
            $file = $frame['file_name'] ?? '';
            $line = $frame['line_number'] ?? '';
            $frameClass = $frame['class'] ?? '';
            $method = $frame['method'] ?? '';

            $caller = $frameClass ? "{$frameClass}::{$method}" : $method;
            $lines[] = "  {$caller} ({$file}:{$line})";
        }

        return static::toCustom(implode("\n", $lines), 'Exception');
    }

    protected static function convertTrace(array $content): array
    {
        $frames = $content['frames'] ?? [];
        $lines = [];

        foreach ($frames as $i => $frame) {
            $file = $frame['file_name'] ?? '';
            $line = $frame['line_number'] ?? '';
            $class = $frame['class'] ?? '';
            $method = $frame['method'] ?? '';

            $caller = $class ? "{$class}::{$method}" : $method;
            $lines[] = "#{$i} {$caller} ({$file}:{$line})";
        }

        return static::toCustom(implode("\n", $lines), 'Trace');
    }

    protected static function convertCaller(array $content): array
    {
        $frame = $content['frame'] ?? [];
        $file = $frame['file_name'] ?? '';
        $line = $frame['line_number'] ?? '';
        $class = $frame['class'] ?? '';
        $method = $frame['method'] ?? '';

        $caller = $class ? "{$class}::{$method}" : $method;

        return static::toCustom("{$caller} ({$file}:{$line})", 'Caller');
    }

    protected static function convertCarbon(array $content): array
    {
        $formatted = $content['formatted'] ?? 'null';
        $timezone = $content['timezone'] ?? '';

        $text = $timezone ? "{$formatted} ({$timezone})" : (string) $formatted;

        return static::toCustom($text, 'Carbon');
    }

    protected static function convertMeasure(array $content): array
    {
        $name = $content['name'] ?? 'default';

        if ($content['is_new_timer'] ?? false) {
            return static::toCustom("Started timer '{$name}'", 'Measure');
        }

        $totalTime = $content['total_time'] ?? 0;
        $timeSinceLastCall = $content['time_since_last_call'] ?? 0;

        return static::toCustom(
            "{$name}: {$totalTime}ms total, {$timeSinceLastCall}ms since last call",
            'Measure'
        );
    }

    protected static function convertJsonString(array $content): array
    {
        $value = $content['value'] ?? '';

        return static::toCustom($value, 'JSON');
    }

    protected static function convertNotify(array $content): array
    {
        return static::toCustom($content['value'] ?? '', 'Notify');
    }

    protected static function convertApplicationLog(array $content): array
    {
        $value = $content['value'] ?? '';

        if (! empty($content['context'])) {
            $value .= "\n".static::stripHtml($content['context']);
        }

        return static::toCustom($value, 'Application Log');
    }

    protected static function convertExecutedQuery(array $content): array
    {
        $sql = $content['sql'] ?? '';
        $time = $content['time'] ?? null;
        $connection = $content['connection_name'] ?? '';

        $parts = [$sql];

        $meta = [];

        if ($time !== null) {
            $meta[] = "{$time}ms";
        }

        if ($connection) {
            $meta[] = $connection;
        }

        if ($meta) {
            $parts[] = implode(' | ', $meta);
        }

        return static::toCustom(implode("\n", $parts), 'Query');
    }

    protected static function convertEvent(array $content): array
    {
        return static::toCustom($content['name'] ?? '', 'Event');
    }

    protected static function convertJobEvent(array $content): array
    {
        $eventName = $content['event_name'] ?? '';
        $job = $content['job'] ?? '';

        return static::toCustom("{$eventName}: {$job}", 'Job');
    }

    protected static function convertMailable(array $content): array
    {
        $lines = [];

        if (! empty($content['subject'])) {
            $lines[] = "Subject: {$content['subject']}";
        }

        if (! empty($content['from'])) {
            $lines[] = 'From: '.static::formatAddresses($content['from']);
        }

        if (! empty($content['to'])) {
            $lines[] = 'To: '.static::formatAddresses($content['to']);
        }

        if (! empty($content['cc'])) {
            $lines[] = 'CC: '.static::formatAddresses($content['cc']);
        }

        if (! empty($content['bcc'])) {
            $lines[] = 'BCC: '.static::formatAddresses($content['bcc']);
        }

        return static::toCustom(implode("\n", $lines), 'Mail');
    }

    protected static function convertEloquentModel(array $content): array
    {
        $className = $content['class_name'] ?? '';
        $attributes = $content['attributes'] ?? [];

        $lines = [$className];

        foreach ($attributes as $key => $value) {
            $lines[] = "  {$key}: ".static::stripHtml($value);
        }

        return static::toCustom(implode("\n", $lines), 'Model');
    }

    protected static function convertView(array $content): array
    {
        $viewPath = $content['view_path_relative_to_project_root']
            ?? $content['view_path']
            ?? '';

        return static::toCustom($viewPath, 'View');
    }

    protected static function convertResponse(array $content): array
    {
        $statusCode = $content['status_code'] ?? '';
        $lines = ["Status: {$statusCode}"];

        if (! empty($content['json'])) {
            $lines[] = json_encode($content['json'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } elseif (! empty($content['content'])) {
            $body = $content['content'];

            if (is_string($body) && strlen($body) > 1000) {
                $body = substr($body, 0, 1000).'...';
            }

            $lines[] = (string) $body;
        }

        return static::toCustom(implode("\n", $lines), 'Response');
    }

    protected static function toCustom(string $text, string $label): array
    {
        return [
            'type' => 'custom',
            'content' => [
                'content' => nl2br(htmlspecialchars($text)),
                'label' => $label,
            ],
        ];
    }

    /**
     * @param  mixed  $value
     */
    protected static function stripHtml($value): string
    {
        if (! is_string($value)) {
            return json_encode($value) ?: '';
        }

        return trim(html_entity_decode(strip_tags($value)));
    }

    protected static function formatAddresses(array $addresses): string
    {
        return implode(', ', array_map(function ($address) {
            if (is_array($address)) {
                $email = $address['email'] ?? $address['address'] ?? '';
                $name = $address['name'] ?? '';

                return $name ? "{$name} <{$email}>" : $email;
            }

            return (string) $address;
        }, $addresses));
    }
}
