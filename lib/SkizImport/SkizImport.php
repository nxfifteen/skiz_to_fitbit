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

    use djchen\OAuth2\Client\Provider\Fitbit;
    use GuzzleHttp\Exception\BadResponseException;
    use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
    use League\OAuth2\Client\Token\AccessToken;

    define("FITBIT_COM", "https://api.fitbit.com");

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

        private $activitId;

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

        public function pullFitbit( $path, $returnObject = false )
        {
            if (is_null($this->fitbitLibrary)) {
                $personal = "_personal";

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
            } catch (IdentityProviderException $e) {
                return null;
            }

            if ( $returnObject ) {
                $response = json_decode(json_encode($response), FALSE);
            }

            return $response;
        }

        /**
         * @SuppressWarnings(PHPMD.ExitExpression)
         * @return AccessToken
         */
        private function getAccessToken()
        {
            $userArray = json_decode($_SESSION['accessToken'], true);
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

        public function setActivitId( $key, $id )
        {
            if (is_null($this->activitId)) {
                $this->activitId = [];
            }

            $this->activitId[$key] = $id;
        }

        public function getActivitId( $string )
        {
            if (is_null($this->activitId)) {
                $this->activitId = [];
            }

            if ( array_key_exists($string, $this->activitId) ) {
                return $this->activitId[$string];
            } else {
                return null;
            }
        }

        public function pushFitbit( $path, $pushObject, $returnObject = FALSE, $parseResponse = FALSE )
        {
            if (is_null($this->fitbitLibrary)) {
                $personal = "_personal";

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