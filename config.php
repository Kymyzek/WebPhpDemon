<?php
define('D_ROOT', __DIR__);
define('VENDOR', D_ROOT . DIRECTORY_SEPARATOR .'vendor');
define('WPD_WORK_DIR', D_ROOT . DIRECTORY_SEPARATOR . 'wpd');

spl_autoload_register(function ($class){
    $file = VENDOR . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class). '.php';
    if (is_readable($file))
        require_once $file;
});

