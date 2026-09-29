<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed table-bordered" id="onleaveemployeetbl">
            <thead>
                <th></th>
                <th>Lastname</th>
                <th>Firstname</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>From Time</th>
                <th>To Time</th>
                <th>Leave Date</th>
                <th>Leave Type</th>
                <th>Type</th>
                <th>Date Created</th>
            </thead>
        </table>
    </div>
</div>

<script>
    var to_date = $(document).find('#to_date').val();
    var onleaveemployeetbl = $(document).find('#onleaveemployeetbl');
    if(to_date != ''){
        $.ajax({
            url:PECO.base_url()+'hris/getonleaveemp',
            type:'post',
            data:{"date":to_date},
            dataType:'json'
        }).done(function (data) {
            onleaveemployeetbl.dataTable().empty();
            onleaveemployeetbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: data.dataemp,
                aoColumns: [
                    {"data":"num"},
                    {"data":"lastname"},
                    {"data":"firstname"},
                    {"data":"fromdate"},
                    {"data":"todate"},
                    {"data":"fromtime"},
                    {"data":"totime"},
                    {"data":"leavedate"},
                    {"data":"leavedesc"},
                    {"data":"type"},
                    {"data":"datecreated"}
                ],
                searchHighlight: true
            });
        }).fail(function () {
            PECO.phpError();
        });
    }
</script>