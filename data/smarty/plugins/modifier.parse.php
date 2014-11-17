<?php

function smarty_modifier_parse( $object, $type, $param = null ) {
    if ( is_null( $object ) )
        throw new Exception( "Can't parse NULL." );

    switch ( $type ) {
        case 'edit':
            if ( !$object instanceof fvRoot )
                throw new Exception( "Can not parse table entity for " . get_class( $object ) . " class. Expect fvRoot class." );

            $fields = array( );
            if ( $param ) {
                $object->setLanguage( $param );
                $section = ( string ) $param->code;
                foreach ( $object->getFields() as $key => $field ) {
                    if ( $field->isLanguaged() )
                        $fields[ $key ] = $field;
                }
            } else {
                foreach ( $object->getFields() as $key => $field ) {
                    if ( !$field->isLanguaged() )
                        $fields[ $key ] = $field;
                }
                $section = 'main';
            }

            fvSite::$Template->assign( "fields", $fields );
            fvSite::$Template->assign( "section", $section );
            fvSite::$Template->assign( "entity", $object );

            break;
        case 'table':
            if ( !$object instanceof fvPager )
                throw new Exception( "Can not parse table entity for " . get_class( $object ) . " class. Expect fvPager class." );

            parse_str( $_SERVER[ 'QUERY_STRING' ], $arr );

            foreach ( $arr as $key => $val ) {
                if ( substr( $key, 0, 2 ) == '__' ) {
                    unset( $arr[ $key ] );
                }
            }
            unset( $arr[ 'sort' ] );
            unset( $arr[ 'order' ] );


            fvSite::$Template->assign( "queryString", http_build_query( $arr ) );
            fvSite::$Template->assign( "entityFields", $object->getEntity()->getFields( null, "listable" ) );
            fvSite::$Template->assign( "collection", $object );

            break;
        case 'filter':
            if ( !$object instanceof fvPager )
                throw new Exception( "Can not parse table entity for " . get_class( $object ) . " class. Expect fvPager class." );

            if ( !$param[ 'type' ] || !is_array( $param[ 'fields' ] ) )
                return false;

            //fvSite::$Template->assign("entityFields", $object->getEntity()->getFields());
            fvSite::$Template->assign( "collection", $object );
            fvSite::$Template->assign( "fields", $param[ 'fields' ] );
            fvSite::$Template->assign( "type", strtolower( $param[ 'type' ] ) );

            break;
        default:
            throw new Exception( "Unknown parse type '{$type}'" );
    }

    $old_template_dir = fvSite::$Template->template_dir;
    $old_compile_dir = fvSite::$Template->compile_dir;

    fvSite::$Template->template_dir = fvSite::$fvConfig->get( "path.smarty.template" ) . "templates";
    fvSite::$Template->compile_dir = fvSite::$fvConfig->get( "path.smarty.compile" );

    $result = fvSite::$Template->fetch( "parse." . $type . ".tpl" );

    fvSite::$Template->template_dir = $old_template_dir;
    fvSite::$Template->compile_dir = $old_compile_dir;

    return $result;
}