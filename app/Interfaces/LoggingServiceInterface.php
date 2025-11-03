<?php 

namespace App\Interfaces;

interface LoggingServiceInterface
{
    public function logInfo(string $message): void;
    public function logDebug(string $message): void;
    public function logWarning(string $message): void;
    public function logError(string $message): void;
}