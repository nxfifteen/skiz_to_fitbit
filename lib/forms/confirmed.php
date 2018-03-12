<?php
    set_time_limit(600);

    require_once( dirname(__FILE__) . "/../autoloader.php" );

    $appClass = new SkizImport\SkizImport();

    /** @var SkizImport\Upload\Receive $receiver */
    $receiver = unserialize($_SESSION['SkizImport\Upload\Receive']);
    date_default_timezone_set($receiver->getTimezoneName());

    nxr(0, "User wants us to save tracks from " . $receiver->getTrackName());

    $fitbitActivities = $appClass->pullFitbit('activities.json', TRUE);

    foreach ( $receiver->getSkiRuns() as $index => $skiRun ) {
        nxr(0, "Saving run $index");
        $searchActivity = $skiRun['ACTIVITY_FITBIT'];
        if (is_null($appClass->getActivitId($searchActivity))) {
            foreach ( $fitbitActivities->categories as $userActivity ) {
                if ( $userActivity->name == "Sports and Workouts" ) {
                    if ( !empty($userActivity->subCategories) ) {
                        foreach ( $userActivity->subCategories as $subCat ) {
                            if ( $subCat->name == "Winter activities" ) {
                                foreach ( $subCat->activities as $subActive ) {
                                    if ( $subActive->name == "Skiing" ) {
                                        $appClass->setActivitId("Skiing", $subActive->id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $durationMillis = (strtotime($skiRun['END_ZONE']) - strtotime($skiRun['START_ZONE'])) * 1000;
        $startTime = date("H:i:s", strtotime($skiRun['START_ZONE']));
        $date = date("Y-m-d", strtotime($skiRun['START_ZONE']));

        $postArray = [
            'activityId' => $appClass->getActivitId($searchActivity),
            'startTime' => $startTime,
            'durationMillis' => $durationMillis,
            'date' => $date
        ];

        $apiReturnActivity = json_encode($appClass->pushFitbit('user/-/activities.json', $postArray, TRUE, TRUE));

        if ($receiver->getResourceOwner('encodedId') == $appClass->getSetting("ownerFuid")) {
            $apiReturnHeartRate = json_encode($appClass->pullFitbit('user/-/activities/heart/date/'.date("Y-m-d", strtotime($skiRun['START_ZONE'])).'/1d/1sec/time/'.date("H:i", strtotime($skiRun['START_ZONE'])).'/'.date("H:i", strtotime($skiRun['END_ZONE'])).'.json', TRUE));
        } else {
            $apiReturnHeartRate = NULL;
        }

        $receiver->updateSkiRuns($index, $apiReturnActivity, $apiReturnHeartRate);

        $receiver->createTCX($skiRun, $apiReturnActivity, $apiReturnHeartRate);
    }

    $receiver->createZipDownload();
    $receiver->cleanUp();


    $_SESSION['SkizImport\Upload\Receive'] = serialize($receiver);

    header("Location: /upload/finished");
    die();