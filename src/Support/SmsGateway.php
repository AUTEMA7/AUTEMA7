<?php

declare(strict_types=1);

namespace App\Support;

final class SmsGateway
{
    public static function send(string $phone, string $message): void
    {
        $line = sprintf("[%s] SMS to %s: %s\n", gmdate('c'), $phone, $message);
        file_put_contents(__DIR__ . '/../../storage_sms.log', $line, FILE_APPEND);
    }
}
