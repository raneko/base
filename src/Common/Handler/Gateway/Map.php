<?php

namespace Raneko\Common\Handler\Gateway;

/**
 * Map class.
 * @author Harry Lesmana <harrylesmana@singpost.com>
 * @since 2014-04-16
 */
class Map
{

    /**
     * Module to handle.
     * @var string
     */
    private $module;

    /**
     * Entity to handle.
     * @var string
     */
    private $entity;

    /**
     * Command to handle.
     * @var string
     */
    private $command;

    /**
     * Class name to handle.
     * @var string
     */
    private $className;

    const DEFAULT_MODULE = "default";

    public function __construct()
    {
        $this->Module(self::DEFAULT_MODULE);
    }

    /**
     * @return \Raneko\Common\Handler\Gateway\Map
     */
    public static function create()
    {
        return new self;
    }

    /**
     * Set entity.
     * @param string $entity
     * @return \Raneko\Common\Handler\Gateway\Map
     */
    public function Entity($entity)
    {
        $this->entity = strtolower($entity);
        return $this;
    }

    /**
     * Set command.
     * @param string $command
     * @return \Raneko\Common\Handler\Gateway\Map
     */
    public function Command($command)
    {
        $this->command = strtolower($command);
        return $this;
    }

    /**
     * Set class name.
     * This method will check for class existence and throw exception if class does not exist.
     * @param string $className
     * @return \Raneko\Common\Handler\Gateway\Map
     */
    public function ClassName($className)
    {
        if (!class_exists($className))
        {
            throw new \Exception("Class not found '{$className}'");
        }
        else
        {
            $this->className = $className;
            return $this;
        }
    }

    /**
     * Set module name.
     * @param string $module
     * @return \Raneko\Common\Handler\Gateway\Map
     */
    public function Module($module)
    {
        $this->module = strtolower($module);
        return $this;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

}
