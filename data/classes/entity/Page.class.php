<?php

    class Page extends fvRoot  {

        const LAYOULT_MAIN = "main.tpl";
        const LAYOULT_INNER = "login.tpl";
        private $layoults = array(  self::LAYOULT_MAIN => "Шаблон главной страницы" /*, self::LAYOULT_INNER => "Шаблон внутренних страниц"*/  );
        
        
        static function getEntity(){ return __CLASS__; }

        function validateName($value) {
            $valid = (strlen($value) > 0) && (strtolower($value) !== "default");
            $this->setValidationResult('name', $valid);
            return $valid;

        }

        function validateUrl($value) {
            $valid = (strlen($value) > 0);
            $this->setValidationResult('url', $valid);
            return $valid;

        }

        function getJS(){
            if( strlen($this->js) )
                return explode('|', $this->js);
            else
                return Array();
        }

        function getCSS(){
            if( strlen($this->css) )
                return explode('|', $this->css);
            else
                return Array();
        }

        public function delete() {
            $childPages = Page::getManager()->getByparentId($this->getPk());

            foreach ($childPages as $childPage) {
                $childPage->parentId = 0;
                $childPage->save();
            }

            parent::delete();
        }

        function getPageContent() {
            try {
                if (!$dom = @DOMDocument::loadXML((string)$this->content)) {
                    $dom = new DOMDocument("1.0", fvSite::$fvConfig->get("encoding"));

                    $page = $dom->createElement("page");
                    $page->setAttribute("id", md5(microtime()));

                    $page = $dom->appendChild($page);

                    return $dom->saveXML();
                } else return (string)$this->content;
            }
            catch (Exception $e) {
                var_dump($e->getMessage());
            }
        }
        
        public function __toString() {
            return (string)$this->name;
        }
        
        public function getLayoults()
        {
            return $this->layoults;
        }
        
        public function getLayoltList(){
            $list = Horde_Yaml::loadFile( fvSite::$fvConfig->get("path.application.frontend.config") . "template.yml" );
            $result = array();
            foreach( $list["template"] as $templateName => $template ){
                $result[$templateName] = $template["name"];
            }
            return $result;
        }

        public function showBreadcrumbs( &$array ) {
            if(!$this->parentId->get()) {
                    $elem = "<a href='/'>Главная</a>";
                    return implode(' | ', array_unshift($array, $elem));
            }
            else
                if(!is_array($array)) $array = array();
                $array[] = "<a href='".$this->url->get()."'>{$this->name->get()}</a>";
                return $this->parent->showBreadcrumbs($array);
        }
    }