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

    set_time_limit(600);

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "autoloader.php" );

    $cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache';

    if ( 0 < $_FILES[ 'skizfile' ][ 'error' ] ) {
        nxr(1, 'Error: ' . $_FILES[ 'skizfile' ][ 'error' ]);
        echo "There was a problem with your upload";
    } else {
        if ( file_exists($cacheDir) AND is_writable($cacheDir) ) {
            nxr(0, "Pre-processing started");

            $receiver = new SkizImport\Upload\Receive($cacheDir);
            nxr(1, "Storing users uploaded SKIZ file");
            $receiver->storeUploads($_FILES);
            nxr(1, "Extacting files");
            $receiver->extractSkizFile();
            nxr(1, "Pre-processing extracted files");
            $receiver->readExtracted();

            $_SESSION[ 'SkizImport\Upload\Receive' ] = serialize($receiver);
        }
    }

    header("Location: /upload/confirm");
    die();

    /**
     * @param $dir
     */
    function rrmdir( $dir )
    {
        if ( is_dir($dir) ) {
            $objects = scandir($dir);
            foreach ( $objects as $object ) {
                if ( $object != "." && $object != ".." ) {
                    if ( filetype($dir . DIRECTORY_SEPARATOR . $object) == "dir" ) rrmdir($dir . DIRECTORY_SEPARATOR . $object); else unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }