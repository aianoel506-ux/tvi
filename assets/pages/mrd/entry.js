var MRD = function () {
    // INITIALIZE HIGHLIGHTS SEARCH IN TABLE
    PECO.getHighlightsPlugin();
    PECO.getSweetAlert();

    // VARIABLES

    var dt_analysis = $('#tbl_reading_entry', document);
    var tbl_reading_entry = $('#tbl_reading_entry');
    var tbl_gdlb_customers = $('#tbl_gdlb_customers');
    var frm_reading_entry = $('#frm_submit_reading');
    var frm_analysis_entry = $('#frm_submit_analysis');
    var tbl_gdlb = $('#tbl_gdlb');
    var tbl_assign_gdlb = $('#tbl_assign_gdlb');



    var init_input_navigation = function(tbl) {
        tbl.on('keydown', 'input', function(e){
            if(e.keyCode==40){
                var this_index = $(this).closest('td').index();
                var next_tr = $(this).closest('tr').next();
                var next_input = next_tr.find('td').eq(this_index).find('input');
                next_input.focus();
                setTimeout(function(){
                    next_input.select();
                },50);
            }
            if(e.keyCode==38){
                var this_index = $(this).closest('td').index();
                var next_tr = $(this).closest('tr').prev();
                var next_input = next_tr.find('td').eq(this_index).find('input');
                next_input.focus();
                setTimeout(function(){
                    next_input.select();
                },50);
            }
        });
    };

    var init_reading  = function() {
        PECO.select2Basic($('#schedid'), 'mrd/getreadergdlbsched', 'GDLB..', true, false);
        PECO.DTDefault(tbl_reading_entry, 'Please select GDLB for reading assigned!');


        $('#get_mrd_list').click(function(e){
            var this_btn = $(this);
            var data = $('#schedid', document).select2('val');
            var userid = $('#reader_id', document).val();
            e.preventDefault();
            init_reading_table(data, 0,true, this_btn, userid);
        });

        init_reading_submit();
        //PECO.dtSubDetails(tbl_reading_entry, 'mrd/getmtrinfo');



        init_reading_entry_keyboard();
        init_reading_entry_fn();
        init_input_navigation(tbl_reading_entry);
    };

    var init_reading_entry_fn = function() {

        tbl_reading_entry.on('keypress', '#demand', function(e) {
            var this_row = $(this).closest('tr');
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_reading_row(this_row)==true ) {
                    var index = $('input#demand').index(this) + 1;
                    var this_input = $('input.reading').eq(index).focus();
                    setTimeout(function () {
                        this_input.select();
                    }, 100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                    init_compute_reading_stat();
                }
            }
        });

        tbl_reading_entry.on('keypress', '#netmtr', function(e) {
            var this_row = $(this).closest('tr');
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_reading_row(this_row)==true ) {
                    var index = $('input#netmtr').index(this) + 1;
                    var this_input = $('input.netmtr').eq(index).focus();
                    setTimeout(function () {
                        this_input.select();
                    }, 100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                    init_compute_reading_stat();
                }
            }
        });

        // ENTER NEXT READING INPUT
        tbl_reading_entry.on('keypress', '.reading', function(e) {
            var this_row = $(this).closest('tr');
            if (e.keyCode == 13) {
                e.preventDefault();
                if(init_submit_reading_row(this_row)==true ) {
                    var index = $('input.reading').index(this) + 1;
                    var this_input = $('input.reading').eq(index).focus();
                    setTimeout(function () {
                        this_input.select();
                    }, 100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                    init_compute_reading_stat();
                }
            }
        });


        tbl_reading_entry.on('click', '#btn_del', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_read = $('#reading', this_tr);

            var acct_id = this_.attr('data-id');
            var sched_id = this_.attr('data-schedid');
            $.ajax({
                url: PECO.base_url() + 'mrd/deletetempread',
                type: 'post',
                data: {'acctid': acct_id, 'schedid': sched_id},
                dataType: 'json'
            }).done(function(d) {
                if(d.qry==true) {
                    this_read.val('').trigger('blur');
                    var index_reading = $('input#reading', this_tr);
                    var index = $('input.reading').index(index_reading) + 1;
                    var this_input = $('input.reading').eq(index).focus();
                    setTimeout(function() {
                        this_input.select();
                    },100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                    this_input.closest('tr').addClass('row-info');
                    init_compute_reading_stat();

                }
            }).fail(function(){
                PECO.phpError();
            });
        });


        $('tr td', tbl_reading_entry).on('change', 'input.reading', function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            if(this_.val()!='') {
                this_tr.addClass('row-success');
            }else{
                this_tr.removeClass('row-success');
            }
        });

    };

    var init_gdlb_list_keybaord = function(tbl) {

        // ################################################################
        // ARROW DOWN NEXT READING INPUT
        tbl.on('keydown', 'input', function(e) {
            if (e.which === 40) {
                console.log('Arrow down');
                var index = $('input').index(this) + 1;
                var this_input = $('input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
            }
        });
        // ################################################################

        // ################################################################
        // ARROW UP PREVIOUS READING INPUT ################################
        tbl.on('keydown', 'input', function(e) {
            if (e.which === 38) {
                var index = $('input').index(this) - 1;
                var this_input = $('input').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
            }
        });
        // ################################################################
    };

    var init_reading_entry_keyboard = function() {
        // CLICK ON INPUT HIGHLIGHTS
        $('tr td input.reading', tbl_reading_entry).click(function(){
            tbl_reading_entry.find('tr.row-info').removeClass('row-info');
            $(this).select().closest('tr').addClass('row-info');
            init_compute_reading_stat();
        });

        // ARROW DOWN NEXT READING INPUT
        tbl_reading_entry.on('keydown', '.reading', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code === 40) {
                var index = $('input.reading').index(this) + 1;
                var this_input = $('input.reading').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
                init_compute_reading_stat();
            }
        });

        // ARROW UP PREVIOUS READING INPUT
        tbl_reading_entry.on('keydown', '.reading', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code === 38) {
                var index = $('input.reading').index(this) - 1;
                var this_input = $('input.reading').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
                init_compute_reading_stat();
            }
        });

        // ARROW DOWN NEXT DEMAND INPUT
        tbl_reading_entry.on('keydown', '#demand', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code === 40) {
                var index = $('input#demand').index(this) + 1;
                var this_input = $('input#demand').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
                init_compute_reading_stat();
            }
        });

        // ARROW UP PREVIOUS READING INPUT
        tbl_reading_entry.on('keydown', '#demand', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if (code === 38) {
                var index = $('input#demand').index(this) - 1;
                var this_input = $('input#demand').eq(index).focus();
                setTimeout(function() {
                    this_input.select();
                },100);
                tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
                init_compute_reading_stat();
            }
        });
    };

    var init_findigns_editable = function(nRow, schedid, acctid, Index) {
        if(!$.fn.editable) {
            return false;
        }
        $('#findings_input', nRow).editable({
            value: {
                finding: "Select..",
                remarks: "Remarks...",
            },
            url: PECO.base_url() + 'mrd/updateanalysisrow',
            title: 'Select Findings',
            placeholder: 'Select Findings',
            inputclass: 'form-control',
            emptytext: 'Select Findings',
            placement: 'right',
            params: function (params) {
                var new_reading = $('#reading', nRow).val();
                var new_findings = $('#editable_findings', nRow).val();
                var new_remarks = $('#editable_remarks', nRow).val();
                var push = {};
                push['mtrno'] = params.pk;
                push['schedid'] = schedid;
                push['acctid'] = acctid;
                push['reading'] = new_reading;
                push['findingid'] = new_findings;
                push['remarks'] = new_remarks;
                push['indiex'] = Index;
                return push;
            },
            success: function (d) {
                var New_Index = Index + 1;
                var next_row = $('tbody tr', tbl_reading_entry).eq(New_Index).find('#reading');
                next_row.focus()
                setTimeout(function () {
                    next_row.select();
                }, 200);
            }
        }).on('click', function () {
            var elem = $('#editable_findings', document);
            $.ajax({
                url: PECO.base_url() + 'mrd/getselect2findings',
                type: 'post',
                dataType: 'json',
            }).done(function (d) {
                if (d) {
                    if ($.fn.select2) {
                        elem.select2({
                            allowClear: true,
                            placeholder: 'Select findings...',
                            data: d.list,
                            formatResult: PECO.formatStateEditable, // omitted for brevity, see the source of this page
                            formatSelection: PECO.formatDataSelectionEditable, // omitted for brevity, see the source of this page
                            width: 'resolve', // 100% or resolve
                            dropdownCssClass: '',

                        }).on('change', function (e) {
                            $('#editable_remarks', document).focus();
                        });
                        PECO.select2_slimscroller();
                    }
                } else {
                    elem.select2({
                        allowClear: true,
                        placeholder: 'No data found!',
                        width: 'resolve'
                    });
                }
            }).fail(function () {
                elem.select2({
                    allowClear: true,
                    placeholder: 'PHP Error',
                });
            });
        });
    };

    var init_findings_editable_form = function() {

        (function ($) {
            "use strict";

            var Findings = function (options) {
                this.init('findings', options, Findings.defaults);
            };

            //inherit from Abstract input
            $.fn.editableutils.inherit(Findings, $.fn.editabletypes.abstractinput);

            $.extend(Findings.prototype, {
                /**
                 Renders input from tpl
                 @method render()
                 **/
                render: function() {
                    this.$input = this.$tpl.find('input');
                },

                /**
                 Default method to show value in element. Can be overwritten by display option.

                 @method value2html(value, element)
                 **/
                value2html: function(value, element) {
                    if(!value) {
                        $(element).empty();
                        return;
                    }

                    var data = $('#editable_findings', document).select2('data');
                    var finding_text_arr = data.text.split(' - ');
                    var html = finding_text_arr[0];
                    $(element).html(html);
                },

                /**
                 Gets value from element's html

                 @method html2value(html)
                 **/
                html2value: function(html) {
                    /*
                      you may write parsing method to get value by element's html
                      e.g. "Moscow, st. Lenina, bld. 15" => {city: "Moscow", street: "Lenina", building: "15"}
                      but for complex structures it's not recommended.
                      Better set value directly via javascript, e.g.
                      editable({
                          value: {
                              city: "Moscow",
                              street: "Lenina",
                              building: "15"
                          }
                      });
                    */
                    return null;
                },

                /**
                 Converts value to string.
                 It is used in internal comparing (not for sending to server).

                 @method value2str(value)
                 **/
                value2str: function(value) {
                    var str = '';
                    if(value) {
                        for(var k in value) {
                            str = str + k + ':' + value[k] + ';';
                        }
                    }
                    return str;
                },

                /*
                 Converts string to value. Used for reading value from 'data-value' attribute.

                 @method str2value(str)
                */
                str2value: function(str) {
                    /*
                    this is mainly for parsing value defined in data-value attribute.
                    If you will always set value by javascript, no need to overwrite it
                    */
                    return str;
                },

                /**
                 Sets value of input.

                 @method value2input(value)
                 @param {mixed} value
                 **/
                value2input: function(value) {
                    if(!value) {
                        return;
                    }
                    this.$input.filter('[name="finding"]').val(value.finding);
                    this.$input.filter('[name="remarks"]').val(value.remarks);
                },

                /**
                 Returns value of input.

                 @method input2value()
                 **/
                input2value: function() {
                    return {
                        finding: this.$input.filter('[name="finding"]').val(),
                        remarks: this.$input.filter('[name="remarks"]').val(),
                    };
                },

                /**
                 Activates input: sets focus on the first field.

                 @method activate()
                 **/
                activate: function() {
                    this.$input.filter('[name="finding"]').focus();
                },

                /**
                 Attaches handler to submit form in case of 'showbuttons=false' mode

                 @method autosubmit()
                 **/
                autosubmit: function() {
                    this.$input.keydown(function (e) {
                        if (e.which === 13) {
                            $(this).closest('form').submit();
                        }
                    });
                }
            });

            Findings.defaults = $.extend({}, $.fn.editabletypes.abstractinput.defaults, {
                tpl: '' +
                '<div class="editable-address">' +
                '<em>Finding: </em>' +
                '<div class="input-icon">' +
                '<i class="fa fa-tag" style="top: 0px; margin-top: 10px;"></i>' +
                '<input id="editable_findings" type="text" name="finding" class="form-control" />' +
                '</div>' +
                '</div>'+
                '<div class="editable-address">' +
                '<em>Remark: </em>' +
                '<div class="input-icon">' +
                '<i style="top: 0px; margin-top: 10px;" class="fa fa-comment-o"></i>' +
                '<textarea id="editable_remarks" name="remarks" class="form-control" placeholder="Remarks.."></textarea>' +
                '</div>' +
                '</div>',
            });

            $.fn.editabletypes.findings = Findings;

        }(window.jQuery));
    };

    var init_btn_expand = function() {

        tbl_reading_entry.on('click', '#btn-expand', function () {
            var this_ = $(this);
            var thisTr = this_.closest('tr');
            var thisTr_child = thisTr.children('td').length;
            var data_id = this_.attr('data-id');
            if (this_.hasClass('expanded') == false) {
                thisTr.next('#error').remove();
                this_.removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
                $.ajax({
                    url: PECO.base_url()+'mrd/getmtrinfo',
                    type: 'post',
                    data: {'id': data_id, 'schedid': $('#schedid', thisTr).val(), 'type': 'reading'},
                    dataType: 'json',
                    beforeSend: function () {
                        thisTr.after('<tr id="loading" class="info " ><td colspan="' + thisTr_child + '" class=""><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading..</td></tr>');

                    }
                }).done(function(d){
                    thisTr.after('<tr class="animated fadeIn fast compact '+d.func+'" id="details"><td colspan="' + thisTr_child + '" class="sub-table">' + d.html + '</td></tr>');
                    tbl_reading_entry.find('#loading').remove();

                    init_upload_pics(thisTr.next());
                    init_mtr_pics(thisTr.next(), $('td.mtrno', thisTr).text(), $('#acctid', thisTr).val(), $('#input_year', thisTr).val(), $('#input_month', thisTr).val());

                }).fail(function(){
                    thisTr.after('<tr class="animated fadeIn fast compact danger" id="error"><td colspan="' + thisTr_child + '"><i class="fa fa-times"></i> Error PHP</td></tr>');
                    tbl_reading_entry.find('#loading').remove();
                });
            } else {
                thisTr.next('#details').remove();
                thisTr.next('#error').remove();
                tbl_reading_entry.find('#loading').remove();
                this_.removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
            }
            this_.toggleClass('expanded');
            this_.closest('tr').toggleClass('expand-show');
        });
    };

    var init_btn_truncate_data = function() {
        $(document).on('click', '#btn_truncate_temp_data', function(e) {
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding new pictures',
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
                        url: PECO.base_url() + 'mrd/trundatetestdata',
                        type: 'post',
                        dataType: 'json',
                    }).done(function () {
                        swal.close();
                    });
                }
            });
        });
    }

    var init_btn_forward_addbill = function() {
        $('body').on('click', '#btn_forward_addbill', function(e) {
            e.preventDefault();
            var btn = $(this);
            var btn_html = btn.html();
            var schedid = $('#schedid', document).val();

            var inputs_for_addbills = [];
            // GET ZERO CONSUMPTIONS
            $("td.addbill div.checked", tbl_reading_entry).each(function () {
                var this_ = $(this);
                var row = $(this).closest("tr");
                inputs_for_addbills.push({
                    checked: this_.find('input:checked').val()
                });
            });

            $.ajax({
                url: PECO.base_url() + 'mrd/readinganalysis',
                type: 'post',
                data: {
                    'schedid': schedid,
                    'showall': 1,
                    'print': 2, // FORWARD ANALYSIS
                    'acctids': inputs_for_addbills
                },
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(btn, 'Generating report...');
                }
            }).done(function (d) {
                if (d.qry == true) {
                    var content = '';
                    content += d.header;
                    content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px;">GDLB: <b>' + d.gdlb + '</b></div>';
                    content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">' + d.dates + '</div>';
                    content += '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
                    content += '<table class="table table-condensed tbl-sm print-table-standard">';
                    content += '<thead>';
                    content += '<th>Seq</th>';
                    content += '<th>Servno</th>';
                    content += '<th>Name</th>';
                    content += '<th>MTR</th>';
                    content += '<th>Mtrno</th>';
                    content += '<th>Serial</th>';
                    content += '<th>Mult</th>';
                    content += '<th>Prevrdg</th>';
                    content += '<th>Presrdg</th>';
                    content += '<th>Prev.Kwh</th>';
                    content += '<th>Pres.Kwh</th>';
                    content += '<th>Remarks</th>';
                    content += '</thead>';
                    content += '<tbody>';
                    content += d.html;
                    content += '</tbody>';
                    content += '</table>';
                    content += '<hr style="border: 1px dashed #333; margin: 20px 0px;">';
                    content += '<div style="display:inline-block; margin-bottom: 5px; margin-top: 30px; position: relative;">';
                    content += '<p style="width: 200px; display:inline-block; margin-bottom: 5px; position: absolute; left: 0px;">Total Records: <b>' + d.zero + '</b></p>';
                    content += '<p style="width: 300px; display:inline-block; margin-bottom: 5px; position: absolute; left: 900px;">Printed by: ' + d.printedby + '<br> Date Printed: ' + d.dateprinted + '</p>';
                    content += '</div>';
                    PECO.pecoRepPrint(' ', content, false);
                    PECO.btnSuccess(btn, 'Reports created!', btn_html, 'btn-warning');
                }
            });
        });
    };

    var init_get_addbill_list = function(btn, exec, btn_class) {
        var exec_ = (exec) ? exec : false;
        var btn_html = btn.html();
        var schedid = $('#schedid', document).val();
        $.ajax({
            url: PECO.base_url() + 'mrd/getforaddbill',
            type: 'post',
            data: {
                'schedid': schedid,
                'showall': 1,
                'addbillproc': true,
                'exec': exec_
            },
            dataType: 'json',
            beforeSend: function () {
                PECO.btnLoading(btn, 'Generating report...');
            }
        }).done(function (data) {
            if(data.qry==true) {

                PECO.btnSuccess(btn, 'List loaded!', btn_html, btn_class);

                // ENABLING TABLE SIZE
                var var_table_scroll_height, var_table_records;
                var height = screen.height;
                if(height<=768){
                    var_table_scroll_height = '320px';
                    var_table_records = 20;
                }else{
                    var_table_scroll_height = '420px';
                    var_table_records = 25;
                }

                $('#total_customer_stat', document).text(data.cnt);
                $('#total_customer_addbill', document).text(data.addbill);
                $('#total_recheck', document).text(data.recheck);
                $('#total_cust_wread').text(data.cntread);
                $('#total_kwh_curr').text(data.totalprskwh);
                $('#total_kwh_prev').text(data.totalprvkwh);
                $('#total_cust_zero').text(data.zero);
                $('#total_cust_forbilling').text(data.forbilling);
                $('#ave_kwh_curr').text(data.avkwhcurr);
                $('#ave_kwh_prev').text(data.avkwhprev);
                $('#btn_reader_info').html(data.reader);
                tbl_reading_entry.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    scrollY: var_table_scroll_height,
                    pageLength: var_table_records,
                    aaData: data.list,
                    "searchHighlight": true,
                    aoColumns: [
                        {"data": "seq", sClass: 'number', sWidth: '20px'},
                        {"data": "serviceno", sClass: 'text-primary text-bold'},
                        {"data": "name", sWidth: '300px'},
                        {"data": "meter"},
                        {"data": "meterno", sClass: 'mtrno'},
                        {"data": "serial", "sClass": "text-info"},
                        {"data": "mult", "sClass": "text-danger", sWidth: '100px'},
                        {"data": "curread", sClass: 'number text-success'},
                        {"data": "prevread", sClass: 'number prevread'},
                        {"data": "currcon", sClass: 'number curcon text-success'},
                        {"data": "prevcon", sClass: 'number prevcon'},
                        {"data": "netmet", sClass: 'number netmet'},
                        {"data": "curdem", sClass: 'number curdem'},
                        {"data": "addbill", sClass: 'addbill relative number text-danger', sWidth: '100px'},
                        {"data": "check", sClass: 'number relative checkzero', sWidth: '15px'},
                    ],
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found. </h4>'
                    },
                    columnDefs: [
                        {"orderable": false, searchable: false, "targets": 0},
                        //{"orderable": false, searchable: false, "targets": -2}, // REM
                        {"orderable": false, searchable: false, "targets": -3},
                        {"orderable": false, searchable: false, "targets": -4},
                        {"orderable": false, searchable: false, "targets": -1},
                    ],
                    fnRowCallback: function (nRow, aData, Index) {
                        //$(nRow).addClass(aData.rowbg);
                        $(nRow).find('.icheck').each(function () {
                            var this_ = $(this);
                            var this_td_class = this_.closest('td');
                            var check_color = 'grey';
                            if (this_td_class.hasClass('addbill')) {
                                check_color = 'yellow';
                            }
                            if (this_td_class.hasClass('checkzero')) {
                                check_color = 'red';
                            }
                            this_.iCheck({
                                checkboxClass: 'icheckbox_flat-' + check_color,
                                increaseArea: '20%' // optional
                            });
                        });

                        var schedid = aData.schedid;
                        var acctid = aData.acctid;
                        init_findigns_editable(nRow, schedid, acctid, Index);

                        $(nRow).find('.tooltips').each(function () {
                            $(this).tooltip();
                        });
                    },
                    fnDrawCallback: function () {
                        PECO.select2_scrollertbl(tbl_reading_entry);

                    },
                    "order": [
                        [0, 'asc']
                    ],
                });
                //PECO.dataTableScroller();
            }else{
                PECO.btnSuccess(btn, 'No record found!', btn_html, btn_class);
                PECO.DTAlert(tbl_reading_entry, data.msg, data.func);
            }
        });
    };

    var init_btn_print_analysis = function() {

        $('body').on('click', '#btn_print_report', function(e) {
            e.preventDefault();
            var btn = $(this);
            var btn_html = btn.html();
            var schedid = $('#schedid', document).val();
            $.ajax({
                url: PECO.base_url() + 'mrd/readinganalysis',
                type: 'post',
                data: {
                    'schedid': schedid,
                    'showall': 1,
                    'print': 1,
                },
                dataType: 'json',
                beforeSend: function () {
                    PECO.btnLoading(btn, 'Generating report...');
                }
            }).done(function (d) {
                if (d.qry == true) {
                    var content = '';
                    content += d.header;
                    content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px;">GDLB: <b>' + d.gdlb + '</b></div>';
                    content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">' + d.dates + '</div>';
                    content += '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
                    content += '<table class="table table-condensed tbl-sm print-table-standard">';
                    content += '<thead>';
                    content += '<th>Seq</th>';
                    content += '<th>Service #</th>';
                    content += '<th>Name</th>';
                    content += '<th>Address</th>';
                    content += '<th>MTR</th>';
                    content += '<th>Meter #</th>';
                    content += '<th>Serial</th>';
                    content += '<th>Mult</th>';
                    content += '<th>Prev. Rdng</th>';
                    //content += '<th>Pres. Rdng</th>';
                    //content += '<th>Prev. Kwh</th>';
                    //content += '<th>Pres. Kwh</th>';
                    content += '<th>Remarks</th>';
                    content += '<th>Recheck Read</th>';
                    content += '<th width="200px">Findings</th>';
                    content += '</thead>';
                    content += '<tbody>';
                    content += d.html;
                    content += '</tbody>';
                    content += '</table>';
                    content += '<hr style="border: 1px dashed #333; margin: 20px 0px;">';
                    content += '<div style="display:inline-block; margin-bottom: 5px; margin-top: 30px; position: relative;">';
                    content += '<p style="width: 200px; display:inline-block; margin-bottom: 5px; position: absolute; left: 0px;">Total Records: <b>' + d.recheck + '</b></p>';
                    content += '<p style="width: 200px; display:inline-block; margin-bottom: 5px; position: absolute; left: 300px;">Date Inspected<br> ___________________</p>';
                    content += '<p style="width: 200px; display:inline-block; margin-bottom: 5px; position: absolute; left: 600px;">Inspected By<br> ___________________</p>';
                    content += '<p style="width: 300px; display:inline-block; margin-bottom: 5px; position: absolute; left: 900px;">Printed by: ' + d.printedby + '<br> Date Printed: ' + d.dateprinted + '</p>';
                    content += '</div>';
                    PECO.pecoRepPrint(' ', content, false);
                    PECO.btnSuccess(btn, 'Reports created!', btn_html, 'btn-default');
                }
            });
        });
    };


    var init_btn_print_analysis_bak = function() {
        $('body').on('click', '#btn_print_report', function(e) {
            e.preventDefault();
            var btn_ = $(this);
            if(parseInt($('#total_customer').text()) > 0) {
                var schedid = $('#schedid').val();
                var inputs = [];
                var inputs_recheck = [];
                var inputs_zerocon = [];

                // GET ZERO CONSUMPTIONS
                $("td.chckread div.checked", tbl_reading_entry).each(function () {
                    var this_ = $(this);
                    var row = $(this).closest("tr");
                    inputs_recheck.push({
                        checked: this_.find('input:checked').val()
                    });
                });

                // GET ZERO CONSUMPTIONS
                $("td.addbill div.checked", tbl_reading_entry).each(function () {
                    var this_ = $(this);
                    var row = $(this).closest("tr");
                    inputs_zerocon.push({
                        checked: this_.find('input:checked').val()
                    });
                });


                $.ajax({
                    url: PECO.base_url() + 'mrd/processanalysis',
                    type: 'post',
                    data: {
                        'schedid': schedid,
                        'recheckarr': inputs_recheck,
                        'zeroconarr': inputs_zerocon
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        btn_.addClass('disabled').find('.fa').removeClass('fa-print').addClass('fa-circle-o-notch fa-spin fa-pulse');
                    }
                }).done(function (d) {
                    if (d.qry == true) {
                        var content = '';
                        content += d.header;
                        content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px;">GDLB: <b>'+d.gdlb+'</b></div>';
                        content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">'+d.dates+'</div>';
                        content += '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
                        content += d.content;
                        PECO.pecoRepPrint(' ', content, false);

                        // window.open(PECO.base_url() + 'mrd/printanalysis/' + d.schedid + '/' + d.gdlbid);
                    } else {
                        PECO.initAlerts('Cannot find reading details!', 'Reading Analysis', 'warning');
                    }
                    btn_.removeClass('disabled').find('.fa').removeClass('fa-circle-o-notch fa-spin fa-pulse').addClass('fa-print');
                }).fail(function () {
                    PECO.phpError();
                    btn_.removeClass('disabled').find('.fa').removeClass('fa-circle-o-notch fa-spin fa-pulse').addClass('fa-print');
                });
            }else{
                PECO.initAlerts('Get GDLB Schedule first!', 'System', 'warning');
                $('#schedid').select2('open');
            }
        });
    };

    var init_btn_delete_pic = function() {
        $(document).on('click', '#mtr_pics #btn_delete', function(e){
            e.preventDefault();
            var this_ = $(this);
            this_.closest('div').addClass('border-red-flamingo');
            swal({
                title: "Are you sure?",
                text: 'Adding new pictures',
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
                        url: PECO.base_url() + 'mrd/deletemtrpic',
                        method: 'post',
                        dataType: "json",
                        data: {
                            'homedir': this_.attr('data-dir'),
                            'file': this_.attr('data-file'),
                            'year': this_.attr('data-year'),
                            'month': this_.attr('data-month'),
                        },
                    }).done(function (d) {

                        this_.closest('div').addClass('border-red-flamingo').fadeOut();
                        swal.close();

                        var this_tr = this_.closest('tr');

                        setTimeout(function(){
                            init_mtr_pics(this_tr, $('#frm_read_pic input[name=mtrno]', this_tr).val(), $('#frm_read_pic input[name=acctid]', this_tr).val(), this_.attr('data-year'), this_.attr('data-month'));
                        },500);

                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });

        });
    };

    var init_analysis = function() {

        PECO.select2Basic($('#schedid'), 'mrd/getgdlbsched', 'GDLB..', true, false, false, true);
        PECO.DTDefault(tbl_reading_entry, 'Please select GDLB for reading assigned!');
        //PECO.dtSubDetails(tbl_reading_entry, 'mrd/getmtrinfo');


        $(document).on('hover', '#mtr_pics div', function(e){
            $('#mtr_pics div', document).removeClass('hovered');
            $(this).addClass('hovered');
        });

        $('#showall').select2();

        $('#get_mrd_list').click(function(e){
            e.preventDefault();
            var schedid = $('#schedid').select2('val');
            init_analysis_table(schedid);
        });

        $('#btn_generate_regbill').click(function(e){
            e.preventDefault();
            var this_ = $(this);
            var schedid = $('#schedid').select2('val');
            $.ajax({
                url: PECO.base_url() + 'mrd/processforbilling',
                type: 'post',
                data: {'schedid': schedid, 'billing': 1, 'showall': 2},
                dataType: 'json',
                async: false,
                cache: false,
                beforeSend: function() {
                    this_.find('.fa').removeClass('fa-tag').addClass('fa-circle-o-notch fa-palse fa-spin');
                },
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Billing Process', d.func);
                this_.find('.fa').removeClass('fa-circle-o-notch fa-pulse fa-spin').addClass('fa-tag');
            }).fail(function(){
                PECO.phpError();
                this_.find('.fa').removeClass('fa-circle-o-notch fa-pulse fa-spin').addClass('fa-tag');
            });
        });

        tbl_reading_entry.on('click', '#btn_actual_read', function(e){
            e.preventDefault();
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var acctid = this_row.find('#acctid').val();
            var schedid = this_row.find('#schedid').val();
            var readid = this_row.find('#readid').val();
            var data_stat = this_.attr('data-stat');
            $.ajax({
                url: PECO.base_url() + 'mrd/submitactualreadingrow',
                type: 'post',
                data: {'readid': readid, 'type': data_stat, 'remarks': 'ACTUAL'},
                dataType: 'json',
                beforeSend: function() {
                    this_.removeClass('btn-primary').find('.fa').removeClass('fa-check fa-save').addClass('fa-circle-o-notch fa-spin fa-pulse');
                }
            }).done(function(d){
                if(data_stat==1) {
                    this_.attr('data-stat', 0);
                    this_row.removeClass('info danger warning').addClass('success');
                    this_.removeClass('btn-primary').addClass('btn-success').find('.fa').removeClass('fa-save fa-circle-o-notch fa-spin fa-pulse').addClass('fa-check');
                }else {
                    this_.attr('data-stat', 1);
                    this_row.removeClass('info danger warning success');
                    this_.removeClass('btn-success').addClass('btn-primary').find('.fa').removeClass('fa-check fa-circle-o-notch fa-spin fa-pulse').addClass('fa-save');

                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        shortcut.add('F2', function () {
            var reading_focused = $(':focus', tbl_reading_entry);

            $('#btn-expand.expanded', tbl_reading_entry).each(function() {
                var this_btn = $(this);
                this_btn.trigger('click');
            });

            if(reading_focused.hasClass('reading')) {
                var this_row = reading_focused.closest('tr');
                var this_expand = $('#btn-expand', this_row);
                this_expand.trigger('click');
            }
            return false;
        });

        shortcut.add('Shift+Enter', function () {
            var finding_focused = $('#editable_remarks:focus');
            if(finding_focused.length > 0) {
                var editableform = finding_focused.closest('.editableform');
                var btn_submit = $('.editable-submit', editableform);
                btn_submit.trigger('click');
            }
            return false;
        });

        shortcut.add('esc', function () {
            $('#btn-expand.expanded', tbl_reading_entry).each(function() {
                var this_btn = $(this);
                this_btn.trigger('click');
            });
            return false;
        });



        tbl_reading_entry.on('keypress', '.reading', function(e) {
            var this_row = $(this).closest('tr');
            if (e.keyCode == 13) {
                e.preventDefault();
                $('#findings_input', this_row).trigger('click');
                setTimeout(function(){
                    $('#input_findings', this_row).focus();
                }, 300);

                if(init_submit_reading_row_recheck(this_row)==true ) {
                    var next_row = this_row.next();
                    var next_findings = next_row.find('#findings');
                    console.log(next_findings.val());
                    setTimeout(function () {
                        next_findings.select2('open');
                    }, 100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                    next_row.addClass('row-info');
                    init_compute_reading_stat();
                }
            }
        });

        tbl_reading_entry.on('change', '#findings', function(e) {
            var this_row = $(this).closest('tr');
            var this_reading =  this_row.find('input.reading');
            this_reading.focus();
            setTimeout(function () {
                this_reading.select();
            }, 100);
        });

        init_btn_expand();
        init_btn_delete_pic();
        init_btn_print_analysis();
        init_btn_forward_addbill();
        init_btn_print_account();
        init_reading_entry_keyboard();
    };

    var init_addbill_process = function() {
        PECO.select2Basic($('#schedid'), 'mrd/getgdlbsched', 'GDLB..', true, false, false, true);
        PECO.DTDefault(tbl_reading_entry, 'Please select GDLB for reading assigned!');

        init_btn_expand();

        $('body').on('click', '#btn_get_addbill_list', function(e) {
            e.preventDefault();
            var btn = $(this);
            init_get_addbill_list(btn, false, 'btn-primary');
        });

        $('body').on('click', '#btn_compute_average_kwh', function(e) {
            e.preventDefault();
            var btn = $(this);
            init_get_addbill_list(btn, 1, 'btn-warning');
        });


        tbl_reading_entry.on('keypress', '#input_addbil_kwh', function(e){
            if(e.keyCode == 13) {

                e.preventDefault();
                var this_ = $(this);
                var this_row = this_.closest('tr');
                var this_input = $('#input_addbil_kwh', this_row);

                var acctid = this_row.find('#acctid').val();
                var schedid = this_row.find('#schedid').val();
                var readid = this_input.attr('data-readid');
                var data_stat = this_input.attr('data-stat');
                var this_val = this_input.val();

                $.ajax({
                    url: PECO.base_url() + 'mrd/submitactualreadingrow',
                    type: 'post',
                    data: {
                        'readid': readid,
                        'type': data_stat,
                        'acctid': acctid,
                        'schedid': schedid,
                        'remarks': 'ACTUAL',
                        'value': this_val
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        this_.removeClass('btn-primary').find('.fa').removeClass('fa-check fa-save').addClass('fa-circle-o-notch fa-spin fa-pulse');
                    }
                }).done(function (d) {
                    if (data_stat == 1) {
                        this_.attr('data-stat', 0);
                        this_row.removeClass('info danger warning').addClass('success');
                        this_.removeClass('btn-primary').addClass('btn-success').find('.fa').removeClass('fa-save fa-circle-o-notch fa-spin fa-pulse').addClass('fa-check');
                    } else {
                        this_.attr('data-stat', 1);
                        this_row.removeClass('info danger warning success');
                        this_.removeClass('btn-success').addClass('btn-primary').find('.fa').removeClass('fa-check fa-circle-o-notch fa-spin fa-pulse').addClass('fa-save');

                    }
                }).fail(function () {
                    PECO.phpError();
                });

            }
        });

    };

    var init_mtr_pics = function(this_row, mtrno, acctno, year, month) {
        $.ajax({
            url: PECO.base_url() + 'mrd/getmtrpics',
            method: 'post',
            dataType: "json",
            data: {'mtrno': mtrno, 'acctno': acctno, 'year': year, 'month': month},
            beforeSend: function() {
                $('#mtr_pics', this_row).html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Loading pictures..');
            }
        }).done(function (d) {
            $('#mtr_pics', this_row).html(d.html);

            /*

            $('#mtr_pics .fancybox-button', this_row).fancybox({
                maxWidth: 900,
                maxHeight: 700,
                fitToView: true,
                width: '80%',
                height: '80%',
                autoSize: true,
                closeClick: false,
                openEffect: 'stretch',
                closeEffect: 'stretch'
            });

            */

        });
    };

    var init_upload_pics = function(this_row) {
        $('#frm_read_pic', this_row).submit(function(e) {
            var form = $(this);
            e.preventDefault();
            swal({
                title: "Are you sure?",
                text: 'Adding new pictures',
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
                        url: form.attr('action'),
                        method: form.attr('method'),
                        dataType: "json",
                        data: new FormData(form[0]),
                        processData: false,
                        contentType: false,
                    }).done(function (d) {
                        swal('Upload Picture', d.msg, d.func);
                        init_mtr_pics(this_row, d.mtrno, d.acctid, d.year, d.month);
                    }).fail(function(){
                        swal("Error404: PHP", "Server side error!", "error");
                    });
                }else{
                    swal.close();
                }
            });

        });
    };


    var init_reading_submit = function() {
        frm_reading_entry.submit(function (e) {
            e.preventDefault();
            if(e.keyCode!=13) {
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    type: form.attr('method'),
                    dataType: 'json',
                }).done(function (d) {
                    console.log(d);
                    PECO.initAlerts(d.msg, d.title, d.func);
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });
    };


    var init_meter_tagging_table = function(gdlbid) {
        var tbl = $('#tbl_gdlb_tagging');
        $.ajax({
            url: PECO.base_url() + 'mrd/metertagging',
            type: 'post',
            dataType: 'json',
            data: {'gdlbid': gdlbid},
            beforeSend: function() {
                PECO.DTphpLoading(tbl, 'Loading GDLB Customers list...');
            }
        }).done(function (d) {
            tbl.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                scrollY: '400px',
                aaData: d.data,
                aoColumns: [
                    {"data": "seq", sClass: ''},
                    {"data": "serviceno", sClass: '', sWidth: '70px'},
                    {"data": "name", sClass: '', sWidth: '30%'},
                    {"data": "address", sClass: '', sWidth: ''},
                    {"data": "mtr", sClass: '', sWidth: '5%'},
                    {"data": "meterserial", sClass: '', sWidth: '70px'},
                    {"data": "meterno", sClass: '', sWidth: '70px'},
                    {"data": "reader", sClass: 'input editable telcode', sWidth: '70px'},
                    {"data": "readername", sClass: 'input editable readername', sWidth: '70px'},
                    {"data": "control", sClass: 'controls', sWidth: '50px'},
                ],
                fnRowCallback: function(nRow, aData, index) {

                }
            });
            PECO.dataTableScroller();
        });
    };



    var init_reading_table = function (data, types, loading, thisbtn, userid) {
        var types = (types) ? types : 0;
        var loading_ = (loading) ? true : false;
        var userid_ = (userid) ? userid : false;
        var this_btn, this_btn_html;
        if(thisbtn) {
            this_btn = thisbtn;
            this_btn_html = this_btn.html();
        }
        $.ajax({
            url: PECO.base_url() + 'mrd/readingentry',
            type: 'post',
            dataType: 'json',
            data: {'schedid': data, 'type': types, 'userid': userid_},
            beforeSend: function() {
                if( loading_ ) {
                    tbl_reading_entry.dataTable().empty();
                    PECO.DTphpLoading(tbl_reading_entry, ' Loading customer lists..');
                }

                if(thisbtn) {
                    PECO.btnLoading(this_btn, 'Getting reading sheet...');
                }
            }
        }).done(function (d) {
            $('#custcnt').val(d.cnt);
            $('#custread').val(d.readnum);
            $('#readstat').text(d.readnum+' / '+d.cnt);
            $('#avkwh').text(accounting.format(d.avkwh, 0));

            if(thisbtn) {
                PECO.btnSuccess(this_btn, false, this_btn_html, 'btn-primary' );
            }


            tbl_reading_entry.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                //scrollY: '400px',
                aaData: d.list,
                aoColumns: [
                    {"data": "seq", sWidth: '60px', sClass: 'input number'},
                    {"data": "seqin", sWidth: '20px', sClass: 'input number'},
                    {"data": "serviceno", sWidth: '80px', sClass: "text-primary text-bold"},
                    {"data": "meter", sWidth: '40px'},
                    {"data": "name", sWidth: '200px', sClass: 'text-bold'},
                    {"data": "serial", sClass: "text-info"},
                    {"data": "meterno"},
                    {"data": "curread", sClass: 'input number text-danger bold'},
                    {"data": "demand", sClass: 'input number text-danger'},
                    {"data": "netmtr", sClass: 'input number text-danger'},
                    {"data": "controls", sWidth: '60px', sClass: 'controls text-align-center'}
                ],
                "fnDrawCallback": function (oSettings) {
                },
                fnRowCallback: function(nRow, data) {
                    $(nRow).find('td').each(function() {
                        $(this).addClass('bg-fadeout');
                    });
                    if(data.submitted == true) {
                        $('td', nRow).each(function() {
                            $(this).addClass('active');
                        });
                    }
                    PECO.popOverRow($('.popovers', nRow), true, true, 'popover-blue');

                },
                "order": [
                    [0, 'asc']
                ],
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
            }).on('submit', '#frm_findings_entry', function(e) {
                e.preventDefault();
                PECO.initAlerts('Findings has been submited', 'PECO.reading', 'success');
                e.stopImmediatePropagation();
            }).on('submit', '#frm_mtrseq_entry', function(e) {
                e.preventDefault();
                PECO.initAlerts('Sequence has been updated!', 'PECO.reading', 'success');
                e.stopImmediatePropagation();
            });
            PECO.dataTableScroller();

            init_handler_row_select2(tbl_reading_entry);

        }).fail(function () {
            PECO.phpError();

            if(this_btn.length > 0) {
                PECO.btnErrorPHP(this_btn, this_btn_html, 'btn-primary');
            }
        });
    };

    var init_handler_row_select2 = function(tbl_draw) {
        $('.popovers#readpopovers', tbl_draw).click(function () {
            var this_pop = $(this);
            var this_row = this_pop.closest('tr');
            var this_form_earnings = $('.popover #frm_findings_entry', this_row);
            PECO.select2Basic($('input#findings', this_form_earnings), 'mrd/getselect2findings', 'Select Findings..');
            return false;
        });

    };

    var init_submit_reading_row_recheck_typehead = function(row, data) {
        var submit      = false;

        var this_cur_con = $('td.curcon', row);
        var this_cur_read = $('input.reading', row);
        var this_prev_read = $('td.prevread', row);
        var this_percent = $('td.percent', row);

        $.ajax({
            url: PECO.base_url() + 'mrd/editreadingrow',
            data: data,
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            PECO.initAlerts(d.msg, d.title, d.func);
            this_cur_con.text(d.newcons);
            this_percent.html(d.percent);
            row.removeClass('warning danger info success');
            row.addClass(d.color);
            if(d.recheck == false) {
                $('td.chckread div', row).removeClass('checked').find('input').attr('checked', false);
            }else{
                $('td.chckread div', row).addClass('checked').find('input').attr('checked', true);
            }
            row.removeClass('unsaved').addClass('saved');
        });
        return submit;
    };

    var init_submit_reading_row_recheck = function(this_row) {
        var submit      = false;
        var acct_id     = this_row.find('#acctid').val();
        var schd_id     = this_row.find('#schedid').val();
        var mtr_id      = this_row.find('#mtrid').val();
        var reading     = this_row.find('#reading').val();
        var demand      = this_row.find('#demand').val();
        var findings    = this_row.find('#findings').val();

        var this_cur_con = this_row.find('td.curcon');
        var this_cur_read = this_row.find('input.reading');
        var this_prev_read = this_row.find('td.prevread');
        var this_percent = this_row.find('td.percent');

        $.ajax({
            url: PECO.base_url() + 'mrd/editreadingrow',
            data: {
                'acctid': acct_id,
                'schedid': schd_id,
                'mtrid': mtr_id,
                'reading': reading,
                'demand': demand,
                'findings': findings,
                'recheck': true
            },
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            PECO.initAlerts(d.msg, d.title, d.func);
            this_cur_con.text(d.newcons);
            this_percent.html(d.percent);
            this_row.removeClass('warning danger info success');
            this_row.addClass(d.color);
            if(d.recheck == false) {
                this_row.find('td.chckread div').removeClass('checked').find('input').attr('checked', false);
            }else{
                this_row.find('td.chckread div').addClass('checked').find('input').attr('checked', true);
            }
            this_row.removeClass('unsaved').addClass('saved');
        });
        return submit;
    };

    var init_submit_reading_row = function(this_row) {
        var submit = false;
        var acct_id =   this_row.find('#acctid').val();
        var schd_id =   this_row.find('#schedid').val();
        var mtr_id =    this_row.find('#mtrid').val();
        var reading =   this_row.find('#reading').val();
        var demand =    this_row.find('#demand').val();
        var netmtr =    this_row.find('#netmtr').val();
        $.ajax({
            url: PECO.base_url() + 'mrd/submitreadingrow',
            data: {'acctid': acct_id, 'schedid': schd_id, 'mtrid': mtr_id, 'reading': reading, 'demand': demand, 'netmtr': netmtr},
            type: 'post',
            dataType: 'json',
            async: false,
        }).done(function(d){
            console.log(d);
            submit = d.qry;
            // PECO.initAlerts(d.msg, d.title, d.func, false, false);
        });
        return submit;
    };

    var init_compute_reading_stat = function() {
        var readings_total = 0;
        var readings_cnt = 0;
        var readings_cust = 0;

        $('input.reading', tbl_reading_entry).each(function(){
            readings_total += Number($(this).val());
            readings_cust += 1;
            if($(this).val() != '') {
                readings_cnt += 1;
            }
        });
        var readstat = readings_cnt + ' / ' + readings_cust;
        var avkwh = Number(readings_total) / Number(readings_cnt);
        $('#avkwh').text(accounting.format(avkwh, 0));
        $('#readstat').text(readstat);
    };

    var init_analysis_table =  function(data) {
        var show_all = $('#showall').select2('val');
        tbl_reading_entry.dataTable().empty();
        $.ajax({
            url: PECO.base_url() + 'mrd/readinganalysis',
            type: 'post',
            data: {'schedid': data, 'showall': show_all},
            dataType: 'JSON',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_reading_entry, 'Processing data..');
            },
        }).done(function(data) {
            if(data.qry==true) {


                // ENABLING TABLE SIZE
                var var_table_scroll_height, var_table_records;
                var height = screen.height;
                if(height<=768){
                    var_table_scroll_height = '330px';
                    var_table_records = 20;
                }else{
                    var_table_scroll_height = '430px';
                    var_table_records = 25;
                }


                $('#total_customer_stat', document).text(data.cnt);
                $('#total_customer_addbill', document).text(data.addbill);
                $('#total_recheck', document).text(data.recheck);
                $('#total_cust_wread').text(data.cntread);
                $('#total_kwh_curr').text(data.totalprskwh);
                $('#total_kwh_prev').text(data.totalprvkwh);
                $('#total_cust_zero').text(data.zero);
                $('#total_cust_forbilling').text(data.forbilling);
                $('#ave_kwh_curr').text(data.avkwhcurr);
                $('#ave_kwh_prev').text(data.avkwhprev);
                $('#btn_reader_info').html(data.reader);
                var table = tbl_reading_entry.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    //scrollY: var_table_scroll_height,
                    //pageLength: var_table_records,
                    aaData: data.list,
                    "searchHighlight": true,
                    aoColumns: [
                        //{"data": "expand", sClass: 'text-align-center', sWidth: '10px'},
                        {"data": "seq", sClass: 'number', sWidth: '20px'},
                        {"data": "serviceno", sClass: 'text-primary text-bold'},
                        {"data": "name", sWidth: '300px'},
                        {"data": "meter"},
                        {"data": "meterno", sClass: 'mtrno'},
                        {"data": "serial", "sClass": "text-info"},
                        {"data": "mult", "sClass": "text-danger", sWidth: '100px'},
                        {"data": "curread", sClass: 'number text-success'},
                        {"data": "prevread", sClass: 'number prevread'},
                        {"data": "currcon", sClass: 'number curcon text-success'},
                        {"data": "prevcon", sClass: 'number prevcon'},
                        {"data": "rem", sClass: 'controls findings', sWidth: '150px'},
                        {"data": "curdem", sClass: 'number curdem'},
                        {"data": "netmet", sClass: 'number netmet'},
                        //{"data": "regbill", sClass: 'regbill', sWidth: '15px'},
                        {"data": "percent", sClass: 'number relative percent', sWidth: '120px'}, // ADD RELATIVE FOR PERCENT STATS ABSOLUTE
                        {"data": "addbill", sClass: 'addbill relative', sWidth: '15px'},
                        {"data": "chckread", sClass: 'chckread relative', sWidth: '15px'},
                        {"data": "controls", sClass: 'controls', sWidth: '30px'},
                    ],
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found. </h4>'
                    },
                    columnDefs: [
                        {"orderable": false, searchable: false, "targets": 0},
                        //{"orderable": false, searchable: false, "targets": -2}, // REM
                        {"orderable": false, searchable: false, "targets": -3},
                        {"orderable": false, searchable: false, "targets": -4},
                        {"orderable": false, searchable: false, "targets": -1},
                    ],
                    fnRowCallback: function (nRow, aData, Index) {
                        //$(nRow).addClass(aData.rowbg);
                        $(nRow).find('.icheck').each(function () {
                            var this_ = $(this);
                            var this_td_class = this_.closest('td');
                            var check_color = 'grey';
                            if (this_td_class.hasClass('addbill')) {
                                check_color = 'yellow';
                            }
                            if (this_td_class.hasClass('chckread')) {
                                check_color = 'red';
                            }
                            this_.iCheck({
                                checkboxClass: 'icheckbox_flat-' + check_color,
                                increaseArea: '20%' // optional
                            });
                        });

                        var schedid = aData.schedid;
                        var acctid = aData.acctid;
                        // init_findigns_editable(nRow, schedid, acctid, Index);

                        /*--------------------------------------------
                        | FINDINGS AUTO COMPLETE SEARCH
                        |_____________________________________________
                        */
                        init_findings_typeahead(nRow, schedid, acctid);

                        $(nRow).find('.tooltips').each(function () {
                            $(this).tooltip();
                        });
                    },
                    fnDrawCallback: function () {
                        PECO.select2_scrollertbl(tbl_reading_entry);

                    },
                    "order": [
                        [0, 'asc']
                    ],
                });
                //PECO.dataTableScroller();

            }else{
                PECO.DTAlert(tbl_reading_entry, data.msg, data.func);
            }
        }).fail(function() {
            PECO.DTDefault(tbl_reading_entry, ' No Record found: Error404');
        });

    };

    var formatDataSelection = function (route) {

        if (!route.id) {
            return route.text;
        }
        var $route = $('<span><i class="fa fa-check text-success"></i> ' + route.text.split('-', 1) + '</span>');
        return $route
    }

    var formatState = function (route) {
        var text_arr = route.text.split('-');
        if (!route.id) {
            return route.text;
        }
        var $route = $(
            '<span class="text-primary"><b>' + text_arr[0] + '</b> - ' + text_arr[1] + '</span>'
        );
        return $route;
    }


    var init_gdlb_table_modal = function() {
        var dist = $('#modal_select_district', document).val();
        var datesched = $('#entry_scheddate', document).val();
        var empid = $('#entry_empid', document).val();
        var rmonth = $('#entry_rmonth', document).val();
        var ryear = $('#entry_ryear', document).val();

        $.ajax({
            url: PECO.base_url() + 'mrd/getgdlblist',
            dataType: 'json',
            type: 'POST',
            data: {'dist': dist, 'datesched': datesched, 'empid': empid, 'rmonth': rmonth, 'ryear': ryear},
            beforeSend: function() {
                tbl_assign_gdlb.dataTable().empty();
                PECO.DTphpLoading(tbl_assign_gdlb, ' GDLB Lists.. ');
            }
        }).done(function (d) {
            tbl_assign_gdlb.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                scrollY: '300px',
                aaData: d.data,
                "order": [[ 0, "asc" ]],
                aoColumns: [
                    {"data": "gdlb", sClass: '', "orderable": false},
                    {"data": "gdlb", sClass: 'gdlb'},
                    {"data": "stats", sClass: '', sWidth: '120px'},
                    {"data": "sched", sClass: ''},
                    {"data": "control", sWidth: '30px', sClass: 'text-align-center', "orderable": false},
                ],
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No record found.'
                },
                fnRowCallback: function(nRow, aData, Index) {
                    $(nRow).find('td').eq(1).css('position', 'relative');
                    PECO.iCheckRow($('input[type=checkbox]', nRow), 'minimal', 'red');

                    var index = Index + 1;
                    $('td:eq(0)', nRow).html(index);
                    if(aData.assigned=='checked') {
                        $(nRow).addClass('checked');
                        $('td', nRow).addClass('active');
                    }

                    if(aData.assigned=='read') {
                        $(nRow).addClass('read');
                        $('td', nRow).addClass('active');
                        $('td *', nRow).addClass('text-danger');
                        $('td * *', nRow).addClass('text-danger');
                    }
                },
                searchHighlight: true,
                drawCallback: function() {
                    setTimeout(function(){
                        PECO.dataTableScroller();
                    },100);
                }
            });

            var tableWrapper = jQuery('#tbl_gdlb_wrapper');

            tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
            PECO.initDTNicescroller();
        }).fail(function (d) {
            PECO.phpError();
        });
    };

    var  sortTable = function() {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("reading_sheet_table");
        switching = true;
        /*Make a loop that will continue until
         no switching has been done:*/
        while (switching) {
            //start by saying: no switching is done:
            switching = false;
            rows = table.getElementsByTagName("tr");
            /*Loop through all table rows (except the
             first, which contains table headers):*/
            for (i = 1; i < (rows.length - 1); i++) {
                //start by saying there should be no switching:
                shouldSwitch = false;
                /*Get the two elements you want to compare,
                 one from current row and one from the next:*/
                x = rows[i].getElementsByTagName("td")[0];
                y = rows[i + 1].getElementsByTagName("td")[0];
                //check if the two rows should switch place:
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    //if so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
            if (shouldSwitch) {
                /*If a switch has been marked, make the switch
                 and mark that a switch has been done:*/
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    var print_gdlb_select = function(this_, gdlbid, userid) {
        if(gdlbid && gdlbid > 0) {
            searchIDs = gdlbid;
        }else {
            var searchIDs = $("#tbl_assign_gdlb tr td input.checkbox:checked", document).map(function () {
                return $(this).val();
            }).get(); // <---
        }

        if(searchIDs!='') {
            $.ajax({
                url: PECO.base_url() + 'mrd/getmrdacctlist',
                data: {'schedid': searchIDs, 'userid': userid},
                dataType: 'json',
                type: 'POST',
                beforeSend: function() {
                    this_.html('<i class="fa fa-circle-o-notch fa-spin"></i> Processing..');
                }
            }).done(function (d) {
                var content='';
                content += d.header;
                content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px;">GDLB: <b>'+d.gdlb+'</b></div>';
                content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">'+d.dates+'</div>';
                content += '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
                content += '<table class="table print-table-standard" id="reading_sheet_table">';
                content += '<thead>';
                content += '<th>SEQ</th>';
                content += '<th>SERVNO</th>';
                content += '<th>MTR</th>';
                content += '<th>NAME</th>';
                content += '<th>MTR NO.</th>';
                content += '<th>SERIAL</th>';
                content += '<th width="100px">READING</th>';
                content += '</thead>';
                content += '<tbody>';
                for(var i = 0; i<d.num; i++) {
                    content += '<tr>';
                    content += '<td>'+d.data[i].seq+'</td>';
                    content += '<td>'+d.data[i].serviceno+'</td>';
                    content += '<td>'+d.data[i].mtr+'</td>';
                    content += '<td>'+d.data[i].name+'<br>'+d.data[i].address+'</td>';
                    content += '<td>'+d.data[i].meterserial+'</td>';
                    content += '<td>'+d.data[i].meterno+'</td>';
                    content += '<td></td>';
                    content += '</tr>';
                }
                content += '</tbody>';
                content += '</table>';
                content += 'Total Records: <b>' + d.num + '</b>';


                setTimeout(function(){
                    sortTable();
                }, 300);

                PECO.pecoRepPrint('', content, false);
                this_.html('<i class="fa fa-print"></i> Print Schedules');
            }).fail(function (d) {
                PECO.phpError();
                this_.html('<i class="fa fa-print"></i> Print Schedules');
            });
        }else{
            PECO.initAlerts('Please select GDLB from the list above!', 'Warning', 'warning');
        }

    };

    var init_gdlb_selected = function(schedid, userid) {

        $.ajax({
            url: PECO.base_url() + 'mrd/getgdlbcustomers',
            data: {'schedid': schedid, 'userid': userid},
            dataType: 'json',
            type: 'POST',
            beforeSend: function() {
                tbl_gdlb_customers.dataTable().empty();
                PECO.DTphpLoading(tbl_gdlb_customers, ' Loading customer lists...');
            }

        }).done(function (d) {
            tbl_gdlb_customers.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                scrollY: '350px',
                aaData: d.data,
                aoColumns: [
                    {"data": "seq", sClass: ''},
                    {"data": "serviceno", sClass: ''},
                    {"data": "name", sClass: '', sWidth: '25%'},
                    {"data": "address", sClass: '', sWidth: '25%'},
                    {"data": "meterno", sClass: ''},
                    {"data": "meterserial", sClass: ''},
                    {"data": "tagging", sClass: 'tagging', sWidth: '15%'},
                    {"data": "control", sClass: 'controls'},
                ],
                fnRowCallback: function(nRow, aData, index) {
                    PECO.usersSelectTagging($("#input_tagread", nRow), false, false);
                }
            });
            PECO.dataTableScroller();
        }).fail(function (d) {
            PECO.phpError();
        });

        tbl_reading_entry.on('click', '#btn_specific_reader', function(e){
            e.preventDefault();
            var this_ = $(this);
            var acct_id = this_.attr('data-acctid');

            $('#edit_reader_spec').find('#acctid').val(acct_id);
            $.ajax({
                url: PECO.base_url() + 'mrd/getacctreaders',
                type: 'post',
                data: {'acctid': acct_id},
                dataType: 'json'
            }).done(function(d){
                console.log(d);
                if(d.qry==true) {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, d.users);
                }else {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, false);
                }
            }).fail(function(){
                PECO.phpError();
            });
        });

    };


    var init_gdlb_selected_reader = function(schedid, userid) {

        $.ajax({
            url: PECO.base_url() + 'mrd/getgdlbcustomersspecific',
            data: {'schedid': schedid, 'userid': userid},
            dataType: 'json',
            type: 'POST',
            beforeSend: function() {
                tbl_gdlb_customers.dataTable().empty();
                PECO.DTphpLoading(tbl_gdlb_customers, ' Loading customer lists...');
            }

        }).done(function (d) {
            tbl_gdlb_customers.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                scrollY: '350px',
                aaData: d.data,
                aoColumns: [
                    {"data": "seq", sClass: ''},
                    {"data": "serviceno", sClass: '', sWidth: '70px'},
                    {"data": "name", sClass: '', sWidth: '30%'},
                    {"data": "address", sClass: '', sWidth: '30%'},
                    {"data": "meterno", sClass: '', sWidth: '70px'},
                    {"data": "meterserial", sClass: '', sWidth: '70px'},
                    {"data": "control", sClass: 'controls', sWidth: '50px'},
                ],
                fnRowCallback: function(nRow, aData, index) {
                    PECO.usersSelectTagging($("#input_tagread", nRow), false, false);
                }
            });
            PECO.dataTableScroller();
        }).fail(function (d) {
            PECO.phpError();
        });

        tbl_reading_entry.on('click', '#btn_specific_reader', function(e){
            e.preventDefault();
            var this_ = $(this);
            var acct_id = this_.attr('data-acctid');

            $('#edit_reader_spec').find('#acctid').val(acct_id);
            $.ajax({
                url: PECO.base_url() + 'mrd/getacctreaders',
                type: 'post',
                data: {'acctid': acct_id},
                dataType: 'json'
            }).done(function(d){
                console.log(d);
                if(d.qry==true) {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, d.users);
                }else {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, false);
                }
            }).fail(function(){
                PECO.phpError();
            });
        });


    };


    var init_reading_schedule_calendar = function(loading) {
        var loading_ = (loading) ? true : false;

        var container_schedule_calendar = $('#container_schedule_calendar', document);

        var date_start = $('#schedule_date_start', document).val();
        var date_end = $('#schedule_date_end', document).val();
        var billmo = $('#schedule_billmo', document).val();
        var billyr = $('#schedule_billyr', document).val();

        $.ajax({
            url: PECO.base_url() + 'mrd/getmrdcalendardt',
            type: 'post',
            data: {'billmo': billmo, 'billyr': billyr, 'datestart': date_start, 'dateend': date_end},
            dataType: 'json',
            beforeSend: function() {
                if(loading_) {
                    $('#tbl_schedule_calendar tbody').html('<tr><td><h3 style="margin-top: 10px;"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading schedules....</h3></td>');
                    // container_schedule_calendar.html('<h3 style="margin-top: 30px;"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading schedules....</h3>');
                    // PECO.DTphpLoading($('#tbl_schedule_calendar'));
                    // $('#tbl_schedule_calendar').DataTable().clear();
                }
            }
        }).done(function(d){
            $('#tbl_schedule_calendar tbody', document).html('');
            $('#tbl_schedule_calendar thead tr', document).html('');
            if(d.columns.length>0){
                for(th = 0; th<d.columns.length; th++) {
                    $('#tbl_schedule_calendar thead tr', document).append('<th class="dynamic '+d.columns[th]['sClass']+'">'+d.columns[th]['text']+'</th>');
                }
            }
            setTimeout(function() {

                var tables = $('#tbl_schedule_calendar').DataTable({
                    bDestroy: true,
                    info: false,
                    //scrollY: "500px",
                    scrollX: true,
                    scrollCollapse: true,
                    paging: false,
                    saveState: true,
                    fixedColumns: {
                        leftColumns: 2,
                    },
                    searchHighlight: true,
                    aoColumns: d.columns,
                    bStateSave: true,
                    bProcessing: false,
                    aaData: d.list,
                    fnRowCallback: function(nRow, Data) {
                        $('.tooltips', nRow).tooltip();
                    },
                    drawCallback: function() {
                        setTimeout(function(){
                            PECO.dataTableScroller();
                        }, 100);
                    }
                });

                /*
                $('#tbl_schedule_calendar tbody').on( 'mouseenter', 'td', function () {
                    var colIdx = tables.cell(this).index().column;
                    $(tables.cells().nodes()).removeClass('active');
                    $(tables.column(colIdx).nodes()).addClass('active');
                });
                */

            }, 200);
        }).fail(function(err){
            PECO.phpError();
        });
    };

    var init_reading_schedule = function() {

        //init_gdlb_table();
        init_reading_schedule_calendar(true);
        init_btn_truncate_data();
        init_print_reading_sheet_btn();

        $('.select2', document).each(function() {
            $(this).select2({'placeholder': 'Select Month..'});
        });

        $(document).on('click', '#btn_get', function() {
            $('#tbl_schedule_calendar', document).DataTable().destroy();
            var td_cnt = $('#tbl_schedule_calendar thead tr th').length;
            $('#tbl_schedule_calendar tbody').html('<tr><td colspan="'+td_cnt+'"><h3 style="margin-top: 10px;"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading schedules....</h3></td>');

            init_reading_schedule_calendar();
        });

        $('#btn_toggle_weekend', document).click(function(e) {
            e.preventDefault();
            $('#tbl_schedule_calendar td.danger', document).toggle();
            $('#tbl_schedule_calendar th.danger', document).toggle();
            $('.dataTables_scrollHeadInner th.danger', document).toggle();
            PECO.dataTableScroller();
            $(this).toggleClass('active');
        });




        $('#tbl_schedule_calendar', document).on('contextmenu', 'tr td.date-gdlb .list-group li', function(e) {
            // ar_table.find('tr').removeClass('info');
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            var this_reader = this_.closest('.data-reader');
            var this_reader_id = this_reader.attr('data-reader');

            console.log(this_id);
            console.log(this_reader_id);


            if (this_id > 0) {
                e.preventDefault();
                // WRITE THE CONTEXT MENU IN THE PAGE
                var context_menu_list = '<ul id="monthly_context_menu" class="custom-menu">' +
                    '<li style="background: #00A8FF; color: #fff; font-weight: bold;">Menu</li>' +
                    '<li data-action="print" data-schedid="'+this_id+'" data-reader="' + this_reader_id + '" ><i class="fa fa-print fa-fw text-primary"></i> Print Reading Sheet</li>' +
                    '</ul>';
                $('body').append(context_menu_list);

                this_.closest('.data-gdlb').addClass('active');

                // Show contextmenu
                $(".custom-menu").finish().toggle(100).// In the right position (the mouse)
                css({top: e.pageY + "px", left: e.pageX + "px"});

                var windowHeight = $(window).height() / 2;
                var windowWidth = $(window).width() / 2;
                if (e.clientY > windowHeight && e.clientX <= windowWidth) {
                    $(".custom-menu").css("left", e.clientX);
                    $(".custom-menu").css("bottom", $(window).height() - e.clientY);
                    $(".custom-menu").css("right", "auto");
                    $(".custom-menu").css("top", "auto");
                }
            }

        });


        $(document).click(function(e){
            if ($(".custom-menu").has(e.target).length === 0) {
                $(".custom-menu").hide(100);
                $('#monthly_context_menu').remove();
            }
        });

        $('body').on('click', '.custom-menu li', function(e){
            e.preventDefault();
            var this_ = $(this);
            var schedid =  this_.attr('data-schedid');
            var userid =  this_.attr('data-reader');

            // This is the triggered action name
            switch($(this).attr("data-action")) {
                // A case for each action. Your actions here
                case "print":
                    print_gdlb_select(this_, schedid, userid);
                    break;
            }
            // Hide it AFTER the action was triggered

        });


        $("#edit_reader_spec").draggable({
            handle: ".modal-header"
        });

        $(document).on('click', '#btn_delete', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_li = this_.closest('li.list-group-item');
            var this_id = this_.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'mrd/delreadsched',
                type: 'post',
                data: {'id': this_id},
                dataType: 'json'
            }).done(function(d) {
                if(d.qry) {
                    this_li.remove();
                }else{
                    alert('qry is false!');
                }
            });
        });


        $(document).on('click', '#btn_delete_all', function(e) {
            e.preventDefault();

            var this_ = $(this);
            var this_td = this_.closest('td');
            var this_id = this_.attr('data-id');
            var this_month = this_.attr('data-month');
            var this_year = this_.attr('data-year');
            var this_sched = this_.attr('data-sched');


            swal({
                title: "Are you sure?",
                text: "Delete all assigned GDLB to this schedule ("+this_sched+")",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'mrd/delreadschedall',
                        type: 'post',
                        data: {'id': this_id, 'sched': this_sched, 'month': this_month, 'year': this_year},
                        dataType: 'json'
                    }).done(function (d) {
                        if (d.qry==true) {
                            if(d.delcnt>0) {
                                for(di=0; di <= d.delcnt; di++) {
                                    console.log(d.delids[di] );
                                    $('ul li#' + d.delids[di] , this_td).remove();
                                }
                            }
                            swal.close();
                        } else {
                            swal("Delete",'SQL Error', "error");
                        }
                    });
                }else{
                    swal.close();
                }
            });
        });

        $('.date-picker').datepicker({
            // rtl: PECO.isRTL(),
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd'
        });
        // CHANGE THIS TO FUNCTION THAT WILL REVIEW EACH FORM INSIDE ROWS
        $('#tbl_reading_hist').on('blur', 'tr #readamt, tr #readstat', function (e) {
            e.preventDefault();
            var input = $(this);
            PECO.row_validation(input);
        });

        tbl_reading_entry.on('click', '#btn_specific_reader', function(e){
            e.preventDefault();
            var this_ = $(this);
            var acct_id = this_.attr('data-acctid');

            $('#edit_reader_spec').find('#acctid').val(acct_id);
            $.ajax({
                url: PECO.base_url() + 'mrd/getacctreaders',
                type: 'post',
                data: {'acctid': acct_id},
                dataType: 'json'
            }).done(function(d){
                console.log(d);
                if(d.qry==true) {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, d.users);
                }else {
                    PECO.usersSelectTagging($("#meter_reader_input"), false, false);
                }
            }).fail(function(){
                PECO.phpError();
            });

        });

        $('#frm_assign_specific_reader').submit(function(e){
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d){
                PECO.initAlerts(d.msg, d.title, d.func);
                if(d.qry==true) {
                    $('#edit_reader_spec').modal('toggle');
                }
            }).fail(function(){
                PECO.phpError();
            });
        });

        PECO.usersSelectTagging($("#emp_input"), false, false);
        /*
        $("#emp_input").select2({
                //url: base_url+"admin/sample_select2",
                tags: true,
                triggerChange: true,
                allowClear: true,
                maximumSelectionLength: 3,
                ajax: {
                    url: PECO.base_url() + "admin/select2getusers",
                    type: 'post',
                    dataType: 'json',
                    quietMillis: 100,
                    data: function (term) {
                        return {
                            term: term
                        };
                    },
                    results: function (data) {
                        return {
                            results: $.map(data.list, function (item) {
                                return {
                                    text: item.text,
                                    id: item.id
                                };

                            })

                        };

                    }


                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                escapeMarkup: function (markup) {
                    return markup;
                }, // let our custom formatter work
                formatResult: PECO.formatData, // omitted for brevity, see the source of this page
                formatSelection: PECO.formatDataSelection, // omitted for brevity, see the source of this page
            }).change(function () {
                // ADD AJAX UPDATE IF APPLICABLE //
                console.log('TYPE: ' + $(this).val());
            });
        */

        $("#district").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: PECO.base_url() + "admin/selectdistrict",
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
            console.log('TYPE: ' + $(this).val());
            var this_val = $(this).val();
            if (this_val != '') {

                $("#lot_book").select2({
                    //url: base_url+"admin/sample_select2",
                    tags: true,
                    triggerChange: true,
                    allowClear: true,
                    maximumSelectionLength: 3,
                    ajax: {
                        url: PECO.base_url() + "mrd/getgdlb",
                        type: 'post',
                        dataType: 'json',
                        quietMillis: 100,
                        data: function (term) {
                            return {
                                term: this_val
                            };
                        },
                        results: function (data) {
                            var myResults = [];
                            $.each(data.list, function (index, item) {
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
                    console.log('TYPE: ' + $(this).val());
                });

                $('#lot_book').select2("enable", true).select2("val", '');
            } else {
                $('#lot_book').select2("false", true).select2("val", '');
            }
        });



        init_assign_form();

        tbl_reading_entry.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
        });
    };

    var init_print_reading_sheet_btn = function() {
        $(document).on('click', '#btn_print_cust_list', function(e){
            e.preventDefault();
            var this_ = $(this);
            var schedid = this_.attr('data-id');
            var userid = this_.attr('data-user');
            print_gdlb_select(this_, schedid, userid);
        });
    };

    function init_mrd_reports() {
        var modal_select_district = $('#modal_select_district', document);
        PECO.select2Basic($('#schedid'), 'mrd/getgdlbsched', 'GDLB..', true, true);
        PECO.select2Basic(modal_select_district, 'mrd/getgdlbdist', 'Select District..', true, true, false);
        // init_gdlb_table_modal();
        PECO.DTDefault(tbl_assign_gdlb, 'Select District first!');
        modal_select_district.change(function(e) {
            var this_ = $(this);
            if(this_.val() != '') {
                init_gdlb_table_modal();
            }else{
                PECO.DTDefault(tbl_assign_gdlb, 'Select District first!');
            }
        });

        tbl_assign_gdlb.on('click', 'tr', function(){
            var this_tr = $(this).closest('tr');
            if(this_tr.hasClass('checked') == false) {
                var checkBoxes = this_tr.find('input.checkbox');
                checkBoxes.iCheck('toggle');
                $('td', this_tr).toggleClass('info');
            }
        });


        $(document).on('submit', '#frm_assign_sched', function (e) {
            e.preventDefault();
            var form = $(this);
            var modal = $('#modal_ajax', document);
            swal({
                title: "Assign?",
                text: "Please confirm assigning schedule.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json',
                    }).done(function (d) {

                        swal("Assigning", 'Assigning Completed!', "success");
                        modal.modal('hide');
                        $(d.cell, document).html(d.html);

                        var orig_fcol_height = $('.DTFC_LeftBodyLiner', document).height();
                        var new_fcol_height = Number(orig_fcol_height) + Number($(d.cell, document).height());
                        $('.zui-sticky-col .' + d.empid).each(function () {
                            $(this).css('height', $(d.cell, document).height() + 'px');
                            $(this).animate({
                                height: $(d.cell, document).height() + 'px',
                            }, 500);
                            $(this).closest('tr').css('height', $(d.cell, document).height() + 'px');
                        });

                        $('.DTFC_LeftBodyLiner')
                            .css('height', new_fcol_height + 'px')
                            .css('max-height', new_fcol_height + 'px');

                        $('.DTFC_LeftBodyWrapper')
                            .css('height', new_fcol_height + 'px')
                            .css('max-height', new_fcol_height + 'px');


                        PECO.dataTableScroller();


                    }).fail(function () {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
            e.stopImmediatePropagation();
        });
    };


    var init_assign_form = function() {

        var frm_scheduler = $('#frm_assign_sched', document);
        frm_scheduler.submit(function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function (d) {
                console.log(d);
                //init_gdlb_table();
                PECO.initAlerts(d.msg, 'Assign Schedule', d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var init_outsource = function() {

        $('#showall').select2({
            placeholder: 'Select...',
            allowClear: true
        });

        PECO.DTDefault(dt_analysis, 'Get Analyze');

        $(dt_analysis).on('keypress', '#reading', function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            if(code === 13) {
                $('#input_findings', this_tr).focus();
            }
        });

        $(document).on('submit', '#frm_upload_extfile', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                dataType: "json",
                data: new FormData(form[0]),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    dt_analysis.dataTable().empty();
                    PECO.DTphpLoading(dt_analysis, "Uploading file, and analyzing...");
                }
            }).done(function (d) {
                init_tbl_extanalysis(d.list);
                PECO.dataTableScroller();
            }).fail(function(){
                swal("Error404: PHP", "Server side error!", "error");
            });
        });

        init_btn_expand();
        init_btn_delete_pic();
        init_btn_print_analysis();
        init_btn_print_account();
        init_reading_entry_keyboard();
    };

    var init_btn_print_account = function() {

        $(document).on('click', '#btn_acct_print', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_html = this_.html();
            var servno = this_.attr('data-servno');
            var mtr = this_.attr('data-mtr');

            $.ajax({
                url: PECO.base_url() + 'ar/getbilling',
                type: 'post',
                dataType: 'json',
                data: {'servno': servno, 'mtr': mtr, 'limit': 12, 'year': 2018},
                beforeSend: function() {
                    this_.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Wait..');
                }
            }).done(function (d) {
                this_.html(this_html);
                var html = '';

                //first row
                html += '<div class="row">';
                html += '<div class="col-md-6 col-xs-6">';
                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Name</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.name+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Address</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.address+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 25%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Status</span>';
                html += '<span style="width: 75%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.status+'</span>';
                html += '</li>';
                html += '</ul>';


                html += '</div>';

                html += '<div class="col-md-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">GDLB</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.gdlb+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Rate</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.rate+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">MULT</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default">'+d.mult+'</span>';
                html += '</li>';
                html += '</ul>';

                html += '</div>';

                html += '<div class="col-md-2 col-xs-2" style="postion: relative !important;">';
                html += '<img height="height: 70px;" style="" src="' + PECO.base_url() + 'query/barcode/' + d.servno + '" />';
                html += '</div>';


                html += '</div>';

                //-----------------------------//

                html += '<div class="row" style="border-top:solid 1px gray;">';

                html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name" >Total Balance</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtbal+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Total Interest</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtint+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Due</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtdue+'</span>';
                html += '</li>';
                html += '</ul>';
                html += '</div>';

                html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Total Amount Paid</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtpaid+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Last Pay Date</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.lastpay+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 60%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Current</span>';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important; padding-right: 20px; text-align: right !important" class="label-default" >'+d.amtcur+'</span>';
                html += '</li>';
                html += '</ul>';

                html += '</div>';

                html += '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';

                html += '<ul class="list-group summary column no-border list-group-xs">';
                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Average KWH</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.kwhave+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">Meter No.</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.mtrno+'</span>';
                html += '</li>';

                html += '<li class="list-group-item">';
                html += '<span style="width: 40%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-name">No. of Bills</span>';
                html += '<span style="width: 50%; display: inline-block; position: relative; margin: 0px 0px !important; padding: 0px 0px !important;" class="label-default" >'+d.nobills+'</span>';
                html += '</li>';
                html += '</ul>';

                html += '</div>';

                html += '</div>';

                //--------------------------------

                //table start
                html += '<div class="row" style="margin-top: 10px !important; border-top: 1px solid lightslategray">';
                html += '<div class="col-md-12">';

                html += '<table class="table table-bordered table condensed tbl-xs">';

                html += '<thead>';

                html += '<tr>';

                html += '<th rowspan="2">Month</th>';
                html += '<th rowspan="2">Year</th>';
                html += '<th rowspan="2">KWH</th>';
                html += '<th rowspan="2">Bill No.</th>';
                html += '<th rowspan="2">Amount Due</th>';
                html += '<th rowspan="2">Interest</th>';
                html += '<th rowspan="2">Amount Paid</th>';
                html += '<th rowspan="2">Due Date</th>';
                html += '<th rowspan="2">Date Paid</th>';
                html += '<th rowspan="2">Balance</th>';
                html += '<th colspan="5">Referrals</th>';

                html += '</tr>';

                html += '<tr>';

                html += '<th>C</th>';
                html += '<th>R</th>';
                html += '<th>PN</th>';
                html += '<th>U</th>';
                html += '<th>J</th>';

                html += '</tr>';

                html += '</thead>';

                html += '<tbody>';


                for(var index = 0;index < d.list.length; index++){
                    html += '<tr>';
                    html += '<td>'+d.list[index].month+'</td>';
                    html += '<td>'+d.list[index].year+'</td>';
                    html += '<td align="right">'+d.list[index].kwh+'</td>';
                    html += '<td>'+d.list[index].billno+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].current+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].interest+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].amtpaid+'</td>';
                    html += '<td>'+d.list[index].duedate+'</td>';
                    html += '<td>'+d.list[index].datepaid+'</td>';
                    html += '<td align="right" style="text-align: right !important;">'+d.list[index].balance+'</td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '<td></td>';
                    html += '</tr>';
                }


                html += '</tbody>';


                html += '</table>';

                html += '</div><footer></footer>';
                html += '</div>';

                PECO.pecoRepPrint("Statement of Account" , html);
            });
        });
    };

    var init_tbl_extanalysis = function(data) {
        var cust_num = 0;
        dt_analysis.dataTable().empty();
        dt_analysis.dataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            //scrollY: '300px',
            aaData: data,
            aoColumns: [
                {"data": "expand", sClass: ''},
                {"data": 'servno', sClass: 'text-primary text-bold'},
                {"data": 'name', sClass: '', sWidth: '300px'},
                {"data": 'mtr'},
                {"data": 'mtrno', sClass: 'mtrno'},
                {"data": 'mtrser', sClass: 'text-info'},
                {"data": 'mult', sClass: 'text-danger'},
                {"data": 'prsrdg', sClass: 'number text-success'},
                {"data": 'prvrdg', sClass: 'number'},
                {"data": 'prskwh', sClass: 'number text-success prskwh',
                    mRender: function(data) {
                        if(data==0) {
                            return '<span class="text-danger">' + data + '</span>';
                        }else{
                            return data;
                        }
                    }
                },
                {"data": 'prvkwh', sClass: 'number prvkwh',
                    mRender: function(data) {
                        if(data==0) {
                            return '<span class="text-danger">' + data + '</span>';
                        }else{
                            return data;
                        }
                    }
                },
                {"data": 'findings', sClass: 'input findings '},
                {"data": 'demand', sClass: 'number'},
                {"data": 'netmtr', sClass: 'number'},
                {"data": 'incdec', sClass: 'text-info', sWidth: '110px'},
                {"data": 'addbill', sClass: 'text-center addbill', sWidth: '5px', orderable: false, searchable: false},
                {"data": 'chckread', sClass: 'text-center chckread', sWidth: '5px', orderable: false, searchable: false},
                {"data": 'printed', sClass: 'text-center printed', sWidth: '5px', orderable: false, searchable: false},
                {"data": 'control', sClass: 'control'}
            ],
            fnRowCallback(nRow, aData, aIndex) {
                var schedid = aData.schedid;
                var acctid = aData.acctid;
                // init_findigns_editable(nRow, schedid, acctid, aIndex);
                $('.tooltips', nRow).each(function() {
                    $(this).tooltip();
                });

                PECO.popOverRow($('.popovers', nRow), true, true, 'popover-danger');

                $(nRow).find('.icheck').each(function () {
                    var this_ = $(this);
                    var this_td_class = this_.closest('td');
                    var check_color = 'grey';
                    if (this_td_class.hasClass('addbill')) {
                        check_color = 'yellow';
                    }
                    if (this_td_class.hasClass('chckread')) {
                        check_color = 'red';
                    }

                    PECO.iCheckRow(this_, 'flat', check_color);
                });

                init_findings_typeahead(nRow, schedid, acctid);
                cust_num += 1;
            },
            language: PECO.DTEmptyMessage('No records found!'),
        });

        $('#total_customer').text(cust_num);
    };


    var init_findings_typeahead = function(nRow, schedid, acctid) {
        var input_findings = $('#input_findings', nRow);
        var input_reading = $('#reading', nRow);
        var input_prevreading = $('#prevreading', nRow);

        input_findings.keypress(function(e) {
            var code = (e.keyCode ? e.keyCode : e.which);
            if(code == 13) {
                e.preventDefault();
            }
        });

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/mrdfindings?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), input_findings.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "codes",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media"><b class="text-glow-yellow">{{codes}}</b> - {{descs}}</div>'].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            event.preventDefault();
            var msg = '';
            msg += 'Schedid: ' + schedid + "<br>";
            msg += 'Acctid: ' + acctid + "<br>";
            msg += 'Presrdg: ' + input_reading.val() + "<br>";
            msg += 'Prevrdg: ' + input_prevreading.val() + "<br>";
            msg += 'Finding: ' + selection.sysid + "<br>";
            msg += 'Code: ' + input_findings.val() + "<br>";
            var mtrid = $('#mtrid', nRow).val();
            var demand = $('#demand', nRow).val();
            var data = {
                'acctid': acctid,
                'schedid': schedid,
                'mtrid': mtrid,
                'reading': input_reading.val(),
                'demand': demand,
                'findings': selection.sysid,
                'recheck': true
            };

            if(init_submit_reading_row_recheck_typehead($(nRow), data) == true) {
                // JUMP TO NEXT INDEX OF AN INPUT
                var index = $('input#input_findings').index(this) + 1;
                var this_input = $('input#reading').eq(index).focus();
                setTimeout(function () {
                    this_input.select();
                }, 100);
                tbl_reading_entry.find('tr.row-info').removeClass('row-info');
                this_input.closest('tr').addClass('row-info');
            }
        });
    };

    var init_encoding = function() {
        handler_person_search();

        PECO.DTDefault(tbl_reading_entry, 'Please select GDLB for reading assigned!');

        $('#get_mrd_list', document).click(function(e){
            e.preventDefault();
            var gdlbid = $('#reader_schedid', document).select2('val');
            var userid = $('#reader_id', document).val();
            var this_btn = $(this);
            init_reading_table(gdlbid, false, true, this_btn, userid);
        });



        $(document).on('click', '#get_mrd_mrseq', function(e) {
            e.preventDefault();
            var btn = $(this);
            var btn_html = btn.html();
            var gdlbid = $('#reader_schedid', document).select2('val');

            swal({
                title: "Are you sure?",
                text: "Update Meter Sequence from Legacy server",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'mrd/updatemetersequence',
                        type: 'post',
                        data: {gdlbid: gdlbid},
                        dataType: 'json',
                        beforeSend: function () {
                            PECO.btnLoading(btn, 'Updating all meter sequence...');
                        }
                    }).done(function (d) {
                        swal.close();
                        PECO.btnSuccess(btn, btn_html, 'btn-warning');
                    }).fail(function () {
                        swal.close();
                        PECO.btnErrorPHP(btn, btn_html, 'btn-warning');
                    });
                } else {
                    swal.close();
                }
            });
        });

        $(document).on('click', '#btn_import_to_legacy', function(e) {
            e.preventDefault();
            var schedid = $('#reader_schedid', document).select2('val');
            var userid = $('#reader_id', document).val();
            var btn = $(this);
            init_reading_import_legacy(schedid, userid, btn);
        });


        init_reading_submit();
        init_reading_entry_fn();
        init_reading_entry_keyboard();
    };

    var init_reading_import_legacy = function(schedid, userid, btn) {
        var btn_html = btn.html();
        $.ajax({
            url: PECO.base_url() + 'mrd/importreadingtolegacy',
            type: 'post',
            dataType: 'json',
            data: {'schedid': schedid, 'userid': userid,},
            beforeSend: function() {
                PECO.btnLoading(btn, 'Processing...');
            }
        }).done(function (d) {
            PECO.btnSuccess(btn, 'Done!', btn_html, 'btn-danger');
            PECO.initAlerts('Inserted: ' + d.inserted + ' / ' + d.numrows, 'PECO.net', 'info');
        });
    };



    var init_meter_tagging = function() {
        var tbl = $('#tbl_gdlb_tagging', document);

        $(document).on('click', '#btn_update_sequences', function(e) {
            e.preventDefault();
            var btn = $(this);
            var btn_html = btn.html();
            swal({
                title: "Are you sure?",
                text: "Update Meter Sequence from Legacy server",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'mrd/updatemetersequence',
                        type: 'post',
                        data: {},
                        dataType: 'json',
                        beforeSend: function() {
                            PECO.btnLoading(btn, 'Updating all meter sequence...');
                        }
                    }).done(function(d) {
                        swal.close();
                        PECO.btnSuccess(btn, btn_html, 'btn-primary');
                    }).fail(function() {
                        swal.close();
                        PECO.btnErrorPHP(btn, btn_html,'btn-primary');
                    });
                }else{
                    swal.close();
                }
            });

        });

        PECO.select2Basic($('#select2gdlb', document), 'query/select2gdlb', 'Select GDLB');
        PECO.DTDefault(tbl, 'Select GDLB First..');
        $('#get_gdlb_list').click(function(e){
            var gdlbid = $('#select2gdlb').select2('val');
            e.preventDefault();
            init_meter_tagging_table(gdlbid);
        });
        init_gdlb_list_keybaord(tbl);

        tbl.on('keyup', 'input#input_reader', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_val = this_.val();
            var this_reader_name = $('td.readername', this_tr);
            if(this_.val().length > 1) {
                $.ajax({
                    url: PECO.base_url() + 'mrd/queryreadercodeinfo',
                    type: 'post',
                    data: {'telcode': this_val},
                    dataType: 'json',
                }).done(function(d) {
                    if(d.qry==true) {
                        this_reader_name.html(d.name);
                    }else{
                        this_reader_name.html('');
                    }
                });
            }else{
                this_reader_name.html('');
            }
        });

        tbl.on('keypress', 'input', function(e) {

            var this_ = $(this);
            var this_row = this_.closest('tr');
            if (e.keyCode == 13) {
                e.preventDefault();
                var index = $('input').index(this) + 1;
                var this_input = $('input').eq(index).focus();
                var this_val = this_.val();
                if(save_mtr_tagging_row(this_) == true) {
                    // this_input.val(this_val); // COPY THE VALUE OF THE PREVIOUS INPUT
                    setTimeout(function () {
                        this_input.select();
                    }, 100);
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info row-danger');
                    this_input.closest('tr').addClass('row-info');
                } else {
                    tbl_reading_entry.find('tr.row-info').removeClass('row-info row-danger');
                    this_input.closest('tr').addClass('row-danger');
                }
            }
        });

        tbl.on('click', '#btn_clear', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_acctid = this_.attr('data-acctid');
            var this_userid = this_.attr('data-userid');
            $.ajax({
                url: PECO.base_url() + 'mrd/clearmtrtagging',
                type: 'post',
                data: {'acctid': this_acctid, 'userid': this_userid},
                dataType: 'json'
            }).done(function(d) {
                if(d.qry == true) {
                    this_row.find('.telcode input').val('');
                    this_row.find('.readername').text('');
                    this_.remove();
                }
            });
        });

        $(document).on('click', '#btn_update_legacy_gdlb', function (e) {

            var tbl_seq_tab_list = $('#tbl_seq_tab_list', document);
            e.preventDefault();
            var gdlbid = $('#legacy_gdlbid', document);
            var gdlbid_val = gdlbid.select2('val');

            $.ajax({
                url: PECO.base_url() + 'mrd/updatefromlegacyseqtab',
                type: 'post',
                data: {'gdlbid': gdlbid_val},
                dataType: 'json',
                beforeSend: function () {
                    PECO.DTphpLoading(tbl_seq_tab_list, 'Updating....');
                }
            }).done(function (d) {
                if (d.qry == true) {
                    tbl_meter_seqtab(tbl_seq_tab_list, gdlbid_val);
                }
            });
        });


        $(document).on('click', '#btn_get_legacy_gdlb', function (e) {

            var tbl_seq_tab_list = $('#tbl_seq_tab_list', document);
            e.preventDefault();
            var gdlbid = $('#legacy_gdlbid', document);
            var gdlbid_val = gdlbid.select2('val');
            tbl_meter_seqtab(tbl_seq_tab_list, gdlbid_val);
        });

    };

    var save_mtr_tagging_row = function(this_) {
        var res = false;
        var this_acctid = this_.attr('data-acctid');
        var this_telcode = this_.val();
        $.ajax({
            url: PECO.base_url() + 'mrd/savemtrtagging',
            type: 'post',
            data: {'acctid': this_acctid, 'telcode': this_telcode},
            dataType: 'json',
            async: false,
            cache: false,
        }).done(function(d) {
            if(d.qry==true) {
                res = true;
                this_.closest('tr').find('.controls').html(d.control);
            }
        });
        return res;
    };


    var handler_person_search = function(admin) {
        var admin_ = (admin) ? admin : false;
        var lastname = $('#reader_lastname', document);
        var readername = $('#reader_name', document);
        var sched_input = $('#reader_schedid', document);
        var userid = $('#reader_id', document);

        lastname.val('').trigger('change');
        sched_input.val('').trigger('change');

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/employeesearch?query=%QUERY&dept=14", wildcard: "%QUERY"}
        });

        a.initialize(), lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p><b>{{dept}}</b></p><p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            readername.text(selection.firstname + ' ' + selection.lastname);
            userid.val(selection.userid);
            sched_input.val('').trigger('change');
            init_reader_gdlb(selection.sysid, admin_);
            $('#gdlb_label', document).html(selection.telcode);

        }).on('keyup', function() {
            if($(this).val() == '') {
                sched_input.val('').trigger('change');
                $('#gdlb_label', document).html('READER');
            }
        });
    };


    var formatDataListBasic = function (data) {
        if (data.loading)
            return data.name;

        var markup;
        var text = data.text.split(' - ');
        var text2 = (text[1]) ? ' - ' + text[1] : '';
        var text3 = (text[2]) ? text[2] : '';
        if(text[2]) {
            markup = '<span class="select2-list"><i class="fa fa-circle-o text-info"></i> <b>' + text[0] + '</b> ' + text2 + ' <span class="pull-right label label-info">'+text3+'</span></span>';
        }else{
            markup = '<span class="select2-list"><i class="fa fa-circle-o text-info"></i> <b>' + text[0] + '</b> ' + text2 + '</span>';
        }
        return markup;
    };

    var formatDataSelectionBasic = function (data) {
        return '<i class="fa fa-check text-success"></i> ' + data.text.split(' - ', 1);
    };

    var init_reader_gdlb = function(readerid, admin) {
        var elem = $('#reader_schedid', document);
        $.ajax({
            url: PECO.base_url() + 'mrd/getnextreadergdlbsched',
            dataType: 'json',
            type: "POST",
            data:{"reader": readerid, 'admin': admin},
        }).done(function (d) {
            if(d.qry == true) {
                if  ($.fn.select2) {
                    elem.select2({
                        allowClear: true,
                        placeholder: 'Select...',
                        data: d.list,
                        formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
                        formatSelection: formatDataSelectionBasic, // omitted for brevity, see the source of this page
                        width: 'resolve', // 100% or resolve
                        // dropdownCssClass : 'select2-bigdrop',
                    });
                    PECO.select2_slimscroller();
                }
            }else{
                elem.select2({
                    allowClear: true,
                    placeholder: 'Select schedule..',
                    width: 'resolve'
                });
            }
        }).fail(function() {
            elem.select2({
                allowClear: true,
                placeholder: 'PHP Error',
            });
        });
    };

    function init_meter_seqtab() {
        var tbl = $(document).find('#modal_ajax #tbl_seq_tab_list');

        var gdlbid = $('#legacy_gdlbid', document);
        var gdlbid_val = gdlbid.select2('val');

        if (gdlbid_val > 0) {
            tbl_meter_seqtab(tbl, gdlbid_val);
        }
        
        PECO.select2Basic($('#legacy_gdlbid', document), 'query/select2gdlb', 'Select GDLB..');
    };

    function tbl_meter_seqtab(tbl, gdlbid) {
        $.ajax({
            url: PECO.base_url() + 'mrd/getlegacyseqtab',
            type: 'post',
            data: {'gdlbid': gdlbid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl, 'Loading data from legacy server...');
            }
        }).done(function(d){
            tbl.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list,
                scrollY: '400px',
                aoColumns: [
                    {"data": "servno", sClass: ''},
                    {"data": "mtr", sClass: ''},
                    {"data": "mtrno", sClass: ''},
                    {"data": "ref", sClass: ''},
                    {"data": "status", sClass: ''},
                ]
            });
        });
    };


    var init_data_fix = function() {
        PECO.select2Basic($('#reader_schedid'), 'mrd/getreadergdlbsched', 'GDLB..', true, false, false, true);
        PECO.select2Basic($('#reader_dist'), 'mrd/getgdlbdist', 'District..', true, true, false, false);
        PECO.select2Basic($('#reader_month'), 'systems/select2month', 'Month..', false, false, false, false, true);
        var tbl = $('#tbl_mrd_sched_records', document);
        PECO.DTDefault(tbl, 'Scan data first!');



        $(document).on('click', '#btn_scan_sched_reader_data', function(e) {
            e.preventDefault();
            var year = $('#reader_year', document).val();
            var month = $('#reader_month', document).val();
            var dist = $('#reader_dist', document).val();
            var data = {'year': year, 'month': month, 'dist': dist};
            init_mrd_sched_data(tbl, data, false, false);
        });

        /*

        $(document).on('click', '#btn_fix_sched_reader_data', function(e) {
            e.preventDefault();
            var year = $('#reader_year', document).val();
            var month = $('#reader_month', document).val();
            var dist = $('#reader_dist', document).val();
            var data = {'year': year, 'month': month, 'dist': dist};
            var this_ = $(this);
            var tbl = $('#tbl_mrd_sched_records', document);
            init_mrd_sched_data(tbl, data, this_, true);
        });
        */

        $(document).on('click', '#btn_fix_sched_reader_data', function(e) {
            e.preventDefault();
            var this_cnt = 0;
            $('a#btn_row_fix', tbl).each(function() {
                this_cnt += 1;
            });
            init_fix_loop(this_cnt, 0);
        });

    };


    var init_fix_loop = function(cnt, index) {
        var tbl = $('#tbl_mrd_sched_records', document);
        var this_btn = $('a#btn_row_fix', tbl).eq(index);
        var this_btn_html = this_btn.html();
        var this_tr = this_btn.closest('tr');

        var gdlbid = $('input#gdlbid', this_tr).val();
        var schedid = $('input#schedid', this_tr).val();
        var userid = $('input#userid', this_tr).val();
        var year = $('#reader_year', document).val();
        var month = $('#reader_month', document).val();

        $.ajax({
            url: PECO.base_url() + 'mrd/fixscheddatarow',
            type: 'post',
            data: {'index': index, 'gdlbid': gdlbid, 'schedid': schedid, 'userid': userid, 'year': year, 'month': month},
            dataType: 'json',
            beforeSend: function() {
                PECO.btnLoading(this_btn, 'Processing..');
            }
        }).done(function(d) {
            this_btn.closest('tr td').removeClass('danger');
            this_btn.removeClass('btn-danger').addClass('btn-success');
            PECO.btnSuccess(this_btn, ' ', this_btn_html, 'btn-success');
            $('td.status', this_tr).append(d.datacnt);
            if(d.indexn < cnt) {
                init_fix_loop(cnt, d.indexn);
            }
        });
    };


    var init_mrd_sched_data = function(tbl, data, btn, exec) {
        var is_exec = (exec) ? 1 : 0;
        if(is_exec == true) {
            var btn_html = btn.html();
        }
        $.ajax({
            url: PECO.base_url() + 'reports/getmrdscheddata',
            type: 'post',
            data: {'data': data, 'exec': is_exec},
            dataType: 'json',
            beforeSend: function() {
                if(is_exec==true) {
                    PECO.btnLoading(btn, 'Analizing data...');
                }else{
                    PECO.DTphpLoading(tbl, 'Scanning records...');
                }
            }
        }).done(function(d) {
            if(is_exec == true) {
                if (d.qry == true) {
                    PECO.btnSuccess(btn, 'Records fixed!', btn_html, 'btn-danger');
                    init_tbl_mrd_sched_data(tbl, d.list);
                } else {
                    PECO.DTDefault(tbl, 'No record(s) found, data is good!');
                }
            }else {
                if (d.qry == true) {
                    init_tbl_mrd_sched_data(tbl, d.list);
                }else{
                    PECO.DTDefault(tbl, 'No record(s) found, data is good!');
                }
            }
        }).fail(function() {
            if(is_exec == true) {
                PECO.btnErrorPHP(btn, btn_html, 'btn-danger');
            }else {
                PECO.DTphpError(tbl);
            }
        });
    };

    var init_tbl_mrd_sched_data = function(tbl, data) {
        tbl.DataTable({
            bDestroy: true,
            bPaginate: false,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            aaData: data,
            aoColumns: [
                {"data": "sysid", sClass: 'number', sWidth: '5%'},
                {"data": "gdlb", sClass: 'number', sWidth: ''},
                {"data": "gdlbstat", sClass: 'number', sWidth: ''},
                {"data": "assignments", sClass: '', sWidth: ''},
                {"data": "schedule", sClass: 'schedule'},
                {"data": "reading", sClass: 'reading number'},
                {"data": "status", sClass: 'status', sWidth: '40%'},
                {"data": "fix", sClass: 'controls', sWidth: ''}
            ],
            fnRowCallback: function (nRow, aData, aIndex) {
                $('td', nRow).each(function () {
                    $(this).addClass(aData.rowclass);
                });
            }
        });
    };


    return {
        reading: function () {
            init_reading();
        },
        encoding: function () {
            init_encoding();
        },
        analysis: function () {
            init_analysis();
        },
        addbillprocess: function () {
            init_addbill_process();
        },
        metertagging: function() {
            init_meter_tagging();
        },
        getlegacyseqtab: function() {
            init_meter_seqtab();
        },
        scheduler: function() {
            return init_reading_schedule();
        },
        mrdreports: function() {
            return init_mrd_reports();
        },
        gdlbcustomer: function(schedid, userid) {
            init_gdlb_selected(schedid, userid);
        },
        gdlbcustomerreader: function(schedid, userid) {
            init_gdlb_selected_reader(schedid, userid);
        },
        outsource: function() {
            init_outsource();
        },
        datafix: function() {
            init_data_fix();
        }
    };
}();


