<?php
function smarty_modifier_date_human( $string, $language, $showTime = false )
{
   return fvDate::getReadableDate( $string, $language, $showTime );
}
?>
