<?php

namespace Raneko\Common\Model;

/**
 * Abstract to model classes.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-15
 */
class ModelAbstract
{

    public function getVars()
    {
        return get_object_vars($this);
    }

    public function toArray()
    {
        $result = array();
        $data = $this->getVars();
        return $this->convertToArray($result, $data);
    }

    private function convertToArray(array &$result, array &$data)
    {
        foreach ($data as $_key => $_value)
        {
            if (is_object($_value))
            {
                /* Object type */
                $result[$_key] = $_value->toArray();
            }
            else if (is_array($_value))
            {
                /* Array type (can be array of object or scalar) */
                $_subResult = array();
                $result[$_key] = $this->convertToArray($_subResult, $_value);
            }
            else
            {
                /* Scalar type */
                $result[$_key] = $_value;
            }
        }
        return $result;
    }

}
