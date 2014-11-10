<?php

namespace Raneko\Log\Post;

/**
 * Post implementation for loggly.
 * Special mapping:
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-02
 */
class Loggly extends \Raneko\Log\Post\PostAbstract
{

    /**
     * @var \Raneko\External\Loggly
     */
    private $loggly;

    /**
     * Construct a post object for loggly.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->loggly = new \Raneko\External\Loggly($token);
    }

    protected function _post(\Raneko\Common\Message $message)
    {
        if (strlen($message->getText()) > 0)
        {
            $_data = array(
                "source" => $message->getMethod(),
                "message" => $message->getText()
            );
            if (strlen($message->getUser()) > 0)
            {
                $_data["user"] = $message->getUser();
            }
            if (count($message->getData() > 0))
            {
                $_data["data"] = $message->getData();
            }

            $this->loggly->post($_data, $message->getTags());
        }
        return TRUE;
    }

}
