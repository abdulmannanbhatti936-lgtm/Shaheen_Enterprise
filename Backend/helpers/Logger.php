<?php
/**
 * Global Logger - Phase 4 Production Ready
 * Handles error recording to disk instead of browser display.
 */
class Logger
{
    private static $logFile = __DIR__ . '/../logs/error.log';

    public static function log($message, $level = 'ERROR')
    {
        $date = date('Y-m-d H:i:s');
        $logEntry = "[$date] [$level]: $message" . PHP_EOL;

        // Ensure directory exists
        if (!file_exists(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0777, true);
        }

        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Global Error Handler to catch all PHP errors
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        $message = "PHP Error [$errno]: $errstr in $errfile on line $errline";
        self::log($message, 'CRITICAL');
        return false; // Let the default handler work if needed
    }

    /**
     * Global Exception Handler
     */
    public static function handleException($exception)
    {
        $message = "Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
        self::log($message, 'FATAL');
        
        // Return a clean error page in production
        if (!headers_sent()) {
            http_response_code(500);
            echo "<h1>Something went wrong.</h1><p>Our team has been notified. Please try again later.</p>";
        }
    }
}
?>
