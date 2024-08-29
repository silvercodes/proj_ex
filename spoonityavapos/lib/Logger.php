<?php
require_once 'ConfigManager.php';

/**
 * Class Singleton Logger
 */
class Logger
{

    /**
     * @var null|self instance
     */
    private static $_instance = null;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var bool|resource
     */
    private $file;

    /**
     * @var array
     */
    private $options = [
        'dateFormat' => 'H:i:s d-M-Y'
    ];

    /**
     * Object of config from app.ini
     * @var array|null
     */
    private $config;

    /**
     * Logger constructor.
     * @throws Exception
     */
    private function __construct() {
        $this->config = ConfigManager::getAppConfig()->logger;

        $filePath = ROOT . $this->config->filePath;

        $this->filePath = $filePath;
        if (!file_exists($this->filePath)) {
            $this->file = fopen($filePath, 'w') or exit("Can't create $this->filePath");
        }
        if (!is_writable($filePath)) {
            throw new Exception("ERROR: Unable to write to file! $this->filePath", 1);
        }
    }

    /**
     * prevent the instance from being cloned
     */
    private function __clone () {}

    /**
     * prevent from being deserialize
     */
    private function __wakeup () {}

    /**
     * @return Logger|null
     * @throws Exception
     */
    public static function getLogger() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Write to log file with tag INFO
     * @param string $message
     */
    public function info($message) {
        if ($this->config->loggingInfo)
            $this->writeLog($message, 'INFO');
    }

    /**
     * Write to log file with tag ERROR
     * @param string $message
     */
    public function error($message) {
        if ($this->config->loggingErrors)
            $this->writeLog($message, '!-ERROR-!');
    }

    /**
     * Write to log file with tag DEBUG
     * @param string $message
     */
    public function debug($message) {
        if ($this->config->loggingDebug)
            $this->writeLog($message, '=DEBUG=');
    }

    /**
     * Open log file for writing
     */
    private function openFile() {
        $this->file = fopen($this->filePath, 'a') or exit("Can't open $this->filePath");
    }

    /**
     * Write log
     * @param string $message
     * @param string $tag
     */
    private function writeLog($message, $tag) {
        if (!is_resource($this->file)) {
            $this->openFile();
        }

        $path = ($_SERVER['SERVER_NAME'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
        $time = date($this->options['dateFormat']);

        fwrite($this->file, " [$time] [$path]\n[$tag] $message\n");
    }


    /**
     * Logger destructor.
     */
    public function __destruct() {
        if($this->file) {
            fclose($this->file);
        }
    }
}