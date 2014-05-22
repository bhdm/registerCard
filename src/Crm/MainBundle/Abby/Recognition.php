<?php
namespace Crm\MainBundle\Abby;

class Recognition{

    protected $applicationId;

    protected $password;

    protected $filename;

    protected $path;

    protected $error; // Тут массив ошибок

    public function __construct(){
        $this->applicationId = 'taxoCard';
        $this->password = 'ILCL63gtPIQP7GOXDlqgHS4F';
    }

    /**
     * @param mixed $applicationId
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return mixed
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getXml($file = NULL){

    }


}