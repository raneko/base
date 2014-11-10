<?php

namespace Raneko\UID;

/**
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-07-03
 */
abstract class UIDAbstract
{

    /**
     * Generate UID.
     * @return string
     */
    public function generate()
    {
        $result = $this->_generate();
        return $result;
    }

    protected abstract function _generate();
}
