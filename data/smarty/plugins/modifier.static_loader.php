<?php

function smarty_modifier_static_loader($link)
{
    return new StaticResourceLoader($link);
}
