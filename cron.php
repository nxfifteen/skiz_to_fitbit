<?php
    $cacheDir = dirname(__FILE__) . '/cache';
    if ( file_exists($cacheDir) AND is_writable($cacheDir) ) {
        cleanUpDownlods($cacheDir, time());
    }

    function cleanUpDownlods($dir, $now)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if (is_file($dir . "/" . $file)) {
                    if ($now - filemtime($dir . "/" . $file) >= 60 * 20) { // 2 days
                        unlink($dir . "/" . $file);
                    }
                } else if (is_dir($dir . "/" . $file)) {
                    cleanUpDownlods($dir . "/" . $file, $now);
                }
            }
        }
    }