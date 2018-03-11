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

    namespace SkizImport;

    require_once( dirname(__FILE__) . "/../autoloader.php" );

    class SkizImport
    {
        /**
         * @var Config
         */
        protected $settings;

        /**
         * SkizImport constructor.
         */
        public function __construct( )
        {
            $this->setSettings(new Config());
        }

        /**
         * @return \SkizImport\Config
         */
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * Get settings from config class
         *
         * @param string $key        Settings key to return
         * @param null   $default    Default value, if nothing already held in settings
         * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
         *
         * @return string
         */
        public function getSetting( $key, $default = NULL )
        {
            return $this->getSettings()->get($key, $default);
        }

        /**
         * @param \SkizImport\Config $settings
         */
        private function setSettings( $settings )
        {
            $this->settings = $settings;
        }


    }