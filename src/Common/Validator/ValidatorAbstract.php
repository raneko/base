<?php

namespace Raneko\Common\Validator;

/**
 * Abstract for validator class.
 * @author Harry Lesmana <harry@raneko.com>
 */
abstract class ValidatorAbstract
{

    private $isTerminal;
    private $relevantFieldList;

    public function __construct()
    {
        $this->_setTerminal();
        $this->_setFieldList();
    }

    /**
     * Validate parameters.
     * @param string $method Originating method invoking this validation.
     * @param array $params Parameter to validate.
     * @return boolean
     */
    public function validate($method, $params)
    {
        return $this->_validate($method, $params);
    }

    protected abstract function _validate($method, $params);

    /**
     * Indicate whether result of FALSE of this instance should cause other validation to stop.
     * @param boolean $isTerminal
     */
    protected function _setTerminal($isTerminal = FALSE)
    {
        $this->isTerminal = $isTerminal;
    }

    /**
     * Indicate whether result of FALSE of this instance should cause other validation to stop.
     * @return boolean
     */
    public function isTerminal()
    {
        return $this->isTerminal;
    }

    /**
     * Set fields relevant to this instance of validation.
     * @param array $fieldList
     */
    protected function _setFieldList($fieldList = array())
    {
        $this->relevantFieldList = $fieldList;
    }

    /**
     * List of all fields relevant to validation.
     * @return array
     */
    public function getFieldList()
    {
        return $this->relevantFieldList;
    }

}
