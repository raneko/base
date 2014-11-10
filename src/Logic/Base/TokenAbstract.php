<?php

namespace Raneko\Logic\Base;

/**
 * Class to handle token.
 * @author Harry Lesmana <harry@raneko.com>
 */
abstract class TokenAbstract
{

    private $modelToken;

    /**
     * Indicate owner of the token
     * @var int 
     */
    private $entityId;

    /**
     * Indicate owner type of the token
     * @var int
     */
    private $entityTypeId;

    /**
     * Indicate event of which the token is tied to
     * @var int
     */
    private $eventId;

    /**
     * Token generated
     * @var string
     */
    private $token;

    /**
     * Indicate TTL of token in seconds
     * @var int 
     */
    private $ttl;

    /**
     * Indicate whether 1 owner could have more than 1 token for the same event.
     * @var boolean
     */
    private $isAllowMultiple;

    /**
     * Indicate whether token has been persisted to database.
     * @var boolean
     */
    private $isPersisted;

    public function __construct()
    {
        $this->modelToken = \Raneko\Extend\Zend\ModelFactory::getModel("a_token");
        $this->_init();
        $this->isAllowMultiple = FALSE;
    }

    protected function _init()
    {
        $this->entityId = NULL;
        $this->entityTypeId = NULL;
        $this->eventId = NULL;
        $this->token = NULL;
        $this->ttl = 3600;
        $this->isPersisted = FALSE;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function getEntityTypeId()
    {
        return $this->entityTypeId;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }

    public function setEntityTypeId($entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
    }

    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    public function getIsAllowMultiple()
    {
        return $this->isAllowMultiple;
    }

    /**
     * Set whether a single entity is allowed to have multiple token.
     * Use case: for login token as user can sign in from different devices.
     * @param boolean $isAllowMultiple
     */
    public function setIsAllowMultiple($isAllowMultiple)
    {
        $this->isAllowMultiple = $isAllowMultiple;
    }

    public function getToken()
    {
        return $this->token;
    }

    protected function _setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Generate token and persist to database.
     * @return string NULL if failed to generate new token.
     */
    public function generate()
    {
        $proceed = TRUE;
        $result = NULL;

        $this->isPersisted = FALSE;

        /* Check if minimum parameter(s) requirement is met */
        if ($proceed && ($this->entityId === NULL || $this->entityTypeId === NULL || $this->eventId === NULL))
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Basic parameter not met");
        }

        /* Check if additional parameter(s) requirement is met */
        if ($proceed && !$this->_validate())
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Extended parameter not met");
        }

        /* Generate token and check for uniqueness */
        if ($proceed)
        {
            $_retry = 10;
            $_isUnique = FALSE;
            do
            {
                $this->_generate();
                $_isUnique = $this->_checkUnique();
                $_retry--;
            }
            while (!$_isUnique && $_retry > 0);

            if ($_isUnique === TRUE)
            {
                $this->_persist();

                $result = $this->getToken();
            }
            else
            {
                $proceed = FALSE;
            }
        }

        return $result;
    }

    /**
     * Actual method to generate the token.
     * This method should not worry about uniqueness and call _setToken() directly.
     */
    protected abstract function _generate();

    /**
     * Validate whether parameter given is sufficient and value is valid to generate the token.
     * @return boolean Return TRUE value if no implementation
     */
    protected abstract function _validate();

    /**
     * Check whether there exist same token for a given event.
     * @return boolean
     */
    private function _checkUnique()
    {
        $record = $this->modelToken->fetchRow(array(
            "token = ?" => $this->token,
            "event_id = ?" => $this->eventId
        ));
        if (in_array($record, array(NULL, FALSE)))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Persist value to database
     */
    private function _persist()
    {
        $proceed = TRUE;
        $result = NULL;

        /* Check if data has been persisted */
        if ($proceed && $this->isPersisted)
        {
            $proceed = FALSE;
        }

        /* Remove any existing record if token does not allow multiple value */
        if ($proceed && !$this->isAllowMultiple)
        {
            $this->modelToken->delete(array(
                "owner_id = ?" => $this->entityId,
                "owner_type_id = ?" => $this->entityTypeId,
                "event_id = ?" => $this->eventId
            ));
        }

        /* Add data to database */
        if ($proceed)
        {
            $_data = array(
                "token" => $this->token,
                "owner_id" => $this->entityId,
                "owner_type_id" => $this->entityTypeId,
                "event_id" => $this->eventId,
                "expired_on" => new \Zend_Db_Expr("DATE_ADD(UTC_TIMESTAMP(), INTERVAL {$this->ttl} SECOND)")
            );

            $this->modelToken->insert($_data);
        }

        return $result;
    }
}
