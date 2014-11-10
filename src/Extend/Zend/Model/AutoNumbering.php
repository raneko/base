<?php

namespace Raneko\Extend\Zend\Model;

/**
 * This class is to handle generation of auto numbering.    
 * Extends from Zend_Db_Table_Abstract directly instead of \Raneko\Extend\Zend\ModelAbstract.
 * Refer to schema of `a_auto_numbering` from "\docs\db_schema.sql".
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 20140516
 */
class AutoNumbering extends \Zend_Db_Table_Abstract
{

    public function getNextCode($entity)
    {
        $result = NULL;
        $proceed = TRUE;
        $record = $this->fetchRow(array("entity_name = ?" => $entity));

        if ($proceed && !in_array($record, array(NULL, FALSE)) && $record["is_table"] == 1)
        {
            $_isUnique = FALSE;
            $_model = \Raneko\Extend\Zend\ModelFactory::getModel($entity);
            while (!$_isUnique)
            {
                $this->update(array("next_number" => new \Zend_Db_Expr("next_number + 1")), array("id = ?" => $record["id"]));
                $_code = $this->generateCode($record["prefix"], $record["leading_zero"], $record["next_number"], $record["suffix"]);

                $_record = $_model->fetchRow(array("{$record["field_name"]} = ?" => $_code));
                if (in_array($_record, array(NULL, FALSE)))
                {
                    $_isUnique = TRUE;
                    $result = $_code;
                }
            }
        }

        return $result;
    }

    /**
     * Generate code.
     * @param string $prefix
     * @param integer $leadingZero
     * @param integer $nextNumber
     * @param string $suffix
     * @return string
     */
    private function generateCode($prefix, $leadingZero, $nextNumber, $suffix)
    {
        return $prefix . str_pad($nextNumber, $leadingZero, "0", STR_PAD_LEFT) . $suffix;
    }

}
