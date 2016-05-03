<?php

namespace Raneko\Base\Handler;

/**
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2015-11-18
 */
abstract class HandlerAbstract {

    /**
     * Parameters.
     * @var array 
     */
    private $parameterList = array();

    /**
     * @var \Spec\Model\Result[]
     */
    private $resultList = array();

    /**
     * List of fields mandatory to the process.
     * It will by default be used for validation.
     * @var array
     */
    protected $_mandatoryFieldList = array();

    public function getParameter($key = NULL) {
        if ($key === NULL) {
            return $this->parameterList;
        } else {
            return isset($this->parameterList[$key]) ? $this->parameterList[$key] : NULL;
        }
    }

    public function setParameterList($parameterList) {
        $this->parameterList = $parameterList;
        return $this;
    }

    /**
     * @param \Spec\Model\Result $result
     */
    protected function addResult(\Spec\Model\Result $result) {
        $this->resultList[] = $result;
    }

    /**
     * @return \Spec\Model\Result[]
     */
    public function getResultList() {
        return $this->resultList;
    }

    public function execute() {
        $this->validate();
        $this->_prepare();
        $this->_execute();
    }

    abstract protected function _execute();

    public function validate() {
        try {
            $this->checkMandatoryFields();
            $this->_validate();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    abstract protected function _prepare();

    abstract protected function _validate();

    /**
     * @param string $data
     * @return \Spec\Model\Result
     */
    protected function createResult($data, $extension = "tmp") {
        $filename = \Spec\App::getFilename(str_ireplace(array("\\"), array("_"), get_class($this)), $extension);

        file_put_contents($filename, $data);

        $result = new \Spec\Model\Result();
        $result->setCode("0");
        $result->setFile($filename);
        $result->setIsSuccess(TRUE);

        return $result;
    }

    /**
     * Check whether mandatory fields are all present in the payload.
     * @param array $fieldList List of mandatory fields to be checked. Key value pair is accepted with field as the key and description of the field as the value.
     * Example: 
     * [
     *  "startDate" => "Date in YYYY-MM-DD which indicates starting date",
     *  "endDate" => "Date in YYYY-MM-DD which indicates end date"
     * ]
     * @return boolean
     * @throws Exception
     */
    public function checkMandatoryFields($fieldList = NULL) {
        $errorList = array();
        $result = TRUE;

        /* Try to guess which list should be used */
        $fieldList = is_null($fieldList) ? $this->_mandatoryFieldList : array();

        foreach ($fieldList as $key => $value) {
            $actualKey = is_numeric($key) ? $value : $key;

            if (!isset($this->parameterList[$actualKey]) || strlen($this->parameterList[$actualKey]) == 0) {
                $errorMessage = "Field `{$actualKey}` is expected but not set";
                is_numeric($key) || $errorMessage .= " " . $value;
                $errorList[] = $errorMessage;
                $result = FALSE;
            }
        }

        if (!$result) {
            throw new \Exception(implode("\n", $errorList));
        }

        return $result;
    }

}
