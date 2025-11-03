<?php 

namespace App\Interfaces;

interface LoggingServiceInterface
{
    // Basic generic logger
    public function log(string $message): void;

    // Level-specific helpers
    public function logInfo(string $message): void;
    public function logDebug(string $message): void;
    public function logWarning(string $message): void;
    public function logError(string $message): void;
 
}