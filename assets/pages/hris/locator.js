var LOCATOR  = function(){


    var init_main = function(){
        init_events();
    };

    var init_events = function(){
        $(document).on('submit','#submitlocatorslip',function (e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: "Leave will be sent for approval.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm){
                if (isConfirm) {
                    var this_ = $(this);
                    $.ajax({
                        url:this_.attr("action"),
                        type:this_.attr("method"),
                        data:this_.serialize(),
                        dataType:'json'
                    }).done(function (d) {
                        swal("Sent!", d.msg, d.func);
                        $('#submitlocatorslip')[0].reset();
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Processing canceled", "error");
                }
            });


        });
    };

    return{
        init:function(){
            init_main();
        }
    }
}();