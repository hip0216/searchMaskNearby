<?php

function createFile($file){
    file_put_contents($file,file_get_contents('src/tokenExample.php'));
}

function setToken($token,$type){
    $file = 'src/token.php';
    if(!file_exists($file)){
        createFile($file);
    }
    $contents = file_get_contents($file);
    
    $index = strpos($contents,$type);
    $start = strpos($contents,'\'',$index)+1;
    $end = strpos($contents,'\'',$start);

    $contents = substr_replace($contents,$token,$start,$end-$start);
    file_put_contents($file,$contents);
}
