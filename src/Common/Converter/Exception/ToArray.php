<?php

namespace Raneko\Common\Converter\Exception;

/**
 * Convert Exception to array.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-27
 */
class ToArray extends \Raneko\Common\Converter\ConverterAbstract
{

    /**
     * @param \Exception $data
     */
    public function convert($data)
    {
        $result = array(
            "message" => $data->getMessage(),
            "code" => $data->getCode(),
            "file" => $data->getFile(),
            "line" => $data->getLine()
        );
        return $result;
    }

}
