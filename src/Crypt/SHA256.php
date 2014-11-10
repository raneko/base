<?php

namespace Raneko\Crypt;

/**
 * Class to handle hashing with SHA256 method.
 * @author Harry
 * @since 20140123
 */
class SHA256
{
    /**
     * @var \Raneko\Extend\PasswordHash
     */
    private $hash;

    public function __construct()
    {
        $this->hash = new \Raneko\Extend\PasswordHash();
        $this->config = array();
    }

    /**
     * Hash a value.
     * @param string $value
     * @return string Hash elements separated by `:`. Structure:
     * - `hash_type`
     * - `iteration`
     * - `IV`
     * - `hash_value`
     */
    public function hash($value)
    {
        return $this->hash->createHash($value);
    }

    /**
     * Validate a value against stored hash elements.
     * @param string $value
     * @param string $hash
     * @return boolean
     */
    public function validate($value, $hash)
    {
        return $this->hash->validatePassword($value, $hash);
    }

}
