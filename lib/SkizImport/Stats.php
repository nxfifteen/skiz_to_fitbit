<?php
    /**
     * Created by IntelliJ IDEA.
     * User: stuar
     * Date: 12/03/2018
     * Time: 13:40
     */

    namespace SkizImport;

    use Medoo\Medoo;

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "autoloader.php" );

    class Stats
    {
        /**
         * @var medoo
         */
        private $newMedoClass;

        /**
         * @var Config
         */
        protected $settings;

        /**
         * Stats constructor.
         */
        public function __construct()
        {
            $this->setSettings(new Config());
            $this->setNewMedoClass(new medoo([
                'database_type' => $this->getSettings()->get("db_type"),
                'database_name' => $this->getSettings()->get("db_name"),
                'server'        => $this->getSettings()->get("db_server"),
                'username'      => $this->getSettings()->get("db_username"),
                'password'      => $this->getSettings()->get("db_password"),
                'charset'       => 'utf8'
            ]));

            $this->dbPrefix = $this->getSettings()->get("db_prefix");
        }

        private function dbStoreValue($tableName, $colum, $value) {
            $this->getNewMedoClass()->insert($this->dbPrefix . $tableName, [ $colum => $value ]);

            //nxr(0, $this->getNewMedoClass()->log());
        }

        private function dbStoreIncrement($tableName, $colum, $value) {
            if ( $this->getNewMedoClass()->has($this->dbPrefix . $tableName, [ $colum => $value ]) ) {
                $used = $this->getNewMedoClass()->get($this->dbPrefix . $tableName, "used", [ $colum => $value ]);
                $this->getNewMedoClass()->update($this->dbPrefix . $tableName, [ "used" => $used + 1 ], [ $colum => $value ]);
            } else {
                $this->getNewMedoClass()->insert($this->dbPrefix . $tableName, [
                    $colum => $value,
                    "used" => 1
                ]);
            }

            //nxr(0, $this->getNewMedoClass()->log());
        }

        public function recordNewUpload( $filesize )
        {
            //nxr(0, "STATS: New $filesize SKIZ uploaded");
            $this->dbStoreValue("uploaded", "filesize", $filesize);
        }

        public function recordNewTimeZone( $timezoneName )
        {
            //nxr(0, "STATS: New TimeZone " . $timezoneName);
            $this->dbStoreIncrement("timezone", "timezone", $timezoneName);
        }

        public function recordRunCount( $count )
        {
            //nxr(0, "STATS: $count new runs found");
            $this->dbStoreValue("runs", "runs", $count);
        }

        public function recordActivityType( $lookupActivityType )
        {
            //nxr(0, "STATS: $lookupActivityType Used");
            $this->dbStoreIncrement("activity", "activity", $lookupActivityType);
        }

        public function recordRunDuration( $durationMillis )
        {
            //nxr(0, "STATS: $durationMillis duration recorded");
            $this->dbStoreValue("duration", "duration", $durationMillis);
        }

        public function recordHeartRateAvailable()
        {
            //nxr(0, "STATS: Heart Rate was available");
            $this->dbStoreValue("heart", "heart", 1);
        }

        public function recordZipDownloadSize( $filesize )
        {
            //nxr(0, "STATS: $filesize downloaded");
            $this->dbStoreValue("downloaded", "downloaded", $filesize);
        }

        public function recordDistance( $totalDistance )
        {
            //nxr(0, "STATS: $totalDistance distance");
            $this->dbStoreValue("distance", "distance", $totalDistance);
        }

        public function recordNewSession()
        {
            //nxr(0, "STATS: New session started");
            $this->dbStoreValue("sessions", "sessions", 1);
        }

        public function recordNewUser( $gen_cookie_hash )
        {
            //nxr(0, "STATS: New user $gen_cookie_hash recorded");
            $this->dbStoreIncrement("users", "user", $gen_cookie_hash);
        }

        public function recordUpdatedRun()
        {
            //nxr(0, "STATS: Updated run started");
            $this->dbStoreValue("runs_created", "created", 0);
        }

        public function recordNewRun()
        {
            //nxr(0, "STATS: New run started");
            $this->dbStoreValue("runs_created", "created", 1);
        }

        /**
         * @return mixed
         */
        private function getNewMedoClass()
        {
            return $this->newMedoClass;
        }

        /**
         * @param mixed $newMedoClass
         */
        private function setNewMedoClass( $newMedoClass )
        {
            $this->newMedoClass = $newMedoClass;
        }

        /**
         * @return \SkizImport\Config
         */
        private function getSettings()
        {
            return $this->settings;
        }

        /**
         * @param \SkizImport\Config $settings
         */
        private function setSettings( $settings )
        {
            $this->settings = $settings;
        }

        public function getCreatedFitbitActivities()
        {
            $returnValue = $this->getNewMedoClass()->count($this->dbPrefix . 'runs_created', [ 'created' => 1 ]);
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getUniqueUsers()
        {
            $returnValue = $this->getNewMedoClass()->count($this->dbPrefix . 'users');
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getUpdatedFitbitActivities()
        {
            $returnValue = $this->getNewMedoClass()->count($this->dbPrefix . 'runs_created', [ 'created' => 0 ]);
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getSkiTracksRuns()
        {
            $returnValue = $this->getNewMedoClass()->sum($this->dbPrefix . 'runs', 'runs');
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getSkiTracksSession()
        {
            $returnValue = $this->getNewMedoClass()->count($this->dbPrefix . 'sessions');
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getTotalSkiTime()
        {
            $returnValue = $this->secToHR($this->getNewMedoClass()->sum($this->dbPrefix . 'duration', 'duration'));
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getTotalSkiDistance()
        {
            $returnValue = $this->getNewMedoClass()->sum($this->dbPrefix . 'distance', 'distance');
            $returnValue = number_format(($returnValue / 1000), 2);
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue . " km";
        }

        public function getPopularActivity()
        {
            $returnValue = join(", ", $this->getNewMedoClass()->select($this->dbPrefix . 'activity', 'activity', ["ORDER" => [ "used" => "DESC" ], "LIMIT" => 3]));
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        public function getPopularTimeZone()
        {
            $returnValue = join(", ", $this->getNewMedoClass()->select($this->dbPrefix . 'timezone', 'timezone', ["ORDER" => [ "used" => "DESC" ], "LIMIT" => 3]));
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue;
        }

        private function secToHR( $seconds )
        {
            $returnString = "";
            if ( $seconds > 0 ) {
                $hours = floor($seconds / 3600);
                $minutes = floor(( $seconds / 60 ) % 60);
                $seconds = $seconds % 60;

                if ( $hours > 0 ) {
                    $returnString = $returnString . $hours . " hrs ";
                }
                if ( $minutes > 0 ) {
                    $returnString = $returnString . $minutes . " min ";
                }
                if ( $seconds > 0 ) {
                    $returnString = $returnString . $seconds . " sec";
                }
            }

            if ( $seconds == 0 ) {
                $returnString = "0 sec";
            }

            return $returnString;
        }

        public function getTotalUploadedFiles()
        {
            $returnValue = $this->getNewMedoClass()->sum($this->dbPrefix . 'uploaded', 'filesize');
            $returnValue = number_format(($returnValue / 1024), 2);
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue . " MB";
        }

        public function getTotalDownloadedFiles()
        {
            $returnValue = $this->getNewMedoClass()->sum($this->dbPrefix . 'downloaded', 'downloaded');
            $returnValue = number_format(($returnValue / 1024), 2);
            //nxr(0, $this->getNewMedoClass()->log());
            return $returnValue . " MB";
        }
    }