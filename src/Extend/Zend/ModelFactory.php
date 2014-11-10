<?php

namespace Raneko\Extend\Zend;

use Raneko\Extend\Zend\Model\General;

/**
 * Factory to generate model instance.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-04-01
 */
class ModelFactory
{

    private static $modelList;

    /**
     * Get model instance for given table name.
     * @param string $tableName
     * @return \Zend_Db_Table_Abstract
     */
    private static function _getModel($tableName)
    {
        if (!isset(self::$modelList))
        {
            self::$modelList = array();
        }
        if (!isset(self::$modelList[$tableName]))
        {
            self::$modelList[$tableName] = new General(array("name" => $tableName));
        }
        return self::$modelList[$tableName];
    }

    /**
     * Get model instance for given table name.
     * @param string $tableName
     * @return \Zend_Db_Table_Abstract
     */
    public static function getModel($tableName)
    {
        return self::_getModel($tableName);
    }

    /**
     * Get autonumbering model.
     * @param string $tableName
     * @return \Raneko\Extend\Zend\Model\AutoNumbering
     */
    public static function autoNumbering($tableName)
    {
        if (!isset(self::$modelList[$tableName]))
        {
            self::$modelList[$tableName] = new \Raneko\Extend\Zend\Model\AutoNumbering(array("name" => $tableName));
        }
        return self::$modelList[$tableName];
    }

}
