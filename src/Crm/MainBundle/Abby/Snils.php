<?php
namespace Crm\MainBundle\Abby;

class Snils extends Recognition{


    public function __construct(){
        parent::__construct();
        $this->filename='snils.jpg';
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
        $xml  =  preg_replace('/.*([0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{2}).*/', '$1', $xml);

        $this->data['snils']= $xml;

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