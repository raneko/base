<?php

namespace Raneko\Common\Converter;

/**
 * Abstract class for conversion related matters.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 20140515
 */
abstract class ConverterAbstract
{

    /**
     * Convert data.
     * @param mixed $data
     * @return mixed FALSE will be returned if conversion is failed.
     */
    abstract public function convert($data);
}
