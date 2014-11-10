<?php

namespace Raneko\Common\Result\Presenter;

/**
 * Abstract class to present result.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-16
 */
abstract class PresenterAbstract
{

    /**
     * Present result.
     * @param \Raneko\Common\Result $result
     * @param boolean $asValue Indicates whether result is to be returned as value.
     */
    public function present($result, $asValue = FALSE)
    {
        $string = $this->_present($result);
        if ($asValue)
        {
            return $string;
        }
        else
        {
            echo $string;
        }
    }

    /**
     * @param \Raneko\Common\Result $result
     * @return string String to be echo-ed
     */
    abstract protected function _present($result);

    /**
     * Decode string response to array.
     * @param string $response
     * @return array
     */
    public function decodeToArray($response)
    {
        $result = NULL;
        try
        {
            $result = $this->_decodeToArray($response);
            return $result;
        }
        catch (Exception $ex)
        {
            return NULL;
        }
    }

    /**
     * @param string $response
     * @return \Raneko\Common\Result
     * @throws \Exception If decoding failed.
     */
    abstract protected function _decodeToResult($response);

    /**
     * Decode string response to result object.
     * @param string $response
     * @return \Raneko\Common\Result
     */
    public function decodeToResult($response)
    {
        $result = NULL;
        try
        {
            $result = $this->_decodeToResult($response);
            return $result;
        }
        catch (Exception $ex)
        {
            return NULL;
        }
    }

    /**
     * @param string $response
     * @return array
     * @throws Exception If decoding failed.
     */
    abstract protected function _decodeToArray($response);
}
