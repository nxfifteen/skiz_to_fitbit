<?php
    /**
     * This file is part of NxFIFTEEN Fitness SkizImport.
     * Copyright (c) 2018. Stuart McCulloch Anderson
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     *
     * @package     SkizImport
     * @version     0.0.1.x
     * @since       0.0.0.1
     * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link        https://nxfifteen.me.uk NxFIFTEEN
     * @link        https://nxfifteen.me.uk/nxcore Project Page
     * @link        https://nxfifteen.me.uk/gitlab/rocks/core Git Repo
     * @copyright   2018 Stuart McCulloch Anderson
     * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
     */

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "functions.php" );

    spl_autoload_register(function ( $className ) {
        $namespace = str_replace("\\", DIRECTORY_SEPARATOR, __NAMESPACE__);
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $class = dirname(__FILE__) . DIRECTORY_SEPARATOR . ( empty($namespace) ? "" : $namespace . DIRECTORY_SEPARATOR ) . "{$className}.php";

        if ( file_exists($class) ) {
            /** @noinspection PhpIncludeInspection */
            require_once( $class );
        } else {
            echo $class;
            die();
        }

    });

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php" );
