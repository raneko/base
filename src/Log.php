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
        self::$messageList = array();
        self::$posterList = array();        
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
    
    public function _baseLogMessage(\Raneko\Common\Message $message)
    {
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
     * 
     * @param string $method
     * @param string $code
     * @param type $type Log type from \Monolog\Logger
     * @param \Raneko\Common\Message $message
     * @param type $userMessage
     */
    protected function _baseLog($method, $code, $type, $message, $userMessage = null)
    {
        $message = new \Raneko\Common\Message();
        $message->Code($code);
        $message->Method($method);
        $message->Type($type);
        $message->AudienceApp();
        $message->Text($message);

        $this->_baseLogMessage($message);
    }

    public static function info($method, $message, $userMessage = NULL)
    {
        return self::codeInfo(self::DEFAULT_CODE, $method, $message, $userMessage);
    }

    public static function error($method, $message, $userMessage = NULL)
    {
        return self::codeError(self::DEFAULT_CODE, $method, $message, $userMessage);
    }

    public static function warn($method, $message, $userMessage = NULL)
    {
        return self::codeWarn(self::DEFAULT_CODE, $method, $message, $userMessage);
    }
    
    public static function debug($method, $message, $userMessage = NULL)
    {
        return self::codeDebug(self::DEFAULT_CODE, $method, $message, $userMessage);
    }    

    public static function critical($method, $message, $userMessage = NULL)
    {
        return self::codeCritical(self::DEFAULT_CODE, $method, $message, $userMessage);
    }
    
    public static function codeDebug($code, $method, $message, $userMessage = null)
    {
        return self::getInstance()->_baseLog($method, $code, \Monolog\Logger::DEBUG, $message, $userMessage);
    }
    
    public static function codeInfo($code, $method, $message, $userMessage = null)
    {
        return self::getInstance()->_baseLog($method, $code, \Monolog\Logger::INFO, $message, $userMessage);
    }
    
    public static function codeWarn($code, $method, $message, $userMessage = null)
    {
        return self::getInstance()->_baseLog($method, $code, \Monolog\Logger::WARNING, $message, $userMessage);
    }
    
    public static function codeError($code, $method, $message, $userMessage = null)
    {
        return self::getInstance()->_baseLog($method, $code, \Monolog\Logger::ERROR, $message, $userMessage);
    }
    
    public static function codeCritical($code, $method, $message, $userMessage = null)
    {   
        return self::getInstance()->_baseLog($method, $code, \Monolog\Logger::CRITICAL, $message, $userMessage);
    }
    
}
