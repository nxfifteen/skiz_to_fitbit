<?php
    /** @var SkizImport\Upload\Receive $receiver */
    $receiver = unserialize($_SESSION['SkizImport\Upload\Receive']);
    date_default_timezone_set($receiver->getTimezoneName());
?>

<div class="row" id="ajaxOutput">
    <div class="col-12 col-md-8">
        <div class="card card-inverse card-success">
            <div class="card-header">
                <h3>Saved <?php echo count($receiver->getSkiRuns()); ?> Runs</h3>
            </div>
            <div class="card-header" id="stageName">
                <ol>
                <?php
                    foreach ( $receiver->getSkiRuns() as $skiRun ) {
                        echo "<li>"
                            . $skiRun['ACTIVITY']
                            . " Run " . $skiRun['NUMBER']
                            . " - "
                            . date("H:i:s", strtotime($skiRun['START_ZONE'])) . " to " . date("H:i:s", strtotime($skiRun['END_ZONE']))
                            . ", <em>Stored in Fitbit as (" . $skiRun['ACTIVITY_FITBIT'] . ")</em>. <a class='badge badge-pill badge-primary' href='https://www.fitbit.com/activities/exercise/".$skiRun['FITBIT']."' target='_blank'>View On Fitbit</a>"
                            . "</li>";
                    }
                ?>
                </ol>
            </div>
            <div class="card-footer">
                <a href="/upload" class="btn btn-sm btn-success"> Upload Another </a>
                <a href="<?php echo $receiver->getDownloadPath(); ?>" class="btn btn-sm btn-primary pull-right"> Download TCX ZIP Files </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="row">
            <div class="col-12">
                <div class="card card-inverse card-info" id="stageProg">
                    <?php $tracksFile = $receiver->getFileContentsTracks(); ?>
                    <div class="card-header">
                        <h4><?php echo $tracksFile['@attributes']['name']; ?></h4>
                    </div>
                    <div class="card-block">
                        Date: <strong><?php echo $receiver->getTrackDate(); ?></strong><br />
                        Description: <strong><?php echo ucwords($tracksFile['@attributes']['description']); ?></strong><br />
                        Activity: <strong><?php echo ucwords($tracksFile['@attributes']['activity']); ?></strong><br />
                        Conditions: <strong><?php echo ucwords($tracksFile['@attributes']['conditions']); ?></strong><br />
                        Weather: <strong><?php echo ucwords($tracksFile['@attributes']['weather']); ?></strong><br />
                        TimeZone, GMT Offset: <?php echo $receiver->getTimezoneName() ?> (<?php echo $receiver->getTimezoneOffset() ?>)<br />

                        <hr />
                        Total Descent Distance: <strong><?php echo number_format($tracksFile['metrics']['descentdistance'] * 0.001, 2); ?> km</strong><br />
                        Total Ascent Distance: <strong><?php echo number_format($tracksFile['metrics']['ascentdistance'] * 0.001, 2); ?> km</strong><br />
                        Average Descent Speed: <strong><?php echo number_format($tracksFile['metrics']['averagedescentspeed'] * 3.6, 2); ?> kph</strong><br />
                        Max Descent Speed: <strong><?php echo number_format($tracksFile['metrics']['maxdescentspeed'] * 3.6, 2); ?> kph</strong>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>