<?php
    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php");

    setcookie('_nx_skiz_usr', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER[ 'SERVER_NAME' ]);
    setcookie('_nx_skiz', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER[ 'SERVER_NAME' ]);

    nxr_destroy_session();

    header("Location: /login.php");