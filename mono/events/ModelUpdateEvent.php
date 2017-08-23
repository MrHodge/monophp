<?php

namespace mono\events;


use mono\models\Event;
use mono\models\Model;

class ModelUpdateEvent extends Event
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
    private $fieldsToUpdate;

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
        foreach ($this->getFieldsToUpdate() as $key => $value) {
            if (array_key_exists($key, $this->getFields())) {
                $this->getModel()->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getFieldsToUpdate()
    {
        return $this->fieldsToUpdate;
    }

    /**
     * @param array $fieldsToUpdate
     */
    public function setFieldsToUpdate($fieldsToUpdate)
    {
        $this->fieldsToUpdate = $fieldsToUpdate;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setUpdateField($key, $value) {
        $this->fieldsToUpdate[$key] = $value;
    }

}