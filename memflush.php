<?php
$memCacheHandler = new Memcache();
$memCacheHandler->connect( localhost , 11211 );
if($memCacheHandler->flush())
    echo "Мемкеш очищен";

