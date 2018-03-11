<!-- Breadcrumb -->
<?php
    $breadcrums = explode("/", $url_namespace);

    echo '<ol class="breadcrumb">';
    foreach ( $breadcrums as $breadcrum ) {
        echo '<li class="breadcrumb-item">'.$breadcrum.'</li>';
    }
    echo '</ol>';
