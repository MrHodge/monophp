<?php

namespace mono\database\classes;

use mono\database\models\CollectionObject;


//TODO add where to collection clause
class Collection
{

    /**
     * @var CollectionObject[]
     */
    private $collectionObjects = null;


    /**
     * @param int $position
     * @return null|CollectionObject
     */
    public function getObject($position)
    {
        if(isset($this->collectionObjects[$position])){
            return $this->collectionObjects[$position];
        }
        return null;
    }

    /**
     * @param string $id
     * @return null|CollectionObject
     * @throws \Exception
     */
    public function objectById($id)
    {
        CollectionObject::testId($id);
        if(!(is_string($id) || is_numeric($id))) throw new \Exception("CollectionObject ID may only be a string or integer");
        foreach ($this->collectionObjects as $object) {
            if($object->getId() == $id){
                return $object;
            }
        }
        return null;
    }

    /**
     * @param array $whereArr
     * @param boolean $caseSensitive
     * @return Collection
     * @throws \Exception
     */
    public function getObjectsWhere($whereArr, $caseSensitive = false)
    {
        if(!is_array($whereArr)) throw new \Exception("getObjectsWhere() expected the first parameter to be an array");
        $objects = [];
        foreach ($this->collectionObjects as $object) {
            foreach ($whereArr as $key => $value) {
                if(isset($object->getValue()->$key)) {
                    if($caseSensitive && $object->getValue()->$key != $value) continue 2;
                    if(strtolower($object->getValue()->$key) != strtolower($value)) continue 2;
                } else {
                    continue 2;
                }
            }
            $objects[] = $object;
        }
        $this->setObjects($objects);
        return $this;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function deleteObject($id){
        CollectionObject::testId($id);
        if(!(is_string($id) || is_numeric($id))) throw new \Exception("CollectionObject ID may only be a string or integer");
        foreach ($this->collectionObjects as $object) {
            if($object->getId() == $id){
                unset($object);
            }
        }
        return true;
    }

    /**
     * @return \stdClass|CollectionObject
     */
    public function first()
    {
        foreach ($this->getObjects() as $object){
            return $object;
        }
        return null;
    }

    /**
     * @return CollectionObject
     */
    public function last()
    {
        return end($this->collectionObjects);
    }

    /**
     * @param CollectionObject[] $collectionObjects
     * @throws \Exception
     */
    public function setObjects($collectionObjects)
    {
        if(!is_array($collectionObjects)) throw new \Exception("Collection objects must be an array");
        $realObjectArray = [];

        $pos = 0;

        foreach ($collectionObjects as $value) {
            if(self::testObject($value)){
                $realObjectArray[$pos] = $value;
            } else {
                $realObjectArray[$pos] = new CollectionObject($pos, $value);
            }
            $pos++;
        }

        $this->collectionObjects = $realObjectArray;
    }

    /**
     * @return CollectionObject[]
     */
    public function getObjects()
    {
        return $this->collectionObjects;
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->collectionObjects);
    }

    /**
     * @param CollectionObject $object
     * @return bool
     */
    public static function testObject($object) {
       return self::testObjects([$object]);
    }

    /**
     * @param CollectionObject[] $objects
     * @return bool
     */
    public static function testObjects($objects) {
        if(!is_array($objects)) return false;
        foreach ($objects as $object) {
            if(!($object instanceof CollectionObject)) return false;
        }
        return true;
    }
}