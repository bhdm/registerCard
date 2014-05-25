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
        $xml = $this->xml;
        $xml  =  preg_replace('/<charParams( [^>]+)?>(.*)<\/charParams>/isU', '$2', $xml);
        $xml  =  preg_replace('/<formatting( [^>]+)?>(.*)<\/formatting>/isU', '$2', $xml);
        $xml  =  preg_replace('/<par( [^>]+)?>(.*)<\/par>/isU', '$2', $xml);
        $xml  =  preg_replace('/<block( [^>]+)?>(.*)<\/block>/isU', '$2', $xml);
        $xml  =  preg_replace('/<text( [^>]+)?>(.*)<\/text>/isU', '$2', $xml);
        $xml  =  preg_replace('/<region( [^>]+)?>(.*)<\/region>/isU', '', $xml);
        $xml  =  preg_replace('/<separator( [^>]+)?>(.*)<\/separator>/isU', '', $xml);
        $array = array('/Фамилия/','/РОССИЙСКАЯ ФЕДЕРАЦИЯ/','/паспорт/','/Выдан/','/Пол/','/Нол/','/рождения/','/Дата/','/Место/','/рождения/','/„Ыд\.«/','/Отчество/','/Имя/');
        $array2 = array('','','','','','','','','','','','','');
        $xml = preg_replace("/\r\n/",'',$xml);
        $xml = preg_replace($array,$array2,$xml);
        $xml = new \SimpleXMLElement($xml);

        $xml2 = $this->objectToArray($xml->page);

        $xml = array();
        foreach ($xml2['line'] as $key => $val){
            if (is_array($val)){
                $xml[$key] = '';
            }else{
                $xml[$key] = $val;
            }
        }

//        $xml = $xml2['line'];
        $this->data['firstName']=$xml[6];
        $this->data['lastName']=$xml[7];
        $this->data['surName']=$xml[8];
        $this->data['passportPlace']=$xml[0].$xml[1].$xml[2].$xml[3];
        $this->data['passportDate']= preg_replace('/.*([0-9]{2}\.[0-9]{2}\.[0-9]{4}).*/','$1',$xml[4]);
        $this->data['passportCode']= preg_replace('/.*([0-9]{3}\-[0-9]{3}).*/','$1',$xml[4]);
        $this->data['passportNumber']= preg_replace('/.*([0-9]{2} [0-9]{2} [0-9]{6}).*/','$1',$xml[13]);

        echo '<pre>';
        print_r($this->data);
        echo '</pre>';
        exit;
        return $xml;
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

    public function objectToArray($object)
    {
        if (is_object($object))
        {
            $object = get_object_vars($object);
        }

        if (is_array($object))
        {
            return array_map( array($this, __FUNCTION__), $object );
        }
        else
        {
            return $object;
        }
    }
}