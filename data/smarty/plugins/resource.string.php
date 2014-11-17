<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dmitry Kukarev
 * Date: 11.05.12
 * Time: 16:57
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 */
function smarty_resource_string_source($tpl_name, &$tpl_source, &$smarty)
{
    // do database call here to fetch your template,
    // populating $tpl_source with actual template contents
    $tpl_source = $tpl_name;
    // return true on success, false to generate failure notification
    return true;
}

function smarty_resource_string_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    // do database call here to populate $tpl_timestamp
    // with unix epoch time value of last template modification.
    // This is used to determine if recompile is necessary.
    $tpl_timestamp = false; // this example will always recompile!
    // return true on success, false to generate failure notification
    return true;
}

function smarty_resource_string_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_string_trusted($tpl_name, &$smarty)
{
    // not used for templates
}