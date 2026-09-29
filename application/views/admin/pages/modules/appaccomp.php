<div class="btn-group pull-right">
    <button class="btn btn-danger" id="btn_clear_cadtrans"><i class="fa fa-times"></i> <i class="fa fa-warning"></i> Clear Applications Transactions</button>
    <button class="btn btn-default"><i class="fa fa-print"></i> Reports</button>
</div>


<table class="table table-hover table-striped" id="tbl_application_accomp">
    <thead>
    <th><i class="fa fa-reorder"></i></th>
    <th>Name</th>
    <th>Address</th>
    <th>Rate</th>
    <th>Deposit</th>
    <th>Status</th>
    <th></th>
    </thead>
</table>




<script>
    var APPACCOMPLISHMENTS = function() {
        PECO.getSweetAlert();

        var init_applications_accomplishments = function() {
            $('#btn_clear_cadtrans', document).click(function(e) {
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "Clear C.A.D. Transactions, will delete all temporary transactions. Note: this is for development porpuses only.",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Clear!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.post(PECO.base_url() + 'cad/clearcadtrans', function (d) {
                            swal.close();
                            PECO.initAlerts(d.msg, 'PECO.net', d.func);
                        }, 'json');
                    }else{
                        swal.close();
                    }
                });
            });
        };

        return {
            init: function() {
                init_applications_accomplishments();
            }
        }

    }();


    APPACCOMPLISHMENTS.init();
</script>