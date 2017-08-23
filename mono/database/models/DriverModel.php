<?php

namespace mono\database\models;

use mono\database\classes\Collection;

abstract class DriverModel extends Collection
{

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $table_prefix;

    /**
     * @var bool
     */
    private $whereClause;

    /**
     * @var boolean
     */
    private $executed;

    /**
     * @var integer
     */
    private $batch;

    /**
     * @var string
     */
    private $batchString;

    /**
     * @param string $subject
     * @return DriverModel
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param mixed $key
     * @return DriverModel|null|CollectionObject
     * @throws \Exception
     */
    public function get($key = null) {
        if($key) {

            $collectionObjects = [];

            if (is_array($key)) {
                foreach ($this->getObjects() as $collectionObject) {
                    $objectValues = [];
                    if (is_object($collectionObject->getValue())) {
                        foreach ($key as $value) {
                            if (!is_string($value)) {
                                throw new \Exception("The key must be a string value.");
                            }
                            if (isset($collectionObject->getValue()->$value)) {
                                $objectValues[$value] = $collectionObject->getValue()->$value;
                            } else {
                                throw new \Exception("No value found by the key \"{$value}\"");
                            }
                        }
                        $collectionObject->setValue((object)$objectValues);
                        $collectionObjects[] = $collectionObject;
                    } else {
                        throw new \Exception("Results from database was not an object.");
                    }
                }
            } else if (is_string($key)) {
                foreach ($this->getObjects() as $collectionObject) {
                    $objectValues = [];
                    if (is_object($collectionObject->getValue())) {
                        if (isset($collectionObject->getValue()->$key)) {
                            $objectValues[$key] = $collectionObject->getValue()->$key;
                        } else {
                            throw new \Exception("No value found by the key \"{$key}\"");
                        }
                        $collectionObject->setValue((object)$objectValues);
                        $collectionObjects[] = $collectionObject;
                    } else {
                        throw new \Exception("Results from database was not an object.");
                    }
                }
            } else {
                throw new \Exception("The key must be a string or an array value.");
            }
            $this->setObjects($collectionObjects);
        }
        return $this;
    }

    /**
     * @param array $whereArr
     * @return DriverModel
     */
    public abstract function where($whereArr = []);

    /**
     * If update fails the function shall return null
     * @param array $toUpdate
     * @return mixed
     */
    public abstract function update($toUpdate = []);

    /**
     * @param array $toInsert
     * @return DriverModel|null
     */
    public abstract function insert($toInsert = []);


    /**
     * @param int $limit
     * @param null $order
     * @return Dr
     */
    public abstract function delete($limit = 0, $order = null);

    /**
     * @return DriverModel
     */
    public abstract function emptyObjects();

    /**
     * Each driver should implement their own execute function.
     *
     * @return DriverModel
     */
    public function execute(){
        if($this->executed){
            return null;
        }
        return $this;
    }

    /**
     * @return int
     */
    public abstract function lastId();

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return bool
     */
    public function isWhereClause()
    {
        return $this->whereClause;
    }

    /**
     * @param bool $whereClause
     */
    public function setWhereClause($whereClause)
    {
        $this->whereClause = $whereClause;
    }

    public function getObject($position)
    {
        $this->execute();
        return parent::getObject($position);
    }

    public function objectById($id)
    {
        $this->execute();
        return parent::objectById($id);
    }

    public function last()
    {
        $this->execute();
        $objects = $this->getObjects();
        return end($objects)->getValue();
    }

    public function count()
    {
        $this->execute();
        return parent::count();
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->table_prefix;
    }

    /**
     * @param string $table_prefix
     */
    public function setTablePrefix($table_prefix)
    {
        $this->table_prefix = $table_prefix;
    }

    /**
     * @return int
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param int $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }

    /**
     * @return string
     */
    public function getBatchString()
    {
        return $this->batchString;
    }

    /**
     * @param string $batchString
     */
    public function setBatchString($batchString)
    {
        $this->batchString = $batchString;
    }

}