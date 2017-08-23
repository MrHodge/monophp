<?php
namespace mono\database\models;

class CollectionObject
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var mixed
     */
    private $value;

    /**
     * CollectionObject constructor.
     * @param mixed $id
     * @param mixed $value
     * @throws \Exception
     */
    public function __construct($id, $value)
    {
        self::testId($id);
        $this->setId($id);
        $this->setValue($value);
    }

    /**
     * @return array|mixed|CollectionObject
     */
    public function first()
    {
        if(is_array($this->value)){
            foreach ($this->value as $value){
                return $value;
            }
        }
        return $this->value;
    }

    /**
     * @return array|mixed|CollectionObject
     */
    public function last()
    {
        if(is_array($this->value)) {
            return end($this->objects);
        }
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        self::testId($id);
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

    /**
     * @param mixed $id
     * @throws \Exception
     */
    public static function testId($id) {
        if(!(is_string($id) || is_numeric($id))) throw new \Exception("CollectionObject ID may only be a string or integer");
    }

}