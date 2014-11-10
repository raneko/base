<?php

namespace Raneko\Logic\Base\Token;

/**
 * Generate token to be used for SMS verification.
 * @author Harry Lesmana <harry@raneko.com>
 */
class SMS extends \Raneko\Logic\Base\TokenAbstract
{

    /**
     * Number of digits to be generated.
     * @var int 
     */
    private $length;

    public function __construct()
    {
        parent::__construct();
        $this->setLength(0);
    }

    protected function _generate()
    {
        /* Define bottom and top limit */
        $limitBottom = pow(10, $this->getLength() - 1);
        $limitTop = pow(10, $this->getLength()) - 1;

        $this->_setToken(rand($limitBottom, $limitTop));
    }

    protected function _validate()
    {
        $result = TRUE;

        if ($this->getLength() <= 0)
        {
            $result = FALSE;
            \Raneko\Log::error(__METHOD__, "Length not set");
        }

        return $result;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;
    }
}
