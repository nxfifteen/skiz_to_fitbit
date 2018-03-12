<?php
    set_time_limit(600);

    require_once( dirname(__FILE__) . "/../autoloader.php" );

    $cacheDir = dirname(__FILE__) . '/../../cache';

    if ( 0 < $_FILES['skizfile']['error'] ) {
        nxr(1, 'Error: ' . $_FILES['skizfile']['error']);
        echo "There was a problem with your upload";
    } else {
        if ( file_exists($cacheDir) AND is_writable($cacheDir) ) {
            $receiver = new SkizImport\Upload\Receive($cacheDir);
            $receiver->storeUploads($_FILES);
            $receiver->extractSkizFile();
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