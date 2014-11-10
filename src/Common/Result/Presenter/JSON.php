<?php

namespace Raneko\Common\Result\Presenter;

/**
 * Class to present result as JSON.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-16
 */
class JSON extends \Raneko\Common\Result\Presenter\PresenterAbstract
{

    /**
     * @param \Raneko\Common\Result $result
     * @return string
     */
    protected function _present($result)
    {
        $data = array(
            "code" => $result->getResultCode(),
            "header" => $result->getHeaderList(),
            "message" => array(),
            "info" => $result->getInfo(),
            "data" => $result->getData()
        );

        $messageListResult = $result->getMessageList();
        if (is_array($messageListResult))
        {
            foreach ($messageListResult as $_message)
            {
                $_data = array(
                    "text" => $_message->getText(),
                    "type" => $_message->getType(),
                    "url" => $_message->getURLInfo(),
                    "audience" => $_message->getAudiences(TRUE)
                );
                $data["message"][] = $_data;
            }
        }

        $messageListLog = \Raneko\Log::getMessageList();
        if (is_array($messageListLog))
        {
            foreach ($messageListLog as $_message)
            {
                $_data = array("text" => $_message->getText(),
                    "type" => $_message->getType(),
                    "url" => $_message->getURLInfo(),
                    "audience" => $_message->getAudiences(TRUE)
                );
                $data["message"][] = $_data;
            }
        }

        if (APPLICATION_ENV !== "production")
        {
            $data["debug"] = $result->getDebugList();
        }
        return json_encode($data);
    }

    /**
     * Decode response string.
     * @param string $response
     * @return array
     * @throws \Exception
     */
    protected function _decodeToArray($response)
    {
        $result = json_decode($response, TRUE);
        if (!is_null($result))
        {
            return $result;
        }
        else
        {
            throw new \Exception("Failed to decode response" . PHP_EOL . $response);
        }
    }

    protected function _decodeToResult($response)
    {
        $result = new \Raneko\Common\Result();

        $responseArray = $this->_decodeToArray($response);
        if ($responseArray["code"] == "0")
        {
            $result->setResultSuccess();
        }
        else
        {
            $result->setResultFailed($responseArray["code"]);
        }
        $result->setData($responseArray["data"]);
        $result->setInfo($responseArray["info"]);
        foreach ($responseArray["header"] as $_key => $_value)
        {
            $result->addHeader($_key, $_value);
        }
        foreach ($responseArray["message"] as $_message)
        {
            $_commonMessage = \Raneko\Common\Message::create();
            $_commonMessage->Text($_message["text"]);
            $_commonMessage->URLInfo($_message["url"]);
            $_commonMessage->Type($_message["type"]);
            $_commonMessage->Audience($_message["audience"]);
            switch ($_message["audience"])
            {
                case "app":
                    $_commonMessage->AudienceApp();
                    break;
                case "user":
                    $_commonMessage->AudienceUser();
                    break;
                case "all":
                    $_commonMessage->AudienceAll();
                    break;
            }
            $result->addMessage($_commonMessage);
        }

        return $result;
    }

}
