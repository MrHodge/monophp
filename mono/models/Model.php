<?php

namespace mono\models;

use mono\database\classes\Collection;
use mono\database\classes\Database;
use mono\database\models\CollectionObject;
use mono\events\ModelCreateEvent;
use mono\events\ModelLoadEvent;
use mono\events\ModelUpdateEvent;

abstract class Model
{

    /**
     * @var string
     */
    protected $uniqueKey = "id";

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $databaseId;

    /**
     * @var integer
     */
    private static $batch;


    /**
     * @param array $whereArray
     * @param string $databaseId
     * @return Collection
     * @throws \Exception
     */
    public static function where($whereArray, $databaseId = "default")
    {
        if(!is_array($whereArray))throw new \Exception("Model where() expected first parameter to be an array");

        $class = get_called_class();


        $model = new $class();

        if($model->getSubject() == null)
        {
            $classNameParts = explode("\\", $class);
            $model->setSubject(plural(strtolower(end($classNameParts))));
        }
        $model->setDatabaseId($databaseId);


        $database = new Database();
        $database = $database::initialize($databaseId);
        if(self::$batch) {
            $database->setBatch(self::$batch);
            $model = new $class();
            $database->setBatchString($model->getUniqueKey() . ' > {{offset}} AND ' . $model->getUniqueKey() . ' < {{offset_batch}}');
        }
        $results = $database->setSubject($model->getSubject())->where($whereArray)->execute();

        $collectionObjects = [];

        foreach ($results->getObjects() as $object) {
            $model = new $class();
            $model->setDatabaseId($databaseId);
            $fields = self::getFields($model);
            $event = new ModelLoadEvent();
            $event->setModel($model);
            $event->setFieldsFromDatabase($object->getValue());
            $event->setFields($fields);
            $event->call();
            if(!$event->isCancelled()) {
                $collectionObjects[] = new CollectionObject($object->getId(), $event->getModel());
            }
        }
        $collection = new Collection();
        $collection->setObjects($collectionObjects);
        return $collection;
    }

    /**
     * @param $keysAndValues
     * @param string $databaseId
     * @return Model
     * @throws \Exception
     */
    public static function create($keysAndValues, $databaseId = "default") {

        if(!is_array($keysAndValues))throw new \Exception("Model create() expected the first parameter to be an array");

        $class = get_called_class();

        $model = new $class();

        if($model->getSubject() == null)
        {
            $classNameParts = explode("\\", $class);
            $model->setSubject(plural(strtolower(end($classNameParts))));
        }
        $model->setDatabaseId($databaseId);

        $database = new Database();
        $database = $database::initialize($databaseId);
        $database = $database->setSubject($model->getSubject());

        $fields = self::getFields($model);
        $event = new ModelCreateEvent();
        $event->setModel($model);
        $event->setFieldsToInsert($keysAndValues);
        $event->setFields($fields);
        $event->call();
        if(!$event->isCancelled()) {
            if ($database->insert($event->getFieldsToInsert())) {
                if(array_key_exists($event->getModel()->getUniqueKey(), $event->getFields())){
                    $event->getModel()->{$event->getModel()->getUniqueKey()} = intval($database->lastId());
                }
                return $event->getModel();
            }
        }
        return null;

    }

    public static function batch($batch) {
        if($batch > 0) {
            self::$batch = $batch;
        }
        return new static();
    }

    /**
     * @param string $databaseId
     * @return Collection
     */
    public static function all($databaseId = "default"){
        $class = get_called_class();

        $model = new $class();

        if($model->getSubject() == null)
        {
            $classNameParts = explode("\\", $class);
            $model->setSubject(plural(strtolower(end($classNameParts))));
        }
        $model->setDatabaseId($databaseId);

        $database = new Database();
        $dbDriver = $database::initialize($databaseId);
        $dbDriver = $dbDriver->setSubject($model->getSubject());

        if(self::$batch) {
            $dbDriver->setBatch(self::$batch);
            $dbDriver->setBatchString(self::getUniqueKey() . ' > {{offset}} AND ' . self::getUniqueKey() . ' < {{offset_batch}}');
        }

        $collectionObjects = [];
        foreach ($dbDriver->get()->getObjects() as $object) {
            $model = new $class();
            $model->setDatabaseId($databaseId);
            $fields = self::getFields($model);
            $event = new ModelLoadEvent();
            $event->setModel($model);
            $event->setFieldsFromDatabase($object->getValue());
            $event->setFields($fields);
            $event->call();
            if(!$event->isCancelled()) {
                $collectionObjects[] = new CollectionObject($object->getId(), $event->getModel());
            }
        }
        $collection = new Collection();
        $collection->setObjects($collectionObjects);
        return $collection;
    }

    public function update() {
        $database = new Database();
        $database = $database::initialize($this->getDatabaseId());
        $database = $database->setSubject($this->getSubject());

        $realFields = [];

        $fields = self::getFields($this);
        if(array_key_exists($this->getUniqueKey(), $fields)) {
            unset($fields[$this->getUniqueKey()]);
        }
        if(array_key_exists($this->getSubject(), $fields)) {
            unset($fields[$this->getSubject()]);
        }
        if(array_key_exists($this->getDatabaseId(), $fields)) {
            unset($fields[$this->getDatabaseId()]);
        }

        foreach ($fields as $key => $value) {
            $realFields[$key] = $this->$key;
        }

        $event = new ModelUpdateEvent();
        $event->setModel($this);
        $event->setFieldsToUpdate($realFields);
        $event->setFields($fields);
        $event->call();
        if(!$event->isCancelled()) {
            if ($database->where([$this->getUniqueKey() => $this->{$this->getUniqueKey()}])->update($event->getFieldsToUpdate())) {
                return $event->getModel();
            }
        }
        return null;
    }

    /**
     * @param $fieldName
     * @return mixed|null
     */
    public function get($fieldName) {
        if(!isset($this->$fieldName))return null;
        return $this->$fieldName;
    }

    /**
     * @param $fieldName
     * @param mixed $value
     * @return mixed|null
     */
    public function set($fieldName, $value) {
        $this->$fieldName = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function delete() {
        $database = new Database();
        $database = $database::initialize($this->getDatabaseId())->setSubject($this->getSubject());
        if($database->where([$this->getUniqueKey() => $this->{$this->getUniqueKey()}])->delete()){
            return true;
        } else {
            return false;
        }
    }

    protected static function getFields($class) {
        $arr = [];
        $reflection = new \ReflectionClass($class);
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($vars as $var){
            if($var->isPrivate() || $var->isProtected())continue;
            $arr[$var->getName()] = $var->getValue(new $class());
        }
        return $arr;
    }

    /**
     * @return string
     */
    public function getUniqueKey()
    {
        return $this->uniqueKey;
    }

    /**
     * @param string $uniqueKey
     */
    public function setUniqueKey($uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getDatabaseId()
    {
        return $this->databaseId;
    }

    /**
     * @param mixed $databaseId
     */
    public function setDatabaseId($databaseId)
    {
        $this->databaseId = $databaseId;
    }

}