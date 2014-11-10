<?php

namespace Raneko\UID\Token\Generator;

/**
 * Standard token generator.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-22
 */
class Standard extends \Raneko\UID\UIDAbstract
{

    /**
     * Length of token in bytes
     * @var int 
     */
    private $length = 16;

    /**
     * Set token length.
     * @param int $length
     * @return \Raneko\UID\Token\Standard
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    protected function _generate()
    {
        return bin2hex(openssl_random_pseudo_bytes($this->length));
    }

}
