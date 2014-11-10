<?php

namespace Raneko;

/**
 * @author Harry
 * @since 20131213
 */
class Log
{

    const DEFAULT_CODE = "0";

    /**
     * @var \Raneko\Log
     */
    private static $instance;

    /**
     * Template list.
     * @var array
     */
    private static $logTemplateList;

    /**
     * List of message.
     * @var Raneko\Common\Message
     */
    private static $messageList;

    /**
     * List of poster.
     * Poster is object to push message to log repository.
     * @var \Raneko\Log\Post\PostAbstract
     */
    private static $posterList;

    private function __construct()
    {
        $this->loadLogTemplate();
        self::$messageList = array();
        self::$posterList = array();
    }

    private function loadLogTemplate()
    {
        self::$logTemplateList = array();

        $template = new \Raneko\Log\Template();
        $template->setsystemMessageTemplate("{sys_message}");
        $template->setuserMessageTemplate("{usr_message}");
        $template->setuserInstructionTemplate("{usr_instruction}");
        self::$logTemplateList[self::DEFAULT_CODE] = $template;
    }

    /**
     * Get log template.
     * @param string $code
     * @return \Raneko\Log\Template
     */
    private function getLogTemplate($code)
    {
        $_code = trim(strtolower($code));
        if (isset(self::$logTemplateList[$_code]))
        {
            return self::$logTemplateList[$_code];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Get list of message.
     * @return \Raneko\Common\Message Array of message.
     */
    public static function getMessageList()
    {
        return self::$messageList;
    }

    /**
     * @return \Raneko\Log
     */
    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Add poster for the log.
     * @param string $label Poster label.
     * @param \Raneko\Log\Post\PostAbstract $poster
     */
    public static function addPoster($label, \Raneko\Log\Post\PostAbstract $poster)
    {
        self::getInstance()->_addPoster($label, $poster);
    }

    /**
     * Get poster.
     * @param string $label
     * @return \Raneko\Log\Post\PostAbstract
     */
    public static function getPoster($label)
    {
        return self::getInstance()->_getPoster($label);
    }

    /**
     * Add poster for the log.
     * @param string $label Poster label.
     * @param \Raneko\Log\Post\PostAbstract $poster
     */
    protected function _addPoster($label, \Raneko\Log\Post\PostAbstract $poster)
    {
        if (!array_key_exists("label", self::$posterList))
        {
            $poster->setEnabled(TRUE);
            self::$posterList[$label] = $poster;
        }
    }

    /**
     * Get poster object.
     * @param string $label
     * @return \Raneko\Log\Post\PostAbstract
     */
    protected function _getPoster($label)
    {
        if (array_key_exists($label, self::$posterList))
        {
            return self::$posterList[$label];
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Add message to the list.
     * @param \Raneko\Common\Message $message
     */
    protected function _addMessage(\Raneko\Common\Message $message)
    {
        self::$messageList[] = $message;
    }

    public function baseLog($method, $code, $type, $data = array())
    {
        $message = new \Raneko\Common\Message();
        $message->Code($code);
        $message->Method($method);
        $message->Type($type);
        $message->AudienceApp();

        $template = $this->getLogTemplate($code);
        if ($template !== NULL)
        {
            /* Use the template to form log message */
            $template->setData($data);
            $message->Text($template->getSystemMessage());
        }
        else
        {
            $data["code_error"] = "Code not found";
            $message->Text(json_encode($data));
        }

        $this->_addMessage($message);

        foreach (self::$posterList as $_poster)
        {
            if ($_poster->isEnabled())
            {
                $_poster->post($message);
            }
        }
    }

    /**
     * Info message based on code.
     * @param string $method
     * @param string $code
     * @param array $data
     */
    public static function infoCode($method, $code, $data = array())
    {
        self::getInstance()->baseLog($method, $code, \Raneko\Common\Message::TYPE_INFO, $data);
    }

    /**
     * Error message based on code.
     * @param string $method
     * @param string $code
     * @param array $data
     */
    public static function errorCode($method, $code, $data = array())
    {
        self::getInstance()->baseLog($method, $code, \Raneko\Common\Message::TYPE_ERROR, $data);
    }
    
    /**
     * Warning message based on code.
     * @param string $method
     * @param string $code
     * @param array $data
     */
    public static function warnCode($method, $code, $data = array())
    {
        self::getInstance()->baseLog($method, $code, \Raneko\Common\Message::TYPE_WARNING, $data);
    }

    public static function info($method, $systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        return self::infoCode($method, self::DEFAULT_CODE, self::getDefaultData($systemMessage, $userMessage, $userInstruction));
    }

    public static function error($method, $systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        return self::errorCode($method, self::DEFAULT_CODE, self::getDefaultData($systemMessage, $userMessage, $userInstruction));
    }

    public static function warn($method, $systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        return self::warnCode($method, self::DEFAULT_CODE, self::getDefaultData($systemMessage, $userMessage, $userInstruction));
    }
    
    public static function debug($method, $systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        return self::errorCode($method, self::DEFAULT_CODE, self::getDefaultData($systemMessage, $userMessage, $userInstruction));
    }    

    private static function getDefaultData($systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        $result = array(
            "sys_message" => $systemMessage,
            "usr_message" => TRUE === $userMessage ? $systemMessage : $userMessage,
            "usr_instrucion" => $userInstruction
        );
        return $result;
    }

    public static function critical($method, $systemMessage, $userMessage = NULL, $userInstruction = NULL)
    {
        return self::criticalCode($method, self::DEFAULT_CODE, self::getDefaultData($systemMessage, $userMessage, $userInstruction));
    }

    /**
     * Critical message based on code.
     * @param string $method
     * @param string $code
     * @param array $data
     */
    public static function criticalCode($method, $code, $data = array())
    {
        self::getInstance()->baseLog($method, $code, \Raneko\Common\Message::TYPE_CRITICAL, $data);
    }
}
