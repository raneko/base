<?php

namespace Raneko\UID;

/**
 * Class to generate GUID.
 * @author Harry <harry@raneko.com>
 * @since 2014-01-02
 * @link http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid Implementation of answer by Jack
 * @link http://stackoverflow.com/questions/1705008/simple-proof-that-guid-is-not-unique
 */
class GUID4 extends \Raneko\UID\UIDAbstract
{

    /**
     * Generate UUID version 4.
     * @return string New UUID in uppercase.
     * @since 20140102
     * @author Harry
     */
    protected function _generate()
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); /* set version to 0010 */
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); /* set bits 6-7 to 10 */

        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

}
