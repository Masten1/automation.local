<?php

    function smarty_function_parseTable($params, &$smarty) {

        $collection = $params["collection"];

        $entity = $collection->getEntity();
        $path = fvSite::$fvConfig->get('dir_web_root') . $params["path"];


        $lang = ( $entity instanceof iLocaled ) ? $entity->getLangVersion() : "";

        $allFields = array_merge_recursive(  $entity->getAllForeignFields() ,  $entity->getAllFields() );

        $tableHeaderField = array();

        foreach( $allFields as $fieldName => $parameters ) {
            $temp = array();
            if( $parameters["listable"] == "true" ) {
                $temp =  array( "field" => $fieldName , "name" => $parameters[ "name" ] , "is_sortable" => $parameters["is_sortable"] , "view" => "table" );
                if( method_exists(  $entity , $parameters[ "tmethod" ] )  )
                    $temp[ "call_method" ] = $parameters[ "tmethod" ];
                elseif(  $parameters[ "tmethod" ] )
                    $temp[ "method" ] = $parameters[ "tmethod" ];

            }
            if( count( $temp ))
             $tableHeaderField[] = $temp;
        }


        set( "collection", $collection );
        set( "tableFields", $tableHeaderField );
        set( "ajax", $ajax );
        set( "token", md5( rand(100,200) ) );

        return show("table.tpl"); 
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