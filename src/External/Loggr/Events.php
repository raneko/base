<?php

namespace Raneko\External\Loggr;

use Raneko\External\Loggr\FluentEvent;
use Raneko\External\Loggr\DataType;

class Events
{

    private $_logKey;
    private $_apiKey;

    function __construct($logKey, $apiKey)
    {
        $this->_logKey = $logKey;
        $this->_apiKey = $apiKey;
    }

    /**
     * @return \Raneko\External\Loggr\FluentEvent
     */
    public function Create()
    {
        return new FluentEvent($this->_logKey, $this->_apiKey);
    }

    public function CreateFromException($exception)
    {
        ob_start();
        var_dump($exception->getTrace(), 5);
        $stack = str_replace("\t", "----", str_replace("\n", "<br>", ob_get_clean()));

        $data = "<b>MESSAGE:</b> " . $exception->getMessage() . "<br>";
        $data .= "<b>FILE:</b> " . $exception->getFile() . ", " . $exception->getLine() . "<br>";
        $data .= "<b>CODE:</b> " . get_class($exception) . "<br>";
        $data .= "<br><b>BACK TRACE:</b> " . backtrace();

        return $this->Create()
                        ->Text($exception->getMessage())
                        ->Tags("error " . get_class($exception))
                        ->Data($data)
                        ->DataType(DataType::html);
    }

    public function CreateFromVariable($var)
    {
        ob_start();
        var_dump($var);
        $trace = str_replace("\t", "----", str_replace("\n", "<br>", ob_get_clean()));

        $data = "<pre>" . $trace . "</pre>";

        return $this->Create()
                        ->Data($data)
                        ->DataType(DataType::html);
    }
}
