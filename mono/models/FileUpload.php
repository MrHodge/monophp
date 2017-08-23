<?php

namespace mono\models;

class FileUpload
{
    /**
     * @var array
     */
    private $file = [];

    /**
     * @var string
     */
    private $fileName = "";

    /**
     * @var array
     */
    private $allowedFiles = [];

    /**
     * @var string
     */
    private $uploadLocation = "";

    /**
     * @var string
     */
    private $savedFileLocation = "";

    /**
     * @var int
     */
    private $maxSize = 0;

    /**
     * @var bool
     */
    private $hasError = false;

    /**
     * @var string
     */
    private $errorCode = "";

    /**
     * @var string
     */
    private $errorMessage = "";

    /**
     * @var bool
     */
    private $complete = false;

    /**
     * @var bool
     */
    private $overwrite = false;

    /**
     * @param $allowedFiles
     */
    public function __construct($allowedFiles)
    {
        $this->setAllowedFiles($allowedFiles);
    }

    public function saveFile() {
        if(empty($this->getFile()["name"])){
            $this->setHasError(true);
            $this->setErrorCode("NO_FILE");
            return;
        }
        $location = $this->getUploadLocation();
        $realFileName = explode(".", trim(basename($this->getFile()["name"])));
        $type = end($realFileName);
        $fileName = $this->getFileName();
        if(empty($fileName)){
            $this->setFileName(basename($this->getFile()["name"]));
        } else {
            $this->setFileName($this->getFileName() . "." . $type);
        }
        if (!empty($location)) {
            if (is_dir($location)) {
                $file = $location . $this->getFileName();
                if (in_array($type, $this->getAllowedFiles())) {
                    if ($this->getFile()["size"] > $this->getMaxSize()) {
                        if($this->isOverwrite() && file_exists($file)) unlink($file); //If overwrite then delete the current file
                        if (!file_exists($file)) {
                            if (move_uploaded_file($this->getFile()["tmp_name"], $file)) {
                                $this->setHasError(false);
                                $this->setSavedFileLocation($file);
                                $this->setComplete(true);
                            } else {
                                $this->setHasError(true);
                                $this->setErrorCode("UPLOAD_ERROR");
                            }
                        } else {
                            $this->setHasError(true);
                            $this->setErrorCode("FILE_EXISTS");
                        }
                    } else {
                        $this->setHasError(true);
                        $this->setErrorCode("FILE_TOO_LARGE");
                    }
                } else {
                    $this->setHasError(true);
                    $this->setErrorCode("FILE_NOT_ALLOWED");
                }
            } else {
                $this->setHasError(true);
                $this->setErrorCode("INVALID_UPLOAD_LOCATION");
            }
        } else {
            $this->setHasError(true);
            $this->setErrorCode("NO_UPLOAD_LOCATION");
        }
    }

    /**
     * @return array
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param array $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return array
     */
    public function getAllowedFiles()
    {
        return $this->allowedFiles;
    }

    /**
     * @param array $allowedFiles
     */
    public function setAllowedFiles($allowedFiles)
    {
        $this->allowedFiles = $allowedFiles;
    }

    /**
     * @return string
     */
    public function getUploadLocation()
    {
        return $this->uploadLocation;
    }

    /**
     * @param string $uploadLocation
     */
    public function setUploadLocation($uploadLocation)
    {
        $this->uploadLocation = $uploadLocation;
    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @param boolean $hasError
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param string $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return boolean
     */
    public function isComplete()
    {
        return $this->complete;
    }

    /**
     * @param boolean $complete
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;
    }

    /**
     * @return string
     */
    public function getSavedFileLocation()
    {
        return $this->savedFileLocation;
    }

    /**
     * @param string $savedFileLocation
     */
    public function setSavedFileLocation($savedFileLocation)
    {
        $this->savedFileLocation = $savedFileLocation;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $message = "";
        switch($this->getErrorCode()) {
            case "NO_UPLOAD_LOCATION":
                $message = "Please provide a upload location.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "INVALID_UPLOAD_LOCATION":
                $message = "Please provide a valid upload location.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "FILE_NOT_ALLOWED":
                $message = "Please use a valid file type.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "FILE_TOO_LARGE":
                $message = "The file you uploaded is too large.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "FILE_EXISTS":
                $message = "This file already exists.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "UPLOAD_ERROR":
                $message = "Upload error, contact an administrator.";
                $this->setErrorMessage($message);
                return $message;
                break;
            case "NO_FILE":
                $message = "Please select a file to upload";
                $this->setErrorMessage($message);
                return $message;
                break;
            default:
                return $this->errorMessage;
                break;
        }
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return boolean
     */
    public function isOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * @param boolean $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

}