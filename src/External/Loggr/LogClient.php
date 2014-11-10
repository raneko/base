<?php

namespace Raneko\External\Loggr;

class LogClient
{

    private $_logKey;
    private $_apiKey;

    function __construct($logKey, $apiKey)
    {
        $this->_logKey = $logKey;
        $this->_apiKey = $apiKey;
    }

    public function Post($event)
    {
        // format data
        $qs = $this->CreateQuerystring($event);
        $data = "apikey=" . $this->_apiKey . "&" . $qs;

        // write without waiting for a response
        $fp = fsockopen('post.loggr.net', 80, $errno, $errstr, 30);
        $out = "POST /1/logs/" . $this->_logKey . "/events HTTP/1.1\r\n";
        $out.= "Host: " . "post.loggr.net" . "\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: " . strlen($data) . "\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($data))
            $out.= $data;

        fwrite($fp, $out);
        fclose($fp);
    }

    public function CreateQuerystring($event)
    {
        $res = "";
        $res .= "text=" . urlencode($event->Text);
        if (isset($event->Source))
            $res .= "&source=" . urlencode($event->Source);
        if (isset($event->User))
            $res .= "&user=" . urlencode($event->User);
        if (isset($event->Link))
            $res .= "&link=" . urlencode($event->Link);
        if (isset($event->Value))
            $res .= "&value=" . urlencode($event->Value);
        if (isset($event->Tags))
            $res .= "&tags=" . urlencode($event->Tags);
        if (isset($event->Latitude) && isset($event->Longitude))
            $res .= "&geo=" . urlencode($event->Latitude) . "," . urlencode($event->Longitude);
        if (isset($event->Ip))
            $res .= "&geo=ip:" . urlencode($event->Ip);
        else
            $res .= "&geo=ip:" . urlencode(\Raneko\App::getIp());

        $data = "";
        if (isset($event->Data))
        {
            if ($event->DataType == DataType::html)
                $data = "@html\r\n" . urlencode($event->Data);
            else
                $data = urlencode($event->Data);
        }
        $res .= "&data={$data}";

        return $res;
    }

}

function backtrace()
{
    $output = "<div style='text-align: left; font-family: monospace;'>\n";
    $backtrace = debug_backtrace();

    $defaults = array(
        'class' => '',
        'type' => '',
        'function' => '',
        'line' => '',
        'file' => ''
    );

    foreach ($backtrace as $bt)
    {
        $args = '';
        foreach ($bt['args'] as $a)
        {
            if (!empty($args))
            {
                $args .= ', ';
            }
            switch (gettype($a))
            {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)) . ((strlen($a) > 64) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array(' . count($a) . ')';
                    break;
                case 'object':
                    $args .= 'Object(' . get_class($a) . ')';
                    break;
                case 'resource':
                    $args .= 'Resource(' . strstr($a, '#') . ')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
            }
        }


        $bt += $defaults;

        $output .= "<br />\n";
        $output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
        $output .= "<b>call:</b> {$bt['class']}{$bt['type']}{$bt['function']}($args)<br />\n";
    }
    $output .= "</div>\n";
    return $output;
}
