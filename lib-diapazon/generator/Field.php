<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu.savy
 * Date: 21/08/13
 * Time: 10:23
 * To change this template use File | Settings | File Templates.
 */

namespace Diapazon\Generator;

use Diapazon\Tools;

class Field
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $defaultValue;

    /** @var string */
    private $sequence;

    public function __construct($name)
    {
        $this->name         = $name;
        $this->type         = null;
        $this->defaultValue = null;
        $this->sequence     = null;
    }

    /**
     * @return string
     */
    public function getCapName()
    {
        return Tools::capitalize($this->name);
    }

    /**
     * @return string
     */
    public function defaultValuetoString()
    {
        if (is_null($this->defaultValue))
            return 'null';
        else if (is_string($this->defaultValue))
            return "'$this->defaultValue'";
        else
            return $this->defaultValue;
    }

    /**
     * @param string $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param string $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}