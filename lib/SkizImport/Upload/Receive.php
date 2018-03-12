<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 11/03/2018
     * Time: 19:15
     */

    namespace SkizImport\Upload;

    use splitbrain\PHPArchive\ArchiveIOException;
    use splitbrain\PHPArchive\Zip;

    require_once( dirname(__FILE__) . "/../../autoloader.php" );

    define("TRACK_SEGMENT_UNKNOWN", 0);
    define("TRACK_SEGMENT_ASCENT", 1);           // used for cycling and nordic", 0); mountaineering -> WinterSportAscending
    define("TRACK_SEGMENT_DESCENT", 2);             // used for cycling and nordic.             -> WinterSportDescending
    define("TRACK_SEGMENT_REST", 3);
    define("TRACK_SEGMENT_FLAT", 4);                 // used for cycling.
    define("TRACK_SEGMENT_TIME_INTERVAL_1HR", 5);
    define("TRACK_SEGMENT_DISTANCE1", 6);
    define("TRACK_SEGMENT_DISTANCE5", 7);
    define("TRACK_SEGMENT_SKI_RUN", 8);              // used for downhill skiing                -> WinterSportSkiRun
    define("TRACK_SEGMENT_SKI_OFF_PISTE", 8);        //                                         -> WinterSportOffPisteSkiRun
    define("TRACK_SEGMENT_SKI_LIFT", 10);             // used for downhill skiing                -> WinterSportSkiLift
    define("TRACK_SEGMENT_SKI_XC", 11);
    define("TRACK_SEGMENT_SNOWSHOE", 12);
    define("TRACK_SEGMENT_HIKING", 13);
    define("TRACK_SEGMENT_RESTURANT", 14);
    define("TRACK_SEGMENT_PICNIC", 15);

    define("ACTIVITY_UNKNOWN", 0);
    define("ACTIVITY_SNOW_SKIING", 1);
    define("ACTIVITY_SNOW_SNOWBOARDING", 2);
    define("ACTIVITY_SNOW_SNOWMOBILE", 3);
    define("ACTIVITY_SNOW_X_COUNTRY", 4);
    define("ACTIVITY_SNOW_SNOWSHOE", 5);
    define("ACTIVITY_SNOW_TELEMARK", 6);
    define("ACTIVITY_SNOW_MONOSKI", 7);
    define("ACTIVITY_SLEDDING", 8);
    define("ACTIVITY_SITSKI", 9);
    define("ACTIVITY_SNOW_KITING", 10);
    define("ACTIVITY_SNOW_BIKE", 11);
    define("ACTIVITY_DOG_SLEDDING", 12);
    define("ACTIVITY_SKI_TOURING", 13);
    define("ACTIVITY_SKI_MOUNTAINEERING", 14);
    define("ACTIVITY_ICE_YACHTING", 15);
    define("ACTIVITY_SPLIT_BOARDING", 16);
    define("ACTIVITY_FAT_BIKE", 17);

    class Receive
    {

        private $cacheDir;
        private $resourceOwner;
        private $extractedPath;
        private $uploadPath;
        private $uploadedSkizFile;
        private $skiRuns;
        private $timezoneName;
        private $timezoneOffset;
        private $trackName;
        private $trackDate;
        private $fileContentsTracks;
        private $downloadPath;

        /**
         * Receive constructor.
         */
        public function __construct( $cacheDir )
        {
            $this->setCacheDir($cacheDir);
            $this->setResourceOwner(json_decode($_SESSION[ 'resourceOwner' ], TRUE));
            $this->setUploadPath($cacheDir . "/uploads/" . $this->getResourceOwner('encodedId'));
        }

        /**
         * @param mixed $cacheDir
         */
        private function setCacheDir( $cacheDir )
        {
            $this->cacheDir = $cacheDir;
        }

        /**
         * @param mixed $resourceOwner
         */
        private function setResourceOwner( $resourceOwner )
        {
            $this->resourceOwner = $resourceOwner;
        }

        /**
         * @param mixed $uploadPath
         */
        private function setUploadPath( $uploadPath )
        {
            $this->uploadPath = $uploadPath;
        }

        /**
         * @param null $key
         * @return mixed
         */
        public function getResourceOwner( $key = NULL )
        {
            if ( !is_null($key) && array_key_exists($key, $this->resourceOwner) ) {
                return $this->resourceOwner[ $key ];
            } else {
                return $this->resourceOwner;
            }
        }

        /**
         * @return mixed
         */
        public function getTimezoneName()
        {
            return $this->timezoneName;
        }

        /**
         * @return mixed
         */
        public function getTimezoneOffset()
        {
            return $this->timezoneOffset;
        }

        /**
         * @param $uploads
         */
        public function storeUploads( $uploads )
        {
            if ( !file_exists($this->getUploadPath()) ) {
                mkdir($this->getUploadPath(), 0755, TRUE);
            }

            foreach ( $uploads as $uploadedFile ) {
                if ( file_exists($this->getUploadPath() . '/' . $uploadedFile[ 'name' ]) ) {
                    unlink($this->getUploadPath() . '/' . $uploadedFile[ 'name' ]);
                }

                move_uploaded_file($uploadedFile[ 'tmp_name' ], $this->getUploadPath() . '/' . $uploadedFile[ 'name' ]);

                $this->setUploadedSkizFile($this->getUploadPath() . '/' . $uploadedFile[ 'name' ]);
            }

            nxr(0, "Store file as " . $this->getUploadedSkizFile());
        }

        /**
         * @return mixed
         */
        private function getUploadPath()
        {
            return $this->uploadPath;
        }

        /**
         * @param mixed $uploadedSkizFile
         */
        private function setUploadedSkizFile( $uploadedSkizFile )
        {
            $this->uploadedSkizFile = $uploadedSkizFile;
        }

        /**
         * @return mixed
         */
        private function getUploadedSkizFile()
        {
            return $this->uploadedSkizFile;
        }

        /**
         *
         */
        public function extractSkizFile()
        {
            $tar = new Zip();
            try {
                $path = $this->getUploadPath() . "/" . str_ireplace(".skiz", "", basename($this->getUploadedSkizFile()));
                if ( !file_exists($path) ) {
                    mkdir($path, 0755, TRUE);
                }

                $tar->open($this->getUploadedSkizFile());
                $tar->extract($path);

                $this->setExtractedPath($path);

                unlink($this->getUploadedSkizFile());

            } catch ( ArchiveIOException $e ) {
            }
        }

        /**
         * @param mixed $extractedPath
         */
        private function setExtractedPath( $extractedPath )
        {
            $this->extractedPath = $extractedPath;
        }

        public function readExtracted()
        {
            if ( $this->checkExtracted() ) {
                $trackFile = simplexml_load_file($this->getExtractedPath() . "/Track.xml");
                $this->fileContentsTracks = json_encode($trackFile);
                $this->timezoneOffset = (String)$trackFile[ 'tz' ];
                $plusMinus = substr((String)$trackFile[ 'tz' ], 0, 1);
                $hoursOffset = (int)explode(":", (String)$trackFile[ 'tz' ])[ 0 ];

                if ( $plusMinus == "+" ) {
                    $timezoneName = timezone_name_from_abbr("", $hoursOffset * 3600, FALSE);
                } else {
                    $timezoneName = timezone_name_from_abbr("", ( $hoursOffset * 3600 ) * -1, FALSE);
                }

                nxr(0, "TimeZone set to " . $timezoneName);
                $this->timezoneName = $timezoneName;
                date_default_timezone_set($timezoneName);

                $this->setTrackName((String)$trackFile[ 'name' ]);
                $this->setTrackDate(date("Y-m-d", strtotime((String)$trackFile[ 'start' ])));

                $loops = 0;
                $this->skiRuns = [];
                $fh = fopen($this->getExtractedPath() . "/Segment.csv", 'r');
                while ( ( $data = fgetcsv($fh, 100, ",") ) !== FALSE ) {
                    $loops = $loops + 1;
                    if ( count($data) >= 5 && $data[ 2 ] == TRACK_SEGMENT_SKI_RUN ) {
                        $this->skiRuns[] = [
                            "START_TS"        => explode(".", $data[ 0 ])[ 0 ],
                            "END_TS"          => explode(".", $data[ 1 ])[ 0 ],
                            "START_ZONE"      => str_ireplace("+", ".000+", date("c", $data[ 0 ])),
                            "END_ZONE"        => str_ireplace("+", ".000+", date("c", $data[ 1 ])),
                            "TYPE"            => $data[ 2 ],
                            "ACTIVITY"        => $this->lookupActivityType($data[ 3 ]),
                            "ACTIVITY_FITBIT" => $this->lookupActivityTypeFitbit($data[ 3 ]),
                            "NUMBER"          => $data[ 4 ],
                            "NAME"            => $data[ 5 ]
                        ];
                    }

                    if ( $loops > 50 ) break;
                }
                fclose($fh);
            } else {
                return FALSE;
            }
        }

        public function checkExtracted()
        {
            if ( !file_exists($this->getExtractedPath() . "/Nodes.csv") ) {
                nxr(2, "No Nodes found uploaded");
                nxr(3, $this->getExtractedPath() . "/Nodes.csv");

                return FALSE;
            } else if ( !file_exists($this->getExtractedPath() . "/Segment.csv") ) {
                nxr(2, "No Segment found uploaded");
                nxr(3, $this->getExtractedPath() . "/Segment.csv");

                return FALSE;
            } else if ( !file_exists($this->getExtractedPath() . "/Track.xml") ) {
                nxr(2, "No Track found uploaded");
                nxr(3, $this->getExtractedPath() . "/Track.xml");

                return FALSE;
            } else {
                return TRUE;
            }
        }

        /**
         * @return mixed
         */
        private function getExtractedPath()
        {
            return $this->extractedPath;
        }

        /**
         * @return mixed
         */
        public function getSkiRuns()
        {
            return $this->skiRuns;
        }

        /**
         * @return mixed
         */
        public function getTrackName()
        {
            return $this->trackName;
        }

        /**
         * @param mixed $trackName
         */
        public function setTrackName( $trackName )
        {
            $this->trackName = $trackName;
        }

        /**
         * @return mixed
         */
        public function getTrackDate()
        {
            return $this->trackDate;
        }

        /**
         * @param mixed $trackDate
         */
        public function setTrackDate( $trackDate )
        {
            $this->trackDate = $trackDate;
        }

        /**
         * @return mixed
         */
        public function getFileContentsTracks()
        {
            return json_decode($this->fileContentsTracks, TRUE);
        }

        public function createTCX( $skiRun, $apiReturn, $apiReturnHeartRate )
        {
            $apiReturn = json_decode($apiReturn, TRUE);

            if (!is_null($apiReturnHeartRate))
                $apiReturnHeartRate = json_decode($apiReturnHeartRate, TRUE);

            $tempDir = $this->getCacheDir() . "/temp/" . $this->getResourceOwner('encodedId');
            if ( !file_exists($tempDir) ) {
                mkdir($tempDir, 0755, TRUE);
            }

            list($gpsPoints, $totalDistance) = $this->findGPSPoints($skiRun);
            $tcxContents = '';
            $tcxContents .= $this->templateTCXHeader($skiRun, $apiReturn, $totalDistance);
            $tcxContents .= "                <Track>\n";
            foreach ( $gpsPoints as $gpsPoint ) {
                $tcxContents .= "                    <Trackpoint>\n";
                $tcxContents .= "                        <Time>" . str_ireplace("+", ".000+", date("c", $gpsPoint[ 'UTC' ])) . "</Time>\n";
                $tcxContents .= "                        <Position>\n";
                $tcxContents .= "                            <LatitudeDegrees>" . $gpsPoint['LAT'] . "</LatitudeDegrees>\n";
                $tcxContents .= "                            <LongitudeDegrees>" . $gpsPoint['LON'] . "</LongitudeDegrees>\n";
                $tcxContents .= "                        </Position>\n";
                $tcxContents .= "                        <AltitudeMeters>" . $gpsPoint['ALT'] . "</AltitudeMeters>\n";
                $tcxContents .= "                        <DistanceMeters>" . $gpsPoint['DISTANCE'] . "</DistanceMeters>\n";
                if (is_array($apiReturnHeartRate)) {
                    $tcxContents .= "                        <HeartRateBpm>\n";
                    $tcxContents .= "                            <Value>" . $this->findHeartRate(date("H:i:s", $gpsPoint[ 'UTC' ]), $apiReturnHeartRate) . "</Value>\n";
                    $tcxContents .= "                        </HeartRateBpm>\n";
                }
                $tcxContents .= "                    </Trackpoint>\n";
            }
            $tcxContents .= "                </Track>\n";
            $tcxContents .= $this->templateTCXFooter();

            if (file_exists($tempDir . "/" . $skiRun['NAME'] . ".tcx")) {
                unlink($tempDir . "/" . $skiRun['NAME'] . ".tcx");
            }
            file_put_contents($tempDir . "/" . $skiRun['NAME'] . ".tcx", $tcxContents);

        }

        /**
         * @return mixed
         */
        private function getCacheDir()
        {
            return $this->cacheDir;
        }

        private function lookupActivityType( $int )
        {
            switch ( $int ) {
                case ACTIVITY_UNKNOWN:
                    return "Unknown";
                    break;
                case ACTIVITY_SNOW_SKIING:
                    return "Skiing";
                    break;
                case ACTIVITY_SNOW_SNOWBOARDING:
                    return "Snowboarding";
                    break;
                case ACTIVITY_SNOW_SNOWMOBILE:
                    return "Snowmobiling";
                    break;
                case ACTIVITY_SNOW_X_COUNTRY:
                    return "Cross Country Skiing";
                    break;
                case ACTIVITY_SNOW_SNOWSHOE:
                    return "Snowshoeing";
                    break;
                case ACTIVITY_SNOW_TELEMARK:
                    return "Telemark";
                    break;
                case ACTIVITY_SNOW_MONOSKI:
                    return "Mono Ski";
                    break;
                case ACTIVITY_SLEDDING:
                    return "Sledding, tobogganing, bobsledding, luge";
                    break;
                case ACTIVITY_SITSKI:
                    return "Sit Ski";
                    break;
                case ACTIVITY_SNOW_KITING:
                    return "Snow Kiting";
                    break;
                case ACTIVITY_SNOW_BIKE:
                    return "Snow Bike";
                    break;
                case ACTIVITY_DOG_SLEDDING:
                    return "Dog Sledding";
                    break;
                case ACTIVITY_SKI_TOURING:
                    return "Ski Touring";
                    break;
                case ACTIVITY_SKI_MOUNTAINEERING:
                    return "Ski Mountaineering";
                    break;
                case ACTIVITY_ICE_YACHTING:
                    return "Ice Yachting";
                    break;
                case ACTIVITY_SPLIT_BOARDING:
                    return "Split Boarding";
                    break;
                case ACTIVITY_FAT_BIKE:
                    return "Fat Bike";
                    break;
            }
        }

        private function lookupActivityTypeFitbit( $int )
        {
            switch ( $int ) {
                case ACTIVITY_SNOW_SNOWMOBILE:
                    return "Snowmobiling";
                    break;
                case ACTIVITY_SNOW_X_COUNTRY:
                    return "Cross Country Skiing";
                    break;
                case ACTIVITY_SNOW_SNOWSHOE:
                    return "Snowshoeing";
                    break;
                case ACTIVITY_SLEDDING:
                    return "Sledding, tobogganing, bobsledding, luge";
                    break;
                default:
                    return "Skiing";
                    break;
            }
        }

        /**
         * Calculates the great-circle distance between two points, with
         * the Haversine formula.
         * @param float $latitudeFrom  Latitude of start point in [deg decimal]
         * @param float $longitudeFrom Longitude of start point in [deg decimal]
         * @param float $latitudeTo    Latitude of target point in [deg decimal]
         * @param float $longitudeTo   Longitude of target point in [deg decimal]
         * @param float $earthRadius   Mean earth radius in [m]
         * @return float Distance between points in [m] (same as earthRadius)
         */
        private function haversineGreatCircleDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000 )
        {
            // convert from degrees to radians
            $latFrom = deg2rad($latitudeFrom);
            $lonFrom = deg2rad($longitudeFrom);
            $latTo = deg2rad($latitudeTo);
            $lonTo = deg2rad($longitudeTo);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            return $angle * $earthRadius;
        }

        private function findGPSPoints( $skiRun )
        {

            nxr(0, "Looking for GPS points for " . $skiRun['NAME']);
            nxr(1, "Looking for date between " . $skiRun[ 'START_TS' ] . " and " . $skiRun[ 'END_TS' ]);

            $loops = 0;
            $totalDistance = 0;
            $prevLat = 0;
            $prevLon = 0;
            $gpsPoints = [];
            $fh = fopen($this->getExtractedPath() . "/Nodes.csv", 'r');
            while ( !feof($fh) ) {
                $data = fgetcsv($fh, 200, ",");
                $loops = $loops + 1;
                if ( $data[ 0 ] >= $skiRun[ 'START_TS' ] && $data[ 0 ] <= $skiRun[ 'END_TS' ] ) {
                    if ( $prevLat == 0 || $prevLon == 0 ) {
                        $totalDistance = 0;
                    } else {
                        $distanceTraveled = $this->haversineGreatCircleDistance($prevLat, $prevLon, $data[ 1 ], $data[ 2 ]);
                        $totalDistance += $distanceTraveled;
                    }

                    $gpsPoints[] = [
                        "UTC"      => $data[ 0 ],
                        "LAT"      => $data[ 1 ],
                        "LON"      => $data[ 2 ],
                        "ALT"      => $data[ 3 ],
                        "HEADING"  => $data[ 4 ],
                        "VELOCITY" => $data[ 5 ],
                        "H_ACC"    => $data[ 6 ],
                        "V_ACC"    => $data[ 7 ],
                        "DISTANCE" => $totalDistance
                    ];

                    $prevLat = $data[ 1 ];
                    $prevLon = $data[ 2 ];
                }

                if ( $data[ 0 ] > $skiRun[ 'END_TS' ] || $loops > 30000 ) break;
            }
            fclose($fh);

            return [ $gpsPoints, $totalDistance ];
        }

        private function templateTCXHeader( $skiRun, $apiReturn, $totalDistance )
        {
            $trackFile = $this->getFileContentsTracks();
            $tcxContents = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
            $tcxContents .= "<TrainingCenterDatabase xmlns=\"http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2\">\n";
            $tcxContents .= "    <Activities>\n";
            $tcxContents .= "        <Activity Sport=\"" . $skiRun[ 'ACTIVITY' ] . "\">\n";
            $tcxContents .= "            <Id>" . $skiRun[ 'START_ZONE' ] . "</Id>\n";
            $tcxContents .= "            <Lap StartTime=\"" . $skiRun[ 'START_ZONE' ] . "\">\n";
            $tcxContents .= "                <TotalTimeSeconds>" . ( $apiReturn[ 'activityLog' ][ 'duration' ] / 1000 ) . "</TotalTimeSeconds>\n";
            $tcxContents .= "                <DistanceMeters>" . $totalDistance . "</DistanceMeters>\n";
            $tcxContents .= "                <Calories>" . $apiReturn[ 'activityLog' ][ 'calories' ] . "</Calories>\n";
            $tcxContents .= "                <AverageHeartRateBpm>\n";
            $tcxContents .= "                    <Value>0</Value>\n";
            $tcxContents .= "                </AverageHeartRateBpm>\n";
            $tcxContents .= "                <MaximumHeartRateBpm>\n";
            $tcxContents .= "                    <Value>0</Value>\n";
            $tcxContents .= "                </MaximumHeartRateBpm>\n";
            $tcxContents .= "                <MaximumSpeed>" . $trackFile[ 'metrics' ][ 'maxdescentspeed' ] . "</MaximumSpeed>\n";
            $tcxContents .= "                <Cadence>0</Cadence>\n";
            $tcxContents .= "                <Intensity>Active</Intensity>\n";
            $tcxContents .= "                <TriggerMethod>Manual</TriggerMethod>\n";
            return $tcxContents;
        }

        private function templateTCXFooter()
        {
            $trackFile = $this->getFileContentsTracks();
            $tcxContents = "            </Lap>\n";
            $tcxContents .= "            <Creator xsi:type=\"Device_t\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
            $tcxContents .= "                <Name>NxSkizImporter/" . $trackFile[ '@attributes' ][ 'device' ] . "</Name>\n";
            $tcxContents .= "                <UnitId>0</UnitId>\n";
            $tcxContents .= "                <ProductID>0</ProductID>\n";
            $tcxContents .= "            </Creator>\n";
            $tcxContents .= "        </Activity>\n";
            $tcxContents .= "    </Activities>\n";
            $tcxContents .= "</TrainingCenterDatabase>\n";
            return $tcxContents;

        }

        private function findHeartRate( $date, $apiReturnHeartRate )
        {
            $lastValue = -2;
            foreach ( $apiReturnHeartRate['activities-heart-intraday']['dataset'] as $item ) {
                if (strtotime(date("Y-m-d ") . $item['time']) < strtotime(date("Y-m-d ") . $date)) {
                    if ( $item[ 'time' ] == $date ) {
                        return $item[ 'value' ];
                    } else {
                        $lastValue = $item[ 'value' ];
                    }
                } else {
                    return $lastValue;
                }
            }

            return -1;
        }

        public function createZipDownload()
        {
            $tempDir = $this->getCacheDir() . "/temp/" . $this->getResourceOwner('encodedId');
            $downloadDir = $this->getCacheDir() . "/downloads/" . $this->getResourceOwner('encodedId');
            if ( !file_exists($downloadDir) ) {
                mkdir($downloadDir, 0755, TRUE);
            }
            $tracksFile = $this->getFileContentsTracks();
            $downloadFile = $downloadDir . "/" . str_ireplace("/","-",$tracksFile['@attributes']['name']) . ".zip";

            $tar = new Zip();
            try {
                $tar->create($downloadFile);
                if (is_dir($tempDir)) {
                    $objects = scandir($tempDir);
                    foreach ($objects as $object) {
                        if ($object != "." && $object != "..") {
                            if (filetype($tempDir."/".$object) == "file")
                                $tar->addFile($tempDir."/".$object, $object);
                        }
                    }
                    reset($objects);
                }

                $tar->close();

                $this->downloadPath = "/cache/" . "/downloads/" . $this->getResourceOwner('encodedId') . "/" . str_ireplace("/","-",$tracksFile['@attributes']['name']) . ".zip";
            } catch ( ArchiveIOException $e ) {}
        }

        public function cleanUp()
        {
//            $tempDir = $this->getCacheDir() . "/temp/" . $this->getResourceOwner('encodedId');
//            $this->rrmdir($tempDir);
//            $uploadDir = $this->getCacheDir() . "/uploads/" . $this->getResourceOwner('encodedId');
//            $this->rrmdir($uploadDir);
//
//            $now   = time();
//            $this->cleanUpDownlods($this->getCacheDir() . "/downloads", $now);

        }

        private function cleanUpDownlods($dir, $now)
        {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    if (is_file($dir . "/" . $file)) {
                        if ($now - filemtime($dir . "/" . $file) >= 60 * 20) { // 2 days
                            unlink($dir . "/" . $file);
                        }
                    } else if (is_dir($dir . "/" . $file)) {
                        $this->cleanUpDownlods($dir . "/" . $file, $now);
                    }
                }
            }
        }

        /**
         * @param $dir
         */
        private function rrmdir( $dir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        }

        /**
         * @return mixed
         */
        public function getDownloadPath()
        {
            return $this->downloadPath;
        }

        public function updateSkiRuns( $index, $apiReturnActivity, $apiReturnHeartRate )
        {
            $apiReturnActivity = json_decode($apiReturnActivity, true);
            $this->skiRuns[$index]['FITBIT'] = $apiReturnActivity["activityLog"]["logId"];
            if (is_null($apiReturnHeartRate)) {
                $this->skiRuns[ $index ][ 'HEART' ] = TRUE;
            } else {
                $this->skiRuns[ $index ][ 'HEART' ] = FALSE;
            }
        }
    }