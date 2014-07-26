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
            '/Фамилия/','/РОССИЙСКАЯ/','/ФЕДЕРАЦИЯ/','/паспорт/','/Выдан/','/выдан/',
            '/Пол/','/Нол/','/рождения/','/Дата/','/Место/','/рождения/',
            '/„Ыд\.«/','/Отчество/','/Имя/',
            '/Личный/','/код/','/подпись/','/подразделения/' ,'/выдачи/', '/Паспорт/'
        );
//        $array2 = array('','','','','','','','','','','','','','','','','','','');
        $xml = preg_replace("/\r\n/",'',$xml);
        $xml = preg_replace($array,'',$xml);
        $xml = new \SimpleXMLElement($xml);
        $xml2 = $this->objectToArray($xml->page);
        $xml3 = array();
        foreach($xml2['line'] as $k => $val){
            if (is_array($val) || $val == null || empty($val) ){
                unset($xml2['line'][$k]);
            }else{
                $xml3[] = $xml2['line'][$k];
            }
        }
        $xml2 = $xml3;
        $xml = $xml2;
//        print_r($xml2);
//        exit;

//        $xml = array();
//        foreach ($xml2['line'] as $key => $val){
//            if (is_array($val)){
//                $xml[$key] = '';
//            }else{
//                $xml[$key] = $val;
//            }
//        }


//        var_dump($xml);
//        exit;
        $this->data['firstName']=( substr_count($xml[5],' ') > 1 ? '' : $xml[5]);
        $this->data['lastName']=( substr_count($xml[4],' ') > 1 ? '' : $xml[4]);
        $this->data['surName']=( substr_count($xml[6],' ') > 1 ? '' : $xml[6]);
        $this->data['passportPlace']=( substr_count($xml[0].$xml[1].$xml[2],' ') > 7 ? '' : $xml[0].$xml[1].$xml[2]);
        $this->data['passportDate']= preg_replace('/.*([0-9]{2}\.[0-9]{2}\.[0-9]{4}).*/','$1',implode(' ',$xml));
        $this->data['passportCode']= preg_replace('/.*([0-9]{3}[\-\~][0-9]{3}).*/','$1',implode(' ',$xml));
        $this->data['passportCode']= str_replace('~','-',$this->data['passportCode']);
        $this->data['passportNumber']= preg_replace('/.*([0-9]{2}.+[0-9]{2}.+[0-9]{6}).*/','$1', implode(' ',$xml));

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