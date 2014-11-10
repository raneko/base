<?php

namespace Raneko\Common;

class Result
{

    const CODE_SUCCESS = "0";
    const CODE_FAILED = "F";

    /**
     * Response code
     * @var string
     */
    private $code;

    /**
     * Indicates whether response is success.
     * @var boolean
     */
    private $isSuccess;

    /**
     * Header data which is relevant to the request but not to the dataset.
     * @var array
     */
    private $headerList;

    /**
     * Information relevant to the dataset but not the dataset itself.
     * e.g. Number of record found, number of page, etc.
     * @var array
     */
    private $info;

    /**
     * Dataset
     * @var array
     */
    private $data;

    /**
     * List of message.
     * @var \Raneko\Common\Message
     */
    private $messageList;

    /**
     * List of debug message.
     * @var array
     */
    private $debugList;

    /**
     * Create new message.
     * @return \Raneko\Common\Message
     */
    public function createMessage()
    {
        return new \Raneko\Common\Message();
    }

    public function __construct()
    {
        $this->code = self::CODE_FAILED;
        $this->isSuccess = FALSE;
        $this->data = array();
        $this->messageList = array();
        $this->debugList = array();
        $this->info = array();
        $this->headerList = array();
    }

    /**
     * Add key value pair to header.
     * @param string $key
     * @param mixed $value
     */
    public function addHeader($key, $value)
    {
        $this->headerList[$key] = $value;
    }

    /**
     * Get list of header.
     * @return array
     */
    public function getHeaderList()
    {
        return $this->headerList;
    }

    /**
     * Set result code.
     * Avoid using this method and use setResultSuccess() or setResultFailed().
     * @param string $code
     * @param boolean $isSuccess Indicates whether result is success or failed.
     */
    public function setResult($code, $isSuccess = FALSE)
    {
        $this->code = $code;
        $this->isSuccess = $isSuccess;
    }

    /**
     * Set result as success.
     * By default if will use default success code.
     * @param string $code
     */
    public function setResultSuccess($code = self::CODE_SUCCESS)
    {
        $this->setResult($code, TRUE);
    }

    /**
     * Set result as failed.
     * Code for success is not allowed to be used.
     * @param string $code
     */
    public function setResultFailed($code = self::CODE_FAILED)
    {
        $this->setResult($code, FALSE);
    }

    public function isResultSuccess()
    {
        return $this->isSuccess;
    }

    public function isResultFailed()
    {
        return !$this->isResultSuccess();
    }

    public function setInfo($info = array())
    {
        $this->info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get dataset
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get result code.
     * @return string
     */
    public function getResultCode()
    {
        return $this->code;
    }

    /**
     * Add message.
     * @param \Raneko\Common\Message $message
     */
    public function addMessage($message)
    {
        $this->messageList[] = $message;
    }

    /**
     * Get list of message stored by this result object.
     * @return \Raneko\Common\Message Array of message object
     */
    public function getMessageList()
    {
        return $this->messageList;
    }

    /**
     * Add debug message.
     * @param string $message
     */
    public function addDebug($message)
    {
        $this->debugList[] = $message;
    }

    /**
     * Get list of debug message added.
     * @return array Array of string
     */
    public function getDebugList()
    {
        return $this->debugList;
    }

    /**
     * Remove header.
     * @param string $key Key to be removed. NULL to remove all header.
     */
    public function removeHeader($key = NULL)
    {
        if ($key === NULL)
        {
            $this->headerList = array();
        }
        else
        {
            unset($this->headerList[$key]);
        }
    }

}
