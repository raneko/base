<?php

namespace Raneko\Log\Post;

/**
 * Post implementation for loggr.
 * Special mapping:
 * - `source` <- `method`
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-02
 */
class Loggr extends \Raneko\Log\Post\PostAbstract
{

    /**
     * @var \Raneko\External\Loggr
     */
    private $loggr;

    /**
     * Construct a post object for loggr.net.
     * @param string $logKey
     * @param string $apiKey
     */
    public function __construct($logKey, $apiKey)
    {
        $this->loggr = new \Raneko\External\Loggr($logKey, $apiKey);
    }

    protected function _post(\Raneko\Common\Message $message)
    {
        $event = $this->loggr->Events->Create();

        $event->Text($message->getText());

        $event->Tags($message->getTags(TRUE));

        $dataUser = $message->getUser();
        if (strlen($dataUser) > 0)
        {
            $event->User($dataUser);
        }

        $dataSource = $message->getMethod();
        if (strlen($dataSource) > 0)
        {
            $event->Source($dataSource);
        }

        $dataData = $message->getData(TRUE);
        if (strlen($dataData) > 0)
        {
            $event->Data($dataData);
        }

        $event->Post();

        return TRUE;
    }

}
