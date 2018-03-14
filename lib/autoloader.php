<?php
    /**
     * This file is part of NxFIFTEEN SkiTracks/Fitbit Importer.
     * Copyright (c) 2018. Stuart McCulloch Anderson
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     *
     * @package     NxFIFTEEN SkiTracks/Fitbit Importer
     * @version     0.0.1.x
     * @since       0.0.1.0
     * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link        https://nxfifteen.me.uk NxFIFTEEN
     * @link        https://nxfifteen.me.uk/rocks/skiz Project Page
     * @link        https://nxfifteen.me.uk/gitlab/rocks/skiz Git Repo
     * @copyright   2018 Stuart McCulloch Anderson
     * @license     https://license.nxfifteen.rocks/gpl-3/2018/ GNU GPLv3
     */

    session_start();

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "functions.php" );

    spl_autoload_register(function ( $className ) {
        $namespace = str_replace("\\", DIRECTORY_SEPARATOR, __NAMESPACE__);
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $class = dirname(__FILE__) . DIRECTORY_SEPARATOR . ( empty($namespace) ? "" : $namespace . DIRECTORY_SEPARATOR ) . "{$className}.php";

        if ( file_exists($class) ) {
            /** @noinspection PhpIncludeInspection */
            require_once( $class );
        }

    });

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php" );
