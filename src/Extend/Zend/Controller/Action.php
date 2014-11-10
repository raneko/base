<?php

namespace Raneko\Extend\Zend\Controller;

/**
 * Extension to Zend_Controller_Action.
 * Extract parameters to variable, perform default logging.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 */
class Action extends \Zend_Controller_Action
{

    /**
     * Parameters captured by the getAllParams().
     * @var array
     */
    protected $params;

    /**
     * Data for the view.
     * @var array
     */
    protected $data;

    public function init()
    {
        parent::init();

        $this->params = $this->getAllParams();

        /**
         * Initialize data and pass parameters as initial data for the view.
         */
        $this->data = array();
        $this->_addData("params", $this->params);
    }

    /**
     * Add data to be passed to the view.
     * @param string $_key
     * @param mixed $_value
     */
    protected function _addData($_key, $_value)
    {
        $this->data[$_key] = $_value;
        $this->view->data = $this->data;
    }

}
