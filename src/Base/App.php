<?php

namespace Raneko\Base;

/**
 * For required config, please refer to docs/app.ini.placeholder.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2015-11-18
 */
class App {

    const STATIC_KEY_DB_ADAPTER = "zend_db";
    const STATIC_KEY_DB_ADAPTER_READONLY = "zend_db_ro";
    const STATIC_KEY_CONFIG = "zend_config";
    const STATIC_KEY_MONOLOG = "monolog";

    private static $data = array();

    public static function getFilename($context = "", $extension = "tmp") {
        strlen($context) == 0 || $context .= "_";
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $context . self::uuid4() . "." . $extension;
    }

    /**
     * UUID v4.
     * @return string
     */
    public static function uuid4($separator = "") {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); /* set version to 0010 */
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); /* set bits 6-7 to 10 */

        $result = strtoupper(vsprintf("%s%s{$separator}%s{$separator}%s{$separator}%s{$separator}%s%s%s", str_split(bin2hex($data), 4)));


        return $result;
    }

    public static function rawQuery($query, $dbHost, $dbUser, $dbPass, $dbName) {
        $mysqli = new \mysqli();
        $mysqli->init();
        $mysqli->options(MYSQLI_OPT_LOCAL_INFILE, true);
        $mysqli->real_connect($dbHost, $dbUser, $dbPass, $dbName);
        /* check connection */
        if ($mysqli->connect_errno) {
            throw new \Exception("Error during DB connection failure:" . $mysqli->error);
        }

        $result = $mysqli->query($query);
        $query_result_array = array();
        $query_result_array['error'] = $mysqli->error;
        $query_result_array['errorno'] = $mysqli->errno;
        $mysqli->close();
        if ($query_result_array['errorno'] <> null && $query_result_array['errorno'] <> "") {
            throw new \Exception("Error during Query processing:" . $query_result_array['errorno'] . "|Error:" . $query_result_array['error']);
        }
        return $query_result_array;
    }

    /**
     * Get Zend DB adapter.
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbName
     * @param int $dbPort
     * @return \Zend_Db_Adapter_Abstract Zend DB adapter.
     */
    public static function getDbAdapter($dbHost, $dbUser, $dbPass, $dbName, $dbPort = "3306") {
        $key = self::STATIC_KEY_DB_ADAPTER . md5($dbHost . $dbName . $dbUser);
        if (!isset(self::$data['getDbAdapter'][$key])) {
            $config = array(
                'dbname' => $dbName,
                'host' => $dbHost,
                'username' => $dbUser,
                'password' => $dbPass,
                'port' => $dbPort,
                'charset' => "utf8"
            );
            self::$data['getDbAdapter'][$key] = new \Zend_Db_Adapter_Pdo_Mysql($config);
        }

        return self::$data['getDbAdapter'][$key];
    }

    /**
     * Set cache data.
     * @param string $key
     * @param mixed $data
     */
    private static function setData($key, $data) {
        self::$data[$key] = $data;
    }

    /**
     * Get cached data.
     * @param string $key
     * @return mixed Cached data.
     */
    private static function getData($key) {
        return isset(self::$data[$key]) ? self::$data[$key] : NULL;
    }

    /**
     * Register configuration file.
     * Only 1 registration is allowed per config name.
     * @param string $file Absolute path to the config file.
     * @param string $configName Name to label the config.
     * @throws \Exception
     */
    public static function registerConfigFile($file, $configName = "app") {
        $key = self::STATIC_KEY_CONFIG . "-{$configName}";
        $result = self::getData($key);
        if ($result === NULL) {
            try {
                $result = new \Zend_Config_Ini($file);
                self::addData($key, $result);
            } catch (\Exception $ex) {
                throw $ex;
            }
        } else {
            throw new \Exception("Config '{$configName}' is already registered");
        }
    }

    /**
     * Get config object.
     * @param string $configName Config label.
     * @return \Zend_Config
     */
    public static function getConfig($configName = "app") {
        $key = self::STATIC_KEY_CONFIG . "-{$configName}";
        return self::getData($key);
    }

    /**
     * Get monolog logger.
     * @return \Monolog\Logger
     */
    public static function getLog($name = "app") {
        $key = self::STATIC_KEY_MONOLOG . "-" . $name;
        $result = self::getData($key);
        if (!isset($result)) {
            /* Try to get config for path and level */
            $logPath = sys_get_temp_dir();
            $logLevel = \Monolog\Logger::DEBUG;
            $config = self::getConfig();
            if ($config !== NULL) {
                !isset($config->app->log->path) || $logPath = $config->app->log->path;
                !isset($config->app->log->level) || $logLevel = $config->app->log->level;
            }

            $result = new \Monolog\Logger($name);
            $handler = new \Monolog\Handler\RotatingFileHandler($logPath . DIRECTORY_SEPARATOR . $name . ".log", 20, $logLevel);
            $result->pushHandler($handler);
            self::addData($key, $result);
        }
        return $result;
    }

}
