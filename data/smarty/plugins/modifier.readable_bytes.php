<?php

function smarty_modifier_readable_bytes($bytes)  {
    $size = $bytes / 1024;
    if($size < 1024)
        {
        $size = number_format($size, 2);
        $size .= ' KiB';
        } 
    else 
        {
        if($size / 1024 < 1024) 
            {
            $size = number_format($size / 1024, 2);
            $size .= ' MiB';
            } 
        else if ($size / 1024 / 1024 < 1024)  
            {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GiB';
            } 
        }
    return $size;
}