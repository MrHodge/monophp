<?php

/**
 * @param $class
 * @param string $identifier
 * @param mixed $field
 * @return string
 */
function getPHPAttribute($class, $identifier, $field = null){
    $arrs = getPHPAttributes($class);
    foreach ($arrs as $arr) {
        if($field != null && ($field instanceof ReflectionMethod)) {
            if(isset($arr["methods"][$field->getName()][$identifier])){
                return $arr["methods"][$field->getName()][$identifier];
            }
        } elseif($field != null && ($field instanceof ReflectionProperty)) {
            if(isset($arr["properties"][$field->getName()][$identifier])){
                return $arr["properties"][$field->getName()][$identifier];
            }
        }else {
            if(isset($arr["attributes"][$identifier])){
                return $arr["attributes"][$identifier];
            }
        }
    }
    return null;
}

/**
 * @param $class
 * @return array
 */
function getPHPAttributes($class)
{
    if(is_object($class)){
        $class = get_class($class);
        $rc = new ReflectionClass($class);

        $classAttributes = explode("@", $rc->getDocComment());

        $attributesArr = [
            $class => [
                "attributes" => [],
                "methods" => [],
                "properties" => [],
            ]
        ];

        foreach ($classAttributes as $attribute)
        {
            $keys = explode(" ", trim($attribute));
            $id = null;
            $value = "";
            $pos = 0;
            foreach ($keys as $key)
            {
                $key = trim($key);
                if(startsWith("*", $key)){
                    break 1;
                }
                if($pos == 0) {
                    $id = $key;
                } else {
                    $value .= $key . " ";
                }
                $pos++;
            }
            $value = trim($value);
            if(!strlen($value)) $value = null;
            if($id == null || $value == null) continue;
            $attributesArr[$class]["attributes"][$id] = explode(" ", trim($value));

        }
        foreach ($rc->getMethods() as $method) {
            $methodAttributes = explode("@", $method->getDocComment());
            foreach ($methodAttributes as $attribute)
            {
                $keys = explode(" ", trim($attribute));
                $id = null;
                $value = "";
                $pos = 0;
                foreach ($keys as $key)
                {
                    $key = trim($key);
                    if(startsWith("*", $key)){
                        break 1;
                    }
                    if($pos == 0) {
                        $id = $key;
                    } else {
                        $value .= $key . " ";
                    }
                    $pos++;
                }
                $value = trim($value);
                if(!strlen($value)) $value = null;
                if($id == null || $value == null) continue;
                $attributesArr[$class]["methods"][$method->name][$id] = explode(" ", trim($value));

            }
        }
        foreach ($rc->getProperties() as $property) {
            $propertyAttributes = explode("@", $property->getDocComment());
            foreach ($propertyAttributes as $attribute)
            {
                $keys = explode(" ", trim($attribute));
                $id = null;
                $value = "";
                $pos = 0;
                foreach ($keys as $key)
                {
                    $key = trim($key);
                    if(startsWith("*", $key)){
                        break 1;
                    }
                    if($pos == 0) {
                        $id = $key;
                    } else {
                        $value .= $key . " ";
                    }
                    $pos++;
                }
                $value = trim($value);
                if(!strlen($value)) $value = null;
                if($id == null || $value == null) continue;
                $attributesArr[$class]["properties"][$property->getName()][$id] = explode(" ", trim($value));
            }
        }
        return $attributesArr;
    }
    return [];
}

/**
 * @param stdClass $class
 * @return string
 */
function getShortName($class) {
    $reflect = new ReflectionClass($class);
    return $reflect->getShortName();
}