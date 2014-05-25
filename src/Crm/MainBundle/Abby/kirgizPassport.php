<?php
namespace Crm\MainBundle\Abby;

class Driver2 extends Recognition{


    public function __construct(){
        parent::__construct();
        $this->filename='kirgizPassport.jpg';
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
//        $xml  =  preg_replace('/.*([0-9]{3}-[0-9]{3}-[0-9]{3}-[0-9]{2}).*/', '$1', $xml);

        echo $xml;
        exit;


        $this->data['driverDate1']=  preg_replace('/.*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*/is', '$2', $xml);
        $this->data['driverDate2']=  preg_replace('/.*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*([0-9]{2}[\.\-][0-9]{2}[\.\-][0-9]{4}).*/is', '$3', $xml);
        $this->data['driverNumber']= preg_replace('/.*([0-9]{2}.*[0-9]{6}).*/', '$1', $xml);

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