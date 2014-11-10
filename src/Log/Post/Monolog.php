<?php

namespace Raneko\Log\Post;

/**
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-02
 */
class Monolog extends \Raneko\Log\Post\PostAbstract
{

    /**
     * Logger object for Monolog.
     * @var \Monolog\Logger
     */
    private $monolog;

    /**
     * Type conversion to Monolog type.
     * @var array
     */
    private $conversionList;

    /**
     * Construct post object for Monolog.
     * @param string $logfile Log file location.
     */
    public function __construct($logfile)
    {
        $this->monolog = new \Monolog\Logger("APP");
        $handler = new \Monolog\Handler\StreamHandler($logfile, \Monolog\Logger::DEBUG);
        $this->monolog->pushHandler($handler);

        $this->initConversion();
    }

    /**
     * Initialize type conversion list from \Raneko\Common\Message to \Monolog\Logger.
     */
    private function initConversion()
    {
        $this->conversionList = array(
            \Raneko\Common\Message::TYPE_DEBUG => \Monolog\Logger::DEBUG,
            \Raneko\Common\Message::TYPE_INFO => \Monolog\Logger::INFO,
            \Raneko\Common\Message::TYPE_WARNING => \Monolog\Logger::WARNING,
            \Raneko\Common\Message::TYPE_ERROR => \Monolog\Logger::ERROR,
            \Raneko\Common\Message::TYPE_CRITICAL => \Monolog\Logger::CRITICAL
        );
    }

    protected function _post(\Raneko\Common\Message $message)
    {
        $type = $this->getMonologType($message->getType());
        $text = $message->getText();

        /* Prepare message context */
        $context = array(
            "code" => $message->getCode(),
            "method" => $message->getMethod()
        );
        foreach ($context as $_key => $_value)
        {
            if (strlen($_value) == 0)
            {
                unset($context[$_key]);
            }
        }
        
        $this->monolog->log($type, $text, $context);
    }

    /**
     * Get type in Monolog standard.
     * @param string $type
     * @return string
     */
    private function getMonologType($type)
    {
        if (isset($this->conversionList[$type]))
        {
            return $this->conversionList[$type];
        }
        else
        {
            return \Monolog\Logger::DEBUG;
        }
    }
}
