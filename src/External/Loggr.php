<?php

namespace Raneko\External;

use Raneko\External\Loggr\Events;

class Loggr
{

    /**
     * @var \Raneko\External\Loggr\Events
     */
    public $Events;

    function __construct($logKey, $apiKey)
    {
        $this->Events = new Events($logKey, $apiKey);
    }

    public function trapExceptions()
    {
        set_error_handler(array($this, "errorHandler"));
        set_exception_handler(array($this, "exceptionHandler"));
    }

    public function errorHandler($code, $message, $file, $line)
    {
        if ($code == E_STRICT && $this->reportESTRICT === false)
            return;

        ob_start();
        var_dump(debug_backtrace());
        $stack = str_replace("\n", "<br>", ob_get_clean());

        $data = "@html\r\n";
        $data .= "<b>MESSAGE:</b> " . $message . "<br>";
        $data .= "<b>FILE:</b> " . $file . ", " . $line . "<br>";
        $data .= "<b>CODE:</b> " . $code . "<br>";
        $data .= "<br><b>STACK TRACE:</b> " . $stack;

        $this->Events->Create()
                ->Text($message)
                ->Tags("error")
                ->Data($data)
                ->Post();
    }

    public function exceptionHandler($exception)
    {
        ob_start();
        var_dump($exception->getTrace());
        $stack = str_replace("\n", "<br>", ob_get_clean());

        $data = "@html\r\n";
        $data .= "<b>MESSAGE:</b> " . $exception->getMessage() . "<br>";
        $data .= "<b>FILE:</b> " . $exception->getFile() . ", " . $exception->getLine() . "<br>";
        $data .= "<b>CODE:</b> " . get_class($exception) . "<br>";
        $data .= "<br><b>STACK TRACE:</b> " . $stack;

        $this->Events->Create()
                ->Text($exception->getMessage())
                ->Tags("error exception")
                ->Data($data)
                ->Post();
    }
}
