<?php

namespace Raneko\Common\Handler\Gateway;

/**
 * Gateway to dispatch the correct handler based on given command
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-16
 */
abstract class GatewayAbstract
{

    /**
     * Mapping of handler.
     * @var \Raneko\Common\Handler\Gateway\Map Array of map.
     */
    private $handlerMap = array();

    public function __construct()
    {
        $this->_loadMapList();
    }

    /**
     * Add map to the list.
     * @param \Raneko\Common\Handler\Gateway\Map $map
     */
    protected function _addMap(\Raneko\Common\Handler\Gateway\Map $map)
    {
        $this->handlerMap[] = $map;
    }

    /**
     * Load Map.
     * Implementation can be hardcoded, read frome file or database, etc.
     */
    abstract protected function _loadMapList();

    /**
     * Relay request to registered handler
     * @param string $entity
     * @param string $command
     * @param array $params
     * @return \Raneko\Common\Result
     */
    public function process($entity, $command, $params = array())
    {
        $handler = $this->_getHandler($entity, $command);
        $result = $handler->process($params);

        return $result;
    }

    /**
     * Find handler.
     * @param string $entity
     * @param string $command
     * @param string $module
     * @return \Raneko\Common\Handler\HandlerAbstract NULL if record not found or class does not exist.
     * @throws \Exception
     */
    private function _getHandler($entity, $command, $module = \Raneko\Common\Handler\Gateway\Map::DEFAULT_MODULE)
    {
        $result = NULL;
        $className = NULL;
        $proceed = TRUE;

        /* Check if handler is registered */
        if ($proceed)
        {
            $className = $this->getHandlerClassName($entity, $command, $module);
            if ($className === NULL)
            {
                $proceed = FALSE;
                throw new \Exception("Handler class for '{$entity}-{$command}' not found");
            }
        }

        /* Check if handler class exist */
        if ($proceed)
        {
            if (!class_exists($className))
            {
                $proceed = FALSE;
                throw new \Exception("Handler class '{$className}' for '{$entity}-{$command}' not found");
            }
            else
            {
                $result = new $className;
            }
        }

        return $result;
    }

    /**
     * Get class name registered as handler.
     * This method does not verify class existence.
     * @param string $entity
     * @param string $command
     * @param string $module
     * @return string|NULL Class which will handle the request. NULL if no match found.
     */
    public function getHandlerClassName($entity, $command, $module = \Raneko\Common\Handler\Gateway\Map::DEFAULT_MODULE)
    {
        $result = NULL;

        /* Instantiate a map object as there is normalization process to the values */
        $map = \Raneko\Common\Handler\Gateway\Map::create()->Entity($entity)->Command($command)->Module($module);

        /* Try to find class name of the handler */
        foreach ($this->handlerMap as $_map)
        {
            if ($_map->getEntity() == $map->getEntity() && $_map->getCommand() == $map->getCommand() && $_map->getModule() == $map->getModule())
            {
                $result = $_map->getClassName();
                break;
            }
        }

        return $result;
    }

    /**
     * Get list of map.
     * @return \Raneko\Common\Handler\Gateway\Map List of map registered.
     */
    public function getMapList()
    {
        return $this->handlerMap();
    }

}
