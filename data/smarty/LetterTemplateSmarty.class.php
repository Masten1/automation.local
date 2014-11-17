<?php
include_once(FV_ROOT.'smarty/smarty.class.php');
include_once(FV_ROOT.'smarty/Smarty_Compiler.class.php');
class LetterTemplateSmarty extends Smarty {
    /**
     * The class used for compiling templates.
     *
     * @var string
     */
    var $compiler_class        =   'LetterTemplateSmartyCompiler';

    function _get_compile_path($resource_name)
    {
        return $this->_get_auto_filename($this->compile_dir, null,
            $this->_compile_id) . '.php';
    }

    function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {
        throw new LetterTemplateSmartyException($error_msg, $error_type);
    }

    /**
     * trigger Smarty plugin error
     *
     * @param string $error_msg
     * @param string $tpl_file
     * @param integer $tpl_line
     * @param string $file
     * @param integer $line
     * @param integer $error_type
     */
    function _trigger_fatal_error($error_msg, $tpl_file = null, $tpl_line = null,
                                  $file = null, $line = null, $error_type = E_USER_ERROR)
    {
        throw new LetterTemplateSmartyException($error_msg, $error_type);
    }
}

class LetterTemplateSmartyCompiler extends Smarty_Compiler {
    function _trigger_fatal_error($error_msg, $tpl_file = null, $tpl_line = null,
                                  $file = null, $line = null, $error_type = E_USER_ERROR)
    {
        throw new LetterTemplateSmartyException($error_msg, $error_type);
    }
}

class LetterTemplateSmartyException extends Exception {}

