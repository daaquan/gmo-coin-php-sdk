<?php
spl_autoload_register(function($class){
    $prefix = 'GmoCoin\\';
    if (strncmp($class, $prefix, strlen($prefix)) === 0) {
        $path = __DIR__.'/../src/'.str_replace('\\','/',substr($class, strlen($prefix))).'.php';
        if (file_exists($path)) require $path;
    }
});
