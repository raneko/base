<?php

namespace Raneko\External;

/**
 * Interface class to loggly service.
 * When using loggly, try to leverage the use of tags for categorization.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-20
 */
class Loggly
{

    /**
     * Token to connect to loggly.
     * @var string
     */
    private $token;

    /**
     * Base URL of loggly.
     * @var string
     */
    private $url = "http://logs-01.loggly.com";

    /**
     * @param string $token Token to be used to call the service.
     */
    public function __construct($token)
    {
        $this->setToken($token);
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set token to be used to call the service.
     * @param string $token
     * @return \Raneko\External\Loggly
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Set URL to be used to call the service.
     * @param string $url URL e.g. http://logs-01.loggly.com
     * @return \Raneko\External\Loggly
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Post message to loggly.
     * @param array $message Message to be posted to loggly.
     * @param array $tags Tags to be included.
     * @return \Raneko\Common\Result
     */
    public function post(array $message, array $tags = array())
    {
        $result = new \Raneko\Common\Result();
        $proceed = TRUE;

        if ($proceed && count($message) == 0)
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Message is empty");
        }

        if ($proceed)
        {
            $_url = "{$this->url}/inputs/{$this->token}";
            if (count($tags) > 0)
            {
                $_url .= "/tag/" . implode(",", $tags);
            }
            $_body = json_encode($message);
            
            $result = $this->_post($_url, $_body);
        }

        return $result;
    }

    /**
     * Post data to loggly.
     * This is the underlying method to invoke the service.
     * @param string $url
     * @param string $body Body to be sent.
     * @return \Raneko\Common\Result
     */
    protected function _post($url, $body)
    {
        $result = new \Raneko\Common\Result();
        $proceed = TRUE;

        if ($proceed && !isset($this->token))
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Token is not set");
        }

        if ($proceed && strlen($this->url) == 0)
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Base URL is not set");
        }

        if ($proceed && strlen($url) == 0)
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "URL is not set");
        }

        if ($proceed && strlen($body) == 0)
        {
            $proceed = FALSE;
            \Raneko\Log::error(__METHOD__, "Body is not set");
        }

        if ($proceed)
        {
            /* Initiate connection */
            $_ch = curl_init();

            curl_setopt($_ch, CURLOPT_URL, $url);
            curl_setopt($_ch, CURLOPT_POST, 1);
            curl_setopt($_ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($_ch, CURLOPT_RETURNTRANSFER, 1); /* Don"t echo result */
            curl_setopt($_ch, CURLOPT_VERBOSE, 1);
            curl_setopt($_ch, CURLOPT_HEADER, 1); /* Get header */
            curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, false); /* Ignore CA */
            curl_setopt($_ch, CURLOPT_HTTPHEADER, array("Content-Type: text/plain"));

            /* Execute POST */
            $_result = curl_exec($_ch);

            $_info = array(
                "url" => $url,
                "requestBody" => $body,
                "CURLError" => curl_error($_ch)
            );
            $_response = NULL;

            /* Check if CURL call successful */
            if ($_result !== FALSE)
            {
                $_headerSize = curl_getinfo($_ch, CURLINFO_HEADER_SIZE);

                $_info["responseHeader"] = substr($_result, 0, $_headerSize);
                $_response = json_decode(substr($_result, $_headerSize), TRUE);

                if (isset($_response["response"]) && $_response["response"] == "ok")
                {
                    $result->setResultSuccess();
                }
                else
                {
                    $proceed = FALSE;
                }
            }

            $result->setInfo($_info);
            $result->setData($_response);

            /* Close connection */
            curl_close($_ch);
        }

        return $result;
    }

}
