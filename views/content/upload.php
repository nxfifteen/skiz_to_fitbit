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
?>
<form id="upload_skitracks" action="/upload/send" method="post" enctype="multipart/form-data">
    <input type="hidden" name="formId" value="uploadSkiTracks">

    <div class="row">

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Ski Track Upload
                </div>
                <div class="card-block">

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="skizfile">Skiz File</label>
                                <input type="file" class="form-control-file" id="skizfile" name="skizfile"
                                       accept=".skiz">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-primary">Upload Skiz File</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</form>