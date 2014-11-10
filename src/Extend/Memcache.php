<?php

namespace Raneko\Extend;

/**
 * Extension to Memcache class
 * @author Harry
 * @since 20130522
 */
class Memcache
{

    const DEFAULT_HOST = "127.0.0.1";
    const DEFAULT_PORT = "11211";
    const DEFAULT_TIMEOUT_CONNECT = 5;
    const DEFAULT_TIMEOUT_LIFETIME = 3600;

    private $config;

    /**
     * Indicates whether connection to Memcache server is established.
     * @var boolean
     */
    private $isConnected;

    /**
     * @var \Memcache
     */
    private $memcache;

    /**
     * @param string $host Host of the Memcached server
     * @param int $port Port to connect to Memcached server
     * @param int $timeout Timeout in seconds
     * @return \Raneko\Extend\Memcache
     * @author Harry
     * @since 20130522
     */
    public function __construct($host = self::DEFAULT_HOST, $port = self::DEFAULT_PORT, $timeout = self::DEFAULT_TIMEOUT_CONNECT)
    {
        $this->isConnected = FALSE;

        $this->config = array(
            "host" => $host,
            "port" => $port
        );

        $this->memcache = new \Memcache();
        $this->isConnected = $this->memcache->connect($host, $port, $timeout);
    }

    /**
     * Set cache
     * @param string $key
     * @param mixed $value
     * @param int $timeout
     * @return boolean
     */
    public function set($key, $value, $timeout = self::DEFAULT_TIMEOUT_LIFETIME)
    {
        if ($this->isConnected)
        {
            $this->memcache->set($this->getKey($key), $value, MEMCACHE_COMPRESSED, $timeout);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get cache stored.
     * @param string $key
     * @return mixed|NULL
     */
    public function get($key)
    {
        $result = NULL;

        if ($this->isConnected)
        {
            $_result = $this->memcache->get($this->getKey($key));
            if ($_result !== FALSE)
            {
                $result = $_result;
            }
        }

        return $result;
    }

    private function getKey($key)
    {
        return \Raneko\App::getHost() . "-" . $key;
    }

}
