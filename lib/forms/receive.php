<?php
    set_time_limit(600);

    require_once( dirname(__FILE__) . "/../autoloader.php" );

    $cacheDir = dirname(__FILE__) . '/../../cache';

    if ( 0 < $_FILES['skizfile']['error'] ) {
        nxr(1, 'Error: ' . $_FILES['skizfile']['error']);
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

            $_SESSION['SkizImport\Upload\Receive'] = serialize($receiver);
        }
    }

    header("Location: /upload/confirm");
    die();

    /**
     * @param $dir
     */
    function rrmdir( $dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }