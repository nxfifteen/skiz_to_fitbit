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
                                <input type="file" class="form-control-file" id="skizfile" name="skizfile" accept=".skiz">
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