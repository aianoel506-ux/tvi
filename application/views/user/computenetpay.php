<div class="page-content-wrapper">
    <div class="page-content  animated fadeInUp fast">
        <div class="row">

            <div class="col-md-3">
                <form class="" id="compute_form" action="<?php echo base_url('admin/computenetpayprocess'); ?>" method="post">
                    <h3>Filter</h3>
                    <div class="form-group">
                        <label>Year</label>
                        <input class="form-control" name="year" placeholder="year" value="<?php echo date('Y'); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Month</label>
                        <input id="select2month" class="form-control" name="month" placeholder="month" />
                    </div>
                    <div class="form-group">
                        <label>Employee</label>
                        <input id="empselect" class="form-control" name="empid" placeholder="Employee" />
                    </div>
                    <div class="form-group">
                        <label>Pay Type</label>
                        <select class="form-control" name="paytype" placeholder="Pay Type" >
                            <option></option>
                            <option value="1">1st Half</option>
                            <option value="1">2nd Half</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pay Class</label>
                        <select class="form-control" name="payclass" placeholder="Pay Class" >
                            <option></option>
                            <option value="1">Confidential</option>
                            <option value="128">Rank And File</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default">Compute</button>
                    </div>
                </form>
            </div>
            <div class="col-md-9">
                <h3>PRE Preview</h3>
                <pre id="compute_preview">
                    <?php
                        /**
                         * Created by PhpStorm.
                         * User: fader
                         * Date: 3/21/2018
                         * Time: 8:17 AM
                         */
                        print_r($compute);
                    ?>
                </pre>
            </div>
        </div>
        <script>

            PECO.getSweetAlert();
            PECO.getSelect2Plugins();
            PECO.select2Basic($('#select2month'), 'systems/select2month', 'Select Month...', false, false, '<?php echo (int)date('m');?>');

            var compute_form                = $('#compute_form');
            var compute_preview             = $('#compute_preview');

            compute_form.submit(function(e){
                var form = $(this);
                e.preventDefault();
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'html'
                }).done(function(d) {
                    compute_preview.text(d);
                });
            });

            PECO.employeeSelectTagging($('#empselect'), true, 159);

        </script>
    </div>
</div>
