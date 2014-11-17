<?php


abstract class fvComponent {

	private $_templateName = "base";
	private $_html;

	/**
	 * Render Component with some template
	 * @return string HTML code 
	 **/
	function render() {
		if( $this->_html )
			return $this->_html;

        fvSite::$Template->assign( "this", $this );
        $old_template_dir = fvSite::$Template->template_dir;
        $old_compile_dir = fvSite::$Template->compile_dir;

        fvSite::$Template->template_dir = rtrim(fvSite::$fvConfig->get( "path.components" ), "/") . "/" . $this->getComponentName() . "/";
        fvSite::$Template->compile_dir = fvSite::$fvConfig->get( "path.smarty.compile" );

        $result = fvSite::$Template->fetch( $this->getTemplateName() . ".tpl", null, crc32($this->getComponentName().$this->getTemplateName()) );

        fvSite::$Template->template_dir = $old_template_dir;
        fvSite::$Template->compile_dir = $old_compile_dir;

        return $result;
	}

	function prerender(){
        $this->_html = $this->render();
        return $this;
	}

	function __toString(){
		try{
			return $this->render();
		} catch( Exception $e ){
			if( !FV_DEBUG_MODE || !defined("FV_DEBUG_MODE") )
				return "Error while render «" . get_class() . "» Component.";

            return "<h1>{$e->getMessage()}</h1> " . StringFunctions::parseException( $e );
		}
	}

	abstract function getComponentName();

	/**
	 * Return current TemplateName
	 * @return string template name of file wich will be used while rendering component.
	 */
	public function getTemplateName() {
	    return preg_replace( "/\.tpl$/", "", trim($this->_templateName) );
	}
	
	/**
	 * Set template Name
	 * @param string $_templateName name of file wich will be used while rendering component.
	 * @return static
	 */
	public function setTemplateName($_templateName) {
	    $this->_templateName = (string)$_templateName;
	    return $this;
	}

}
