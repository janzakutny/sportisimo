<?php

declare(strict_types=1);

namespace App;

use Tracy\Logger as TracyLogger;

class Logger extends TracyLogger
{
    /** @var bool */
    private $useStdout = false;


    /**
     * Enable using standard output for logs
     */
    public function enableStdout()
    {
        $this->useStdout = true;
    }


    /**
     * Logs message or exception to file and sends email notification.
     * @param  mixed  $message
     * @param  string  $level  one of constant ILogger::INFO, WARNING, ERROR (sends email), EXCEPTION (sends email), CRITICAL (sends email)
     * @return string|null logged error filename
     */
    public function log($message, $level = self::INFO)
    {
        if (!$this->directory) {
            throw new \LogicException('Logging directory is not specified.');
        } elseif (!is_dir($this->directory)) {
            throw new \RuntimeException("Logging directory '$this->directory' is not found or is not directory.");
        }

        $exceptionFile = $message instanceof \Throwable
            ? $this->getExceptionFile($message, $level)
            : null;
        $line = static::formatLogLine($message, $exceptionFile);
        $file = $this->directory . '/' . strtolower($level ?: self::INFO) . '.' . ($_SERVER['SERVER_NAME'] ?? php_uname('n')) . '.log';

        $handle = @fopen($file, 'a');
        if (!$handle) {
            throw new \RuntimeException("Unable to write to log file '$file'. Is directory writable?");
        }

        flock($handle, LOCK_EX);
        fwrite($handle, $line . PHP_EOL);
        flock($handle, LOCK_UN);
        fclose($handle);

        if ($this->useStdout) {
            if (in_array($level, [self::ERROR, self::EXCEPTION, self::CRITICAL], true)) {
                @file_put_contents('php://stderr', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
            } else {
                @file_put_contents('php://stdout', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
            }
        }

        if ($exceptionFile) {
            $this->logException($message, $exceptionFile);
        }

        if (in_array($level, [self::ERROR, self::EXCEPTION, self::CRITICAL], true)) {
            $this->sendEmail($message);
        }

        return $exceptionFile;
    }
}
