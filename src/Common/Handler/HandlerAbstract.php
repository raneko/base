<?php

namespace Raneko\Common\Handler;

/**
 * @author Harry
 * @since 2013-01-14
 */
abstract class HandlerAbstract
{

    /**
     * @var \Raneko\Common\Validator\ValidatorAbstract
     */
    private $validatorList;

    /**
     * List of field to be checked for mandatory constraint
     * @var array
     */
    private $constraintFieldListMandatory;

    /**
     * List of field to be checked for numeric constraint
     * @var array
     */
    private $constraintFieldListNumeric;

    /**
     * List of field to be checked for email constraint
     * @var array
     */
    private $constraintFieldListEmail;

    /**
     * User ID in process,
     * This value could be most of the time obtained from App::getId(), but sometimes the value is to be overridden by parameter `user_id`.
     * @var int
     */
    private $userId;

    /**
     * Constraint defining whether someone must be logged in to invoke the process.
     * @var boolean
     */
    private $constraintIsMustLogin;

    /**
     * Result of the process
     * @var \Raneko\Common\Result
     */
    protected $result;

    /**
     * Parameters
     * @var array
     */
    protected $params;

    public function __construct()
    {
        $this->validatorList = array();

        $this->_setFieldListMandatory();
        $this->_setFieldListNumeric();
        $this->_setRequireActor();
        $this->_setFieldListEmail();

        $this->result = new \Raneko\Common\Result();
    }

    /**
     * Add validator.
     * Handler could have multiple validator while the actual process is limited to just one.
     * @param \Raneko\Common\Validator\ValidatorAbstract $validator
     */
    public function addValidator(\Raneko\Common\Validator\ValidatorAbstract $validator)
    {
        $this->validator[] = $validator;
    }

    /**
     * Set field list to be checked for mandatory constraint.
     * @param array $fieldList
     */
    protected function _setFieldListMandatory($fieldList = array())
    {
        $this->constraintFieldListMandatory = $fieldList;
    }

    /**
     * Set field list to be checked for numeric constraint
     * @param array $fieldList
     */
    protected function _setFieldListNumeric($fieldList = array())
    {
        $this->constraintFieldListNumeric = $fieldList;
    }

    /**
     * Set field list to be checked for email constraint
     * @param array $fieldList
     */
    protected function _setFieldListEmail($fieldList = array())
    {
        $this->constraintFieldListEmail = $fieldList;
    }

    /**
     * Indicate whether this process require an actor or can be invoked by anyone.
     * @param boolean $value
     */
    protected function _setRequireActor($value = TRUE)
    {
        $this->constraintIsMustLogin = $value;
    }

    /**
     * Process request.
     * @param array $params Parameter to be processed.
     * @return \Raneko\Common\Result
     */
    public function process($params = array())
    {
        $this->result = new \Raneko\Common\Result();
        $this->params = $params;
        $proceed = TRUE;

        $method = $this->_getClass();

        /* Normalize value (child) */
        if ($proceed)
        {
            $this->_normalize();
        }

        /* Get user ID */
        if ($proceed && isset($this->params["user_id"]) && strlen($this->params["user_id"]) > 0)
        {
            $this->userId = $this->params["user_id"];
        }
        else
        {
            $this->userId = \Raneko\App::getId();
        }

        /* Perform validation (parent) */
        if ($proceed)
        {
            /* Check if process requires login state */
            if ($this->constraintIsMustLogin === TRUE && in_array($this->userId, array(0, NULL)))
            {
                $proceed = FALSE;
                \Raneko\Log::error($method, "Require login to proceed");
            }
            /* Check for mandatory fields constraint */
            if (is_array($this->constraintFieldListMandatory) && count($this->constraintFieldListMandatory) > 0)
            {
                $_result = \Raneko\Common\Validation::fieldListMandatory($method, $this->constraintFieldListMandatory, $this->params);
                if ($_result === FALSE)
                {
                    $proceed = FALSE;
                }
            }
            /* Check for numeric fields constraint */
            if (is_array($this->constraintFieldListNumeric) && count($this->constraintFieldListNumeric) > 0)
            {
                $_result = \Raneko\Common\Validation::fieldListNumeric($method, $this->constraintFieldListNumeric, $this->params);
                if ($_result === FALSE)
                {
                    $proceed = FALSE;
                }
            }
            /* Check for email fields constraint */
            if (is_array($this->constraintFieldListEmail) && count($this->constraintFieldListEmail) > 0)
            {
                $_result = \Raneko\Common\Validation::fieldListEmail($method, $this->constraintFieldListEmail, $this->params);
                if ($_result === FALSE)
                {
                    $proceed = FALSE;
                }
            }
        }

        /* Perform validation (child) */
        if ($proceed && !$this->_validate())
        {
            $proceed = FALSE;
        }

        /* Perform validation (external validator) */
        if ($proceed && isset($this->validatorList) && count($this->validatorList) > 0)
        {
            $_result = TRUE;
            foreach ($this->validatorList as $_validator)
            {
                $_result = $_result && $_validator->validate($method, $this->params);

                /* Check if validator is terminal and break the iteration if it is */
                if ($_result == FALSE && $_validator->isTerminal())
                {
                    break;
                }
            }

            if ($_result == FALSE)
            {
                $proceed = FALSE;
            }
        }

        /* Invoke actual process (child) */
        if ($proceed)
        {
            $this->_process();
        }

        return $this->result;
    }

    /**
     * Normalize parameter value.
     * Access $this->params directly.
     */
    protected abstract function _normalize();

    /**
     * Actual processor.
     * To be overriden by the child.
     * Access $this->result directly.
     */
    protected abstract function _process();

    /**
     * Actual validator.
     * To be overrided by the child.
     * TO return TRUE by default if there is no implementation.
     * This validator is on top of the other validator via addValidator() method if any.
     * Access $this->params directly
     * @return boolean
     */
    protected abstract function _validate();

    /**
     * Get class name of the child.
     * This is important to identify actual processing class.
     * @return string
     */
    protected abstract function _getClass();

    /**
     * Get parameter passed to the process.
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get user id.
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

}
