<?php

namespace Raneko\External\AWS;

/**
 * Deal with SNS.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-05-21
 */
class SNS
{

    private $isLoaded = FALSE;
    private $data;

    /**
     * Get raw data from SNS.
     * @return array|NULL Data from SNS. NULL if failed to decode message.
     */
    public function getData()
    {
        if (!$this->isLoaded)
        {
            $this->data = json_decode(file_get_contents("php://input"), TRUE);

            if ($this->data === FALSE)
            {
                $this->data = NULL;
            }

            $this->isLoaded = TRUE;
        }
        return $this->data;
    }

    /**
     * Get mesage portion of the message.
     * @return mixed
     */
    public function getMessage()
    {
        if (!$this->isLoaded)
        {
            if ($this->getData() === NULL)
            {
                return NULL;
            }
        }
        return isset($this->data["Message"]) ? $this->data["Message"] : NULL;
    }

}
