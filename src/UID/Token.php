<?php

namespace Raneko\UID;

/**
 * Generate standard token.
 * This class does not check for uniqueness and does not allow persistence.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 */
class Token extends \Raneko\UID\Token\TokenAbstract
{

    protected function _isUnique($entityId, $entityType, $tokenType, $token)
    {
        return TRUE;
    }

    protected function _persist($entityId, $entityType, $tokenType, $token)
    {
        return TRUE;
    }
}
