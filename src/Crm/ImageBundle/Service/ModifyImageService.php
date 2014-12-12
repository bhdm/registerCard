<?php
/**
 * Created by PhpStorm.
 * User: bhd.m@ya.ru
 * Date: 12.12.14
 * Time: 18:54
 */
namespace Crm\MainBundle\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\TwigBundle\TwigEngine as Templating;

class ModifyImageService
{

    protected $error = array
    (
        0 => 'Ошибка загрузки или обработки изображения'
    );

    protected $resource;  #Ресурс

    public function __construct($filePath,$imageMimeType){
        $size = getimagesize($filePath);
        try{
            $this->resource = imagecreatefromstring($filePath);
        }catch(Exception $e){
            $this->resource = $this->error(0);
        }



        $this->resource = imagecreatetruecolor($size[0],$size[1]);
    }


    public function error($error){
        $im  = imagecreatetruecolor(150, 30);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 1, 5, 5, $this->error[$error], $tc);
        $this->resource = $im;

        return $this;
    }

    public function rotate($degree){}

    public function crop($x,$y,$x2,$y2){}

    /**
     * @param $filter ( BINARY | BLACKANDWHITE )
     */
    public function filter($filter){}

    public function getPath(){}

    public function get(){}
}

