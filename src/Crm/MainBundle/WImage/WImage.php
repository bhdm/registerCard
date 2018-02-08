<?php
    namespace Crm\MainBundle\WImage;

    class WImage{

        static public function getResourceImage($path)
        {
            $resource = imagecreatefromjpeg($path);
            return $resource;
        }

        static public function getTmpPath($resource){
            $pathName = tempnam('/var/www/upload/tmp','img-');
            imagejpeg($resource, $pathName);
            return $pathName;
        }

        static public function cropSign( $path, $w, $h , $base = false){

            $image = self::getResourceImage($path);
            $crop = imagecreatetruecolor($w,$h);
            $white = imagecolorallocate($crop, 255, 255, 255);
            imagefill($crop, 0, 0, $white);

            $ph = imagesy($image) / $h;
            $width = imagesx($image) /$ph;
            $margin = ($w-$width)/2;
            $height = $h;
            imagecopyresized( $crop, $image, $margin, 0,0, 0, $width, $height, imagesx($image), imagesy($image) );

            $tmpPath = self::getTmpPath($crop);

            if ($base == false){
                return $tmpPath;
            }else{
                return self::imgToBase($tmpPath);
            }
        }

        static public function convertPNGto8bitPNG($path) {
            $srcimage = imagecreatefrompng($path);
            list($width, $height) = getimagesize($path);
            $img = imagecreatetruecolor($width, $height);
            $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagecolortransparent($img, $bga);
            imagefill($img, 0, 0, $bga);
            imagecopy($img, $srcimage, 0, 0, 0, 0, $width, $height);
            imagetruecolortopalette($img, false, 255);
            imagesavealpha($img, true);
            $tmpPath = self::getTmpPath($img);

            return self::imgToBase($tmpPath);
        }



        static public function imageToBlackAndWhite($path) {
            $im = imagecreatefromjpeg('/var/www/'.$path['path']);
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
            $pathName = tempnam('/var/www/upload/tmp','img-');
//        header('Content-Type: image/jpeg');
//        imagejpeg($im);
            imagejpeg($im, $pathName);
            return $pathName;
        }

        static public function imgToBase($pathName, $mimeType = 'image/jpeg'){

            if ($mimeType != 'image/jpeg'){
                if ($mimeType == 'image/png' ){
                    $image = imagecreatefrompng($pathName);
                    imagejpeg($image, $pathName);
                    imagedestroy($image);
                }elseif($mimeType == 'image/gif'){
                    $image = imagecreatefromgif($pathName);
                    imagejpeg($image, $pathName);
                    imagedestroy($image);
                }elseif( strripos($mimeType, 'bmp') !== false ){
                    $image = self::ImageCreateFromBMP($pathName);
                    imagejpeg($image, $pathName);
                    imagedestroy($image);
                }

            }


            $path= $pathName;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }

        public function imageCreateFromBMP($filename){
//Ouverture du fichier en mode binaire
            if (! $f1 = fopen($filename,"rb")) return FALSE;

//1 : Chargement des ent�tes FICHIER
            $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
            if ($FILE['file_type'] != 19778) return FALSE;

//2 : Chargement des ent�tes BMP
            $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
            $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
            if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
            $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
            $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
            $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
            $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
            $BMP['decal'] = 4-(4*$BMP['decal']);
            if ($BMP['decal'] == 4) $BMP['decal'] = 0;

//3 : Chargement des couleurs de la palette
            $PALETTE = array();
            if ($BMP['colors'] < 16777216)
            {
                $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
            }

//4 : Cr�ation de l'image
            $IMG = fread($f1,$BMP['size_bitmap']);
            $VIDE = chr(0);

            $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
            $P = 0;
            $Y = $BMP['height']-1;
            while ($Y >= 0)
            {
                $X=0;
                while ($X < $BMP['width'])
                {
                    if ($BMP['bits_per_pixel'] == 24)
                        $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
                    elseif ($BMP['bits_per_pixel'] == 16)
                    {
                        $COLOR = unpack("n",substr($IMG,$P,2));
                        $COLOR[1] = $PALETTE[$COLOR[1]+1];
                    }
                    elseif ($BMP['bits_per_pixel'] == 8)
                    {
                        $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                        $COLOR[1] = $PALETTE[$COLOR[1]+1];
                    }
                    elseif ($BMP['bits_per_pixel'] == 4)
                    {
                        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                        if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                        $COLOR[1] = $PALETTE[$COLOR[1]+1];
                    }
                    elseif ($BMP['bits_per_pixel'] == 1)
                    {
                        $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                        if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                        elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                        elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                        elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                        elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                        elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                        elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                        elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                        $COLOR[1] = $PALETTE[$COLOR[1]+1];
                    }
                    else
                        return FALSE;
                    imagesetpixel($res,$X,$Y,$COLOR[1]);
                    $X++;
                    $P += $BMP['bytes_per_pixel'];
                }
                $Y--;
                $P+=$BMP['decal'];
            }

//Fermeture du fichier
            fclose($f1);

            return $res;
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

    }