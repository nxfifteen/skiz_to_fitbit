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

    require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php");

    setcookie('_nx_skiz_usr', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER[ 'SERVER_NAME' ]);
    setcookie('_nx_skiz', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER[ 'SERVER_NAME' ]);

    nxr_destroy_session();

    header("Location: /login.php");