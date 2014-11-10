<?php

namespace Raneko\Crypt;

/**
 * Cryptography class for AES.
 * @link http://aesencryption.net
 * @link http://www.chilkatsoft.com/p/php_aes.txt
 */
class AES
{

    private $config;

    /**
     * Define cipher to be used across the class
     */
    function __construct()
    {
        $this->config = array(
            "cipher" => MCRYPT_RIJNDAEL_128,
            "mode" => MCRYPT_MODE_CBC,
            "key" => NULL,
            "iv" => NULL,
            "iv_hex" => NULL,
            "iv_base64" => NULL,
            "iv_size" => 0
        );
        $cipher = mcrypt_module_open($this->config["cipher"], "", $this->config["mode"], "");
        $this->config["iv_size"] = mcrypt_enc_get_iv_size($cipher);
        mcrypt_module_close($cipher);
    }

    function getConfig()
    {
        return $this->config;
    }

    /**
     * Set private key to be used for encryption and decryption
     * @param string $key Key must be in length of 16, 32 or 64 bytes/characters
     */
    public function setKey($key)
    {
        if (in_array(strlen($key), array(16, 32, 64)))
        {
            $this->config["key"] = $key;
        }
        else
        {
            throw new \Exception("Key must be in size of 128 or 256 bit");
        }
    }

    /**
     * @return boolean
     */
    public function validateParams()
    {
        $result = TRUE;

        if ($result && !isset($this->config["key"]))
        {
            $result = FALSE;
            throw new \Exception("Private key is not set");
        }
        if ($result && (!isset($this->config["iv"]) || strlen($this->config["iv"]) != $this->config["iv_size"]))
        {
            $result = FALSE;
        }

        return $result;
    }

    /**
     * Set initialization vector value.
     * @param string $IV IV in binary string representation.
     * @throws \Exception
     */
    public function setIV($IV)
    {
        if (strlen($IV) == $this->config["iv_size"])
        {
            $this->config["iv"] = $IV;
            $this->config["iv_hex"] = bin2hex($IV);
            $this->config["iv_base64"] = base64_encode($IV);
        }
        else
        {
            throw new \Exception("Invalid IV size, expecting `{$this->config["iv_size"]}`");
        }
    }

    /**
     * Set initialization vector value.
     * @param string $IV IV in base64 representation.
     */
    public function setIVBase64($IV)
    {
        $decodedIV = base64_decode($IV);
        $this->setIV($decodedIV);
    }

    /**
     * Set initialization vector value.
     * @param string $IV IV in hexadecimal representation.
     */
    public function setIVHex($IV)
    {
        $decodedIV = pack("H*", $IV);
        $this->setIV($decodedIV);
    }

    /**
     * Get IV in binary string.
     * @return string
     */
    public function getIV()
    {
        return $this->config["iv"];
    }

    /**
     * Get IV in hexadecimal representation.
     * @return string
     */
    public function getIVHex()
    {
        return $this->config["iv_hex"];
    }

    /**
     * Get IV in base64 representation.
     * @return string
     */
    public function getIVBase64()
    {
        return $this->config["iv_base64"];
    }

    /**
     * IV generated is always 16-byte(128-bit) in size as per dictated by AES specification.
     * @return string Random IV with length according to the 
     */
    public function generateIV()
    {
        return openssl_random_pseudo_bytes($this->config["iv_size"]);
    }

    /**
     * Encrypt clear text using key and IV provided.
     * @param string $clearText
     * @return string Base64 encoded encrypted string
     */
    public function encrypt($clearText, $useRandomIV = FALSE)
    {
        if ($this->validateParams())
        {
            $_iv = $this->config["iv"];
            if ($useRandomIV)
            {
                $_iv = $this->generateIV();
            }
            $_encrypted = trim(base64_encode(mcrypt_encrypt($this->config["cipher"], $this->config["key"], $clearText, $this->config["mode"], $_iv)));
            return "aes:" . base64_encode($_iv) . ":" . $_encrypted;
        }
    }

    /**
     * Decrypt an encrypted string using key and IV provided.
     * @param string $encryptedText Base64 encoded encrypted string
     * @example "KpEwBebEv8nhGsMPNMbQ9A==:jfoWCmer+X0XvTM4qFYqxg==" IV:KpEwBebEv8nhGsMPNMbQ9A==, Message:jfoWCmer+X0XvTM4qFYqxg==
     * @example "KpEwBebEv8nhGsMPNMbQ9A==" Message:KpEwBebEv8nhGsMPNMbQ9A==, 
     * @return string Decrypted string
     */
    public function decrypt($encryptedText)
    {
        if ($this->validateParams())
        {
            $_elements = explode(":", $encryptedText);
            if (count($_elements) == 1)
            {
                return trim(mcrypt_decrypt($this->config["cipher"], $this->config["key"], base64_decode($encryptedText), $this->config["mode"], $this->config["iv"]));
            }
            else if (count($_elements) == 3)
            {
                return trim(mcrypt_decrypt($this->config["cipher"], $this->config["key"], base64_decode($_elements[2]), $this->config["mode"], base64_decode($_elements[1])));
            }
            else
            {
                throw new \Exception("Unknwown format '{$encryptedText}'");
            }
        }
    }

}
