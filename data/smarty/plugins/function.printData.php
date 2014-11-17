<?php

    function smarty_function_printData( $params, &$smarty ) 
    {
        $field = $params["field"];

        set( "name", $field["name"] );
        set( "fieldName" , $field["field"]  );
        set( "options" , $field[ "options" ]  );
        set( "entity" , $params[ "entity" ] );
        
        if( $params["entity"] )
            set( "path" , strtolower( get_class( $params[ "entity" ] ) ) );
                           
        return show( $field[ "view" ] . "." .  $field[ "method" ]  . ".tpl" ); 
    }


?>
