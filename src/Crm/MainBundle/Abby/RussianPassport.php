<?php
namespace Crm\MainBundle\Abby;

class RussianPassport extends Recognition{

    public function getText(){
        $xml = $this->xml;
        $xml  =  preg_replace('/<charParams( [^>]+)?>(.*)<\/charParams>/isU', '$2', $xml);
        $xml  =  preg_replace('/<formatting( [^>]+)?>(.*)<\/formatting>/isU', '$2', $xml);
        $xml  =  preg_replace('/<par( [^>]+)?>(.*)<\/par>/isU', '$2', $xml);
        $xml  =  preg_replace('/<block( [^>]+)?>(.*)<\/block>/isU', '$2', $xml);
        $xml  =  preg_replace('/<text( [^>]+)?>(.*)<\/text>/isU', '$2', $xml);
        $xml  =  preg_replace('/<region( [^>]+)?>(.*)<\/region>/isU', '', $xml);
        $xml  =  preg_replace('/<separator( [^>]+)?>(.*)<\/separator>/isU', '', $xml);
        $array = array(
            '/Фамилия/','/РОССИЙСКАЯ/','/ФЕДЕРАЦИЯ/','/паспорт/','/Выдан/',
            '/Пол/','/Нол/','/рождения/','/Дата/','/Место/','/рождения/',
            '/„Ыд\.«/','/Отчество/','/Имя/',
            '/Личный/','/код/','/подпись/','подразделения'
        );
        $array2 = array('','','','','','','','','','','','','','','','','','','');
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

        $this->data['firstName']=$xml[8];
        $this->data['lastName']=$xml[7];
        $this->data['surName']=$xml[9];
        $this->data['passportPlace']=$xml[2].$xml[3].$xml[4];
        $this->data['passportDate']= preg_replace('/.*([0-9]{2}\.[0-9]{2}\.[0-9]{4}).*/','$1',implode(' ',$xml));
        $this->data['passportCode']= preg_replace('/.*([0-9]{3}[\-\~][0-9]{3}).*/','$1',implode(' ',$xml));
        $this->data['passportCode']= str_replace('~','-',$this->data['passportCode']);
        $this->data['passportNumber']= preg_replace('/.*([0-9]{2} [0-9]{2} [0-9]{6}).*/','$1', implode(' ',$xml));

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