<?php

namespace App\Helpers;

/**
 * Maneja la escritura de logs en un archivo.
 */
class Logger
{
    private string $logFile;

    public function __construct(string $logFilePath)
    {
        $this->logFile = $logFilePath;
        $dir = dirname($this->logFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * Registra un mensaje con el nivel especificado.
     */
    public function log(string $message, string $level = 'INFO'): void
    {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }

    /**
     * Registra un mensaje de información.
     */
    public function info(string $message): void
    {
        $this->log($message, 'INFO');
    }

    /**
     * Registra un mensaje de error.
     */
    public function error(string $message): void
    {
        $this->log($message, 'ERROR');
    }
}
