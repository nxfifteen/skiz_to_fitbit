<!-- Breadcrumb -->
<?php
    $breadcrums = explode("/", $url_namespace);

    echo '<ol class="breadcrumb">';
    foreach ( $breadcrums as $breadcrum ) {
        echo '<li class="breadcrumb-item">'.ucwords($breadcrum).'</li>';
    }
    echo '</ol>';
