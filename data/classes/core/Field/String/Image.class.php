<?php

class Field_String_Image extends Field_String_File {

    public $acceptedTypes = "*.jpg;*.jpeg;*.gif;*.png;";

    private $width  = 0;
    private $height = 0;

    function __construct(array $fieldSchema, $name) {
        parent::__construct($fieldSchema, $name);

        if (isset($fieldSchema["width"])) {
            $this->width = (int) $fieldSchema["width"];
        }

        if (isset($fieldSchema["height"])) {
            $this->height = (int) $fieldSchema["height"];
        }
    }

    public function getAspectRatio(){
        if( $this->width * $this->height > 0 )
            return $this->width/$this->height;
        return 0;
    }

    public function getRealPath($web) {
        return ( $web ) ? fvSite::$fvConfig->get("path.upload.web_images") :
            fvSite::$fvConfig->get("path.upload.images");
    }

    function getEditMethod() {
        return self::EDIT_METHOD_UPLOAD_IMAGE;
    }

    public function __toString() {
        return $this->thumb();
    }

    /**
     * Формирует имя тумбочки
     * @param int $wigth
     * @param int $height
     * @return string
     */
    private function thumbName($width = null, $height = null, $type = null, $web = true) {
        $info = pathinfo($this->get());

        $base = $info["filename"];
        $base .= ( $width )  ? "_w{$width}" : "";
        $base .= ( $height ) ? "_h{$height}" : "";
        $base .= ( $type )   ? "_m{$type}" : "";

        $base .= "." . $info["extension"];
        return $base;
    }

    /** адрес тумбочки */
    public function thumb($web = true, $width = null, $height = null, $type = null ) {
        try {
            return $this->thumbPath($web, $width, $height, $type);
        }
        catch (EImageNoSourceException $e) {
            if ($web)
                return fvSite::$fvConfig->get("path.noImage");
            else
                return false;
        }
        catch (EImageException $e) {
            $realPath = $e->getMessage();
            $a = fvMediaLib::createThumbnail($this->thumbPath(false),
                                             $realPath,
                                             Array("width"       => $width,
                                                   "height"      => $height,
                                                   "resize_type" => $type ) );
            return $this->thumb($web, $width, $height, $type);
        }
    }

    /** путь к тумбочке */
    private function thumbPath($web = true, $width = null, $height = null, $type = null ) {
        if (!$this->checkSource())
            throw new EImageNoSourceException();

        $dir = $this->getRealPath($web);
        $dirReal = $this->getRealPath(false);
        $file = $this->thumbName($width, $height, $type, $web);
        if (!file_exists($dirReal . $file))
            throw new EImageException($dirReal . $file);

        return $dir . $file;
    }

    /** удалить все файлы */
    public function delete( $fileBase = null ) {
        $fileBase = ( $fileBase ) ? $fileBase : $this->get();
        if ( empty( $fileBase ) )
            return false;
        if ( parent::delete( $fileBase ) ) {
            $filePhrase = pathinfo( $fileBase );
            $files = glob( $this->getRealPath( false ) . $filePhrase[ "filename" ] . "*" );
            foreach ( $files as $file ) {
                @unlink( $file );
            }
            return true;
        }
        return false;
    }

    /**
     * Ну это совсем пиздец какой-то
     * @todo Запилить какой-то виджет для полей(?)
     */
    public function asAdorned(){
        return sprintf( "<img class='backend_img' src='%s'/>", (string)$this );
    }
}