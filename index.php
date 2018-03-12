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

    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php");
    if (!isloggedIn()) {
        header("Location: ./login.php");
    }

    $resourceOwner = json_decode($_SESSION['resourceOwner'], true);

    $url_namespace = getNameSpace();

//    nxr(0, "Namespace Called: " . $url_namespace);

    switch ($url_namespace) {
        case "upload/send":
            require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR . "receive.php" );
            break;
        case "upload/confirmed":
            require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR . "confirmed.php" );
            break;
    }

    $pageContent = dirname(__FILE__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "content" . DIRECTORY_SEPARATOR . $url_namespace . ".php";
//    if ( $pageContent != "" ) {
//        nxr(0, "Content Loaded: " . $pageContent);
//    }

    if (!file_exists($pageContent)) {
        header("Location: /views/pages/404.html");
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Import SkiTrack exported SKIZ files into Fitbit as activities">
    <meta name="author" content="Stuart McCulloch Anderson">
    <meta name="keyword" content="Fitbit, SkiTracks, Ski Tracks, Fitbit Import, SKIZ">
    <link rel="shortcut icon" href="img/favicon-32x32.png">

    <title>SkiTracks Fitbit Importer | NxFIFTEEN Rocks</title>

    <!-- Icons -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="/css/style.css" rel="stylesheet">
    <script src="/node_modules/jquery/dist/jquery.min.js"></script>

</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<header class="app-header navbar">
    <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">&#9776;</button>
    <a class="navbar-brand" href="#"></a>

    <ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item">
            <a class="nav-link navbar-toggler sidebar-toggler" href="#">&#9776;</a>
        </li>

        <?php require_once( 'views/comps/html.topMenu.php' ); ?>
    </ul>

    <?php require_once( 'views/comps/html.topUserMenu.php' ); ?>
</header>

<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <?php require_once( 'views/comps/html.navBar.php' ); ?>
        </nav>
    </div>

    <!-- Main content -->
    <main class="main">
        <?php require_once( 'views/comps/html.breadcrumb.php' ); ?>

        <div class="container-fluid">
            <div id="animated fadeIn">
                <?php
                    /** @noinspection PhpIncludeInspection */
                    require_once($pageContent);
                ?>
            </div>
        </div>
        <!-- /.conainer-fluid -->
    </main>

    <aside class="aside-menu">
        <?php require_once( 'views/comps/html.asideMenu.php' ); ?>
    </aside>

</div>

<footer class="app-footer">
    <a href="https://nxfifteen.me.uk/gitlab/rocks/skiz">NxFITNESS Skiz Importer</a> &copy; <?php echo date('Y'); ?> Stuart
    McCulloch Anderson. <span class="float-right d-sm-down-none" id="app-footer-project">An <a href="https://nxfifteen.me.uk">NxFIFTEEN</a> Rocks project, powered by <a href="http://coreui.io">CoreUI</a></span>
</footer>

<!-- Bootstrap and necessary plugins -->
<script src="/node_modules/tether/dist/js/tether.min.js"></script>
<script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/node_modules/pace-js/pace.min.js"></script>


<!-- Plugins and scripts required by all views -->
<script src="/node_modules/chart.js/dist/Chart.min.js"></script>


<!-- GenesisUI main scripts -->

<script src="/js/app.js"></script>

</body>

</html>
