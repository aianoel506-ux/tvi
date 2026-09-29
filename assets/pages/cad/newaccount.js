
var CAD  = function() {

    PECO.getSelect2Plugins();
    PECO.getSweetAlert();

    var form = $('#frm_newaccount');
    var steps = $('.steps');

    var print_requirements = function() {
        var html = '';
        $.ajax({
            url: PECO.base_url() + 'cad/getrequirementsres',
            type: 'post',
            data: {
                'ids': $('#acct_req').val()
            },
            dataType: 'json'
        }).done(function (data) {
            var req_num = data.list.length;
            html += '<ul class="list-group summary column">';
            for (req = 0; req < req_num; req++) {
                //var req_text = data[req].text.substring(0, 45);
                var req_text = data.list[req].text;
                html += '<li class="list-group-item"><span class="label label-default">' + req_text + '</span></li>';
            }
            html += '</ul>';
            PECO.pecoRepPrint('Application Requirements', html);
        });
    };
    var format = function (state) {
        if (!state.id) return state.text;
        return state.text;
    };

    var init_validation_wizard = function() {
        $('#form_wizard_1').find('.button-submit').hide();

        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        var contact_person = ('') ? false : true;

        var frm_newaccount = $('#frm_newaccount',document);

        form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {

                //ESSRNO
                essrno: {
                    required: false
                },
                //profile
                firstname: {
                    required: true
                },
                lastname: {
                    required: true
                },
                middlename: {
                    required: false
                },
                birthdate: {
                    required: false
                },
                email: {
                    required: false,
                    email: true
                },
                phone: {
                    required: '#mobile:blank'
                },
                mobile: {
                    required: '#phone:blank'
                },
                gender: {
                    required: false
                },
                address: {
                    required: true
                },
                city: {
                    required: true
                },
                country: {
                    required: true
                },
                region: {
                    required: true
                },
                province: {
                    required: true
                },
                addrspecific: {
                    required: true
                },
                googlemap: {
                    required: false
                },

                // ACCOUNT
                acctrate: {
                    required: true
                },
                accttype: {
                    required: true
                },
                conntype: {
                    required: true
                },
                loctype: {
                    required: true
                },
                acctreq: {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes

            },

            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                    error.insertAfter("#form_gender_error");
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
                PECO.scrollTo(error, -200);
                // PECO.initNicescroll();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {

                if (label.attr("for") == "gender") { // for checkboxes and radio buttons, no need to show OK icon
                    label
                        .closest('.form-group').removeClass('has-error').addClass('has-success');
                    label.remove(); // remove error label here
                } else { // display success icon for other inputs
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                }
            },

            submitHandler: function (form) {
                swal({
                    title: "Are you sure?",
                    text: "New account information will be saved!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, save!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        var form = $('#frm_newaccount');
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                // $('#query-status').html('Loading..');
                            }
                        }).fail(function (d) {
                            PECO.sweetNotif("Error: New account!" , d.msg , 'error')
                        }).done(function (d) {

                            if (d.qry == true) {
                                swal.close();
                                PECO.initAlerts(d.msg, 'New Account', d.func);
                                success.show();
                                error.hide();
                                form.find('[id^=select2]').each(function () {
                                    $(this).select2('val','');
                                });
                                form[0].reset();
                            } else {
                                if (d.func == 'warning'){
                                    PECO.sweetNotif("New account!" ,  d.msg, d.func);
                                } else {
                                    swal.close();
                                    PECO.initAlerts(d.msg, 'New Account', d.func);
                                }
                                success.hide();
                                error.show();
                            }
                        });
                    }else{
                        swal.close();
                    }
                });

            }

        });

        // default form wizard

        /*$('#form_wizard_1').bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;

                success.hide();
                error.hide();
                if (form.valid() == false) {
                    return false;
                }
                handleTitle(tab, navigation, clickedIndex);

            },
            onNext: function (tab, navigation, index) {
                success.hide();
                error.hide();

                if (form.valid() == false) {
                    return false;
                }

                handleTitle(tab, navigation, index);
            },
            onPrevious: function (tab, navigation, index) {
                success.hide();
                error.hide();

                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $('#form_wizard_1').find('.progress-bar').css({
                    width: $percent + '%'
                });
                if(current == 3) {


                    $("#acct_type", document).val('');
                    $('#acct_type', document).select2("destroy").trigger('change');

                    $("#owner_type", document).attr('disabled', true).val('');
                    $('#owner_type', document).select2("destroy").trigger('change');

                    $("#loc_type", document).attr('disabled', true).val('');
                    $('#loc_type', document).select2("destroy").trigger('change');

                    PECO.select2Basic($('#acct_type', document), 'admin/select2accttype', 'Select ...', true);

                    $('#acct_type', document).change(function() {
                        var this_ = $(this);
                        var this_val = this_.val();
                        if(this_val > 0) {
                            $("#owner_type", document).attr('disabled', false);
                            select2_ownertype();
                        } else {
                            $("#owner_type", document).attr('disabled', true).val('');
                            $('#owner_type', document).select2("destroy").trigger('change');
                        }
                    });


                }
            }
        });*/



        //$('#form_wizard_1').find('.button-previous').hide();
        /*
         $('#form_wizard_1 .button-submit').click(function () {
         alert('Finished! Hope you like it :)');
         }).hide();
         */

        //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('#country_list', form).change(function () {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });

        $(document).find('#matches_persons_list .md-radio-list').on('click', 'div.md-radio', function(e) {
            e.preventDefault();
            /*
            var md_ = $(this);
            var input_ = md_.find('input');
            var value_ = input_.val();
            alert(value_);
            */
            alert('clicked!');
        });

        $(document).find('#matches_persons_list .md-radio-list').find('input[type=radio]').live('change', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            $.ajax({
                url: PECO.base_url() + 'cad/getselectednamematched',
                type: 'post',
                data: {'personid': this_val},
                dataType: 'json',
            }).done(function(d){

            }).fail(function(){
                PECO.phpError();
            });
        });
    };
    var init_validation_wizard_govt = function() {
        $('#form_wizard_1').find('.button-submit').hide();

        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {

                //ESSRNO
                essrno: {
                    required: false
                },
                //profile
                firstname: {
                    required: false
                },
                lastname: {
                    required: false
                },
                middlename: {
                    required: false
                },
                birthdate: {
                    required: false
                },
                email: {
                    required: false,
                    email: true
                },
                phone: {
                    required: true
                },
                mobile: {
                    required: false
                },
                gender: {
                    required: false
                },
                address: {
                    required: true
                },
                city: {
                    required: true
                },
                country: {
                    required: true
                },
                addrspecific: {
                    required: true
                },

                // ACCOUNT
                acctrate: {
                    required: true
                },
                accttype: {
                    required: true
                },
                conntype: {
                    required: true
                },
                loctype: {
                    required: true
                },
                acctreq: {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                //'payment[]': {
                //   required: "Please select at least one option",
                //    minlength: jQuery.validator.format("Please select at least one option")
                //}
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                    error.insertAfter("#form_gender_error");
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
                PECO.scrollTo(error, -200);
                // PECO.initNicescroll();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {

                if (label.attr("for") == "gender") { // for checkboxes and radio buttons, no need to show OK icon
                    label
                        .closest('.form-group').removeClass('has-error').addClass('has-success');
                    label.remove(); // remove error label here
                } else { // display success icon for other inputs
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                }
            },

            submitHandler: function (form) {
                swal({
                    title: "Are you sure?",
                    text: "New account information will be saved!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, save!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        var form = $('#frm_newaccount');
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                $('#query-status').html('Loading..');
                            }
                        }).fail(function (d) {
                            swal("Error: New account!" , d.msg , 'error');
                        }).done(function (d) {
                            PECO.initAlerts(d.msg, 'New Account', d.func);
                            if (d.qry == true) {
                                success.show();
                                error.hide();
                            } else {
                                success.hide();
                                error.show();
                            }
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });

            }

        });

        // default form wizard

        $('#form_wizard_1').bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;

                success.hide();
                error.hide();
                if (form.valid() == false) {
                    return false;
                }
                handleTitle(tab, navigation, clickedIndex);

            },
            onNext: function (tab, navigation, index) {
                success.hide();
                error.hide();

                if (form.valid() == false) {
                    return false;
                }

                handleTitle(tab, navigation, index);
            },
            onPrevious: function (tab, navigation, index) {
                success.hide();
                error.hide();

                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $('#form_wizard_1').find('.progress-bar').css({
                    width: $percent + '%'
                });
            }
        });

        $('#form_wizard_1').find('.button-previous').hide();
        /*
         $('#form_wizard_1 .button-submit').click(function () {
         alert('Finished! Hope you like it :)');
         }).hide();
         */

//apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('#country_list', form).change(function () {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });

        $(document).find('#matches_persons_list .md-radio-list').on('click', 'div.md-radio', function(e) {
            e.preventDefault();
            /*
            var md_ = $(this);
            var input_ = md_.find('input');
            var value_ = input_.val();
            alert(value_);
            */
            alert('clicked!');
        });

        $(document).find('#matches_persons_list .md-radio-list').find('input[type=radio]').live('change', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            $.ajax({
                url: PECO.base_url() + 'cad/getselectednamematched',
                type: 'post',
                data: {'personid': this_val},
                dataType: 'json',
            }).done(function(d){

            }).fail(function(){
                PECO.phpError();
            });
        });
    };

    var displayConfirm = function () {
        $('#tab5 .form-control-static', form).each(function () {
            var input = $('[name="' + $(this).attr("data-display") + '"]', form);
            if (input.is(":radio")) {
                input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
            }
            if (input.is(":text") || input.is("textarea")) {
                $(this).html(input.val());
            } else if (input.is("select")) {
                $(this).html(input.find('option:selected').text());
            } else if (input.is(":radio") && input.is(":checked")) {
                $(this).html(input.attr("data-title"));
            }
        });
    };

    var init_verification = function(current, form){
        if(current==4) {
            $.ajax({
                url: PECO.base_url()+'query/getnewcustaccountpreview',
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $('#verify_loading').html('<i class="fa fa-spinner  fa-spin fa-pulse text-info" aria-hidden="true"></i> ');
                }
            }).done(function(d){
                $('#app_essrno').html(d.essrno).closest('li').find('#item_check_stats').html(d.essrn_stat);
                $('#app_fname').html(d.firstname).closest('li').find('#item_check_stats').html(d.firstname_count);
                $('#app_mname').html(d.middlename).closest('li').find('#item_check_stats').html(d.middlename_count);
                $('#app_lname').html(d.lastname).closest('li').find('#item_check_stats').html(d.lastname_count);
                $('#app_birthday').html($('#date_birth', document).val()).closest('li').find('#item_check_stats').html('<i class="fa fa-check text-success pull-right"></i>');
                $('#app_email').html(d.email).closest('li').find('#item_check_stats').html(d.emailcount);
                $('#app_mobile').html(d.mobile).closest('li').find('#item_check_stats').html(d.mobilecount);
                $('#app_phone').html(d.phone).closest('li').find('#item_check_stats').html(d.phonecount);
                $('#app_district').html(d.addrdist).closest('li').find('#item_check_stats').html(d.addrdistcount);
                $('#app_account').html(d.appacctmsg).closest('li').find('#item_check_stats').html(d.appacctcnt);
                $('#app_address').html(d.addrspec).closest('li').find('#item_check_stats').html('<i class="fa fa-check text-success pull-right"></i>');

                $(document).find('#input_acctex').val(d.acctex);
                $(document).find('#input_acctra').val(d.acctra);

                if(d.qry == true) {
                    $('#verify_loading').html('<i class="fa fa-check text-success"></i> ');
                    $('#verfiy_result').html(d.html);
                    if (Number(d.num) > 0) {
                        $('#verify_message').html('<span class="text-danger"><i class="fa fa-warning"></i> Note: This application needs to be forwarded to Legal Department for checking..</span>');
                    }

                }
                console.log(d);
            });
        }


    };

    var handleTitle = function (tab, navigation, index) {
        var total = navigation.find('li').length;
        var current = index + 1;
        // init_verification(current, $('#frm_newaccount'));


        // set wizard title
        $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
        // set done steps
        jQuery('li', $('#form_wizard_1')).removeClass("done");
        var li_list = navigation.find('li');
        for (var i = 0; i < index; i++) {
            jQuery(li_list[i]).addClass("done");
        }

        if (current == 1) {
            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1').find('.button-submit').hide();
        } else {
            if (current == total) {
                $('#form_wizard_1').find('.button-submit').show();
            }
            $('#form_wizard_1').find('.button-previous').show();
        }

        if (current >= total) {
            $('#form_wizard_1').find('.button-next').hide();
            $('#form_wizard_1').find('.button-submit').show();
            displayConfirm();
        } else {
            $('#form_wizard_1').find('.button-next').show();
            $('#form_wizard_1').find('.button-submit').hide();
        }
        PECO.scrollTo($('.page-title'));
        //PECO.initNicescroll();
    };

    var getbarangay = function(distid) {
        PECO.select2Basic($('#brgy' , document) , 'cad/getbarangays' , 'Select Barangay' , false,false,false,false,false , distid);
    };

    var init_customers_applications = function () {

        var selectjobtype = $(document).find('#selectjobtype');

        var frm_newaccount = $('#frm_newaccount',document);
        var non_residential = $('#non_residential',frm_newaccount);

        /*$('.icheck', frm_newaccount).each(function(){
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-red', // minimal / square / polaris / futurico // red / green / blue
                radioClass: 'iradio_square-red',
                increaseArea: '20%' // optional
            }).on('ifChecked', function(event){
                var this_ = $(this);
                this_.attr('checked', true);
                apptype = this_.val();
                alert(event.type + ' callback');
            }).on('ifUnchecked', function(event){
                var this_ = $(this);
                this_.attr('checked', false);
                alert(event.type + ' callback');
            });
        });

        $('#btn_reset',frm_newaccount).on('click',function () {
            frm_newaccount.find('[id^=select2]').each(function () {
                $(this).select2('val','');
            });
            frm_newaccount[0].reset();
        });*/

        var apptype = 0;

        var non_res_html = '';
        non_res_html += '<div class="form-group margin-top-10" id="non_res_details">';
        non_res_html += '<label class="col-md-3 control-label"><span class="required"></span> Establishment</label>';
        non_res_html += '<div class="col-md-6">';
        non_res_html += '<input name="corpname" type="text" class="form-control data-entry input-lg" id="corpname" placeholder="Establishment name..." data-toggle="autocomplete" col-name="corpname" value>';
        non_res_html += '<div class="form-control-focus"> </div>';
        non_res_html += '</div>';
        non_res_html += '<div class="col-md-3">';
        non_res_html += '<input name="corpbranch" type="text" class="form-control data-entry input-lg" id="corpbranch" placeholder="Branch" data-toggle="autocomplete" col-name="corpbranch" value>';
        non_res_html += '<div class="form-control-focus"> </div>';
        non_res_html += '</div>';
        non_res_html += '</div>';
        non_res_html += '<hr>';
        non_res_html += '<div class="row">';
        non_res_html += '<div class="col-md-8">';
        non_res_html += '<h4>Contact Person</h4>';
        non_res_html += '</div>';
        non_res_html += '<div class="col-md-4 pull-right">';
        non_res_html += '<label>';
        non_res_html += '<input name="no_person" id="no_person" value="1" type="checkbox" data-checkbox="icheckbox_flat-red" class="icheck"/> No Contact Person';
        non_res_html += '</label>';
        non_res_html += '</div>';
        non_res_html += '</div>';

        var no_person = $('#no_person',frm_newaccount);


        //Event on change of AppType selection.
        $(document).find('.icheck-inline .icheck').each(function () {
            $(this).on('ifChecked',function (event) {
                var this_ = $(this);
                this_.attr('checked', true);
                //console.log('radio change');
                apptype = this_.val();
                if (apptype != 1) {
                    if (!non_residential.find('#non_res_details').length) {
                        non_residential.html(non_res_html).hide().fadeIn(200);
                    }

                    $('#no_person',frm_newaccount).iCheck({
                        checkboxClass: 'icheckbox_flat-red', // minimal / square / polaris / futurico // red / green / blue
                        increaseArea: '20%' // optional
                    }).on('ifChecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', true);
                        //alert('checked');
                        disable_person_info(true);
                    }).on('ifUnchecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', false);
                        disable_person_info(false);
                    });

                    $('#no_person',frm_newaccount).iCheck('uncheck').attr('checked',false);
                } else {
                    non_residential.html('');
                }
            }).on('ifUnchecked',function (event) {
                var this_ = $(this);
                this_.attr('checked', false);
                disable_person_info(false);
            });
        });



        var disable_person_info = function (trigger) {
            $('#person_info',frm_newaccount).find('input,select').each(function () {
                $(this).attr('disabled',trigger);
            });
        };

        //Event when AppType is already selected on page load.
        $(document).find('.icheck-inline .icheck').each(function () {
            var this_ = $(this);
            if (this_.is(':checked')) {
                //alert(apptype);
                apptype = this_.val();
                if (apptype != 1) {
                    non_residential.html(non_res_html).hide().fadeIn(200);
                    $('#no_person',frm_newaccount).on('ifChecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', true);
                        //alert('checked');
                        disable_person_info(true);
                    }).on('ifUnchecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', false);
                        disable_person_info(false);
                    });
                } else {
                    non_residential.html('');
                    disable_person_info(false);
                }
            }
        });


        $('#marital', document).select2({'placeholder': 'Marital Status', allowClear: true,}).change(function() {
            var this_val = $(this).val();
            if(this_val == 5) {
                $('#partner_info').removeClass('hidden');
            }else{
                $('#partner_info').addClass('hidden')
            }
        });

        PECO.select2Basic(selectjobtype, 'cad/getjobtype', 'Select Job Type..', false, false, false);
        $('#corpandgovinfo').addClass('hidden');

        $(document).on('change' , '#district_select' , function () {
            var this_ = $(this);
            if(this_.val() != ''){
                getbarangay(this_.val());
                $(document).find('#brgy').removeAttr("disabled");
            }else{
                getbarangay();
                $(document).find('#brgy').attr('disabled', 'true');
            }
        });

        $('#application_type').select2({
            "allowClear": true,
            "placeholder": 'Select Application Type'
        }).change(function () {
            var this_ = $(this).val();
            if(this_ == ''){
                $('#corpandgovinfo').addClass('hidden');
            }else{
                if (this_ == 1){
                    $('#corpandgovinfo').addClass('hidden');
                }else{
                    $('#corpandgovinfo').removeClass('hidden');
                    if(this_ == 2){
                        $(document).find('#typename').text("Corp. :");
                    }else if(this_ == 3){
                        $(document).find('#typename').text("Gov. :");
                    }
                }
            }
        });

        form.find('input[name$=name],textarea').not('input[name=corpname]').each(function () {
            var this_ = $(this);
            var newVal = false;
            this_.on('blur',function () {
                //console.log(this_.attr('name') + ' value: ' + this_.val());
                newVal = capitalEachWord(this_.val());
                this_.val(newVal);
                //console.log(newVal);
            });
        });

        $('#btn_print_req').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            PECO.print_acct_requirements(this_.attr('data-id'));
        });
        $("#mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $("#podate").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#waranty").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#acct_issued").inputmask("d/m/y", {
            autoUnmask: true
        });

        $('#suffix').select2({'placeholder': 'Suffix', allowClear: true,});
        $('#prefix').select2({'placeholder': 'Prefix', allowClear: true});

        $('#draft_button').click(function () {
            alert('Under Construction.');
        });

        $('#cancel_button').click(function () {
            alert('Under Construction.');
        });

        $("#country_list").select2({
            placeholder: "Select",
            allowClear: true,
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function (m) {
                return m;
            }
        });




        $('#suffix').select2({'placeholder': 'Suffix', allowClear: true,});
        $('#prefix').select2({'placeholder': 'Prefix', allowClear: true});

        // ################################################
        $('#draft_button').click(function () {
            alert('Under Construction.');
        });

        $('#cancel_button').click(function () {
            alert('Under Construction.');
        });
        // ######################################################

        $("#mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $("#podate").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#waranty").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#acct_issued").inputmask("d/m/y", {
            autoUnmask: true
        });


        $("#acct_rate", document).val('');

        $("#stat_conn", document).attr('disabled', true).val('');
        $('#stat_conn', document).select2("destroy").trigger('change');

        $("#owner_type", document).attr('disabled', true).val('');
        $('#owner_type', document).select2("destroy").trigger('change');

        $("#loc_type", document).attr('disabled', true).val('');
        $('#loc_type', document).select2("destroy").trigger('change');



        $(document).on('change', '#tab3 .crit input', function() {
            tbl_requirements();
        });


        $("#acct_user").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_user_basic/",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
        }).change(function () {
            // ADD AJAX UPDATE IF APPLICABLE //
            console.log('USER: ' + $(this).val());
        });

        if (!jQuery().pulsate) {
            return;
        }
        // INITIALIZE LAST NAME AS DROP DOWN PERSON MENU
        // FUNCTION(elementid, detailed = bolean);


        $('#editprofile').click(function () {
            if ($(this).is(':checked')) {
                $('#district_select').attr('disabled', false);
                $('#city_select').attr('disabled', false);
                $('#country_list').attr('disabled', false);
                $('#phone').attr('disabled', false);
                $('#mobile').attr('disabled', false);
                $('#email').attr('disabled', false);
                $('#addrspecific').attr('disabled', false);
            }
            else {
                disabletab3();
            }
        });

        $('#checkcust').click(function () {
            if ($(this).is(':checked')) {
                var addrcity = $('#city_select').val();
                var addrdistrict = $('#district_select').val();
                var country = $('#country_list').val();

                $('#phonecust').html($('#phone').val());
                $('#mobilecust').html($('#mobile').val());
                $('#emailcust').html($('#email').val());
                $('#custspecific').html($('#addrspecific').val());
                $('#custaddress').html(function () {
                    $.ajax({
                        url: base_url + "query/getaddress/",
                        dataType: 'json',
                        type: "POST",
                        data: {
                            'addrcity': addrcity,
                            'addrdistrict': addrdistrict,
                            'country': country
                        }
                    }).done(function (d) {
                        console.log(d.address);
                        $('#custaddress').html(d.address);
                    }).fail(function () {
                        console.log('error');
                    });
                });
                $('#checkedcust').removeClass('hidden');
                $('#uncheckedcust').addClass('hidden');
            } else {
                $('#checkedcust').addClass('hidden');
                $('#uncheckedcust').removeClass('hidden');
            }
        });


        $("#acct_type_selection").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_types",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
            initSelection: function (element, callback) {
                $.ajax({
                    url: base_url + "admin/get_types",
                    dataType: 'json',
                }).done(function (data) {
                    var selections = [];
                    $.each(data, function (index, item) {
                        selections.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });

                    callback(selections)

                });

            },
        }).change(function () {
            console.log($(this).val());
            // ADD AJAX UPDATE IF APPLICABLE //
        });

        $('#country_select, #city_select, #district_select').each(function () {
            $(this).select2();
        });

        // PRINT REQUIREMENTS
        $('#btn_print_req').click(function (e) {
            e.preventDefault();
            print_requirements();
        });

        // Customer Account Preview on tab 5
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#tab5') {
                var input = $('input:disabled').attr('disabled', false);
                var textarea = $('textarea:disabled').attr('disabled', false);
                var select = $('select:disabled').attr('disabled', false);



                $.ajax({
                    url: PECO.base_url() + 'query/generateinputstohtml',
                    data: $('#frm_newaccount').serialize(),
                    type: 'POST',
                    dataType: 'json'
                }).done(function (d) {
                    $('#input_summary', document).html(d.html);
                    setTimeout(function() {
                        PECO.initMapDrawer('#google_map_preview', d.lat, d.lon, d.zoom);
                    },500);
                }).fail(function (d) {
                    console.log(d);
                });

            }
        });
    };


    var select2_accstat = function() {
        var data = { 'codes': 'SAPPS', 'full': 0};
        PECO.select2Basic($("#stat_conn", document), 'cad/getapplicationparam', 'Select define..', false, false, false, false, false, data);
        $("#stat_conn", document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#owner_type", document).attr('disabled', false);
                select2_ownertype();
            } else {
                $("#owner_type", document).attr('disabled', true).val('');
                $('#owner_type', document).select2("destroy").trigger('change');
            }
        });
    };

    var select2_ownertype = function() {
        var data = { 'codes': 'CADOWNERTYPE', 'full': 1};
        PECO.select2Basic($("#owner_type", document), 'cad/getapplicationparam', 'Select ownership..', false, false, false, false, false, data);
        $("#owner_type", document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#loc_type", document).attr('disabled', false);
                select2_paytype();
            } else {
                $("#loc_type", document).attr('disabled', true).val('');
                $('#loc_type', document).select2("destroy").trigger('change');
            }
        });
    };

    var select2_paytype = function() {
        var data = { 'codes': 'CADAPPPAYTYPE', 'full': 1};
        PECO.select2Basic($("#pay_type", document), 'cad/getapplicationparam', 'Select ownership..', false, false, false, false, false, data);
    };


    var tbl_requirements = function() {
        var tbl_temp_requirements = $('#tbl_basic_req', document);
        var tbl_summary_requirements = $('#tbl_summary_req', document);

        $.ajax({
            url: PECO.base_url() + 'cad/getrequirements',
            type: 'post',
            dataType: 'json',
            data: {
                'acctype': $('#acct_type', document).val(),
                'ownertype': $('#owner_type', document).val(),
                'paytype': $('#pay_type', document).val()
            },
            beforeSend: function() {
                PECO.DTphpLoading(tbl_temp_requirements, 'Loading requirements...');
                PECO.DTphpLoading(tbl_summary_requirements, 'Loading requirements...');
            }
        }).done(function(d) {
            tbl_temp_requirements.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                order: [[0,'asc']],
                aoColumns: [
                    {"data":"num", sClass: 'text-align-right', sWidth: '30px'},
                    {"data":"name", sClass:'text-info',sWidth:'90%'}
                ]
            });
            tbl_summary_requirements.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                order: [[0,'asc']],
                aoColumns: [
                    {"data":"num", sClass: 'text-align-right', sWidth: '30px'},
                    {"data":"name", sClass:'text-info',sWidth:'90%'}
                ]
            });
        }).fail(function() {
            PECO.DTphpError(tbl_temp_requirements);
            PECO.DTphpError(tbl_summary_requirements);
        });
    };


    var init_corporation = function() {
        PECO.select2Basic($('#acct_rate', document), 'admin/get_rate_class_corp', 'Select ...', true);
        $('#acct_rate', document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#stat_conn", document).attr('disabled', false);
                select2_accstat();
            } else {
                $("#stat_conn", document).attr('disabled', true).val('');
                $('#stat_conn', document).select2("destroy").trigger('change');
            }
        });
    };

    var init_government = function() {
        PECO.select2Basic($('#acct_rate', document), 'admin/get_rate_class_corp', 'Select ...', true);
        $('#acct_rate', document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#stat_conn", document).attr('disabled', false);
                select2_accstat();
            } else {
                $("#stat_conn", document).attr('disabled', true).val('');
                $('#stat_conn', document).select2("destroy").trigger('change');
            }
        });
    };

    var disabletab3 = function() {
        $('#district_select').attr('disabled', true);
        $('#city_select').attr('disabled', true);
        $('#country_list').attr('disabled', true);
        $('#phone').attr('disabled', true);
        $('#mobile').attr('disabled', true);
        $('#email').attr('disabled', true);
        $('#addrspecific').attr('disabled', true);
    };

    var pulsate = function() {
        jQuery('#pulsate-regular').pulsate({
            color: "#399bc3",
            reach: 50,
            repeat: 2,
            speed: 500,
            glow: true
        }).find('a').focus();
    };

    var removeDisabledInputInform = function() {
        $('#frm_newaccount').each(function (e) {

            // INDIVIDUAL
            if ($('#apptype').val() == 1) {
                $('#firstname').val('').attr('disabled', false);
                $('#middle_initial').val('').attr('disabled', false);
                $('#suffix').val('').attr('disabled', false);
                $('#prefix').val('').attr('disabled', false);
                $('#birthdate').val('').attr('disabled', false);
                $('#marital').val('').attr('disabled', false);
                $('#gender').val('').attr('disabled', false);
            }

            // CORPORATION
            if ($('#apptype').val() == 2) {
                $('#corp_district').val('').attr('disabled', false);
                $('#corp_addrspecific').val('').attr('disabled', false);

                if ($('#persontype').val() == 1) {
                    $('#firstname').val('').attr('disabled', false);
                    $('#middle_initial').val('').attr('disabled', false);
                    $('#suffix').val('').attr('disabled', false);
                    $('#prefix').val('').attr('disabled', false);
                    $('#birthdate').val('').attr('disabled', false);
                    $('#marital').val('').attr('disabled', false);
                    $('#gender').val('').attr('disabled', false);
                }
            }

            /*
             if ($('#profiletype').val() === '1' || $('#profiletype').val() === '') {
             $(this).find('input:not(#apptype):input:not(#lastname):not(input[name="gender"]):not(#moduleid):not(#stagelevel)').val('');
             } else {
             $(this).find('input:not(#profiletype):not(#acctidentity):not(#moduleid):not(#stagelevel):not(#corpidentity):not(#lastname):not(input[name="gender"]):not(#corp_name):not(#corpaddrspecific):not(#corp_district):not(#city_select):not(#district_select):not(#country_select)').val('');
             }
             $(this).find('input').attr('disabled', false);
             */
        });
    };

    // ##############################################################
    // CAD EVALUATION MODULE ########################################
    var table_services = $('#tbl_services');
    var init_evaluation = function(dataid){
        var frm_service = $('#frm_add_service');
        // MESSAGE TO CONSOLE DEVELOPMENT MODE
        if(PECO.sysCheckMode()==true) {
            console.log('Evaluation script initialized!');
        }

        init_dt_services(dataid);
        PECO.select2Basic($('#serv_item'), 'cad/selectservicematerials', 'Select Labor / Services...', false, false);

        $('#frm_add_service').submit(function(e) {
            e.preventDefault();
            var frm_submit = PECO.ajaxFormSubmit(frm_service);
            if(frm_submit) {
                init_dt_services(dataid);
            }
        });
        PECO.initMapSpec('#custmap', dataid, 0);

        $('body').on('click', '#btn_process_ar', function(e){
            e.preventDefault();
            var this_ = $(this);
            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: Process Account Receivables.</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonsPosition: 'right',
                    buttonClass: 'btn-primary, btn-danger',
                    buttonsIcon: 'fa-angle-double-right, fa-times',
                    inputIcon: 'fa fa-user',
                    inputIconPosition: 'left',
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: PECO.base_url()+'cad/processar',
                            type: 'post',
                            data: {'id': this_.attr('data-id')},
                            dataType: 'json',
                            beforeSend: function(){
                                this_.attr('disabled', true);
                            }
                        }).done(function (data) {
                            if(PECO.sysCheckMode()==true) {
                                console.log(data);
                            }
                            if (data.qry == true) {
                                var func = (data.func) ? data.func : 'success';
                                PECO.initAlerts(data.msg, 'Account Receivable', func, true);
                                this_.attr('disabled', true);
                                init_dt_services(dataid);
                            } else {
                                var func = (data.func) ? data.func : 'warning';
                                PECO.initAlerts(data.msg, 'Account Receivable', func, true);
                                this_.attr('disabled', false);
                            }
                        }).fail(function () {
                            PECO.phpError();
                            this_.attr('disabled', false);
                        });
                    }
                });
            if(PECO.sysCheckMode()==true) {
                console.log('Processing Account Receivable...');
            }
        });
    };

    var init_dt_services = function(dataid) {


    };

    var init_edit_owner = function (dataid) {
        tbl_sub_owners(dataid);

        var curr_appname = $('#curr_appname',document);
        var curr_status = $('#curr_status',document);
        var curr_address = $('#curr_address',document);
        var curr_contact = $('#curr_contact',document);
        var sub_owners_list = $('#sub_owners_list',document);
        var remove_owner = $('#remove_owner',document);

        $('#frm_ownership_edit',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: 'post',
                data: this_.serialize(),
                dataType: 'json',
            }).done(function (d) {
                if (d.type == 1) {
                    curr_appname.html(d.owner.appname);
                    curr_status.html(d.owner.status);
                    curr_address.html(d.owner.address);
                    curr_contact.html(d.owner.contact);
                }

                if (d.type == 2) {
                    tbl_sub_owners(d.dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        sub_owners_list.on('click','#remove_owner', function () {
            var this_ = $(this);
            var id = this_.attr('data-id');
            var this_tr = this_.closest('tr');

            $.ajax({
                url: base_url +'cad/removesubowner',
                type: 'post',
                data: {'id': id},
                dataType: 'json',
            }).done(function () {
                this_tr.remove();
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_new_owner',function () {
            var values = [];
            $('#frm_ownership_edit',document).find('.form-control').each(function () {
                $(this).val('');
                $(this).trigger('change');
                console.log($(this).attr('name') + ':' + $(this).val());
            });
            //console.log(values);
            $('#newowner',document).val(1);
        });

        PECO.handleriCheckForm($('#frm_ownership_edit',document));
    };

    var tbl_sub_owners = function (dataid) {
        var sub_owners_list = $('#sub_owners_list',document);
        PECO.DTDefault(sub_owners_list,'No Sub-Owners yet.');

        $.ajax({
            url:base_url + 'cad/dtsubowners',
            type:'post',
            data: {
                'appid' : dataid,
            },
            dataType:'json',
            beforeSend: function () {
                PECO.DTphpLoading(sub_owners_list, 'Fetching Sub-Owners');
            }
        }).done(function (d) {
            sub_owners_list.dataTable().empty();
            sub_owners_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name" , sClass:'text-info',sWidth:'35%'},
                    {"data":"address", sWidth:'40%'},
                    {"data":"contact", sWidth:'25%'},
                    {"data":"remove"},
                ],
                searchHighlight: false
            });
        })
    };

    var frm_jo_newconn = function () {

        //PECO.select2Basic('#rate_class_select','cad/select2rateclass','Select Rate Class...',false,false,false);

        $(document).on('submit','#frm_jo_newconn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Proceed in creating Job Order.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Yes, Create Job Order!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        dataType: 'json',
                    }).done(function (data) {
                        swal(data.title,data.msg,data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_job_order',function () {
            swal({
                title: "Already in process!",
                text: "There is already an existing Job Order for this application.",
                type: "info",
                button: "Okay!"
            });
        });

    };

    // ##############################################################
    // END CAD EVALUATION MODULE ####################################

    var handler_editable = function() {
        // $.fn.editable.defaults.mode = 'inline';

        PECO.getEditablePlugins();

        setTimeout(function(e) {


            $('#essrnoprofile', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'essrno',
                title: 'Edit ESSR No.',
                placement: 'bottom'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_mobile', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'mobile',
                title: 'Add Mobile Number',
                placement: 'right',
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_phone', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'phone',
                title: 'Add Phone Number',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_email', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'email',
                name: 'email',
                title: 'Add Email',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_servno', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'servno',
                name: 'servno',
                title: 'Edit Service Number',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_address', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'addressspec',
                title: 'Edit ESSR No.',
                placement: 'bottom'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            //new editable for district
            $('#input_district', document).editable({
                success: function (response, newValue) {
                    if (!response.success)
                        return response.msg;
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
                    width: 'resolve',
                    placeholder: 'Select...',
                    id: function (item) {
                        return item.id;
                    },
                    ajax: {
                        url: PECO.base_url() + 'hris/select2district',
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
                    initSelection: function (element, callback) {
                        var init_val = element.val();
                        return callback(init_val);
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: PECO.formatStateEditable, // omitted for brevity, see the source of this page
                    formatSelection: PECO.formatDataSelectionEditable, // omitted for brevity, see the source of this page
                },
                url: PECO.base_url() + 'cad/submiteditable',
                name: 'district',
                title: 'Modify District',
                placeholder: 'Modify District',
                inputclass: 'form-control input-large',
                emptytext: 'Enter District',
                placement: 'bottom',
            }).on('click', function () {
                PECO.select2_scroller();
            }).on('shown', function(e, editable) {

                var popover = editable.input.$input[0].closest('.popover');
                var popover_id = popover.id;

                $(document).on('change', editable, function() {

                    var new_value = editable.input.$input[0].value;

                    if (new_value != $(this).val()) {
                        $('#' + popover_id).find('.help-block').html('<div class="alert alert-warning margin-top-20"><i class="fa fa-warning"></i> Are you sure you want to change the district?').show();
                    } else {
                        $('#' + popover_id).find('.help-block').html('').hide();
                    }

                });
            }).on('save', function(e, params) {
                setTimeout(function() {

                    $(this).text(params.newValue);
                }, 300);
            });

            //new editable for marital
            $('#input_marital', document).editable({
                success: function (response, newValue) {
                    if (!response.success)
                        return response.msg;
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
                    width: 'resolve',
                    placeholder: 'Select...',
                    id: function (item) {
                        return item.id;
                    },
                    ajax: {
                        url: PECO.base_url() + 'query/select2civilstatus',
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
                    initSelection: function (element, callback) {
                        var init_val = element.val();
                        return callback(init_val);
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: PECO.formatStateEditable, // omitted for brevity, see the source of this page
                    formatSelection: PECO.formatDataSelectionEditable, // omitted for brevity, see the source of this page
                },
                url: PECO.base_url() + 'cad/submiteditable',
                name: 'marital',
                title: 'Update Marital Status',
                placeholder: 'Update Marital Status',
                inputclass: 'form-control input-large',
                emptytext: 'Select Marital Status',
                placement: 'bottom',
            }).on('click', function () {
                PECO.select2_scroller();
            }).on('shown', function(e, editable) {

                var popover = editable.input.$input[0].closest('.popover');
                var popover_id = popover.id;

                $(document).on('change', editable, function() {

                    var new_value = editable.input.$input[0].value;

                    if (new_value != $(this).val()) {
                        $('#' + popover_id).find('.help-block').html('<div class="alert alert-warning margin-top-20"><i class="fa fa-warning"></i> Are you sure you want to change current marital status?').show();
                    } else {
                        $('#' + popover_id).find('.help-block').html('').hide();
                    }

                });
            }).on('save', function(e, params) {
                setTimeout(function() {

                    $(this).text(params.newValue);
                }, 300);
            });


            $('#gender',document).editable({
                source: [
                    {value: 1, text: 'Male', name: 'gender'},
                    {value: 2, text: 'Female', name: 'gender'},
                ],
            });

            $('.editable', document).editable('toggleDisabled');
            $('#enable_edit').click(function() {
                swal({
                    title: "Toggle Editable?",
                    text: "please confirm toggle of editable fields",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Toggle",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('.editable', document).editable('toggleDisabled');
                        swal.close();
                    }else{
                        swal.close();
                    }
                });
            });


        }, 300);
    };

    var handler_filetags = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'admin/fetchfiletype',
            data: {dataid: dataid},
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                $('#file_tags', document).html('<h4><i class="fa fa-refresh fa-spin text-info"></i> Loading files...</h4>');
            }

        }).done(function(d) {
            $('#file_tags', document).html(d.html);
        });
    };

    var hanlder_filetags_delete = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'admin/deleteallfiles',
            data: {dataid: dataid},
            dataType: 'json',
            type: 'post',
        }).done(function(d) {
            handler_filetags(dataid);
        });
    };

    var fn_application_profile = function(dataid, flowid) {
        //alert(dataid);
        init_application_requirements(dataid);
        profile_events(dataid);
        //handler_editable();
        handler_filetags(dataid);

        $('#refresh_file_tags',document).on('click',function () {
            handler_filetags(dataid);
        });

        $('#clear_file_tags',document).on('click',function () {
            hanlder_filetags_delete(dataid);
        });

        $('#btn_reload_req',document).on('click',function () {
            init_application_requirements(dataid);
        });

        $(document).on('click', '#btn_email_requirements', function(e) {
            e.preventDefault();
            var dataid = $(this).attr('data-id');
            swal({
                title: "Send list of requirements?",
                text: "please confirm sending of list of requirements",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Send",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'cad/sendfinalrequirementlist',
                        type: 'post',
                        dataType: 'json',
                        data: {'dataid': dataid}
                    }).done(function (d) {
                        swal(d.msg, 'Email sending', d.func);
                    });
                } else {
                    swal.close();
                }
            });
        });
    };

    var get_application_charges = function (dataid,moduleid) {
        //cad/getcustomerservices
        // alert(dataid);
        init_application_charges_list(dataid,moduleid);

        $('#btn_reload_charges',document).on('click',function () {
            init_application_charges_list(dataid,moduleid);
        });
    };

    var init_application_charges_list = function (dataid,moduleid) {
        var charges_list = $('#charges_list',document);
        var total_charges = $('#total_charges',document);
        var tbl_charges_list = $('#tbl_charges_list',document);
        $.ajax({
            url: base_url + 'cad/getcustomerservices',
            type: 'post',
            data: {
                appid : dataid,
                moduleid: moduleid
            },
            dataType: 'json',
            beforeSend: function () {
                charges_list.html('<li><h4 align="center"><i class="fa fa-refresh fa-spin fa-pulse text-info"></i> Fetching Charges...</h4></li>');
                total_charges.text('0.00');
                PECO.DTphpLoading(tbl_charges_list,'Fetching charges...');
            }
        }).done(function (d) {
            charges_list.html(d.charges);
            total_charges.text(d.total);
            tbl_charges_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bLengthChange: false,
                bFilter: false,
                aaData: d.chargelist,
                aoColumns: [
                    {"data":"desc", sWidth:'', sClass: 'text-primary'},
                    {"data":"amt", sClass: 'text-align-right'},
                    {"data":"status", sClass: 'controls'},
                ],
                searchHighlight: false
            });
        }).fail(function () {
            charges_list.html('<li><h4><i class="fa fa-exclamation-triangle text-warning"></i> Error fetching charges!</h4></li>');
        });
    };

    var init_application_requirements = function(dataid) {
        //alert(dataid);
        var tbl_requirements_list = $('#tbl_requirements_list', document);
        $.ajax({
            url: PECO.base_url() + 'cad/getcustomerrequirements',
            type: 'post',
            data: {dataid: dataid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_requirements_list, 'Loading requirements...');
            }
        }).done(function(d) {
            tbl_requirements_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                bLengthChange: false,
                bFilter: true,
                aaData: d.list,
                aoColumns: [
                    {"data":"num"},
                    {"data":"text", sWidth:'350PX', sClass: 'text-primary'},
                    {"data":"status", sClass: 'text-align-center'},
                    {"data":"control", sClass: 'controls'},
                ],
                searchHighlight: false
            });
        }).fail(function() {
            PECO.DTphpError(tbl_requirements_list);
        });
    };

    /**/

    var profile_events = function (dataid) {
        //alert(dataid);
        var delete_requirement = $('#delete_requirement',document);
        var tbl_requirements_list = $('#tbl_requirements_list', document);
        tbl_requirements_list.on('click', '#delete_requirement',function () {
            //alert(dataid);
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var reqid = this_.attr('data-id');
            var moduleid = this_.attr('data-module');

            $.ajax({
                url: base_url + 'cad/deleterequirement',
                type: 'post',
                data: {
                    appid: dataid,
                    moduleid: moduleid,
                    reqid: reqid
                },
                dataType: 'json'
            }).done(function (d) {
                if (d.qry == true) {
                    this_tr.html('<tr><td colspan="4">Requirement Removed</td></tr>');
                    setTimeout(
                        function () {
                            this_tr.remove();
                        },1000);
                } else {
                    PECO.initAlerts('Failed to remove requirement.','Fail','error');
                }
            }).fail(function () {
                PECO.initAlerts('Failed to remove requirement.','Fail','error');
            });
        });
    };

    var add_requirements = function (dataid) {
        dt_requirement_list(dataid);
    };

    var dt_requirement_list = function (dataid) {
        var tbl_add_requirement_list = $('#tbl_add_requirement_list',document);
        $.ajax({
            url: base_url + 'cad/addrequirementlist',
            type: 'post',
            dataType: 'json',
            data: {
                dataid: dataid,
            },
            beforeSend: function() {
                PECO.DTphpLoading(tbl_add_requirement_list, 'Loading requirements...');
            }
        }).done(function (d) {
            tbl_add_requirement_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                "bLengthChange": false,
                bFilter: true,
                aaData: d.list,
                aoColumns: [
                    {"data":"num", sClass: 'number'},
                    {"data":"code", sClass: 'text-primary text-align-center'},
                    {"data":"names", sWidth: '95%'},
                    {"data":"select", sClass: 'controls'},
                ],
                searchHighlight:false,
                ordering:       false
            });
        }).fail(function() {
            PECO.DTphpError(tbl_add_requirement_list);
        });

        $('#tbl_add_requirement_list',document).on('click','#add_req_row',function () {
            var this_ = $(this);
            var reqid = this_.attr('data-id');
            var this_tr = this_.closest('tr');

            $.ajax({
                url: base_url + 'cad/addrequirement',
                type: 'post',
                dataType: 'json',
                data: {
                    reqid: reqid,
                    appid: dataid
                },
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                this_tr.remove();
            }).fail(function () {
                PECO.initAlerts('Failed to add requirements.','Fail','error');
            });
        });
    };


    var sample_gdr = function (rate_class,dataid) {
        $.ajax({
            type: "POST",
            url: PECO.base_url() + "inspection/initaccountgdr",
            dataType: "json",
            data: {
                'rate': rate_class,
                'appid': dataid
            }
        }).done(function (result) {
            if (result.qry === true) {
                $('#rates').html(result.rates);
                $('#demand').html(result.demand);
                $('#dailyop').html(result.dailyops);
                $('#monthlyop').html(result.monthlyops);
                $('#totalwatt').html(result.total_load_text);
                $('#totalcost').html(result.deposit_cost_text);
                $('#deposit_cost').val(result.total_init_charges);
                $('#totalinitc').html(result.total_init_charges_text);
                if (result.isecales == true) {
                    $('#totalecales').html(result.total_ecales_amt_text);
                }
            }
        });

        $(document).on('click','#btn_generate_gdr',function () {
            var acctamt = $('#deposit_cost').val();
            swal({
                title: "Are you sure?",
                text: "Continue processing for payment?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Yes, Process for Payment!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: base_url + 'cad/addcustomercharges',
                        type: 'post',
                        data: {
                            acctcode : 162,
                            acctamt : acctamt,
                            dataid : dataid,
                            origin : 35
                        },
                        dataType: 'json',
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });

    };

    var charges_override = function (dataid,moduleid) {
        $('#frm_override_amt',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: 'Override Amount?',
                text: "Are you sure you want to override this amount?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        dataType: 'json',
                        data: this_.serialize()
                    }).done(function (d) {
                        $('#modal_ajax',document).modal('hide');
                        swal('Override Amount Charges', d.msg, d.func);
                        setTimeout(function () {
                            init_application_charges_list(dataid,moduleid);
                        },1000);
                    }).fail(function () {
                        swal('Override Amount Charges', 'PHP Error!', 'error');
                    })
                }
            });
        });
    };

    var capitalEachWord = function (value) {
        var splitStr = value.toLowerCase().split(' ');
        for (var i = 0; i < splitStr.length; i++) {
            // You do not need to check if i is larger than splitStr length, as your for does that for you
            // Assign it back to the array
            if (splitStr[i] == splitStr[i].toUpperCase()) {
                splitStr[i] = splitStr[i].toUpperCase();
            } else {
                splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
            }
        }
        // Directly return the joined string
        return splitStr.join(' ');
    };

    var edit_representative = function (dataid) {

    };

    var load_on_tab = function (dataid) {
        var recom_setup = $('#recom_setup', document);
        var tab = $('a[data-toggle="tab"]', recom_setup);
        var id = 1;

        tab.on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            id = $(e.target).attr('data-id');
            var msg = '';
            var load = '';

            if (id == 1) {
                /** LOAD APPLICATION DATA */
            }
            if (id == 2) {
                /** CLEAR ALL INPUT VALUES */
            }
            if (id == 3) {
                /** LOAD DATATABLE FOR AR HISTORY */
            }
        });
    };

    var init_owner_update = function (dataid) {
        var frm_ownership_edit = $('#frm_ownership_edit',document);
        PECO.handleriCheckForm(frm_ownership_edit);
        handling_owner_update(dataid);
        $.ajax({
            url : PECO.base_url() + 'cad/initownerinfo',
            type : 'post',
            dataType : 'json',
            data : {
                id : dataid
            }
        }).done(function (d) {
            frm_ownership_edit.find('input.form-control').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name)) {
                    var value = d[name];
                    var val = 0;
                    if(typeof value === 'number'){
                        val = parseFloat(value);
                    } else{
                        val = value
                    }
                    this_.val(val).trigger('change');
                }
            });
            frm_ownership_edit.find('textarea.form-control').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name)) {
                    this_.text(d[name]);
                }
            });
            frm_ownership_edit.find('input.icheck').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name) && this_.val() == d[name]) {
                    //alert(this_.val());
                    this_.iCheck('check');
                }
            });
        }).fail(function () {

        })
    };

    var handling_owner_update = function (dataid) {
        var frm_ownership_edit = $('#frm_ownership_edit',document);
        $('#btn_new_owner',document).on('click',function () {
            frm_ownership_edit.find('input').each(function () {
                $(this).trigger('change');
            });
            frm_ownership_edit.find('input.icheck').each(function () {
                $(this).iCheck('uncheck');
            });
            $('#newowner',document).val(1);
        });
    };

    return {
        profile: function(dataid, flowid) {
            fn_application_profile(dataid, flowid);
        },
        application: function() {
            init_customers_applications();
            init_validation_wizard();
        },
        evaluation: function(dataid) {
            init_evaluation(dataid)
        },
        corporation: function() {
            init_corporation();
        },
        government: function() {
            init_government();
            init_customers_applications();
            init_validation_wizard_govt();
        },
        editOwner: function (dataid) {
            init_edit_owner(dataid);
        },
        joNewconn: function () {
            frm_jo_newconn();
            //sample_gdr(rate_class,dataid);
        },
        addRequirements: function (dataid) {
            add_requirements(dataid);
        },
        requirements: function (dataid) {
            init_application_requirements(dataid);
        },
        charges: function (dataid,moduleid) {
            get_application_charges(dataid,moduleid);
        },
        override: function (dataid,moduleid) {
            charges_override(dataid,moduleid);
        },
        ownership: function (dataid) {
            edit_representative(dataid);
        },
        updateOwner: function (dataid) {
            init_owner_update(dataid);
        }

    }
}();
