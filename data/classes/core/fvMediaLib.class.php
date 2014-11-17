<?php

class fvMediaLib {
    const THUMB_WIDTH = 1;
    const THUMB_SQUADRE = 2;
    const THUMB_HEIGHT = 3;
    const THUMB_SQUARE = 4;
    const THUMB_OUTER = 5;
    const THUMB_EXACT = 6;
    const THUMB_WATERMARK = 7;

    /**
     * Method to create Image Thumbnail (use gd library) allowed Images - image/gif, image/jped and image/png
     *
     * @param string $srcFileName - sourse Image Name
     * @param string $destFileName - destination Image Name
     * @param array $params
     */
    public static function createThumbnail($srcFileName, $destFileName, $params = array()) {

        $allowedTypes = array('IMAGETYPE_GIF', 'IMAGETYPE_JPEG', 'IMAGETYPE_PNG');

        $default_type = fvSite::$fvConfig->get('images.default_type', 'normal');

        if (!empty($params['type']))
            $default_type = $params['type'];

        if (!empty($params['width']))
            $width = $params['width'];
        else
            $width = (int) fvSite::$fvConfig->get("images.{$default_type}.width");

        if (!empty($params['height']))
            $height = $params['height'];
        else
            $height = (int) fvSite::$fvConfig->get("images.{$default_type}.height");

        if (!empty($params['resize_type']))
            $type = $params['resize_type'];
        else
            $type = self::THUMB_WATERMARK;

        list($orig_width, $orig_height, $orig_type) = getimagesize($srcFileName);

        $sourceOffsetX = (!empty($params['offsetX']) ) ? intval($params['offsetX']) : 0;
        $sourceOffsetY = (!empty($params['offsetY']) ) ? intval($params['offsetY']) : 0;

        switch ($type) {
            case self::THUMB_WATERMARK:
                $width = $orig_width;
                $height = $orig_height;
                break;

            case self::THUMB_WIDTH:
                if ($orig_width > $width) {
                    $ratio = ($width / $orig_width);
                    $height = round($orig_height * $ratio);
                }
                else {
                    $width = $orig_width;
                    $height = $orig_height;
                }
                break;
            case self::THUMB_HEIGHT:
                if ($orig_height > $height) {
                    $ratio = ($height / $orig_height);
                    $width = round($orig_width * $ratio);
                }
                else {
                    $width = $orig_width;
                    $height = $orig_height;
                }
                break;
            case self::THUMB_SQUADRE:
                if ($width > $height) {
                    $val = "width";
                    $aval = "height";
                }
                else {
                    $val = "height";
                    $aval = "width";
                }

                if (${'orig_' . $val} > ${$val}) {
                    $ratio = (${$val} / ${'orig_' . $val});
                    ${$aval} = round(${'orig_' . $aval} * $ratio);
                }
                else {
                    $width = $orig_width;
                    $height = $orig_height;
                }
                break;
            case self::THUMB_SQUARE:
                $value = ( $height > $width ) ? $width : $height;

                if ($orig_height > $orig_width) {
                    $ratio = ($value / $orig_height);
                    $width = round($orig_width * $ratio);
                }
                else {
                    $ratio = ($value / $orig_width);
                    $height = round($orig_height * $ratio);
                }
                break;
            case self::THUMB_OUTER:
                if (( $orig_width / $orig_height ) > ( $width / $height )) {
                    $orig_width_new = $width * ( $orig_height / $height );
                    $sourceOffsetX = round(( $orig_width - $orig_width_new) / 2);
                    $orig_width = $orig_width_new;
                }
                else {
                    $orig_height_new = $height * ( $orig_width / $width );
                    $sourceOffsetY = round(( $orig_height - $orig_height_new) / 2);
                    $orig_height = $orig_height_new;
                }
                break;
            case self::THUMB_EXACT:
                $orig_width = $width;
                $orig_height = $height;
                break;
            default:
                return true;
                break;
        }

        $origFileExt = '';

         if ($width == $orig_width && $height == $orig_height && !$sourceOffsetX && !$sourceOffsetY && $type != self::THUMB_WATERMARK) {
            copy($srcFileName, $destFileName);
            return true;
        }

        foreach ($allowedTypes as $allowedType) {
            if (defined($allowedType) && (constant($allowedType) == $orig_type)) {
                $origFileExt = strtolower(substr($allowedType, strpos($allowedType, "_") + 1));
            }
        }

        if (!function_exists($functionName = "imagecreatefrom" . $origFileExt)) {
            return false;
        }

        if (($srcImage = call_user_func($functionName, $srcFileName)) === false)
            return false;

        if( $type == self::THUMB_WATERMARK ) {
            $wm = imagecreatefrompng( fvSite::$fvConfig->get("tech_web_root"). '/images/wm.png');

            imagealphablending($wm, false);
            imagesavealpha($wm, true);

            $wmWidth = imagesx( $wm );
            $wmHeight = imagesy ( $wm );

            $left = ($width-$wmWidth)/2;
            $top = ($height-$wmHeight)/2;

            imagecopyresampled($srcImage, $wm, $left, $top, 0, 0, $wmWidth, $wmHeight, $wmWidth, $wmHeight );
            imagedestroy( $wm );
        }

        imagealphablending($srcImage, false);
        imagesavealpha($srcImage, true);

        if (($dstImage = imagecreatetruecolor($width, $height)) === false)
            return false;

        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);

        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $width, $height, $transparent);

        imagecopyresampled($dstImage, $srcImage, 0, 0, $sourceOffsetX, $sourceOffsetY, $width, $height, $orig_width, $orig_height);

        if (!function_exists($functionName = "image" . $origFileExt)) {
            return false;
        }

//        header("Content-Type: " . image_type_to_mime_type($orig_type));
        if (call_user_func($functionName, $dstImage, $destFileName) === false)
            return false;

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return true;
    }

    static function setTransparency($new_image, $image_source) {
        $transparencyIndex = imagecolortransparent($image_source);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0)
            $transparencyColor = imagecolorsforindex($image_source, $transparencyIndex);

        $transparencyIndex = imagecolorallocate($new_image, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
        imagefill($new_image, 0, 0, $transparencyIndex);
        imagecolortransparent($new_image, $transparencyIndex);
    }

    public static function calcDementions($srcFileName, $params=array()) {
        if (!empty($params['type']))
            $default_type = $params['type']; else
            $default_type = fvSite::$fvConfig->get('images.default_type', 'normal');
        if (!empty($params['width']))
            $width = $params['width']; else
            $width = (int) fvSite::$fvConfig->get("images.{$default_type}.width");
        if (!empty($params['height']))
            $height = $params['height']; else
            $height = (int) fvSite::$fvConfig->get("images.{$default_type}.height");
        if (!empty($params['resize_type']))
            $type = $params['resize_type']; else
            $type = (int) fvSite::$fvConfig->get("images.{$default_type}.type");

        list($orig_width, $orig_height, $orig_type) = getimagesize($srcFileName);
        switch ($type) {
            case self::THUMB_WIDTH:
                if ($orig_width > $width) {
                    $ratio = ($width / $orig_width);
                    $height = (int) round($orig_height * $ratio);
                }
                else {
                    $width = $orig_width;
                    $height = $orig_height;
                }
                break;
            case self::THUMB_SQUADRE:

                if ($width > $height) {
                    $val = "width";
                    $aval = "height";
                }
                else {
                    $val = "height";
                    $aval = "width";
                }

                if (${'orig_' . $val} > ${$val}) {
                    $ratio = (${$val} / ${'orig_' . $val});
                    ${$aval} = (int) round(${'orig_' . $aval} * $ratio);
                }
                else {
                    $width = $orig_width;
                    $height = $orig_height;
                }
                break;
            default:
                return false;
                break;
        }

        return array($width, $height, $orig_type, "width=\"$width\" height=\"$height\"");
    }

    /**
     * Returns path to temporal file
     * @param mixed $fileName
     * @param mixed $real is path absolute or real
     * @returns string
     */
    public static function getTemporalFile($fileName, $real=true) {
        $pathToDirectory = ( $real ) ? fvSite::$fvConfig->get("path.upload.temp_image") : fvSite::$fvConfig->get("path.upload.web_temp_image");
        return $pathToDirectory . $fileName;
    }

    function doResize($inputfile, $outputfile, $new_width, $new_height) {
        $foto = $inputfile;
        $destfile = $outputfile;
        if (!is_file($foto))    //file does not exist
            return false;

        $size = getimagesize("$foto");
        if (!$size) //getimagesize fаiled
            return false;

        $width = $size[0];
        $height = $size[1];
        //            echo "[$width $height]<br>";

        if (($width > $new_width) || ($height > $new_height)) {
            if ($width / $height > $new_width / $new_height) {
                $nw = $new_width;
                $nh = $height / $width * $new_width;
                $dst_x = 0;
                $dst_y = ($new_height - $nh) / 2;
                $dst_w = $new_width;
                $dst_h = $nh;
            }
            else {
                $nh = $new_height;
                $nw = $width / $height * $new_height;
                $dst_y = 0;
                $dst_x = ($new_width - $nw) / 2;
                $dst_h = $new_height;
                $dst_w = $nw;
            }

            $srcImage = ImageCreateFromJPEG($foto);

            $destWidth = $new_width;
            $destHeight = $new_height;
            $destImage = imagecreatetruecolor($destWidth, $destHeight);
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefill($destImage, 0, 0, $white);
            imagecopyresampled($destImage, $srcImage, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $width, $height);

            //            echo " $dst_x $dst_y $dst_w $dst_h <br>";
            ImageJPEG($destImage, $destfile);
        }
        else {
            $dst_x = ($new_width - $width) / 2;
            $dst_y = ($new_height - $height) / 2;



            $srcImage = ImageCreateFromJPEG($foto);

            $destWidth = $new_width;
            $destHeight = $new_height;
            $destImage = imagecreatetruecolor($destWidth, $destHeight);
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefill($destImage, 0, 0, $white);
            imagecopyresampled($destImage, $srcImage, $dst_x, $dst_y, 0, 0, $width, $height, $width, $height);

            //            echo " $dst_x $dst_y $dst_w $dst_h <br>";
            ImageJPEG($destImage, $destfile);
        }





        return true;
    }

}

?>
