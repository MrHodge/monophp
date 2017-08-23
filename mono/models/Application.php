<?php

namespace mono\models;



use mono\classes\Log;

abstract class Application
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $version;

    function __construct($name, $author, $description, $version)
    {
        $this->setName($name);
        $this->setAuthor($author);
        $this->setDescription($description);
        $this->setVersion($version);
        Log::info("Initializing App");
        $this->init();
    }

    public abstract function init();

    public abstract function beforePlugins();

    public abstract function beforeRoutes();

    public abstract function beforeController();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return String
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param String $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

}