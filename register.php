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

    use SkizImport\Stats;

    require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php" );

    // Setup the App
    $appClass = new SkizImport\SkizImport();

    $stateGet = filter_input(INPUT_GET, 'state', FILTER_SANITIZE_STRING);
    if ( empty($stateGet) || $stateGet !== $_SESSION[ 'oauth2state' ] ) {
        // Authorise a user against Fitbit's OAuth AIP
        nxr(0, "New GUEST user registration started");

        // Sent the user off too Fitbit to authenticate
        $helper = new djchen\OAuth2\Client\Provider\Fitbit([
            'clientId'     => $appClass->getSetting("api_clientId"),
            'clientSecret' => $appClass->getSetting("api_clientSecret"),
            'redirectUri'  => $appClass->getSetting("http/") . "register.php"
        ]);

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $helper->getAuthorizationUrl([
            'scope' => [
                'activity',
                'heartrate',
                'profile'
            ]
        ]);

        // Get the state generated for you and store it to the session.
        $_SESSION[ 'oauth2state' ] = $helper->getState();

        // Redirect the user to the authorization URL.
        header('Location: ' . $authorizationUrl);
    } else {
        nxr(0, "New GUEST user registration completed");

        $helper = new djchen\OAuth2\Client\Provider\Fitbit([
            'clientId'     => $appClass->getSetting("api_clientId"),
            'clientSecret' => $appClass->getSetting("api_clientSecret"),
            'redirectUri'  => $appClass->getSetting("http/") . "register.php"
        ]);

        // Try to get an access token using the authorization code grant.
        $accessToken = $helper->getAccessToken('authorization_code', [
            'code' => filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING)
        ]);

        // Find out who the new OAuth keys belong too
        $resourceOwner = $helper->getResourceOwner($accessToken);

        $_SESSION[ 'accessToken' ] = json_encode($accessToken);
        $_SESSION[ 'resourceOwner' ] = json_encode($resourceOwner->toArray());

        setcookie(
            '_nx_skiz_usr',
            $resourceOwner->getId(),
            0,
            '/',
            filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING), TRUE, FALSE
        );

        setcookie(
            '_nx_skiz',
            gen_cookie_hash($appClass->getSetting("salt"), $resourceOwner->getId()),
            0,
            '/',
            filter_input(INPUT_SERVER, 'SERVER_NAME', FILTER_SANITIZE_STRING), TRUE, FALSE
        );

        $stats = new Stats();
        $stats->recordNewUser(gen_cookie_hash($appClass->getSetting("salt"), gen_cookie_hash($appClass->getSetting("salt"), $resourceOwner->getId())));

        // Redirect the user to the authorization URL.
        header('Location: ' . $appClass->getSetting("http/") . "#main.html");
    }
