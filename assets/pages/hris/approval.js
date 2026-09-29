var APPROVAL = function () {
    var tbl_leave_requests = $('#tbl_leave_requests', document);


    var fn_approval_handler = function () {
        tbl_approval_handler();
        $(document).on('click', '#btn_row_approval', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var groupid = this_.attr('data-groupid');
            var approvalid = this_.attr('data-approvalid');
            var empid = this_.attr('data-empid');

            swal({
                title: "Approve Leave Application?",
                text: 'Approve',
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'hris/approveleaverequest',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            'empid': empid,
                            'groupid': groupid,
                            'approvalid': approvalid
                        }
                    }).done(function (d) {
                        swal('Approval', d.msg, 'success');
                        tbl_approval_handler();
                    }).fail(function () {
                        swal('Approval', d.msg, 'warning');
                    })
                }
            });
        });
        PECO.dtSubDetails(tbl_leave_requests,'hris/leaveapprovaldetails');
    };

    var fn_approval_reports = function() {

    };

    var tbl_approval_handler = function() {
        $.ajax({
            url: PECO.base_url() + 'hris/dtleaveforapprovalrequest',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_leave_requests, 'Loading leave requests...');
            }
        }).done(function(d){
            $('#tbl_leave_requests' , document).dataTable().empty();
            $('#tbl_leave_requests' , document).dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list,
                aoColumns: [
                    {"data":"num", sWidth: '10px'},
                    {"data":"name"},
                    {"data":"remarks"},
                    {"data":"duration", sWidth: '15%'},
                    {"data":"created", sWidth: '10%'},
                    {"data":"head"},
                    {"data":"executive"},
                    {"data":"control"},
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow) {
                    $('button', nRow).tooltip();
                }
            });
        }).fail(function() {
            PECO.DTphpError(tbl_leave_requests, '<h3 class="text-danger">PHP ERROR: Leave Requests</h3>');
        });
    };

    return{
        init: function () {
            fn_approval_handler();
        },
        reports: function() {
            fn_approval_reports();
        }
    }
}();