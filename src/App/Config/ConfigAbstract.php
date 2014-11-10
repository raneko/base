<?php

namespace Raneko\App\Config;

/**
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2012-11-14
 */
abstract class ConfigAbstract
{

    /**
     * Configuration list in KVP form.
     * @var array
     */
    public $config;

    protected function __construct()
    {
        $this->_setConfig(NULL);
        $this->_loadConfig();
    }

    /**
     * Method to be implemented.
     * All requirement to this method should be passed during construction.
     * Method to call _setConfig() after loading.
     */
    abstract protected function _loadConfig();

    /**
     * Set configuration value.
     * @param array $config
     */
    protected function _setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
