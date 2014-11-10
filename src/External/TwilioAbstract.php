<?php

namespace Raneko\External;

use Raneko\Common\Result;

/**
 * @author Harry Lesmana
 */
abstract class TwilioAbstract
{

    private $apiAccountSID;
    private $apiAuthToken;
    private $apiModule;

    public function __construct()
    {
        
    }

    /**
     * Base method to set API module.
     * @param string $module Module to be handled by this class.
     */
    protected function _setModule($module)
    {
        $this->apiModule = $module;
    }

    public function setCredAccountSID($accountSID)
    {
        $this->apiAccountSID = $accountSID;
    }

    public function setCredAuthToken($authToken)
    {
        $this->apiAuthToken = $authToken;
    }

    /**
     * Invoke API call to Twilio service.
     * @param array $params Parameter to be passed to Twilio.
     * @return \Raneko\Common\Result Structure:
     * - `url`
     * - `params`
     * - `urlGET`
     * - `CURLError`
     * - `responseHeader`
     * - `responseBody`
     */
    public function _invoke($params)
    {
        $result = new Result();
        $proceed = TRUE;

        if ($proceed)
        {
            /* Check if AccountSID is set */
            if (!isset($this->apiAccountSID) || strlen($this->apiAccountSID) == 0)
            {
                $proceed = FALSE;
                \Raneko\Log::error(__METHOD__, "Account SID not set");
            }
            /* Check if Token is set */
            if (!isset($this->apiAuthToken) || strlen($this->apiAuthToken) == 0)
            {
                $proceed = FALSE;
                \Raneko\Log::error(__METHOD__, "Auth token not set");
            }
            /* Check if module is set */
            if (!isset($this->apiModule) || strlen($this->apiModule) == 0)
            {
                $proceed = FALSE;
                \Raneko\Log::error(__METHOD__, "API module not set");
            }
        }

        if ($proceed)
        {
            $_url = "https://api.twilio.com/2010-04-01/Accounts/{$this->apiAccountSID}/{$this->apiModule}.json";

            /* Prepare POST variable */
            $_params = $params;
            $_paramsString = http_build_query($_params);

            /* Initiate connection */
            $_ch = curl_init();

            curl_setopt($_ch, CURLOPT_URL, $_url);
            curl_setopt($_ch, CURLOPT_POST, count($params));
            curl_setopt($_ch, CURLOPT_POSTFIELDS, $_paramsString);
            curl_setopt($_ch, CURLOPT_USERPWD, "{$this->apiAccountSID}:{$this->apiAuthToken}");
            curl_setopt($_ch, CURLOPT_RETURNTRANSFER, 1); /* Don't echo result */
            curl_setopt($_ch, CURLOPT_VERBOSE, 1);
            curl_setopt($_ch, CURLOPT_HEADER, 1); /* Get header */
            curl_setopt($_ch, CURLOPT_SSL_VERIFYPEER, false); /* Ignore CA */

            /* Execute POST */
            $_result = curl_exec($_ch);

            $_response = array(
                "url" => $_url,
                "params" => $_params,
                "urlGET" => $_url . "?" . $_paramsString,
                "CURLError" => curl_error($_ch)
            );

            /* Check if CURL call successful */
            if ($_result !== FALSE)
            {
                $_headerSize = curl_getinfo($_ch, CURLINFO_HEADER_SIZE);

                $_response["responseHeader"] = substr($_result, 0, $_headerSize);
                $_response["responseBody"] = json_decode(substr($_result, $_headerSize), TRUE);

                if (isset($_response["responseBody"]["status"]) && is_numeric($_response["responseBody"]["status"]))
                {
                    $proceed = FALSE;
                    \Raneko\Log::error(__METHOD__, "HTTP {$_response["responseBody"]["status"]}: {$_response["responseBody"]["message"]}, CODE '{$_response["responseBody"]["code"]}' {$_response["responseBody"]["more_info"]}");
                }
                else
                {
                    $result->setResultSuccess();
                }
            }

            /* Close connection */
            curl_close($_ch);

            $result->setData($_response);
        }

        return $result;
    }
}
