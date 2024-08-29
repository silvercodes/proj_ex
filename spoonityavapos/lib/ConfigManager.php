<?php

/**
 * Class Singleton ConfigManager
 */
class ConfigManager
{
    /**
     * @var null|self instance
     */
    private static $_instance = null;

    /**
     * @var array
     */
    private $options = [
        'app' => ROOT . '/config/app.ini',
        'api' => ROOT . '/config/api.php'
    ];

    /**
     * @var object(stdClass)|bool
     */
    private $_appConfig;

    /**
     * @var array
     */
    private $_apiConfig;

    /**
     * ConfigManager constructor.
     */
    private function __construct() {
        try{
            // ---- app ----
            $this->_appConfig = parse_ini_file(
                $this->options['app'],
                true,
                INI_SCANNER_TYPED);

            // convert to object(stdClass)
            $this->_appConfig = json_decode (json_encode ($this->_appConfig), false);

            // ---- API ---
            $this->_apiConfig = include($this->options['api']);

        } catch(Exception $e) {
            var_dump($e->getMessage());
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
     * Return App config
     * @return object|null
     * @throws Exception
     */
    public static function getAppConfig() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance->_appConfig;
    }

    /**
     * Return Spoonity API config
     * @return array
     * @throws Exception
     */
    public static function getSpoonityApiConfig() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance->_apiConfig['spoonity'];
    }

    /**
     * Return AvaPos API config
     * @return array
     * @throws Exception
     */
    public static function getAvaposApiConfig() {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance->_apiConfig['avapos'];
    }
}
