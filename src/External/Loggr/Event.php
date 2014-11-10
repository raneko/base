<?php

namespace Raneko\External\Loggr;

use \Raneko\External\Loggr\DataType;

class Event
{

    public $Text;
    public $Source;
    public $User;
    public $Link;
    public $Data;
    public $Value;
    public $Tags;
    public $Latitude;
    public $Longitude;
    public $Ip;
    public $DataType = DataType::plaintext;

}
