<?php

namespace Raneko\Common;

/**
 * Perform basic non database validation.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-04-04
 */
class Validation {

    /**
     * Validate mandatory fields, make sure the key and the value is there.
     * @param string $method Method invoking the validation.
     * @param array $fieldList List of mandatory fields
     * @param array $params Parameters to be checked
     * @return boolean
     */
    public static function fieldListMandatory($method, $fieldList, $params) {
        $result = TRUE;
        $errorFieldList = array();
        foreach ($fieldList as $_field) {
            if (!isset($params[$_field])) {
                $result = FALSE;
            } elseif (is_array($params[$_field])) {
                if (count($params[$_field]) == 0) {
                    $result = FALSE;
                }
            } else {
                if (strlen($params[$_field]) == 0) {
                    $result = FALSE;
                }
            }
            $errorFieldList[] = $_field;
        }
        if ($result === FALSE && count($errorFieldList) > 0) {
            \Raneko\Log::error($method, "Mandatory fields constraint violated: " . implode(", ", $errorFieldList));
        }
        return $result;
    }

    /**
     * Validate numeric fields, make sure field(s) in the list has numeric value.
     * @param string $method Method invoking the validation.
     * @param array $fieldList List of mandatory fields
     * @param array $params Parameters to be checked
     * @return boolean
     */
    public static function fieldListNumeric($method, $fieldList, $params) {
        $result = TRUE;
        $errorFieldList = array();
        foreach ($fieldList as $_field) {
            if (isset($params[$_field]) && $params[$_field] !== NULL && !is_numeric($params[$_field])) {
                $result = FALSE;
                $errorFieldList[] = "`{$_field}` ({$params[$_field]})";
            }
        }
        if ($result === FALSE && count($errorFieldList) > 0) {
            \Raneko\Log::error($method, "Numeric fields constraint violated: " . implode(", ", $errorFieldList));
        }
        return $result;
    }

    /**
     * Perform DNS lookup to an email address.
     * @param string $method
     * @param string $email
     * @return boolean
     */
    public static function emailLookup($method, $email) {
        $result = TRUE;

        if ($result) {
            $_elements = explode("@", $email, 2);
            if (!checkdnsrr(end($_elements), "MX")) {
                $result = FALSE;
                \Raneko\Log::error($method, "Domain lookup for '{$email}' failed");
            }
        }

        return $result;
    }

    /**
     * Check whether an email is structurally correct.
     * @param string $method
     * @param string $email
     * @return boolean
     */
    public static function email($method, $email) {
        $result = TRUE;

        if ($result && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Raneko\Log::error($method, "Not a valid email address '{$email}'");
            $result = FALSE;
        }

        return $result;
    }

    /**
     * Validate email fields, make sure field(s) in the list has email value.
     * @param string $method Method invoking the validation.
     * @param array $fieldList List of mandatory fields
     * @param array $params Parameters to be checked
     * @return boolean
     */
    public static function fieldListEmail($method, $fieldList, $params) {
        $result = TRUE;
        $errorFieldList = array();
        $errorFieldListDNS = array();
        foreach ($fieldList as $_field) {
            if (isset($params[$_field]) && $params[$_field] !== NULL) {
                if (!filter_var($params[$_field], FILTER_VALIDATE_EMAIL)) {
                    $result = FALSE;
                    $errorFieldList[] = "`{$_field}` ({$params[$_field]})";
                }
                if ($result !== FALSE) {
                    $_elements = explode("@", $params[$_field], 2);
                    if (!checkdnsrr(end($_elements), "MX")) {
                        $result = FALSE;
                        $errorFieldListDNS[] = "`{$_field}` ({$params[$_field]})";
                    }
                }
            }
        }
        if ($result === FALSE && count($errorFieldList) > 0) {
            \Raneko\Log::error($method, "Email fields constraint violated: " . implode(", ", $errorFieldList));
        }
        if ($result === FALSE && count($errorFieldListDNS) > 0) {
            \Raneko\Log::error($method, "DNS lookup failed for: " . implode(", ", $errorFieldListDNS));
        }
        return $result;
    }

}
