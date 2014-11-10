<?php

namespace Raneko\Common;

/**
 * Standardized structure for log message.
 * All message kind of data should be based on this class.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-02
 */
class Message implements \Raneko\Common\InterfaceSelfInstantiate
{

    /**
     * Code of the message
     * @var string
     */
    private $code;

    /**
     * Text is the main element of message.
     * @var string
     */
    private $text;

    /**
     * Type of the message.
     * @var string
     */
    private $type;

    /**
     * Multiple tagList are separated by comma.
     * @var string
     */
    private $tagList;

    /**
     * Audience of the message.
     * @var array
     */
    private $audienceList;

    /**
     * Data to be conveyed.
     * This property might not be necessarily supported by the logger service.
     * @var array
     */
    private $dataList;

    /**
     * User involved in log.
     * @var string
     */
    private $user;

    /**
     * URL containing further information regarding the message.
     * @var string
     */
    private $urlInfo;

    /**
     * Method related to the message.
     * @var string 
     */
    private $method;

    const AUDIENCE_APP = "app";
    const AUDIENCE_USER = "user";
    const AUDIENCE_ALL = "all";
    const TYPE_ERROR = "error";
    const TYPE_WARNING = "warning";
    const TYPE_CRITICAL = "critical";
    const TYPE_INFO = "info";
    const TYPE_DEBUG = "debug";

    public function __construct()
    {
        $this->audienceList = array();
        $this->tagList = array();
        $this->dataList = array();
        $this->TypeDebug();
    }

    /**
     * Set method associated to this message.
     * @param string $method
     * @return \Raneko\Common\Message
     */
    public function Method($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get method associated with this message.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set URL info for this message.
     * @param string $url
     * @return \Raneko\Common\Message
     */
    public function URLInfo($url)
    {
        $this->urlInfo = $url;
        return $this;
    }

    /**
     * Get registered URL info.
     * @return string
     */
    public function getURLInfo()
    {
        return $this->urlInfo;
    }

    /**
     * Set type of the message.
     * @param string $type
     * @return \Raneko\Common\Message
     */
    private function setType($type)
    {
        $allowedTypeList = array(
            self::TYPE_DEBUG,
            self::TYPE_INFO,
            self::TYPE_WARNING,
            self::TYPE_ERROR,
            self::TYPE_CRITICAL
        );
        if (in_array($type, $allowedTypeList))
        {
            $this->type = $type;
            return $this;
        }
        else
        {
            throw new \Exception("Type not allowed '{$type}'");
        }
    }

    /**
     * @return \Raneko\Common\Message
     */
    public function TypeDebug()
    {
        return $this->setType(self::TYPE_DEBUG);
    }

    /**
     * @return \Raneko\Common\Message
     */
    public function TypeInfo()
    {
        return $this->setType(self::TYPE_INFO);
    }

    /**
     * @return \Raneko\Common\Message
     */
    public function TypeError()
    {
        return $this->setType(self::TYPE_ERROR);
    }

    /**
     * @return \Raneko\Common\Message
     */
    public function TypeWarning()
    {
        return $this->setType(self::TYPE_WARNING);
    }

    /**
     * @param string $type
     * @return \Raneko\Common\message
     */
    public function Type($type)
    {
        return $this->setType($type);
    }

    /**
     * @return \Raneko\Common\Message
     */
    public function TypeCritical()
    {
        return $this->setType(self::TYPE_CRITICAL);
    }

    /**
     * Get text.
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get tags.
     * @param boolean $asString Indicates whether result should be given in string instead of array.
     * @return array|string
     */
    public function getTags($asString = FALSE)
    {
        $result = $this->tagList;
        if (!in_array($this->getType(), $result))
        {
            $result[] = $this->getType();
        }
        return $asString ? implode(", ", $result) : $result;
    }

    /**
     * Get list of audience of the message.
     * @return array|string
     */
    public function getAudiences($asString = FALSE)
    {
        return $asString ? implode(", ", $this->audienceList) : $this->audienceList;
    }

    private function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Alias to setText().
     * @param string $text
     * @return \Raneko\Common\Message
     */
    public function Text($text)
    {
        return $this->setText($text);
    }

    /**
     * Set user who is involved in this message.
     * @param string $user
     * @return \Raneko\Common\Message
     */
    public function User($user)
    {
        $user = trim($user);
        $this->user = $user;
        return $this;
    }

    /**
     * Set code of the message
     * @param string $code
     * @return \Raneko\Common\Message
     */
    public function Code($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code associated with this message.
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get user associated with this message.
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Alias to addTags().
     * Type of message will by default be included as tag.
     * @param string $tags Tag, multiple value is separated by comma.
     * @return \Raneko\Common\Message
     */
    public function Tags($tags)
    {
        return $this->addTags($tags);
    }

    /**
     * Add data to the message.
     * @param mixed $data
     * @return \Raneko\Common\Message
     */
    public function Data($data)
    {
        return $this->addData($data);
    }

    /**
     * Add tags to the message.
     * This method will filter for duplicate tags.
     * @param array|string $tagList Value can be in array, string or multiple tags in string separated by comma.
     * @return \Raneko\Common\Message
     */
    private function addTags($tagList)
    {
        if (is_array($tagList) && !empty($tagList))
        {
            foreach ($tagList as $_tag)
            {
                $_tag = trim($_tag);
                if (strlen($_tag) > 0 && !in_array($_tag, $this->tagList))
                {
                    $this->tagList[] = $_tag;
                }
            }
        }
        else
        {
            $_elements = explode(",", $tagList);
            $this->addTags($_elements);
        }
        return $this;
    }

    /**
     * Add application as message audience.
     * @return \Raneko\Common\Message
     */
    public function AudienceApp()
    {
        return $this->addAudience(self::AUDIENCE_APP);
    }

    /**
     * Add everyone as message audience.
     * @return \Raneko\Common\Message
     */
    public function AudienceAll()
    {
        return $this->addAudience(self::AUDIENCE_ALL);
    }

    /**
     * Add user as message audience.
     * @return \Raneko\Common\Message
     */
    public function AudienceUser()
    {
        return $this->addAudience(self::AUDIENCE_USER);
    }

    /**
     * Set audience.
     * @param mixed $audience Value can be given in:
     * - array e.g. array("app", "user")
     * - single value (string) e.g. "app"
     * - multiple value separated by comma (string) e.g. "app, user"
     * @return \Raneko\Common\Message
     */
    public function Audience($audience)
    {
        if (!is_array($audience))
        {
            $audience = explode(",", $audience);
        }
        foreach ($audience as $_audience)
        {
            $this->addAudience($_audience);
        }
        return $this;
    }

    /**
     * Add message audience.
     * @param string $audience
     * @return \Raneko\Common\Message
     */
    private function addAudience($audience)
    {
        $audience = trim($audience);
        if (!in_array($audience, $this->audienceList))
        {
            $this->audienceList[] = $audience;
        }
        return $this;
    }

    /**
     * Create new message object.
     * @return \Raneko\Common\Message;
     */
    public static function create()
    {
        return new self;
    }

    /**
     * Add new data to be conveyed.
     * @param mixed $data
     * @return \Raneko\Common\Message
     */
    private function addData($data)
    {
        $this->dataList[] = $data;
        return $this;
    }

    /**
     * Get data.
     * @param boolean $asString Indicates whether value should be returned as string or as array.
     * @return array|string
     */
    public function getData($asString = FALSE)
    {
        return $asString ? implode(PHP_EOL . PHP_EOL, $this->dataList) : $this->dataList;
    }

    /**
     * Get message type.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
