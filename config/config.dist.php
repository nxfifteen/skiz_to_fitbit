<?php

    $config = [];


    /*
     * Personal settings will therefor overright settings above
     */
    if ( file_exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.inc.php") ) {
        require( dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.inc.php" );
    }