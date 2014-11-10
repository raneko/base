<?php

namespace Raneko\Logic\Base;

/**
 * Generic token generator.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-04-04
 */
class Token extends \Raneko\Logic\Base\TokenAbstract
{

    protected function _generate()
    {
        $this->_setToken(uniqid());
    }

    protected function _isParameterComplete()
    {
        return TRUE;
    }

    protected function _validate()
    {
        return TRUE;
    }
}
