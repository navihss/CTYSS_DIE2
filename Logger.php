<?php
class Logger
{
    private $logFile;

    public function __construct($logFilePath)
    {
        $this->logFile = $logFilePath;
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function log($message, $level = 'INFO')
    {
        $date = date('Y-m-d H:i:s');
        $formattedMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $formattedMessage, FILE_APPEND);
    }

    public function info($message)
    {
        $this->log($message, 'INFO');
    }

    public function error($message)
    {
        $this->log($message, 'ERROR');
    }
}
