<?php

namespace Raneko\Base\Crypt;

/**
 * Class to handle API hash.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-29
 */
class APIHash
{

    private $privateKey;

    public function __construct()
    {
        $this->privateKey = NULL;
    }

    /**
     * Get private key for the hash.
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * Set private key to be used for the hash.
     * @param string $privateKey
     * @return \Raneko\Crypt\APIHash
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * Get hash value.
     * @param mixed $data
     * @return string
     * @throws \Exception
     */
    public function hash($data)
    {
        if (isset($this->privateKey) && strlen($this->privateKey) > 0)
        {
            $valueToHash = NULL;
            if (!is_array($data))
            {
                $valueToHash = $data;
            }
            else
            {
                $_result = NULL;
                $valueToHash = implode(";", $this->walkData($data, NULL, $_result));
            }
            return md5($valueToHash . $this->privateKey);
        }
        else
        {
            throw new \Exception("Private key is not set");
        }
    }

    /**
     * Validate hash value against data and private key.
     * @param string $hash
     * @param mixed $data
     * @return boolean
     */
    public function validate($hash, $data)
    {
        if (isset($this->privateKey) && strlen($this->privateKey) > 0)
        {
            $hashCalculated = $this->hash($data);

            if ($hash != $hashCalculated)
            {
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }
        else
        {
            throw new \Exception("Private key is not set");
        }
    }

    /**
     * Walk through array data and return sorted value.
     * @param array $haystack Chunk of the array to be processed.
     * @param string $key Current array key
     * @param array $result
     * @return array
     */
    private function walkData(array $haystack, $key = NULL, array &$result = null)
    {
        /* This is to initialize the result array and is only needed for the first call of this function */
        if (is_null($result))
        {
            $result = array();
        }
        foreach ($haystack as $_key => $_value)
        {
            /* Check if the key in process is numeric and ignore if it's */
            $_newKey = $key;
            if (!is_numeric($_key))
            {
                $_newKey = is_null($key) ? $_key : "{$key}-{$_key}";
            }

            /* Check whether the value is array and recurse if it's */
            if (is_array($_value))
            {
                empty($_value) || $this->walkData($_value, $_newKey, $result);
            }
            else
            {
                $result[] = "{$_newKey}:{$_value}";
            }
        }

        /* This is only needed in the first function call to retrieve the results */
        sort($result);
        return $result;
    }
}
