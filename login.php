<?php require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "autoloader.php" ); ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Import SkiTrack exported SKIZ files into Fitbit as activities">
    <meta name="author" content="Stuart McCulloch Anderson">
    <meta name="keyword" content="Fitbit, SkiTracks, Ski Tracks, Fitbit Import, SKIZ">
    <link rel="shortcut icon" href="img/favicon-32x32.png">

    <title>SkiTracks Fitbit Importer | NxFIFTEEN Rocks</title>

    <!-- Icons -->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="css/style.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
    <a href="https://nxfifteen.me.uk/gitlab/rocks/skiz" class="github-corner" aria-label="View source on Gitlab" target="_blank"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card-group mb-0">
                    <div class="card card-inverse card-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-block text-center">
                            <div>
                                <h2>Sign in with Fitbit</h2>
                                <p>
                                    Use this service to convert your <strong><a style="color: #FFF" href="https://www.corecoders.com/ski-tracks-app/" target="_blank">SkiTracks</a></strong>
                                    SKIZ export files into <strong><a style="color: #FFF" href="https://www.fitbit.com" target="_blank">Fitbit</a></strong> activity
                                </p>
                                <a href="./register.php" class="btn btn-primary active mt-3" title="Guest sign in using Fitbit">Guest sign in Now!</a>
                                <a href="./private.php" class="btn btn-danger active mt-3" title="Owners sign in here, this will not work for anyone else due to Fitbit OAuth rules">Site owner sign in here!</a>
                            </div>
                        </div>
                    </div>
                    <div class="card card-inverse card-info py-5 d-md-down-none" style="width:44%">
                        <div class="card-block text-center">
                            <div>
                                <h2>Proudly Open Source</h2>
                                <p>This is an <strong><a style="color: #FFF" href="https://nxfifteen.me.uk/rocks">NxFIFTEEN Rocks</a></strong> project. The service is offered as a free service to anyone who wants to use it and is a freely available open source project</p>
                                <a href="https://nxfifteen.me.uk/gitlab/rocks/skiz" class="btn btn-primary active mt-3">View source on Gitlab</a>
                                <a href="https://nxfifteen.me.uk/gitlab/rocks/skiz/wikis/privacy-policy" class="btn btn-primary active mt-3">Privacy Policy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            $siteStats = new \SkizImport\Stats();
        ?>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-group mb-0">
                    <div class="card py-5 d-md-down-none" style="width:44%">
                        <div class="card-block">
                            <div>
                                <h2 class="text-center">We've Processed..</h2>
                                <ul>
                                    <li><?php echo $siteStats->getCreatedFitbitActivities(); ?> new Fitbit activities for <?php echo $siteStats->getUniqueUsers(); ?> users</li>
                                    <li>Produced a further <?php echo $siteStats->getUpdatedFitbitActivities(); ?> TCX files</li>
                                    <li>Received <?php echo $siteStats->getTotalUploadedFiles(); ?> worth of SKIZ files</li>
                                    <li>Provided <?php echo $siteStats->getTotalDownloadedFiles(); ?> with of zip'd TCX file</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card card-inverse card-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-block">
                            <div>
                                <h2 class="text-center">&amp;</h2>
                                <ul>
                                    <li><?php echo $siteStats->getSkiTracksRuns(); ?> SkiTrack runs over <?php echo $siteStats->getSkiTracksSession(); ?> session</li>
                                    <li><?php echo $siteStats->getTotalSkiTime(); ?> of slope time</li>
                                    <li>Covering <?php echo $siteStats->getTotalSkiDistance(); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card card-inverse card-info py-5 d-md-down-none" style="width:44%">
                        <div class="card-block">
                            <div>
                                <h2 class="text-center">Most popular...</h2>
                                <ul>
                                    <li>Activity is <?php echo $siteStats->getPopularActivity(); ?></li>
                                    <li>Time Zone is <?php echo $siteStats->getPopularTimeZone(); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and necessary plugins -->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/tether/dist/js/tether.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>

</body>

</html>
