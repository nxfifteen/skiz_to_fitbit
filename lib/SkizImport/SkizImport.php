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

    namespace SkizImport;

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "autoloader.php" );

    use djchen\OAuth2\Client\Provider\Fitbit;
    use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
    use League\OAuth2\Client\Token\AccessToken;

    define("FITBIT_COM", "https://api.fitbit.com");

    /**
     * Class SkizImport
     * @package SkizImport
     */
    class SkizImport
    {
        /**
         * @var Config
         */
        protected $settings;

        /**
         * @var Fitbit
         */
        protected $fitbitLibrary;

        /**
         * @var
         */
        private $activitId;

        /**
         * SkizImport constructor.
         */
        public function __construct()
        {
            $this->setSettings(new Config());
        }

        /**
         * @param \SkizImport\Config $settings
         */
        private function setSettings( $settings )
        {
            $this->settings = $settings;
        }

        /**
         * @param      $path
         * @param bool $returnObject
         * @return mixed|null
         */
        public function pullFitbit( $path, $returnObject = FALSE )
        {
            if ( is_null($this->fitbitLibrary) ) {
                if ( $_COOKIE[ '_nx_skiz_usr' ] == $this->getSetting("ownerFuid") ) {
//                    nxr(0, "Private Keys Used");
                    $personal = "_personal";
                } else {
//                    nxr(0, "Public Keys Used");
                    $personal = "";
                }

                $this->fitbitLibrary = new Fitbit([
                    'clientId'     => $this->getSetting("api_clientId" . $personal, NULL),
                    'clientSecret' => $this->getSetting("api_clientSecret" . $personal, NULL),
                    'redirectUri'  => $this->getSetting("http/") . "register.php"
                ]);
            }

            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            $path = str_replace(FITBIT_COM . "/1/", "", $path);

            $request = $this->fitbitLibrary->getAuthenticatedRequest('GET', FITBIT_COM . "/1/" . $path, $accessToken);
            // Make the authenticated API request and get the response.

            try {
                $response = $this->fitbitLibrary->getParsedResponse($request);
            } catch ( IdentityProviderException $e ) {
                return NULL;
            }

            if ( $returnObject ) {
                $response = json_decode(json_encode($response), FALSE);
            }

            return $response;
        }

        /**
         * Get settings from config class
         *
         * @param string $key     Settings key to return
         * @param null   $default Default value, if nothing already held in settings
         * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
         *
         * @return string
         */
        public function getSetting( $key, $default = NULL )
        {
            return $this->getSettings()->get($key, $default);
        }

        /**
         * @return \SkizImport\Config
         */
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * @SuppressWarnings(PHPMD.ExitExpression)
         * @return AccessToken
         */
        private function getAccessToken()
        {
            $userArray = json_decode($_SESSION[ 'accessToken' ], TRUE);
            if ( is_array($userArray) ) {
                $accessToken = new AccessToken([
                    'access_token'  => $userArray[ 'access_token' ],
                    'refresh_token' => $userArray[ 'refresh_token' ],
                    'expires'       => $userArray[ 'expires' ]
                ]);

                if ( $accessToken->hasExpired() ) {
                    nxr(0, "This token as expired and needs refreshed");

                    $newAccessToken = $this->fitbitLibrary->getAccessToken('refresh_token', [
                        'refresh_token' => $accessToken->getRefreshToken()
                    ]);

                    // Purge old access token and store new access token to your data store.
                    return $newAccessToken;
                } else {
                    return $accessToken;
                }
            } else {
                nxr(0, 'User does not exist, unable to continue.');
                exit;
            }
        }

        /**
         * @param $string
         * @return mixed|null
         */
        public function getActivitId( $string )
        {
            if ( is_null($this->activitId) ) {
                $this->activitId = [];
            }

            if ( array_key_exists($string, $this->activitId) ) {
                return $this->activitId[ $string ];
            } else {
                return NULL;
            }
        }

        /**
         * @param $key
         * @param $id
         */
        public function setActivitId( $key, $id )
        {
            if ( is_null($this->activitId) ) {
                $this->activitId = [];
            }

            $this->activitId[ $key ] = $id;
        }

        /**
         * @param      $path
         * @param      $pushObject
         * @param bool $returnObject
         * @param bool $parseResponse
         * @return array|mixed|\Psr\Http\Message\ResponseInterface
         */
        public function pushFitbit( $path, $pushObject, $returnObject = FALSE, $parseResponse = FALSE )
        {
            if ( is_null($this->fitbitLibrary) ) {
                if ( $_COOKIE[ '_nx_skiz_usr' ] == $this->getSetting("ownerFuid") ) {
//                    nxr(0, "Private Keys Used");
                    $personal = "_personal";
                } else {
//                    nxr(0, "Public Keys Used");
                    $personal = "";
                }

                $this->fitbitLibrary = new Fitbit([
                    'clientId'     => $this->getSetting("api_clientId" . $personal, NULL),
                    'clientSecret' => $this->getSetting("api_clientSecret" . $personal, NULL),
                    'redirectUri'  => $this->getSetting("http/") . "register.php"
                ]);
            }

            // Try to get an access token using the authorization code grant.
            $accessToken = $this->getAccessToken();

            if ( is_array($pushObject) ) {
                $pushObject = http_build_query($pushObject);
            }

            $request = $this->fitbitLibrary->getAuthenticatedRequest('POST',
                FITBIT_COM . "/1/" . $path, $accessToken,
                [
                    "headers" =>
                        [
                            "Accept-Header" => "en_GB",
                            "Content-Type"  => "application/x-www-form-urlencoded"
                        ],
                    "body"    => $pushObject
                ]);
            // Make the authenticated API request and get the response.

            $response = $this->fitbitLibrary->getResponse($request);


            if ( $parseResponse ) {
                if ( $returnObject ) {
                    $response = json_decode(json_encode($this->fitbitLibrary->parseResponse($response)), FALSE);
                } else {
                    $response = $this->fitbitLibrary->parseResponse($response);
                }
            } else if ( $returnObject ) {
                $response = json_decode(json_encode($response), FALSE);
            }

            return $response;
        }


    }