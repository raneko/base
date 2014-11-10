<?php

namespace Raneko\Extend\Zend;

use Raneko\App;

/**
 * Model abstract extended from Zend_Db_Abstract
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-04-01
 */
abstract class ModelAbstract extends \Zend_Db_Table_Abstract
{

    private static $excludeList;

    /**
     * Set list of fields to be removed from input.
     * Template based fields are excluded by defaults.
     * Needed only if there is another field(s) to be globally excluded.
     * @param array $list List of fields
     */
    public static function setExcludeList(array $list)
    {
        /* Default fields to be removed from input */
        $defaultList = array(
            "id",
            "guid",
            "sys_created_by",
            "sys_created_on",
            "sys_created_ip",
            "sys_modified_by",
            "sys_modified_on",
            "sys_modified_ip",
            "sys_closed_by",
            "sys_closed_on",
            "sys_closed_ip",
            "sys_void_by",
            "sys_void_on",
            "sys_void_ip",
            "sys_last_update"
        );
        if (!isset(self::$excludeList))
        {
            self::$excludeList = $defaultList;
        }
        foreach ($list as $_field)
        {
            if (!in_array($_field, self::$excludeList))
            {
                self::$excludeList[] = $_field;
            }
        }
    }

    public function __construct($config = array())
    {
        if (self::getDefaultAdapter() === NULL)
        {
            self::setDefaultAdapter(App::getZendDbAdapter());
        }
        if (!isset(self::$excludeList))
        {
            self::setExcludeList(array());
        }
        parent::__construct($config);
    }

    public function insert(array $data)
    {
        return parent::insert($this->normalizeInsert($data));
    }

    public function update(array $data, $where)
    {
        $rowsUpdated = parent::update($this->normalizeUpdate($data), $where);
        if ($rowsUpdated > 0)
        {
            $_data = array(
                "sys_modified_by" => App::getId(),
                "sys_modified_on" => App::getZendDbExprUTCTimestamp(),
                "sys_modified_ip" => new \Zend_Db_Expr("INET_ATON('" . App::getIp() . "')")
            );
            parent::update($_data, $where);
        }
        return $rowsUpdated;
    }

    public function delete($where)
    {
        /**
         * @todo Duplicate record to be deleted somewhere else before deletion
         */
        return parent::delete($where);
    }

    /**
     * Normalize data for insert operation.
     * @param array $data
     * @return array
     */
    private function normalizeInsert($data)
    {
        $result = $this->cleanUpFrom($data, self::$excludeList);

        array_key_exists("sys_is_active", $data) || $result["sys_is_active"] = 1;
        array_key_exists("sys_status_code", $data) || $result["sys_status_code"] = "NEW";
        array_key_exists("guid", $data) || $result["guid"] = \Raneko\UID\GUID4::generate();

        $result["sys_status_code"] = strtoupper($result["sys_status_code"]);

        $result["sys_created_by"] = App::getId();
        $result["sys_created_on"] = App::getZendDbExprUTCTimestamp();
        $result["sys_created_ip"] = new \Zend_Db_Expr("INET_ATON('" . App::getIp() . "')");

        /* Encrypt data for certain fields */
        $result = $this->encryptData($result);

        return $this->cleanUp($result);
    }

    /**
     * Normalize data for update operation.
     * @param array $data
     * @return array
     */
    private function normalizeUpdate($data)
    {
        $result = $this->cleanUpFrom($data, self::$excludeList);

        /* Status */
        if (array_key_exists("sys_status_code", $result))
        {
            if (in_array($result["sys_status_code"], array("FIN", "CNX", "RJX")))
            {
                $result["sys_closed_by"] = new \Zend_Db_Expr("IF(ISNULL(sys_closed_by), '" . App::getId() . "', sys_closed_by)");
                $result["sys_closed_on"] = new \Zend_Db_Expr("IF(ISNULL(sys_closed_on), UTC_TIMESTAMP(), sys_closed_on)");
                $result["sys_closed_ip"] = new \Zend_Db_Expr("IF(ISNULL(sys_closed_ip), INET_ATON('" . App::getIp() . "'), sys_closed_ip)");
            }
            if (in_array($result["sys_status_code"], array("VDX")))
            {
                $result["sys_void_by"] = new \Zend_Db_Expr("IF(ISNULL(sys_void_by), '" . App::getId() . "', sys_void_by)");
                $result["sys_void_on"] = new \Zend_Db_Expr("IF(ISNULL(sys_void_on), UTC_TIMESTAMP(), sys_void_on)");
                $result["sys_void_ip"] = new \Zend_Db_Expr("IF(ISNULL(sys_void_ip), INET_ATON('" . App::getIp() . "'), sys_void_ip)");
            }
            if (in_array($result["sys_status_code"], array("CNX", "RJX", "VDX")))
            {
                $result["sys_is_active"] = 0;
            }
            else
            {
                $result["sys_is_active"] = 1;
            }
        }

        /* Encrypt data for certain fields */
        $result = $this->encryptData($result);

        return $this->cleanUp($result);
    }

    /**
     * Clean up data from alien field(s).
     * This is based on the actual fields of the schema.
     * @param array $data
     * @return array 
     */
    private function cleanUp($data)
    {
        $result = $data;

        /* Clean up data */
        $columns = $this->info(\Zend_Db_Table_Abstract::COLS);
        foreach (array_keys($result) as $_key)
        {
            if (!in_array($_key, $columns))
            {
                unset($result[$_key]);
            }
        }

        return $result;
    }

    /**
     * Clean up data from the given fields.
     * @param array $data Data to be cleaned up.
     * @param array $fields Fields to be removed.
     * @return array
     */
    private function cleanUpFrom(array $data, array $fields)
    {
        $result = $data;

        foreach ($result as $_key => $_value)
        {
            if (in_array($_key, $fields))
            {
                unset($result[$_key]);
            }
        }

        return $result;
    }

    /**
     * Encrypt data in key value pair.
     * @param array $data
     */
    private function encryptData(array $data)
    {
        if (NULL !== \Raneko\App::getAES())
        {
            foreach ($data as $_key => $_value)
            {
                if (strpos($_key, "_enc") !== FALSE && strpos($_value, "aes:") !== 0)
                {
                    $data[$_key] = \Raneko\App::getAES()->encrypt($_value, TRUE);
                }
            }
        }
        return $data;
    }

    /**
     * Decrypt data in key value pair.
     * @param array $data
     */
    private function decryptData(array &$data)
    {
        if (NULL !== \Raneko\App::getAES())
        {
            foreach ($data as $_key => $_value)
            {
                if (strpos($_key, "_enc") !== FALSE && strpos($_value, "aes:") !== 0)
                {
                    $data[$_key] = \Raneko\App::getAES()->decrypt($_value);
                }
            }
        }
        return $data;
    }

}
