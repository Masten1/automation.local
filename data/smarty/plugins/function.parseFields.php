<?php

    function smarty_function_parseFields( $params, &$smarty ) 
    {
        $iEntity = $params["entity"];

        $toReturn = "";       
        
        $arrayName = ( $iEntity instanceof iLocaled ) ? "data[{$iEntity->getLangVersion()}]" : "data[main]"; 
        $lang = ( $iEntity instanceof iLocaled ) ? $iEntity->getLangVersion() : "";
                      
        foreach( $iEntity->getFields() as $fieldName => $parameters )
        {

            if( $parameters["editable"] == "true" )
            {
                switch( $parameters["method"] )
                {           
                    case 'textarea':
                        $toReturn .= printTextarea( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $lang );
                        break;
                    case 'iredactor':
                        $toReturn .= printTextarea( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $lang, true );
                        break;
                    case 'checkbox':
                        $toReturn .= printCheckbox( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $lang );
                        break;
                    case 'range':
                        $toReturn .= printRange( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $lang );
                        break;
                    case 'list':
                        $toReturn .= printList( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $iEntity );
                        break;
                    case 'uploader':
                        $toReturn .= printUpload( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $iEntity );
                        break;
                    case 'date':
                        $toReturn .= printDate( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $iEntity );
                        break;
                    case 'input':
                    default:
                        $toReturn .= printInput( $arrayName, $fieldName, $iEntity->get( $fieldName ), $parameters, $lang );
                        break;
                }
            }
        }

        return $toReturn;
    }

    function printInput( $arrayName, $fieldName, $value, $parameters, $lang )
    {
        $string = "<label>{$parameters['name']}</label>";
        $string .= "<input type='text' id='{$fieldName}{$lang}' value='" . htmlentities( $value ,  true  ,"UTF-8" ) . "' name='{$arrayName}[{$fieldName}]'><br />";

        return $string;
    }  

    function printCheckbox( $arrayName, $fieldName, $value, $parameters, $lang )
    {
        $string = "<label for=''>{$parameters['name']}</label>";
        $isChecked = ( $value ) ? "checked='checked'" : '';
        $string .= "<input type='checkbox' id='{$fieldName}{$lang}' value='1' {$isChecked} name='{$arrayName}[{$fieldName}]'><br /><br />";

        return $string;
    }

    function printTextarea( $arrayName, $fieldName, $value, $parameters, $lang,  $rich = false )
    {
        $string = "<label for=''>{$parameters['name']}</label>";
        $class = ( $rich ) ? " class='redactor' style='height: 300px;' " : '';
        $string .= "<textarea id='{$fieldName}{$lang}' {$class} name='{$arrayName}[{$fieldName}]' >{$value}</textarea><br /><br />";

        return $string;
    }
    
    function printRange( $arrayName, $fieldName, $value, $parameters, $lang )
    {
        $string = "<label>{$parameters['name']}</label>";
        
        $min = intval( $parameters["range_min"] ) ? intval( $parameters["range_min"] ) : 0;
        $max = intval( $parameters["range_max"] ) ? intval( $parameters["range_max"] ) : 128;
        $selected_val = intval( $value ) ? intval( $value ) : intval( $parameters["selected"] );
        $options = "";
        
        for( $i = $min; $i <= $max; $i++ )
        {
            $selected = ( $i == $selected_val ) ? " selected = 'selected' " : "";
            $options .= "<option value='{$i}' {$selected}>{$i}</option>";
        }
        
        $string .= "<select id='{$fieldName}{$lang}' name='{$arrayName}[{$fieldName}]'>{$options}</select><br />";

        return $string;
    }  
    
    function printList( $arrayName, $fieldName, $givenValue, $parameters, $iEntity )
    {
        $string = "<label>{$parameters['name']}</label>";
        
        $list = $parameters["list"];
        $variants = $iEntity->$list();
        $lang = ( $iEntity instanceof iLocaled ) ? $iEntity->getLangVersion() : "";
         
        foreach( $variants as $key => $value )
        {
            $selected = ( $key == $givenValue ) ? " selected = 'selected' " : "";
            $options .= "<option value='{$key}' {$selected}>{$value}</option>";
        }
        
        $string .= "<select id='{$fieldName}{$lang}' name='{$arrayName}[{$fieldName}]'>{$options}</select><br />";
        
        return $string;
    }  
    
    function printUpload( $arrayName, $fieldName, $value, $parameters, $iEntity )
    {
        set( "name", $parameters['name'] );
        set( "value", $value );
        set( "arrayName", $arrayName );
        set( "fieldName", $fieldName );
        set( "token", md5( rand(100,200) ) );
                              
        set( "imagePath", $iEntity->getImagePath( "normal" , true , $fieldName ) );
        return show( "edit.upload.tpl" );    
    }    
    function printDate( $arrayName, $fieldName, $value, $parameters, $iEntity )
    {
        set( "name", $parameters['name'] );
        set( "value", $value );
        set( "arrayName", $arrayName );
        set( "fieldName", $fieldName );
        set( "token", md5( rand(100,200) ) );
        
        return show("edit.date.tpl");    
    }
    
    function set( $key, $value )
    {
        fvSite::$Template->assign($key, $value);      
    }
    
    function show( $templateName )
    {
        $old_template_dir = fvSite::$Template->template_dir;
        $old_compile_dir = fvSite::$Template->compile_dir;
        
        fvSite::$Template->template_dir = fvSite::$fvConfig->get("path.smarty.template")."templates";
        fvSite::$Template->compile_dir = fvSite::$fvConfig->get("path.smarty.compile");
        
        $result = fvSite::$Template->fetch($templateName);
        
        fvSite::$Template->template_dir = $old_template_dir;
        fvSite::$Template->compile_dir = $old_compile_dir;
        
        return $result;
    }
?>
