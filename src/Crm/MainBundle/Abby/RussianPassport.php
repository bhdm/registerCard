<?php
namespace Crm\MainBundle\Abby;

class RussianPassport extends Recognition{


    public function __construct(){
        parent::__construct();
        $this->filename='passport-rus.jpg';
        $this->data = array();
    }

    public function getLastName(){
        $rowNumber = 8;
        $row = $this->getRequestRow($rowNumber);
        return $this->removeTrash($row);
    }

    public function getFirstName(){
        $rowNumber = 9;
        $row = $this->getRequestRow($rowNumber);
        return $this->removeTrash($row);
    }

    public function getSurName(){
        $rowNumber = 10;
        $row = $this->getRequestRow($rowNumber);
        return $this->removeTrash($row);
    }

    public function getText(){
        $this->data[] = $this->getLastName();
        $this->data[] = $this->getFirstName();
        $this->data[] = $this->getSurName();
        return $this->data;
    }

    public function removeTrash($text, $space = false){
        $text = str_replace("^", "", $text);
        $text = str_replace(";", "", $text);
        $text = str_replace("'", "", $text);
        if ($space === true){
            $text = str_replace(" ", "", $text);
        }

        return $text;
    }
}