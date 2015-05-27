<?php

namespace Formable\Definition;


/**
 * Class Formable
 * @package Formable\Definition\Formable
 *
 * @Annotation
 */
class Formable
{
    private $name;
    private $options = [];
    private $dataType = 'text';
    private $class;

    public function __construct($options)
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getClass()
    {
        return $this->class;
    }
}