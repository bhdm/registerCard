<?php
namespace Crm\MainBundle\Abby;

class RussianPassport extends Recognition{

    public function __construct(){
        parent::__construct();
        $this->filename='passport-rus.jpg';
    }

    public function getRow($numRow = 0){
        if ($this->xml == NULL ){
            return 'error: XML Не получен';
        }

        $xml = $this->xml;
        $xml = new \SimpleXMLElement($xml);
        $txt = '';
        foreach ( $xml->page->block[$numRow]->text->par->line->formatting->charParams as $charset){
            //if (is_string($charset)){
                $txt .= $charset;
            //}
        }

        return $txt;
    }
}