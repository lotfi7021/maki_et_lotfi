<?php

namespace App\Swagger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class SwaggerLogger extends AbstractLogger
{
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (in_array($level, [LogLevel::WARNING, LogLevel::NOTICE, LogLevel::INFO])) {
            return;
        }
    }
}