<?php

    function smarty_function_parseFilter( $params , &$smarty ) 
    {
        $iEntity = $params[ "entity" ];

        $module = $smarty->_tpl_vars["module"];
        $action = $_REQUEST[ "action" ];


        $arrayName = "filter";
        $return  = array();
    
        $fields = array_merge_recursive( $iEntity->getAllForeignFields() , $iEntity->getAllFields() );

        foreach( $fields as $fieldName => $parameters )
        {
            $temp = array();
            if( $parameters["filter"] == "true" )
            {
                $temp = array( "field" => $fieldName , "name" => $parameters["name"], "foreign" => true , "view" => "filter" );
                
                
                if( method_exists(  $entity , $parameters[ "fmethod" ] )  )
                    $temp[ "call_method" ] = $parameters[ "fmethod" ];
                elseif(  $parameters[ "fmethod" ] )
                    $temp[ "method" ] = $parameters[ "fmethod" ];
            }
            if( count($temp ) )
                $return[] = $temp;
        } 


        



        set( "module", $module );
        set( "action", $action );
        set( "arrayName", $arrayName );
        set( "fields", $return );

        set( "formFilter" , $toReturn  );
        return show( "filter.tpl" ); 
    }
?>
