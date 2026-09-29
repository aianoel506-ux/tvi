var FormEditable = function () {
    var profile_portlet = $('#');
    var requirementstable = $('#requirementstable');
    var dataid = $('#dataid').attr('data-id');
    var origin = $('#dataid').attr('data-origin');

    //scheduling
    var schedtable  = $('#schedtable',document);


    init_profile = function () {
       // IMPORTANT ADD data-toggle="editable" TO PORTLET
      //  PECO.confirmEditable(init_input_editable, $('.data-entry-btn'));
        init_requirements_stat();
        populate_requirements_list();
    };

    //get data from db
    var populate_requirements_list = function(){
        $.ajax({
            url:PECO.base_url()+"admin/populaterequirementslist",
            type:"post",
            data:{"dataid":dataid , "origin":origin},
            dataType:"json",
            beforeSend: function(){
                requirementstable.dataTable().empty();
                PECO.DTphpLoading(requirementstable, 'Loading... ');
            }
        }).done(function (d) {
            populate_requirements_table(d);
        });
    };

    //Auto-Assign all uploaded attachments
    $('#btn_assign_all',document).on('click',function () {
        swal({
            title: "Assign all attachments?",
            text: "All attachments will be auto-assigned.",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-info",
            confirmButtonText: "Yes, do it!",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true
        }, function(isConfirm){
            if (isConfirm) {
                $.ajax({
                    url: base_url + 'cad/autoassignrequirements',
                    type: 'post',
                    data: {
                        dataid : dataid
                    },
                    dataType:'json'
                }).done(function (data) {
                    swal("PECO" , data.msg , data.func);
                    populate_requirements_list();
                }).fail(function () {
                    swal.close();
                });
            } else {
                swal("Cancelled", "Auto-assigning was cancelled.", "error");
            }
        });

    });

    $('#btn_reload_attachments',document).on('click',function () {
        populate_requirements_list();
    });

    //populate data to table
    var populate_requirements_table = function (data) {
        requirementstable.dataTable().empty();
        requirementstable.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.requirementslist,
            aoColumns: [
                {"data":"num" , sWidth: '10%'},
                {"data":"requirements" , sWidth: ''},
                {"data":"comply" , sWidth: '10%'},
                {"data":"control" , sWidth: '100px'}
            ],
            searchHighlight: true
        });
    };

    var init_requirements_stat = function() {



        $('#seniordatefrom').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Validity',
            inputclass: '',
            placeholder: 'From',
            emptytext: 'From',
            placement: 'top'
        });
        $('#seniordateto').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Validity',
            inputclass: '',
            placeholder: 'To',
            emptytext: 'To',
            placement: 'top'
        });

        $('#seniorvaliid').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter valid ID',
            inputclass: 'form-control',
            placeholder: 'valid ID',
            emptytext: 'Enter valid ID',
            placement: 'right'
        });


        $('#essrnoprofile').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter ESSR No.',
            inputclass: 'form-control input-large',
            emptytext: 'Enter ESSR No.',
            placeholder: ' ESSR No.',
            placement: 'bottom',

          /*  params: function(params) {  //params already contain `name`, `value` and `pk`
                return '<h3 class="bold">'+params.value+'</h3>';
            } */
        });


        $(document).on('click' , '#filenameselected' , function (e) {
            var url = $(this).attr("src");
            var id = $('#requirementshiddenval').val();
            $.ajax({
                url:base_url+"admin/updatecustomerrequirements",
                type:"post",
                data:{"url":url ,"id":id , "dataid":dataid},
                dataType:'json',
                beforeSend: function(){
                    $(this).html('Loading..');
                }
            }).done(function (d) {
                var url = d.url;
               $('#assignfilemodallist').modal('hide');
                populate_requirements_list();
            });

        });

        /*
        $.ajax({
            url: PECO.base_url() + 'cad/getacctrequirements',
            type: 'post',
            data: {'id': dataid},
            dataType: 'json',
        }).done(function(d){
            $('#req_stat').html(d.comp + ' / ' + d.res);
        }).fail(function(){
            PECO.phpError();
        });*/

        $('#btn_print_req').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            PECO.print_acct_requirements(this_.attr('data-id'));
            console.log('Printing requirements...');
        });
    };

    var init_delete_coowner = function() {

    };

    var init_input_editable;
    init_input_editable = function () {
        //$.fn.editable.defaults.mode = 'inline';


        $('#firstname').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter firstname',
            inputclass: 'form-control input-medium',
            placeholder: 'Firstname',
            showbuttons: false
        });

        $('#lastname').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter lastname',
            inputclass: 'form-control input-medium',
            placeholder: 'Lastname',
            showbuttons: false
        });

        $('#middlename').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter middlename',
            inputclass: 'form-control input-medium',
            placeholder: 'Middlename',
            showbuttons: false
        });

        $('#contactnumber').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Contact Number',
            inputclass: 'form-control input-medium',
            placeholder: 'Contact Number'
        });

        $('#gender').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Contact Number',
            inputclass: 'form-control input-medium',
            placeholder: 'Contact Number',
            limit: 1,
            source: [
                {value: 1, text: 'Male'},
                {value: 2, text: 'Female'},
            ]
        });

        $('#status').editable({
            success: function (response, newValue) {

                console.log('RES: ' + response);
                if (!response.success) return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;

                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/marital',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },

            },

            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Contact Number',
            inputclass: 'form-control input-medium',
            placeholder: 'Marital Status',
        });

        $('#gender').on('shown', function (e, reason) {
            PECO.initUniform();
        });

        $('#birthdate').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Contact Number',
            inputclass: '',
            placeholder: 'Contact Number'
        });

        $('#address').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Address',
            inputclass: 'form-control input-large',
            placeholder: 'Address',
        });

        $('#coowner').editable({

            success: function (response, newValue) {
                if (!response.success) return response.msg;
            },
            error: function (response, newValue) {
                if (response.status === 500) {
                    return 'Service unavailable. Please try later.';
                } else {
                    return response.responseText;
                }
            },
            select2: {
                //tags: [],
                allowClear: true,
                width: '200px',
                id: function (item) {
                    return item.id;
                },
                ajax: {
                    url: PECO.base_url() + 'query/editableselect2',
                    type: 'post',
                    dataType: 'json',
                    data: function (term) {
                        return {
                            term: term,
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                };
                            })
                        };
                    }
                },
            },

            url: PECO.base_url() + 'cad/editinfo',
            title: 'Co-Owner',
            placeholder: 'Search person..',
            inputclass: 'form-control input-medium',
            emptytext: 'Co-Owner..',
            placement: 'bottom',
        });

        $('#tin').editable({
            url: PECO.base_url() + 'cad/editinfo',
            title: 'Enter Tin Number',
            inputclass: 'form-control input-medium',
            placeholder: 'Tin Number',
        });

        /*
         $('#coowner').each(function(){
         $(this).editable('option','source',$(this).data('getSource'));
         });
         */
    };

    PECO.select2Basic($('#additional_appreq',document),'cad/getadditionalrequirements','Additional Requirements...',false,false,false,false,false,dataid);

    $('#btn_add_requirement',document).on('click', function () {
        var reqid = $('#additional_appreq',document).val();
        $.ajax({
            url: base_url + 'cad/addrequirement',
            type: 'post',
            dataType: 'json',
            data: {
                appid : dataid,
                reqid : reqid
            }
        }).done(function (d) {
            PECO.initAlerts(d.msg,d.title,d.func);
            populate_requirements_list();
            PECO.select2Basic($('#additional_appreq',document),'cad/getadditionalrequirements/'+dataid,'Additional Requirements...',false,false,false);
        });
    });

    return {
        //main function to initiate the module
        init: function () {
            init_profile();
        }

    };

}();