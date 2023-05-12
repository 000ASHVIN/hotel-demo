<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4><?php echo display("phrase_update") ?></h4>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Bulk Upload
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Bulk Upload</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- <form action="<?php echo base_url('add-phrase-csv') ?>" method="post" enctype="multipart/form-data" onsubmit="return csvupload()"> -->
                            <?php echo form_open_multipart('dashboard/language/csvform'); ?>
                           
                                <div>
                                    Download <a href="<?php echo base_url('/edit-phrase/csv/sample') . '/' .$language; ?>">Sample</a>
                                </div>

                                <input type="hidden" name="csrf_test_name" id='csrf_token' value="<?php echo $this->security->get_csrf_hash();?>" />

                                <div class="form-group row">
                                    <label for="file" class="col-sm-2 gallery-inp-hi">File<i
                                            class="text-danger"> * </i></label>
                                    <div class="col-sm-9">
                                        <div>
                                            <input type="file" name="csv_file" id="file" class="custom-input-file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                                            <label for="file">
                                                <i class="fa fa-upload"></i>
                                                <span><?php echo display('choose_file'); ?>…</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary" onclick="return csvupload()">Save changes</button>
                                </div>
                            <!-- </form> -->
                            <?php echo form_close(); ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <?php echo form_open('dashboard/language/addlebel') ?>
                <table id="exdatatable" class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <td colspan="4">
                                <button type="reset" class="btn btn-danger"><?php echo display('reset') ?></button>
                                <button type="submit" class="btn btn-success"><?php echo display('save') ?></button>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="fa fa-th-list"></i></th>
                            <th><?php echo display('phrase_name') ?></th>
                            <th><?php echo display('label') ?></th>
                            <th hidden><?php echo display('language') ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php echo form_hidden('language', $language) ?>
                        <?php if (!empty($phrases)) {?>
                        <?php $sl = 1 ?>
                        <?php foreach ($phrases as $value) {?>
                        <tr <?php echo (empty($value->$language)?"class='background_purchase_edit'":null) ?>>

                            <td><?php echo $sl++ ?></td>
                            <td>
                                <l hidden><?php echo html_escape($value->phrase) ?></l><input type="text"
                                    name="phrase[]" value="<?php echo html_escape($value->phrase) ?>"
                                    class="form-control" readonly>
                            </td>
                            <td>
                                <l hidden><?php echo html_escape($value->$language) ?></l><input type="text"
                                    name="lang[]" value="<?php echo html_escape($value->$language) ?>"
                                    class="form-control">
                            </td>
                            <td><input type="hidden" name="language" value="<?php echo html_escape($language) ?>"
                                    class="form-control"></td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button type="reset" class="btn btn-danger">Reset</button>
                                <button type="submit" class="btn btn-success">Save</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toastrErrorMsg(r) {
        setTimeout(function () {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 1500,
            };
            toastr.error(r);
        }, 1000);
    }

    function toastrSuccessMsg(r) {
        setTimeout(function () {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                showMethod: 'slideDown',
                timeOut: 1500,
            };
            toastr.success(r);
        }, 1000);
    }

    function csvupload() {
        var fd = new FormData();
        var base_url = $("#base_url").val();
        var CSRF_TOKEN = $('#csrf_token').val();
        var file = $('#file').val().split('.').pop().toLowerCase();

        if($.inArray(file, ['xlsx', 'xls', 'csv']) == -1) {
            toastrErrorMsg("File is Required or Invalid");
            return false;
        }

        fd.append('csv_file', $('#file')[0].files[0]);
        fd.append('csrf_test_name', CSRF_TOKEN);

        // $.ajax({
        //     url: base_url + "dashboard/language/csvform",
        //     type: "POST",
        //     data: fd,
        //     enctype: 'multipart/form-data',
        //     processData: false,
        //     contentType: false,
        //     success: function (r) {
        //         toastr.success("<h5>Success</h5>Save Successfully");         
        //         if(r.substr(4,1)==="F")
        //         toastrErrorMsg(r);
        //             // window.location.reload();
        //     }
        // });
        console.log($('#csrf_token').val());
    }

    

</script>