<?php

namespace Raneko\UID\Token;

/**
 * Abstract class to generate token.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-22
 */
abstract class TokenAbstract
{

    /**
     * Generate new token.
     * @param int $entityId
     * @param string $entityType
     * @param string $tokenType
     * @param \Raneko\UID\UIDAbstract $generator Will use \Raneko\UID\Token\Generator\Standard if none supplied.
     * @return string
     */
    public function generate($entityId, $entityType, $tokenType, \Raneko\UID\UIDAbstract $generator = NULL)
    {
        $result = NULL;
        $proceed = TRUE;

        if ($generator === NULL)
        {
            $generator = new \Raneko\UID\Token\Generator\Standard();
        }

        /* Try to generate token, check for uniqueness and give up after a while */
        if ($proceed)
        {
            $_ctr = 0;
            $_ctrMax = 50;
            $_token = NULL;
            $_isUnique = FALSE;
            do
            {
                $_token = $generator->generate();
                $_isUnique = $this->_isUnique($entityId, $entityType, $tokenType, $result);
                $_ctr++;
            }
            while (!$_isUnique && $_ctr <= $_ctrMax);

            if ($_isUnique === TRUE)
            {
                $result = $_token;
            }
            else
            {
                $proceed = FALSE;
                \Raneko\Log::error(__METHOD__, "Unable to generate token");
            }
        }

        return $result;
    }

    /**
     * Check whether token is unique (depends on implementation).
     * @param int $entityId
     * @param string $entityType
     * @param string $tokenType
     * @param string $token
     * @return bool
     */
    abstract protected function _isUnique($entityId, $entityType, $tokenType, $token);

    /**
     * Persist token (depends on implementation).
     * @param int $entityId
     * @param string $entityType
     * @param string $tokenType
     * @param string $token
     * @return bool
     */
    abstract protected function _persist($entityId, $entityType, $tokenType, $token);
}
