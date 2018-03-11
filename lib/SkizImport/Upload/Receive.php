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
    define("ACTIVTIY_SNOW_BIKE", 11);
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
        private function getResourceOwner( $key = NULL )
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
                $path = $this->getUploadPath() . "/" . str_ireplace(".skiz","", basename( $this->getUploadedSkizFile()));
                if ( !file_exists($path) ) {
                    mkdir($path, 0755, TRUE);
                }

                $tar->open($this->getUploadedSkizFile() );
                $tar->extract($path);

                $this->setExtractedPath($path);

                unlink($this->getUploadedSkizFile());

            } catch ( ArchiveIOException $e ) {}
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
            if ($this->checkExtracted()) {
                $trackFile = simplexml_load_file($this->getExtractedPath() . "/Track.xml");
                $this->fileContentsTracks = json_encode($trackFile);
                $this->timezoneOffset = (String)$trackFile['tz'];
                $plusMinus = substr((String)$trackFile['tz'], 0, 1);
                $hoursOffset = (int)explode(":", (String)$trackFile['tz'])[0];

                if ($plusMinus == "+") {
                    $timezoneName = timezone_name_from_abbr("", $hoursOffset * 3600, FALSE);
                } else {
                    $timezoneName = timezone_name_from_abbr("", ($hoursOffset * 3600) * -1, FALSE);
                }

                nxr(0, "TimeZone set to " . $timezoneName);
                $this->timezoneName = $timezoneName;
                date_default_timezone_set($timezoneName);

                $this->setTrackName((String)$trackFile['name']);
                $this->setTrackDate(date("Y-m-d", strtotime((String)$trackFile['start'])));

                $loops = 0;
                $this->skiRuns = [];
                $fh = fopen($this->getExtractedPath() . "/Segment.csv", 'r');
                while (($data = fgetcsv($fh, 100, ",")) !== FALSE) {
                    $loops = $loops + 1;
                    if (count($data) >= 5 && $data[2] == TRACK_SEGMENT_SKI_RUN) {
                        $this->skiRuns[] = [
                            "START_TS" => explode(".", $data[0])[0],
                            "END_TS" => explode(".", $data[1])[0],
                            "START_ZONE" => str_ireplace("+",".000+", date("c", $data[0])),
                            "END_ZONE" => str_ireplace("+",".000+", date("c", $data[1])),
                            "SEGMENT_TYPE" => $data[2],
                            "SEGMENT_ACTIVITY" => $data[3],
                            "SEGMENT_NUMBER" => $data[4],
                            "SEGMENT_NAME" => $data[5]
                        ];
                    }

                    if ($loops > 50) break;
                }
                fclose($fh);
            } else {
                return FALSE;
            }
        }

        public function checkExtracted()
        {
            if (!file_exists($this->getExtractedPath() . "/Nodes.csv")) {
                nxr(2, "No Nodes found uploaded");
                nxr(3, $this->getExtractedPath() . "/Nodes.csv");

                return false;
            } elseif (!file_exists($this->getExtractedPath() . "/Segment.csv")) {
                nxr(2, "No Segment found uploaded");
                nxr(3, $this->getExtractedPath() . "/Segment.csv");

                return false;
            } elseif (!file_exists($this->getExtractedPath() . "/Track.xml")) {
                nxr(2, "No Track found uploaded");
                nxr(3, $this->getExtractedPath() . "/Track.xml");

                return false;
            } else {
                return true;
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
            return json_decode($this->fileContentsTracks, true);
        }

        /**
         * @return mixed
         */
        private function getCacheDir()
        {
            return $this->cacheDir;
        }
    }