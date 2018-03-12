<?php

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php" );

    $cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache';
    if ( file_exists($cacheDir) AND is_writable($cacheDir) ) {

        nxr(0, "Cleaning up downloads");
        nxr(1, "Started...");
        cleanUpDownlods($cacheDir, time());
        RemoveEmptySubFolders($cacheDir);
        nxr(1, "...Completed");
    }

    function cleanUpDownlods($dir, $now)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if (is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                    if ($now - filemtime($dir . DIRECTORY_SEPARATOR . $file) >= 60 * 20) { // 2 days
                        nxr(2, "Deleting " . $dir . DIRECTORY_SEPARATOR . $file);
                        unlink($dir . DIRECTORY_SEPARATOR . $file);
                    }
                } else if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                    cleanUpDownlods($dir . DIRECTORY_SEPARATOR . $file, $now);
                }
            }
        }
    }

    function RemoveEmptySubFolders($path)
    {
        $empty=true;
        foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
        {
            $empty &= is_dir($file) && RemoveEmptySubFolders($file);
        }
        return $empty && rmdir($path);
    }