<?php

namespace mono\events;


use mono\models\Event;
use mono\models\Model;

class ModelLoadEvent extends Event
{

    /**
     * @var Model
     */
    private $model;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var object
     */
    private $fieldsFromDatabase;

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        foreach ($this->getFields() as $name => $value) {
            if (!isset($this->getFieldsFromDatabase()->$name)) continue;
            $this->getModel()->$name = $this->getFieldsFromDatabase()->$name; //Database value
        }
    }

    /**
     * @return object
     */
    public function getFieldsFromDatabase()
    {
        return $this->fieldsFromDatabase;
    }

    /**
     * @param object $fieldsFromDatabase
     */
    public function setFieldsFromDatabase($fieldsFromDatabase)
    {
        $this->fieldsFromDatabase = $fieldsFromDatabase;
        foreach ($this->getFieldsFromDatabase() as $name => $value) {
            if (!isset($this->getFields()->$name)) continue;
            $this->getModel()->$name = $this->getFieldsFromDatabase()->$name;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setFieldFromDatabase($key, $value) {
        $this->fieldsFromDatabase->$key = $value;
    }

}