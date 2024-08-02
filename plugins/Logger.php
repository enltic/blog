<?php
class Logger {
    private $logFile;

    public function __construct($logFile = 'access.log') {
        $logDir = dirname(__FILE__);
        $this->logFile = $logDir . DIRECTORY_SEPARATOR . $logFile;
        date_default_timezone_set('Asia/Shanghai');
    }

    private function determineLogLevel() {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $statusCode = http_response_code();
        if ($httpMethod === 'POST') {
            return 'INFO';
        }
        if ($statusCode >= 400) {
            return 'ERROR';
        }
        return 'WARNING';
    }

    public function log($errorMessage = null) {
        $dateTime = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $level = $this->determineLogLevel();
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No Referer';
        $statusCode = http_response_code();

        $logMessage = "[$dateTime] [{$ip} {$level} {$url}] [Method: {$httpMethod}, Referer: {$referer}, Status: {$statusCode}]";
        if ($errorMessage) {
            $logMessage .= ", Error: {$errorMessage}";
        }
        $logMessage .= PHP_EOL;

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        return $logMessage;
    }

    public function displayLog() {
        if (file_exists($this->logFile)) {
            return file_get_contents($this->logFile);
        }
        return 'No log file found.';
    }

    public function downloadLog() {
        if (file_exists($this->logFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($this->logFile).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($this->logFile));
            readfile($this->logFile);
            exit;
        } else {
            echo 'No log file found.';
        }
    }

    public function clearLog() {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
            echo 'Log file deleted.';
        } else {
            echo 'No log file found.';
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $logger = new Logger();
    if (isset($_GET['d'])) {
        $logger->downloadLog();
    } elseif (isset($_GET['c'])) {
        $logger->clearLog();
    } else {
        echo '<pre>' . htmlspecialchars($logger->displayLog()) . '</pre>';
    }
} else {
    $logger = new Logger();
    $logger->log();
}
?>