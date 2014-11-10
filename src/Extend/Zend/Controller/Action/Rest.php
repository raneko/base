<?php

namespace Raneko\Extend\Zend\Controller\Action;

/**
 * Zend Controller for REST handling purpose.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-16
 */
class Rest extends \Raneko\Extend\Zend\Controller\Action
{

    /**
     * Presenter for the result.
     * @var \Raneko\Common\Result\Presenter\PresenterAbstract
     */
    private $apiPresenter;

    /**
     * Result of the process.
     * Value is initialized during init().
     * @var \Raneko\Common\Result
     */
    private $apiResult;

    /**
     * Gateway to route request to its appropriate handler.
     * Value is to be assigned by child during init().
     * @var \Raneko\Common\Handler\Gateway\GatewayAbstract
     */
    private $apiGateway;

    /**
     * Indicates whether request has been validated using basic criterias.
     * Automatically set during init().
     * Value can be overridden by child.
     * @var boolean
     */
    private $isValidated = FALSE;

    /**
     * Entity to be processed.
     * Automatically populated during init() by taking value from $params["action"].
     * @var string
     */
    private $apiEntity;

    /**
     * Command to be executed.
     * Automatically populated during init() by taking value from $params["action"]
     * @var string 
     */
    private $apiCommand;

    /**
     * Child of class must invoke this method and set gateway for processing.
     * \Raneko\Common\Handler\Gateway\GatewayAbstract
     */
    public function init()
    {
        parent::init();

        $this->apiResult = new \Raneko\Common\Result();

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        /* Populate value */
        $this->populate();

        /* Check the extension and define header and presenter based on that */
        $this->processExtension();

        /**
         * Validate basic parameter.
         * Additional validation can be performed separately by the child.
         * This validation is absolute and process will not continue even child validation is TRUE.
         */
        $this->isValidated = $this->validate();

        $this->_setGateway();
    }

    /**
     * Populate value from parameters.
     * @return boolean
     */
    private function populate()
    {
        $this->params["extension"] = isset($this->params["extension"]) ? trim($this->params["extension"], " .") : NULL;
        $this->apiEntity = isset($this->params["action"]) ? $this->params["action"] : NULL;
        $this->apiCommand = isset($this->params["api_cmd"]) ? $this->params["api_cmd"] : NULL;
    }

    /**
     * Set gateway to handle the process.
     * @param \Raneko\Common\Handler\Gateway\GatewayAbstract $gateway
     */
    protected function _setGateway(\Raneko\Common\Handler\Gateway\GatewayAbstract $gateway = NULL)
    {
        $this->apiGateway = $gateway;
    }

    /**
     * Check extension and prepare some variables based on that.
     */
    private function processExtension()
    {
        switch ($this->params["extension"])
        {
            default:
                $this->getResponse()->setHeader("Content-Type", "application/json; charset=UTF-8");
                $this->apiPresenter = new \Raneko\Common\Result\Presenter\JSON();
                break;
        }
    }

    /**
     * Take over process handling.
     */
    protected function _process()
    {
        if ($this->isValidated)
        {
            if (isset($this->apiGateway))
            {
                $this->apiResult = $this->apiGateway->process($this->apiEntity, $this->apiCommand, $this->params);

                if ($this->apiResult->isResultFailed())
                {
                    $this->getResponse()->setHttpResponseCode(400);
                }
            }
            else
            {
                throw new \Exception("Gateway not set");
            }
        }

        /* Present result */
        $this->apiPresenter->present($this->apiResult);
    }

    /**
     * Validate parameters.
     * @return \Raneko\Common\Result
     */
    private function validate()
    {
        $result = TRUE;
        $recordAPIAccess = NULL;

        /**
         * Check if action exist.
         * This is to minimize unnecessary checking due to init phase to be completed before the Zend actually check 404 action.
         */
        if ($result && !method_exists($this, "{$this->params["action"]}Action"))
        {
            $result = FALSE;
        }

        /* Check if all required fields are present */
        if ($result)
        {
            $_mandatoryList = array(
                "api_key",
                "api_hash",
                "api_cmd"
            );
            if (!\Raneko\Validation::fieldListMandatory(__METHOD__, $_mandatoryList, $this->params))
            {
                $result = FALSE;
                $this->getResponse()->setHttpResponseCode(400);
            }
        }

        /* Check if extension is supported */
        if ($result)
        {
            $_supportedList = array(
                "json"
            );
            if (!in_array($this->params["extension"], $_supportedList))
            {
                $result = FALSE;
                $this->params["extension"] = "json"; /* Set extension to JSON by default */
                \Raneko\Log::error(__METHOD__, "Resource not found");
                $this->getResponse()->setHttpResponseCode(404);
            }
        }

        /* Check if access key is valid */
        if ($result)
        {
            $_adapter = \Raneko\App::getZendDbAdapter(TRUE);
            $_dbSelect = $_adapter->select()
                    ->from("api_access")
                    ->where("api_key = ?", $this->params["api_key"])
            ;

            $recordAPIAccess = $_adapter->fetchRow($_dbSelect);
            if (in_array($recordAPIAccess, array(FALSE, NULL)))
            {
                $result = FALSE;
                \Raneko\Log::error(__METHOD__, "Invalid API credentials");
                $this->getResponse()->setHttpResponseCode(401);
            }
            else
            {
                \Raneko\App::setId($recordAPIAccess["id"]);
            }
        }

        /**
         * Check hash.
         * This is determined by configuration app.api.checkhash (bool)
         */
        if ($result && \Raneko\App::getConfig("app", "api", "hash", "check"))
        {
            if (!isset($this->params["api_hash"]) || strlen($this->params["api_hash"]) == 0)
            {
                $result = FALSE;
            }
            else
            {
                /* Take parameters from $_REQUEST to minimize mixing up with Zend generated parameters */
                $_params = $_REQUEST;
                $_unsetList = \Raneko\App::getConfig("app", "api", "hash", "exclusion");
                foreach ($_unsetList as $_field)
                {
                    unset($_params[$_field]);
                }

                $_hash = new \Raneko\Crypt\APIHash();
                $_hash->setPrivateKey($recordAPIAccess["api_secret"]);

                if (!$_hash->validate($this->params["api_hash"], $_params))
                {
                    $result = FALSE;
                }
            }

            if ($result === FALSE)
            {
                \Raneko\Log::error(__METHOD__, "Invalid API Hash");
                $this->getResponse()->setHttpResponseCode(401);
            }
        }

        return $result;
    }

}
