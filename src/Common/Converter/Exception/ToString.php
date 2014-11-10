<?php

namespace Raneko\Common\Converter\Exception;

/**
 * Convert Exception to array.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-27
 */
class ToString extends \Raneko\Common\Converter\ConverterAbstract
{

    /**
     * @param \Exception $data
     */
    public function convert($data)
    {
        $result = $data->getMessage() . " in " . $data->getFile() . " line " . $data->getLine();
        return $result;
    }

}
