<?php

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.DevelopmentCodeFragment)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    if ( !function_exists("nxr") ) {
        /**
         * NXR is a helper function. Past strings are recorded in a text file
         * and when run from a command line output is displayed on screen as
         * well
         *
         * @param integer             $indentation Log line indenation
         * @param string|array|object $msg         String input to be displayed in logs files
         * @param bool                $includeDate If true appends datetime stamp
         * @param bool                $newline     If true adds a new line character
         * @param bool                $echoLine    Print a new line or not
         */
        function nxr( $indentation, $msg, $includeDate = TRUE, $newline = TRUE, $echoLine = TRUE )
        {
            if ($indentation >= 0) {
                $_SESSION[ 'indentation' ] = $indentation;
            } else if ($indentation < -1) {
                $indentation = $_SESSION[ 'indentation' ] + (($indentation * -1) - 1);
            } else if ($indentation == -1) {
                $indentation = $_SESSION[ 'indentation' ];
            }

            if ( is_array($msg) || is_object($msg) ) {
                $msg = print_r($msg, TRUE);
            }

            for ( $counter = 0; $counter < $indentation; $counter++ ) {
                $msg = " " . $msg;
            }

            if ( $includeDate ) {
                $msg = date("Y-m-d H:i:s") . ": " . $msg;
            }
            if ( $newline ) {
                $msg = $msg . "\n";
            }

            if ( is_writable(dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "logs.log") ) {
                $logFileName = fopen(dirname(__FILE__) . "/../logs.log", "a");
                fwrite($logFileName, $msg);
                fclose($logFileName);
            }

            if ( ( !defined('TEST_SUITE') || TEST_SUITE == FALSE ) && $echoLine !== FALSE && ( !defined('IS_CRON_RUN') || !IS_CRON_RUN ) && php_sapi_name() == "cli" ) {
                echo $msg;
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    if ( !function_exists("nxr_destroy_session") ) {

        function nxr_destroy_session()
        {
            // Unset all of the session variables.
            unset($_SESSION);

            // If it's desired to kill the session, also delete the session cookie.
            // Note: This will destroy the session, and not just the session data!
            if ( ini_get("session.use_cookies") ) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params[ "path" ], $params[ "domain" ],
                    $params[ "secure" ], $params[ "httponly" ]
                );
            }

            // Finally, destroy the session.
            session_destroy();
        }
    }

    function getNameSpace() {
        if ( array_key_exists("REDIRECT_URL", $_SERVER) ) {
            $inputURL = $_SERVER[ 'REDIRECT_URL' ];
        } else {
            $inputURL = "";
        }
        $sysPath = str_ireplace($_SESSION[ 'core_config' ][ 'url' ], "", $_SESSION[ 'core_config' ][ 'http/' ]);

        if ( $sysPath != "/" ) {
            $inputURL = str_replace($sysPath, "", $inputURL);
        }
        if ( substr($inputURL, 0, 1) == "/" ) {
            $inputURL = substr($inputURL, 1);
        }

        $url_namespace = $inputURL;
        if ( $url_namespace == "" || $url_namespace == "dashboard" ) {
            $url_namespace = "main";
        }

        return $url_namespace;
    }

    /**
     * @return bool
     */
    function isloggedIn() {
        $appClass = new SkizImport\SkizImport();

        if (!filter_input(INPUT_COOKIE, '_nx_skiz', FILTER_SANITIZE_STRING) ||
            !filter_input(INPUT_COOKIE, '_nx_skiz_usr', FILTER_SANITIZE_STRING) ||
            filter_input(INPUT_COOKIE, '_nx_skiz', FILTER_SANITIZE_STRING) !=
            gen_cookie_hash( $appClass->getSetting("salt"), filter_input(INPUT_COOKIE, '_nx_skiz_usr', FILTER_SANITIZE_STRING) ) ) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    if ( !function_exists("gen_cookie_hash") ) {
        /**
         * @param String $salt
         * @param String $fuid
         *
         * @return string
         * @internal param array $_POST
         */
        function gen_cookie_hash( $salt, $fuid )
        {
            return hash("sha256", $salt . $fuid . $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'SERVER_ADDR' ] );
        }
    }
