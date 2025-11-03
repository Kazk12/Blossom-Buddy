<?php
namespace App\Services;

use App\Interfaces\LoggingServiceInterface;
use Illuminate\Support\Facades\Log;
class LoggingService implements LoggingServiceInterface
{
    public function logInfo(string $message): void
    {
        Log::info($message);
    }

    public function logDebug(string $message): void
    {
        Log::debug($message);
    }

    public function logWarning(string $message): void
    {
        Log::warning($message);
    }

    public function logError(string $message): void
    {
        Log::error($message);
    }
}