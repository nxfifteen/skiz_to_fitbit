<?php
    /*******************************************************************************
     * This file is part of NxFIFTEEN Fitness Core.
     * Copyright (c) 2018. Stuart McCulloch Anderson
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     *
     * @package     Core
     * @version     0.0.1.x
     * @since       0.0.0.1
     * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
     * @link        https://nxfifteen.me.uk NxFIFTEEN
     * @link        https://nxfifteen.me.uk/nxcore Project Page
     * @link        https://nxfifteen.me.uk/gitlab/rocks/core Git Repo
     * @copyright   2018 Stuart McCulloch Anderson
     * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
     */

    namespace SkizImport;

    require_once( dirname(__FILE__) . "/../autoloader.php" );

    class Config
    {

        /**
         * Array holding application settings
         *
         * @var array
         */
        private $settings = [];

        /**
         * Class constructor
         *
         * @codeCoverageIgnore
         * @SuppressWarnings(PHPMD.Superglobals)
         */
        public function __construct()
        {
            if ( isset($_SESSION) && is_array($_SESSION) && array_key_exists("core_config", $_SESSION) && count($_SESSION[ 'core_config' ]) > 0 ) {
                $this->settings = $_SESSION[ 'core_config' ];
            } else {
                require_once( dirname(__FILE__) . "/../../config/config.dist.php" );
                if ( isset($config) ) {
                    $_SESSION[ 'core_config' ] = $config;
                    $this->settings = $_SESSION[ 'core_config' ];
                }
            }
        }

        /**
         * Return setting value
         * Main function called to query settings for value. Default value can be provided, if not NULL is returned.
         * Values can be queried in the database or limited to config file and 'live' values
         *
         * @param string $key        Setting to query
         * @param string $default    Default value to return
         *
         * @return string Setting value, or default as per defined
         */
        public function get( $key, $default = NULL )
        {
            if (array_key_exists($key, $this->settings)) {
                return $this->settings[ $key ];
            } else {
                return $default;
            }
        }

        /**
         * Set setting value
         * Function to store/change setting values. Values can be stored in the database or held in memory.
         *
         * @param string $key        Setting to query
         * @param string $value      Value to store
         * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
         *
         * @return bool was data stored correctly
         */
        public function set( $key, $value )
        {
            $this->settings[ $key ] = $value;
            return TRUE;
        }

    }