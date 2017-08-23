<?php

namespace mono\models;


class Parameter {

    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $value;

    function __construct($id, $value)
    {
        $this->setId($id);
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}