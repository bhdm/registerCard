<?php
    namespace Crm\MainBundle\WImage;

    class WImage{

        static public function getResourceImage($path)
        {
            $resource = imagecreatefromjpeg($path);
            return $resource;
        }

        static public function getTmpPath($resource){
            $pathName = tempnam('/tmp','img-');
            imagejpeg($resource, $pathName);
            return $pathName;
        }


        static public function cropSign_591_117($path){

            $image = self::getResourceImage($path);
            $crop = imagecreatetruecolor(591,118);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / 118;
            $width = imagesx($image) /$ph;
            $margin = (591-$width)/2;
            $height = 118;
            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

            return self::getTmpPath($crop);
        }

        static public function cropSign( $path, $w, $h ){

            $image = self::getResourceImage($path);
            $crop = imagecreatetruecolor($w,$h);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / $h;
            $width = imagesx($image) /$ph;
            $margin = ($w-$width)/2;
            $height = $h;
            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

            return self::getTmpPath($crop);
        }

        static public function cropSign_285_145($path){

            $image = self::getResourceImage($path);
            $crop = imagecreatetruecolor(285,145);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / 145;
            $width = imagesx($image) /$ph;
            $margin = (285-$width)/2;
            $height = 145;
            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

            return self::getTmpPath($crop);
        }

        public function ImageToBlackAndWhite($path) {
            $im = imagecreatefromjpeg(__DIR__.'/../../../../web/'.$path['path']);
            for ($x = imagesx($im); $x--;) {
                for ($y = imagesy($im); $y--;) {
                    $rgb = imagecolorat($im, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8 ) & 0xFF;
                    $b = $rgb & 0xFF;
                    $gray = ($r + $g + $b) / 3;
                    if ($gray < 0xD0) {

                        imagesetpixel($im, $x, $y, 0xFFFFFF);
                    }else
                        imagesetpixel($im, $x, $y, 0x000000);
                }
            }

            imagefilter($im, IMG_FILTER_NEGATE);
            $pathName = tempnam('/tmp','img-');
//        header('Content-Type: image/jpeg');
//        imagejpeg($im);
            imagejpeg($im, $pathName);
            return $pathName;
        }


    }