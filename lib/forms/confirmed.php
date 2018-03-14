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

    $appClass = new SkizImport\SkizImport();

    /** @var SkizImport\Upload\Receive $receiver */
    $receiver = unserialize($_SESSION[ 'SkizImport\Upload\Receive' ]);
    date_default_timezone_set($receiver->getTimezoneName());
    $receiver->getStatsClass()->recordNewTimeZone($receiver->getTimezoneName());

    nxr(0, "User confirmed they want to start processing the upload");

    nxr(1, "Querying activity code from Fitbit");
    $fitbitActivities = $appClass->pullFitbit('activities.json', TRUE);

    $receiver->getStatsClass()->recordNewSession();
    $receiver->getStatsClass()->recordRunCount(count($receiver->getSkiRuns()));
    nxr(1, "Processing runs");
    foreach ( $receiver->getSkiRuns() as $index => $skiRun ) {
        nxr(2, "Run $index");
        $searchActivity = $skiRun[ 'ACTIVITY_FITBIT' ];
        if ( is_null($appClass->getActivitId($searchActivity)) ) {
            foreach ( $fitbitActivities->categories as $userActivity ) {
                if ( $userActivity->name == "Sports and Workouts" ) {
                    if ( !empty($userActivity->subCategories) ) {
                        foreach ( $userActivity->subCategories as $subCat ) {
                            if ( $subCat->name == "Winter activities" ) {
                                foreach ( $subCat->activities as $subActive ) {
                                    if ( $subActive->name == $searchActivity ) {
                                        $appClass->setActivitId($searchActivity, $subActive->id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $durationMillis = ( strtotime($skiRun[ 'END_ZONE' ]) - strtotime($skiRun[ 'START_ZONE' ]) );
        $receiver->getStatsClass()->recordRunDuration($durationMillis);

        $startTime = date("H:i:s", strtotime($skiRun[ 'START_ZONE' ]));
        $date = date("Y-m-d", strtotime($skiRun[ 'START_ZONE' ]));

        $postArray = [
            'activityId'     => $appClass->getActivitId($searchActivity),
            'startTime'      => $startTime,
            'durationMillis' => $durationMillis * 1000,
            'date'           => $date
        ];

        nxr(3, "Sending activity to Fitbit");
        $apiReturnActivity = $appClass->pushFitbit('user/-/activities.json', $postArray, TRUE, TRUE);
        if ( strtotime((string)$apiReturnActivity->activityLog->lastModified) < strtotime("-10 minutes") ) {
            $receiver->getStatsClass()->recordUpdatedRun();
        } else {
            $receiver->getStatsClass()->recordNewRun();
        }

        $apiReturnActivity = json_encode($apiReturnActivity);

        if ( $receiver->getResourceOwner('encodedId') == $appClass->getSetting("ownerFuid") ) {
            nxr(3, "Downloading Heart Rate Data");
            $apiReturnHeartRate = json_encode($appClass->pullFitbit('user/-/activities/heart/date/' . date("Y-m-d", strtotime($skiRun[ 'START_ZONE' ])) . '/1d/1sec/time/' . date("H:i", strtotime($skiRun[ 'START_ZONE' ])) . '/' . date("H:i", strtotime($skiRun[ 'END_ZONE' ])) . '.json', TRUE));
        } else {
            nxr(3, "Skipping Heart Rate Data");
            $apiReturnHeartRate = NULL;
        }

        if ( !is_null($apiReturnHeartRate) ) {
            $receiver->getStatsClass()->recordHeartRateAvailable();
        }

        $receiver->updateSkiRuns($index, $apiReturnActivity, $apiReturnHeartRate);

        nxr(3, "Creating TCX File for run");
        $receiver->createTCX($skiRun, $apiReturnActivity, $apiReturnHeartRate);
    }

    nxr(2, "Building ZIP file");
    $receiver->createZipDownload();

    nxr(2, "Cleaning uploads folders");
    $receiver->cleanUp();


    $_SESSION[ 'SkizImport\Upload\Receive' ] = serialize($receiver);

    header("Location: /upload/finished");
    die();