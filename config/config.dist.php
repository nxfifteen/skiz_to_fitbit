<?php

    $config = [];


    /*
     * Personal settings will therefor overright settings above
     */
    if ( file_exists(dirname(__FILE__) . "/config.inc.php") ) {
        require( dirname(__FILE__) . "/config.inc.php" );
    }