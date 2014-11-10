<?php

namespace Raneko\Log\Post;

/**
 * Abstract class to post message to log repository.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-02
 */
abstract class PostAbstract
{

    /**
     * Indicates whether poster is enabled.
     * @var boolean
     */
    private $isEnabled = TRUE;

    /**
     * Post message to repository.
     * Actual implementation of this method is to be done by the implementing class.
     * @param \Raneko\Common\Message $message
     * @return \Raneko\Common\Result
     */
    public function post(\Raneko\Common\Message $message)
    {
        $result = new \Raneko\Common\Result();
        $proceed = TRUE;

        /**
         * Check if the poster is active.
         * If poster is not active, there is no need to process the message and just return TRUE.
         */
        if ($proceed && !$this->isEnabled)
        {
            $proceed = FALSE;
            $result->setResultSuccess();
        }

        if ($proceed)
        {
            /* Check if message contain text */
            $text = $message->getText();
            if (strlen($text) > 0)
            {
                $this->_post($message);
                $result->setResultSuccess();
            }
            else
            {
                $_message = $result->createMessage();
                $_message->Text("Text is empty")->TypeError();
                $result->addMessage($_message);
            }
        }

        return $result;
    }

    /**
     * Set whether post is enabled.
     * @param boolean $isEnabled
     * @return \Raneko\Log\Post\PostAbstract
     */
    public function setEnabled($isEnabled = TRUE)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    /**
     * Check whether post is enabled.
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Post the message to repository.
     * @param \Raneko\Common\Message $message
     * @return boolean
     */
    abstract protected function _post(\Raneko\Common\Message $message);
}
