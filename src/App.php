<?php

namespace Raneko;

class App
{

    protected static $_data;

    /**
     * Pointer variable to self::$_data["config"]->config.
     * @var array
     */
    private static $config;

    const KEY_PHP_DATETIME_UTC_TIMESTAMP = "php_datetime_utc_timestamp";
    const KEY_ID = "id";
    const KEY_IP = "ip";
    const KEY_PORT = "port";
    const KEY_CONFIG = "config";
    const KEY_VAR_INC_SERVER = "inc_server";
    const KEY_VAR_EXC_REQUEST = "exc_server";
    const KEY_AES = "aes";
    const KEY_TOKEN = "token";
    const KEY_HEADERS = "headers";

    /**
     * Get PHP DateTime representing current time in UTC timezone.
     * @return \DateTime
     */
    static function getDateTimeUTCTimestamp()
    {
        isset(self::$_data[self::KEY_PHP_DATETIME_UTC_TIMESTAMP]) || self::$_data[self::KEY_PHP_DATETIME_UTC_TIMESTAMP] = new \DateTime(NULL, new \DateTimeZone("UTC"));
        return clone self::$_data[self::KEY_PHP_DATETIME_UTC_TIMESTAMP];
    }

    /**
     * Get user ID who invokes the request.
     * @return int ID of the current user or 0 if user is not recognized. System is recognized as 0.
     */
    static function getId()
    {
        return isset(self::$_data[self::KEY_ID]) ? self::$_data[self::KEY_ID] : 0;
    }

    /**
     * Get IP address who invokes the request.
     * This method take $_SERVER["REMOTE_ADDR"] by default.
     * If special parameter is given or system is behind load balancer, IP will be overridden accordingly.
     * @return string
     */
    static function getIp()
    {
        if (!isset(self::$_data[self::KEY_IP]))
        {
            $_ipAddress = $_SERVER["REMOTE_ADDR"];
            if (isset($_REQUEST["ip"]) && strlen($_REQUEST["ip"]) > 0)
            {
                $_ipAddress = $_REQUEST["ip"];
            }
            elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && strlen($_SERVER["HTTP_X_FORWARDED_FOR"]) > 0)
            {
                $_ipAddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
            }
            self::setIp($_ipAddress);
        }

        return self::$_data[self::KEY_IP];
    }

    /**
     * Get IP address who invokes the request in integer.
     * @return int
     */
    static function getIpLong()
    {
        return ip2long(self::getIp());
    }

    static function getHost()
    {
        return filter_input(INPUT_SERVER, "HTTP_HOST");
    }

    /**
     * Load list of entity from database
     */
    private static function loadEntityList()
    {
        if (!isset(self::$_data[self::KEY_ENTITY_LIST]))
        {
            $_adapter = self::getZendDbAdapter(TRUE);
            $_dbSelect = $_adapter->select()
                    ->from("c_entity", array("entity_id" => "id", "entity_name" => "name", "entity_label" => "label"))
                    ->join("c_entity_field", "c_entity_field.entity_id = c_entity.id", array("field_id" => "id", "field_name" => "name", "field_label" => "label"))
                    ->join("c_entity_option", "c_entity_option.field_id = c_entity_field.id", array("option_id" => "id", "option_value" => "value", "option_label" => "label"))
                    ->order("c_entity.id")
                    ->order("c_entity_field.id")
                    ->order("c_entity_option.label")
            ;
            $_recordList = $_adapter->fetchAll($_dbSelect);

            $_data = array();
            foreach ($_recordList as $_record)
            {
                $_eName = $_record["entity_name"];
                $_fName = $_record["field_name"];
                $_oName = $_record["option_value"];
                if (!isset($_data[$_eName]))
                {
                    $_data[$_eName] = array(
                        "id" => $_record["entity_id"],
                        "label" => $_record["entity_label"],
                        "field_list" => array()
                    );
                }
                if (!isset($_data[$_eName]["field_list"][$_fName]))
                {
                    $_data[$_eName]["field_list"][$_fName] = array(
                        "id" => $_record["field_id"],
                        "label" => $_record["field_label"],
                        "option_list" => array()
                    );
                }

                $_data[$_eName]["field_list"][$_fName]["option_list"][$_oName] = array(
                    "id" => $_record["option_id"],
                    "label" => $_record["option_label"]
                );
            }
            self::$_data[self::KEY_ENTITY_LIST] = $_data;
        }
    }

    public static function setId($id)
    {
        if (!isset(self::$_data[self::KEY_ID]))
        {
            self::$_data[self::KEY_ID] = $id;
        }
    }

    public static function setIp($ip)
    {
        self::$_data[self::KEY_IP] = $ip;
    }

    public static function setPort($port)
    {
        self::$_data[self::KEY_PORT] = $port;
    }

    /**
     * Get URI.
     * @return string
     */
    public static function getURI()
    {
        return filter_input(INPUT_SERVER, "REQUEST_URI");
    }

    /**
     * Configuration object
     * @param \Raneko\App\Config\ConfigAbstract $file
     */
    public static function setConfig($config)
    {
        self::$_data[self::KEY_CONFIG] = $config;
        self::$config = &self::$_data[self::KEY_CONFIG]->config;
    }

    /**
     * Get configuration value.
     * @return mixed
     * @throw \Exception
     */
    public static function getConfig($property = NULL)
    {
        if (is_null($property))
        {
            return self::$config;
        }

        $args = func_get_args();
        if (count($args) == 0)
        {
            throw new \Exception("Undefined property");
        }
        else
        {
            $_root = &self::$config;
            foreach ($args as $_arg)
            {
                if (!isset($_root[$_arg]))
                {
                    throw new \Exception("Property '" . implode(".", $args) . "' not found");
                }
                $_root = &$_root[$_arg];
            }
            return $_root;
        }
    }

    /**
     * Get $_REQUEST variable after filtered.
     * @return array
     */
    public static function getVarRequest()
    {
        if (!isset(self::$_data[self::KEY_VAR_EXC_REQUEST]))
        {
            $_list = array();
            try
            {
                $_list = self::getConfig("app", "log", "exclusion", "request");
                if (!is_array($_list))
                {
                    $_list = strlen($_list) > 0 ? array($_list) : array();
                }
            }
            catch (Exception $ex)
            {
                $_list = FALSE;
            }
            self::$_data[self::KEY_VAR_EXC_REQUEST] = $_list;
        }
        if (self::$_data[self::KEY_VAR_EXC_REQUEST] === FALSE)
        {
            return $_REQUEST;
        }
        else
        {
            $_result = $_REQUEST;
            foreach ($_result as $_key => $_value)
            {
                foreach (self::$_data[self::KEY_VAR_EXC_REQUEST] as $_field)
                {
                    if (stripos($_key, $_field) !== FALSE)
                    {
                        $_result[$_key] = str_repeat("*", strlen($_value));
                    }
                }
            }
            return $_result;
        }
    }

    /**
     * Get $_SERVER after filtered.
     * @return array
     */
    public static function getVarServer()
    {
        if (!isset(self::$_data[self::KEY_VAR_INC_SERVER]))
        {
            $_list = array();
            try
            {
                $_list = self::getConfig("app", "log", "inclusion", "server");
                if (!is_array($_list))
                {
                    $_list = strlen($_list) > 0 ? array($_list) : array();
                }
            }
            catch (Exception $ex)
            {
                $_list = FALSE;
            }
            self::$_data[self::KEY_VAR_INC_SERVER] = $_list;
        }
        if (self::$_data[self::KEY_VAR_INC_SERVER] === FALSE)
        {
            return $_SERVER;
        }
        else
        {
            $_result = array();
            foreach (self::$_data[self::KEY_VAR_INC_SERVER] as $_field)
            {
                $_field = strtoupper($_field);
                $_result[$_field] = isset($_SERVER[$_field]) ? $_SERVER[$_field] : NULL;
            }
            return $_result;
        }
    }

    /**
     * Get request headers.
     * There are confusion in regards to different way of getting the headers in PHP which approach might be platform dependent.
     * This approach is deemed the safest although need to take into consideration that header data might not alway be available.
     * @return array
     */
    public static function getHeaders()
    {
        if (!isset(self::$_data[self::KEY_HEADERS]))
        {
            $_listHeaders = array();
            foreach ($_SERVER as $_key => $_value)
            {
                if (substr($_key, 0, 5) == "HTTP_")
                {
                    $_key = str_replace('_', ' ', substr($_key, 5));
                    $_key = str_replace(' ', '-', ucwords(strtolower($_key)));
                    $_listHeaders[$_key] = $_value;
                }
            }
            self::$_data[self::KEY_HEADERS] = $_listHeaders;
        }
        return self::$_data[self::KEY_HEADERS];
    }

    /**
     * Get default AES handler (using private key from configuration file).
     * Be very careful when handling private key as it might render data useless.
     * @return \Raneko\Crypt\AES
     */
    public static function getAES()
    {
        if (!isset(self::$_data[self::KEY_AES]))
        {
            $_aes = new \Raneko\Crypt\AES();
            $_aes->setKey(self::getConfig("app", "aes", "key"));
            $_aes->setIV($_aes->generateIV());

            self::$_data[self::KEY_AES] = $_aes;
        }
        return self::$_data[self::KEY_AES];
    }

    /**
     * Get port currently being used for processing.
     * Will take $_SERVER["SERVER_PORT"] by default.
     * Value will adjust if application is behind load balancer.
     * @return int
     */
    public static function getPort()
    {
        if (!isset(self::$_data[self::KEY_PORT]))
        {
            $_port = $_SERVER["SERVER_PORT"];
            if (isset($_SERVER["HTTP_X_FORWARDED_PORT"]) && strlen($_SERVER["HTTP_X_FORWARDED_PORT"]) > 0)
            {
                $_port = $_SERVER["HTTP_X_FORWARDED_PORT"];
            }
            self::setPort($_port);
        }

        return self::$_data[self::KEY_PORT];
    }

    /**
     * Get USER AGENT string.
     * @return string
     */
    public static function getUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    /**
     * Get token associated with the call.
     * Token must be externally set first via setToken().
     * @return string|null
     */
    public static function getToken()
    {
        return isset(self::$_data[self::KEY_TOKEN]) ? self::$_data[self::KEY_TOKEN] : NULL;
    }

    /**
     * Set token.
     * Typically is to be set in bootstrap.
     * @param string $token
     */
    public static function setToken($token)
    {
        self::$_data[self::KEY_TOKEN] = $token;
    }

}
