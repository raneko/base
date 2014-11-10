<?php

namespace Raneko\External\Loggr;

use Raneko\External\Loggr\LogClient;
use Raneko\External\Loggr\Event;

class FluentEvent
{

    public $Event;
    private $_logKey;
    private $_apiKey;

    function __construct($logKey, $apiKey)
    {
        $this->_logKey = $logKey;
        $this->_apiKey = $apiKey;
        $this->Event = new Event();
    }

    public function Post()
    {
        $client = new LogClient($this->_logKey, $this->_apiKey);
        $client->Post($this->Event);
        return $this;
    }

    public function Text($text)
    {
        $this->Event->Text = $this->AssignWithMacro($text, $this->Event->Text);
        return $this;
    }

    public function TextF()
    {
        $args = func_get_args();
        return $this->Text(vsprintf(array_shift($args), array_values($args)));
    }

    public function AddText($text)
    {
        $this->Event->Text .= $this->AssignWithMacro($text, $this->Event->Text);
        return $this;
    }

    public function AddTextF()
    {
        $args = func_get_args();
        return $this->AddText(vsprintf(array_shift($args), array_values($args)));
    }

    public function Source($source)
    {
        $this->Event->Source = $this->AssignWithMacro($source, $this->Event->Source);
        return $this;
    }

    public function SourceF()
    {
        $args = func_get_args();
        return $this->Source(vsprintf(array_shift($args), array_values($args)));
    }

    public function User($user)
    {
        $this->Event->User = $this->AssignWithMacro($user, $this->Event->User);
        return $this;
    }

    public function UserF()
    {
        $args = func_get_args();
        return $this->User(vsprintf(array_shift($args), array_values($args)));
    }

    public function Link($link)
    {
        $this->Event->Link = $this->AssignWithMacro($link, $this->Event->Link);
        return $this;
    }

    public function LinkF()
    {
        $args = func_get_args();
        return $this->Link(vsprintf(array_shift($args), array_values($args)));
    }

    public function Data($data)
    {
        $this->Event->Data = $this->AssignWithMacro($data, $this->Event->Data);
        return $this;
    }

    public function DataF()
    {
        $args = func_get_args();
        return $this->Data(vsprintf(array_shift($args), array_values($args)));
    }

    public function AddData($data)
    {
        $this->Event->Data .= $this->AssignWithMacro($data, $this->Event->Data);
        return $this;
    }

    public function AddDataF()
    {
        $args = func_get_args();
        return $this->AddData(vsprintf(array_shift($args), array_values($args)));
    }

    public function Value($value)
    {
        $this->Event->Value = $value;
        return $this;
    }

    public function ValueClear()
    {
        $this->Event->Value = "";
        return $this;
    }

    public function Tags($tags)
    {
        $this->Event->Tags = $tags;
        return $this;
    }

    public function AddTags($tags)
    {
        $this->Event->Tags .= " " . $tags;
        return $this;
    }

    public function Geo($lat, $lon)
    {
        $this->Event->Latitude = $lat;
        $this->Event->Longitude = $lon;
        return $this;
    }

    public function GeoFromIp($ip)
    {
        $this->Event->Ip = $ip;
        return $this;
    }

    public function DataType($datatype)
    {
        $this->Event->DataType = $datatype;
        return $this;
    }

    private function AssignWithMacro($input, $baseStr)
    {
        return str_replace("$$", $baseStr, $input);
    }
}
