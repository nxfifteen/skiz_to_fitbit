<div class="row">

    <div class="col-sm-6 col-md-4">
        <div class="card">
            <div class="card-header">
                Welcome
            </div>
            <div class="card-block">
                <p>I'm really bad at writing documentation, so this will have to evolve over time. Basic instructions
                    are
                    to select 'Upload' from the menu on the left and upload your SKIZ file from Ski Tracks. After
                    confirming
                    your happy with the runs detected click 'Save Runs' to import them into Fitbit.</p>

                <p>Once the runs are saved you will be able to download a ZIP file containing all your processed TCX
                    tracks. These files are available for a maximum of 20 minutes before being deleted.</p>

                <p>Its worth noting that the Fitbit API will not record duplicate activities, so you can upload the same
                    SKIZ file as many times are you would like without worrying about duplication.</p>
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-md-8">

        <div class="col-sm-12">
            <div class="card card-info card-inverse">
                <div class="card-header">
                    SkiTracks
                </div>
                <div class="card-block">
                    Is the worlds most downloaded Ski Tracking app
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="card card-primary card-inverse">
                <div class="card-header">
                    Fitbit
                </div>
                <div class="card-block">
                    Well you all know Fitbit, you wouldn't see this page without an account
                </div>
            </div>
        </div>

    </div>

    <div class="col-sm-12">
        <div class="card card-warning card-inverse">
            <div class="card-header">
                Limitations
            </div>
            <div class="card-block">
                <p>The Fitbit API wont allow us to send GPS track data to the site. That means, while I can create
                    Skiing activities for you, I can not get them to include the GPS data. Instead I bundle all your Ski
                    tracks into TCX files and offer them as a ZIP download.</p>

                <p>There are also limited activity types on Fitbit for winter sports, for example there is no Snow
                    Boarding activity. There for if there is not closer match activites are recorded as skiiing.</p>
            </div>
        </div>
    </div>

    <?php if ( $_COOKIE[ '_nx_skiz_usr' ] == "269VLG" ) { ?>
        <?php $siteStats = new \SkizImport\Stats(); ?>
        <div class="col-md-12">
            <div class="card-group mb-0">
                <div class="card  d-md-down-none">
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
                <div class="card card-inverse card-primary  d-md-down-none">
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
                <div class="card card-inverse card-info  d-md-down-none">
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
    <?php } ?>
</div>