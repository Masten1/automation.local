<?php
/**
 * Smarty plugin
 * @package fvCore
 * @subpackage plugins
 */


/**
 * Smarty cat modifier plugin
 *
 * Type:     modifier
 * Date:     Nov 9, 2011
 * Input:    string to catenate
 * Example:  {"Some text that wants to translate|translate:"keyInFvDictionary"}
 * @author   Sancha
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_translate( $string, $key )
{
    if( empty($key) )
        throw new Exception("Smarty midifier translate requires key parameter");
    
    return fvDictionary::getInstance()->get( $key, $string );
}