<?php
namespace Crm\MainBundle\Abby;

class TadjikPassport extends Recognition{


    public function __construct(){
        parent::__construct();
        $this->filename='tadjik.png';
        $this->data = array();
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
        $xml = preg_replace("/\r\n/",'',$xml);
        $array = array(
            '/ФЕДЕРАЛЬНАЯ/','/МИГРАЦИОННАЯ/','/РАЗРЕШЕНИЕ/','/НА/','/РАБОТУ/','/ИНОСТРАННОМУ/','/ЛИЦУ/','/БЕЗ/','/ГРАЖДАНСТВА/',
            '/Серия/','/Фамилия/','/Дата рождения/','/ФЕДЕРАЛЬНАЯ/','/Гражданство/','/Гражданство/','/М И ГРАЦИОНЯ С Л У Ж Б А/',
            '/СЛУЖБА/','/Г ражданство/','/Действительно до/','/Действительно/','/Имя/','/ГРАЖДАНИНУ/','/ИЛИ/'
        );
        $array2 = array('','','','','','');

        $xml = preg_replace($array,$array2,$xml);

//        $xml  =  preg_replace('/.*([0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{2}).*/', '$1', $xml);

        echo $xml;
        exit;

        $this->data['PassportBirthDay']=  preg_replace('/.*([0-9]{2}\s[0-9]{2}\s[0-9]{3,4}).*/', '$1', $xml);
        $this->data['PassportNumber'] =   preg_replace('/.*([0-9]{14}).*/is', '$1', $xml);

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

        $this->data['lastName'] = $xml[3];
        $this->data['firstName'] = $xml[4];



        echo '<pre>';
        print_r($this->data);
        echo '</pre>';
        exit;
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