<?php

namespace mono\database\drivers;

use mono\database\models\DriverModel;

class PDO extends DriverModel
{

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $database;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $results = [];


    /**
     * @var string
     */
    private $error;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $whereString;

    /**
     * @var array
     */
    private $whereArray;


    public function __construct($host, $port, $database, $table_prefix, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->setTablePrefix($table_prefix);
        $this->connection = new \PDO('mysql:host=' . $this->getHost() . ';port=' . $this->getPort() . ';dbname=' . $this->getDatabase() . ';charset=utf8', $this->getUsername(), $this->getPassword());
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $sql
     * @param array $array
     * @param bool $noResults
     * @param array $array2
     * @internal Do not use this directly as this may change at any given time in any update
     * @return $this
     */
    public function preparedQuery($sql, $array = array(), $noResults = false, $array2 = array())
    {
        if(!empty($this->error)){
            return $this;
        }
        if ($query = $this->connection->prepare($sql)) {
            if (count($array)) {
                $keyPos = 1;
                foreach ($array as $key) {
                    if (is_array($key)) {
                        foreach ($key as $k) {
                            $query->bindValue($keyPos, $k);
                            $keyPos++;
                        }
                    } else {
                        $query->bindValue($keyPos, $key);
                        $keyPos++;
                    }
                }
            }
            if(count($array2)) {
                foreach ($array2 as $key => $value) {
                    $query->bindValue($key, $value);
                }
            }
            if ($query->execute()) {
                if (!$noResults) {
                    $this->results = $query->fetchAll(\PDO::FETCH_OBJ);
                    if (count($this->results) == 1) {
                        $this->results = $this->results[0];
                    }
                }
            }
        }
        return $this;
    }

    public function where($whereArr = [])
    {
        if(!is_array($whereArr)) throw new \Exception("The where function only accepts arrays");
        $this->setWhereClause(true);
        $whereString = "";
        $pos = 0;
        foreach ($whereArr as $key => $value) {
            if ($pos == 0) {
                $whereString .= " WHERE {$key} = ?";
            } else {
                $whereString .= " AND {$key} = ?";
            }
            $pos++;
        }
        $this->whereString = $whereString;
        $this->whereArray = $whereArr;
        return $this;
    }

    public function execute()
    {
        if(!parent::execute()){
            return null;
        }
        $batch_size = $this->getBatch();

        if($batch_size) {
            $offset = 0;
            $done = false;
            $objects = [];
            while (!$done) {
                $batchString = str_replace('{{offset}}', $offset, str_replace('{{offset_batch}}', $offset + $batch_size, $this->getBatchString()));
                if($this->whereArray){
                    $batchString = ' AND ' . $batchString;
                }
                $this->preparedQuery("SELECT * FROM {$this->getTablePrefix()}{$this->getSubject()}{$this->whereString}{$batchString}", $this->whereArray);
                $offset += $batch_size;
                $results = $this->getResults(true);
                if(count($results)) {
                    $objects = array_merge($results, $objects);
                } else {
                    $done = true;
                }
            }
            $this->setObjects($objects);
        } else {
            $this->preparedQuery("SELECT * FROM {$this->getTablePrefix()}{$this->getSubject()} {$this->whereString}", $this->whereArray);
            $this->setObjects($this->getResults(true));
        }
        return $this;
    }


    public function get($key = null)
    {
        $this->execute();
        return parent::get($key);
    }

    public function emptyObjects()
    {
        if($this->isWhereClause()) {
            throw new \Exception("Use delete() when using a where clause");
        } else {
            $this->preparedQuery("TRUNCATE TABLE {$this->getTablePrefix()}{$this->getSubject()}", null, true);
        }
        $this->setObjects([]);
        if($this->error) {
            return null;
        }
        return $this;
    }

    public function delete($limit = 0, $order = null)
    {
        if(!$this->isWhereClause())throw new \Exception("Use where() before delete. If you wish to empty the entire table use emptyObjects");

        $limitString = "";
        $orderString = "";
        if($limit){
            $limitString = " LIMIT " . $limit;
        }
        if($order) {
            $orderString = " ORDER BY " . $order;
        }
        $this->preparedQuery("DELETE FROM {$this->getTablePrefix()}{$this->getSubject()} {$this->whereString}{$orderString}{$limitString}", $this->whereArray, true);
        if($this->error){
            return null;
        }
        return $this;
    }

    public function count()
    {
        if(empty($this->getObjects()) && !$this->isWhereClause()){
            //Set collection to all rows from database
            $this->preparedQuery("SELECT * FROM {$this->getTablePrefix()}{$this->getSubject()}");
            $this->setObjects($this->getResults(true));
        }
        return parent::count();
    }

    public function update($toUpdate = [])
    {
        if(!is_array($toUpdate)) throw new \Exception("The update function only accepts arrays");
        $toUpdateString = "";
        $keyCount = 0;
        foreach ($toUpdate as $key => $value) {
            if ($keyCount == 0) {
                $toUpdateString .= "{$key} = ?";
            } else {
                $toUpdateString .= ", {$key} = ?";
            }
            $keyCount++;
        }
        $array = array_merge_recursive($toUpdate, $this->whereArray);
        $this->preparedQuery("UPDATE {$this->getTablePrefix()}{$this->getSubject()} SET {$toUpdateString} {$this->whereString}", $array, true);
        if($this->error) {
            return null;
        }
        return $this;
    }

    public function insert($toInsert = [])
    {
        $keys = array_keys($toInsert);
        $values = null;
        $x = 1;
        foreach ($toInsert as $field) {
            $values .= "?";
            if ($x < count($toInsert)) {
                $values .= ', ';
            }
            $x++;
        }
        $this->preparedQuery("INSERT INTO {$this->getTablePrefix()}{$this->getSubject()} (`" . implode('`, `', $keys) . "`)VALUES({$values})", $toInsert, true);
        if($this->error) {
            return null;
        }
        return $this;
    }

    public function lastId() {
        $this->preparedQuery("SELECT LAST_INSERT_ID();");
        $id = ((array)$this->getResults());
        if(isset($id["LAST_INSERT_ID()"])){
            $id = $id["LAST_INSERT_ID()"];
            return $id;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param \PDO $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param bool $always_array
     * @return array
     */
    public function getResults($always_array = false)
    {
        if(!is_array($this->results) && $always_array){
            $this->results = [$this->results];
        }
        return $this->results;
    }

    /**
     * @param array $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
}