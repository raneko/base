<?php

namespace Raneko\Common\Converter\PHPArray;

/**
 * Convert array to JSON.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 20140515
 */
class ToJSON extends \Raneko\Common\Converter\ConverterAbstract
{

    public function convert(array $data)
    {
        return json_encode($data);
    }

}
