<?php

namespace mono\events;


use mono\models\Event;
use mono\models\Model;

class ModelCreateEvent extends Event
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
     * @var array
     */
    private $fieldsToInsert;

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
        foreach ($this->getFieldsToInsert() as $key => $value) {
            if (array_key_exists($key, $this->getFields())) {
                $this->getModel()->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getFieldsToInsert()
    {
        return $this->fieldsToInsert;
    }

    /**
     * @param array $fieldsToInsert
     */
    public function setFieldsToInsert($fieldsToInsert)
    {
        $this->fieldsToInsert = $fieldsToInsert;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setInsertField($key, $value) {
        $this->fieldsToInsert[$key] = $value;
    }

}