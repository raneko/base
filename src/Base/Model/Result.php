<?php

namespace Raneko\Base\Model;

/**
 * Model for result.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2016-05-03
 */
class Result {

    /**
     * Result code.
     * Code might indicate whether a result is success or failed, but `isSuccess` is to be used as main indicator.
     * @var string
     */
    private $code;

    /**
     * Indicate whether result is success.
     * @var bool
     */
    private $isSuccess;

    /**
     * Indicate whether retry is allowed should the result is failed.
     * @var bool
     */
    private $isAllowRetry;

    /**
     * Message to be conveyed to the invoker.
     * @var string
     */
    private $message;

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return boolean
     */
    public function getIsSuccess() {
        return $this->isSuccess;
    }

    /**
     * @return boolean
     */
    public function getIsAllowRetry() {
        return $this->isAllowRetry;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $code
     * @return \Raneko\Base\Model\Result
     */
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * @param boolean $isSuccess
     * @return \Raneko\Base\Model\Result
     */
    public function setIsSuccess($isSuccess) {
        $this->isSuccess = $isSuccess;
        return $this;
    }

    /**
     * @param boolean $isAllowRetry
     * @return \Raneko\Base\Model\Result
     */
    public function setIsAllowRetry($isAllowRetry) {
        $this->isAllowRetry = $isAllowRetry;
        return $this;
    }

    /**
     * @param string $message
     * @return \Raneko\Base\Model\Result
     */
    public function setMessage($message) {
        $this->message = $message;
        return $this;
    }

}
