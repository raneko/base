<?php

namespace Raneko\App\Config;

/**
 * Config extension to read from INI file.
 * @author Harry Lesmana
 */
class Ini extends \Raneko\App\Config\ConfigAbstract
{

    /**
     * INI file to be loaded.
     * @var string
     */
    private $configFile;
    
    /**
     * Application environment to be loaded.
     * @var string
     */
    private $configEnvironment;

    /**
     * @param string $configFile INI config file name
     */
    public function __construct($configFile, $environment = APPLICATION_ENV)
    {
        $this->setConfigFile($configFile);
        $this->configEnvironment = $environment;
        parent::__construct();
    }

    private function setConfigFile($fileName)
    {
        if (!file_exists($fileName))
        {
            throw new \Exception("File '{$fileName}' does not exist");
        }
        else
        {
            $this->configFile = $fileName;
        }
    }

    /**
     * @param string $fileName
     * @author Jeremy Gibson <jeremygibson@gmail.com>
     * @return array
     */
    public function readFile($fileName, $environment = APPLICATION_ENV)
    {
        $result = array();

        $p_ini = parse_ini_file($fileName, true);

        $configRaw = array();
        foreach ($p_ini as $namespace => $properties)
        {
            $_elements = explode(":", $namespace);
            $name = isset($_elements[0]) ? trim($_elements[0]) : "";
            $extends = isset($_elements[1]) ? trim($_elements[1]) : "";
            /* create namespace if necessary */
            if (!isset($configRaw[$name]))
                $configRaw[$name] = array();
            /* inherit base namespace */
            if (isset($p_ini[$extends]))
            {
                foreach ($p_ini[$extends] as $prop => $val)
                    $configRaw[$name][$prop] = $this->normalizeValue($val);
            }
            /* overwrite / set current namespace values */
            foreach ($properties as $prop => $val)
                $configRaw[$name][$prop] = $this->normalizeValue($val);
        }

        if (isset($configRaw[$environment]))
        {
            foreach ($configRaw[$environment] as $_key => $_value)
            {
                $_keyElements = explode(".", $_key);
                $_pointer = &$result;
                foreach ($_keyElements as $_keyElement)
                {
                    isset($_pointer[$_keyElement]) || $_pointer[$_keyElement] = NULL;
                    $_pointer = &$_pointer[$_keyElement];
                }
                $_pointer = $_value;
            }
        }

        return $result;
    }

    protected function _loadConfig()
    {
        $config = $this->readFile($this->configFile, $this->configEnvironment);
        $this->_setConfig($config);
    }

    private function normalizeValue($val)
    {
        if (in_array(strtolower($val), array("true")))
        {
            return TRUE;
        }
        if (in_array(strtolower($val), array("false")))
        {
            return FALSE;
        }
        if (strpos($val, ",") !== FALSE)
        {
            $_elements = explode(",", $val);
            $_data = array();
            foreach ($_elements as $_element)
            {
                $_data[] = trim($_element);
            }
            return $_data;
        }
        return $val;
    }
}
