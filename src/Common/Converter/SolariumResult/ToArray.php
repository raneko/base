<?php

namespace Raneko\Common\Converter\SolariumResult;

/**
 * Convert Solarium result to array.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 20140515
 */
class ToArray extends \Raneko\Common\Converter\ConverterAbstract
{

    /**
     * @param \Solarium\QueryType\Select\Result\Result $data
     */
    public function convert($data)
    {
        $result = array();
        foreach ($data as $_doc)
        {
            $_item = array();
            foreach ($_doc as $_key => $_value)
            {
                $_item[$_key] = $_value;
            }
            $result[] = $_item;
        }
        return $result;
    }

}
