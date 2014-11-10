<?php

namespace Raneko\External;

/**
 * Abstract class for SendGrid marketing email functionalities
 * @author harrylesmana@singpost.com <harrylesmana@singpost.com>
 * @since 2014-03-10
 */
class SendGrid
{

    /**
     * API user to invoke service.
     * @var string
     */
    private $apiUser;

    /**
     * API key to invoke service.
     * @var string
     */
    private $apiKey;
    private $apiBaseURL = "https://api.sendgrid.com/api/newsletter/";

    /**
     * Last service invoked.
     * @var \Raneko\Common\Result
     */
    private $lastInvoke = NULL;

    const OUTPUT_JSON = "json";
    const OUTPUT_XML = "xml";

    public function __construct($apiUser, $apiKey)
    {
        $this->apiUser = $apiUser;
        $this->apiKey = $apiKey;
    }

    /**
     * Check user subscription to a list.
     * @param string $list List to be checked.
     * @param string $email Email to be checked.
     * @return boolean
     */
    public function checkSubscription($list, $email)
    {
        $data = array(
            "list" => $list,
            "email" => trim(strtolower($email))
        );

        $response = $this->_invoke("lists/email/get", $data);
        $responseData = $response->getData();

        if (empty($responseData) || $responseData == NULL)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    /**
     * Subscribe user to a list.
     * @param string $list
     * @param string $email
     * @param string $name
     * @param array $params
     * @return boolean
     */
    public function subscribe($list, $email, $name, $params = array())
    {
        $dataAttribute = $params;
        $dataAttribute["email"] = trim(strtolower($email));
        $dataAttribute["name"] = $name;
        $data = array(
            "list" => $list,
            "data" => json_encode($dataAttribute)
        );

        $response = $this->_invoke("lists/email/add", $data);

        return TRUE;
    }

    /**
     * Unsubscribe user from a list.
     * @param string $list
     * @param string $email
     * @return boolean
     */
    public function unsubscribe($list, $email)
    {
        $data = array(
            "list" => $list,
            "email" => trim(strtolower($email))
        );

        $response = $this->_invoke("lists/email/delete", $data);

        return TRUE;
    }

    /**
     * Invoke SendGrid service.
     * @param string $action
     * @param array $params
     * @param string $format Only accepts either 'json' or 'xml'
     * @return \Raneko\Common\Result
     */
    protected function _invoke($action, $params = array(), $format = self::OUTPUT_JSON)
    {
        $result = new \Raneko\Common\Result();
        $proceed = TRUE;

        if ($proceed && strlen($this->apiUser) == 0)
        {
            throw new Exception("API user is not set");
        }

        if ($proceed && strlen($this->apiKey) == 0)
        {
            throw new Exception("API key is not set");
        }

        if ($proceed && (strlen($action) == 0 || !in_array($format, array(self::OUTPUT_JSON, self::OUTPUT_XML))))
        {
            throw new Exception("Format must be `json` or `xml`");
        }

        /* Send POST data */
        if ($proceed)
        {
            /* Form the complete URL */
            $_url = $this->apiBaseURL . "{$action}.{$format}";

            /* Prepare POST variable */
            $_params = $params;
            $_params["api_user"] = $this->apiUser;
            $_params["api_key"] = $this->apiKey;
            $_paramsString = http_build_query($_params);

            /* Initiate connection */
            $_ch = curl_init();

            curl_setopt($_ch, CURLOPT_URL, $_url);
            curl_setopt($_ch, CURLOPT_POST, count($params));
            curl_setopt($_ch, CURLOPT_POSTFIELDS, $_paramsString);
            curl_setopt($_ch, CURLOPT_RETURNTRANSFER, 1); /* Don't echo result */
            curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, false); /* Ignore CA */

            /* Execute post */
            $_curlResponse = curl_exec($_ch);

            $_resultInfo = array(
                "url" => $_url,
                "params" => $_params,
                "urlGET" => $_url . "?" . $_paramsString,
                "cURLError" => curl_error($_ch),
            );

            if ($_curlResponse !== FALSE)
            {
                $result->setResultSuccess();
                $_jsonDecoded = json_decode($_curlResponse, TRUE);
                if ($_jsonDecoded !== FALSE)
                {
                    $result->setData($_jsonDecoded);
                }
            }

            $result->setInfo($_resultInfo);

            /* Close connection */
            curl_close($_ch);
        }

        $this->lastInvoke = $result;

        return $result;
    }

    /**
     * Get last invoked service.
     * @return \Raneko\Common\Result
     */
    public function getLastInvoke()
    {
        return $this->lastInvoke;
    }

}
