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

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php" );

    $cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';
    if ( file_exists($cacheDir) AND is_writable($cacheDir) ) {

        nxr(0, "Cleaning up downloads");
        nxr(1, "Started...");
        cleanUpDownlods($cacheDir, time());
        RemoveEmptySubFolders($cacheDir);
        nxr(1, "...Completed");
    }

    function cleanUpDownlods( $dir, $now )
    {
        $files = scandir($dir);
        foreach ( $files as $file ) {
            if ( $file != "." && $file != ".." ) {
                if ( is_file($dir . DIRECTORY_SEPARATOR . $file) ) {
                    if ( $now - filemtime($dir . DIRECTORY_SEPARATOR . $file) >= 60 * 20 ) { // 2 days
                        nxr(2, "Deleting " . $dir . DIRECTORY_SEPARATOR . $file);
                        unlink($dir . DIRECTORY_SEPARATOR . $file);
                    }
                } else if ( is_dir($dir . DIRECTORY_SEPARATOR . $file) ) {
                    cleanUpDownlods($dir . DIRECTORY_SEPARATOR . $file, $now);
                }
            }
        }
    }

    function RemoveEmptySubFolders( $path )
    {
        $empty = TRUE;
        foreach ( glob($path . DIRECTORY_SEPARATOR . "*") as $file ) {
            $empty &= is_dir($file) && RemoveEmptySubFolders($file);
        }
        return $empty && rmdir($path);
    }