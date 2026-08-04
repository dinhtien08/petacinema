<?php 

session_start();

require_once './configs/env.php';
require_once './configs/helper.php';

spl_autoload_register(function ($class) {    
    $fileName = "$class.php";

    $fileModel              = PATH_MODEL . $fileName;
    $fileController         = PATH_CONTROLLER . $fileName;
    $fileMiddleware         = PATH_MIDDLEWARE . $fileName;

    if (is_readable($fileModel)) {
        require_once $fileModel;
    } 
    else if (is_readable($fileController)) {
        require_once $fileController;
    }
    else if (is_readable($fileMiddleware)) {
        require_once $fileMiddleware;
    }
});

// Điều hướng
require_once './routes/index.php';
