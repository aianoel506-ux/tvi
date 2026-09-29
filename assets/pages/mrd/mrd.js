var MRD = function () {
    var formatDataSelection = function (route) {

        if (!route.id) {
            return route.text;
        }
        var $route = $('<span><i class="fa fa-check text-success"></i> ' + route.text.split('/', 1) + '</span>');
        return $route
    };

    var formatState = function (route) {
        var text_arr = route.text.split('/');
        if (!route.id) {
            return route.text;
        }
        var $route = $(
                '<p><b>' + text_arr[0] + '</b>  <span class="pull-right label label-danger">' + text_arr[1] + '</span></p>'
                );
        return $route;
    };

    var init_gdlb_select = function () {
        $('#get_gdlb_list').select2({
            placeholder: 'Select..',
            allowClear: true,
            formatResult: formatState,
            formatSelection: formatDataSelection
        });
        PECO.select2_scroller();
        
        $('#manualassign').submit(function(e){
            e.preventDefault();
            var form = $(this); 
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'json'
            }).done(function(d){
                PECO.initAlerts(d.msg, 'GDLB Assigned', d.func);
                $('#gdlbname').html(d.gdlbname);
            }).fail(function(){
                PECO.phpError();
            });
        });
		
		
    };
	var init_near_meter_bak = function(dataid) {
		var near_mtr_init = {};
		$.ajax({
			url: PECO.base_url()+'query/getcustnearmtr',
			type: 'post',
			data: {'dataid': dataid},
			dataType: 'json',
		}).done(function(d){
			if(d.qry==true) {
				var near_mtr_init = d.options;
				PECO.meterSelectTagging($("#near_meters"), false, near_mtr_init);
				$('#near_meters').attr('disabled', true);
				$('#savenearmtrbtn').addClass('disabled');
				$('#near_meter_msg').html(d.gdlb);
			}else{
				PECO.meterSelectTagging($("#near_meters"), false, false);
			}
		});
	};

	var init_near_meter = function(dataid) {
	    var tbl_near_meter_list = $('#tbl_near_meter_list',document);
		var near_mtr_init = {};
		$.ajax({
			url: PECO.base_url()+'mrd/customernearmtr',
			type: 'post',
			data: {'dataid': dataid},
			dataType: 'json',
		}).done(function(d){
            tbl_near_meter_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                pageLength: false,
                aaData: d.info,
                "searchHighlight": true,
                aoColumns: [
                    {"data": "num", sClass: 'number', sWidth: '10%'},
                    {"data": "mtrno", sClass: 'text-info text-align-center', sWidth: '25%'},
                    {"data": "srvno", sClass: 'text-danger', sWidth: '25%'},
                    {"data": "address", sWidth: '40%'},
                ],
                language: PECO.DTEmptyMessage('Please return to Inspection to assign Near Meters.'),
                fnRowCallback: function(nRow, aData, index) {

                },
            });
		}).fail(function() {
            PECO.DTphpError(tbl_near_meter_list, '<h4 class="text-danger"><i class="fa fa-times"></i> Error PHP</h4>');
        });
	};

	var init_select_gdlb = function(elem, val) {
	    var district_id = $('#district_id',document).val();
	    var input_lnb_all = $('#input_lnb_all',document);
	    var data = (input_lnb_all.attr('checked')) ? false : district_id;
        PECO.select2Basic(elem, 'query/select2gdlb', 'GDLB Select..', true, false, val,false,false,data);

        input_lnb_all.on('change',function () {
            var this_ = $(this);
            var checked = this_.attr('checked');
            if (checked) {
                PECO.select2Basic(elem, 'query/select2gdlb', 'GDLB Select..', true, false, val,false,false,false);
            } else {
                PECO.select2Basic(elem, 'query/select2gdlb', 'GDLB Select..', true, false, val,false,false,district_id);
            }
        });

        init_gdlb_details(val);
        elem.change(function(){
            var this_ = $(this);
            var this_val = this_.select2('val');
            init_gdlb_details(this_val);
        });

        $('#changegdlb').click(function(){
            var this_ = $(this);
            var app_id = this_.attr('app-id');
            var gdlb_id  = elem.select2('val');
            var input_servno = $('#input_servno',document);
            $.ajax({
                url: PECO.base_url() + 'cad/updategdlb',
                type: 'post',
                data: {'appid': app_id, 'gdlbid': gdlb_id},
                dataType: 'json',
            }).done(function(d){
                PECO.initAlerts(d.msg, 'Update GDLB', d.func);
                if(d.qry==true) {
                    init_near_meter(app_id);
                    input_servno.text(d.servno);
                }
            }).fail(function(){
                PECO.phpError();
            });
        });
    };

	var init_gdlb_details  = function(id) {
	    $.ajax({
            url: PECO.base_url() + 'query/getgdlbdetails',
            type: 'post',
            data: {'gdlbid': id},
            dataType: 'json'
        }).done(function(d) {
            $('#gdlbdist').html(d.district);
            $('#gdlblimit').html(d.limit);
            $('#gdlbcust').html(d.cust);
        }).fail(function(){
            PECO.phpError();
        });
    };

	var init_reading_schedule = function() {
		$('.date-picker').datepicker({
			// rtl: PECO.isRTL(),
			orientation: "left",
			autoclose: true,
			format: 'yyyy-mm-dd'
		});
	};

	var init_findings_main = function() {
	    var tbl_findings_list = $('#tbl_findings_list', document);

        init_tbl_findings_list(tbl_findings_list);

        PECO.select2Basic($('#select2department', document), 'hris/select2department', 'Select department..', true, false);

        $('#btn_refresh_list', document).click(function(e) {
            e.preventDefault();
            init_tbl_findings_list(tbl_findings_list);
        });

        tbl_findings_list.on('click', '#icheck_input', function(e) {
            var input = $(this);
            var input_check = input.is(':checked');
            var input_val = input.val();
            $.ajax({
                url: PECO.base_url() + 'mrd/updfindingsrecheck',
                data: {'id': input_val, 'checked': input_check},
                type: 'post',
                dataType: 'json',
            }).done(function(d) {
                return true;
            });
        });

        $('#select2recheck', document).select2({
            'placeholder': 'Select recheck print..',
            "allowClear": true
        });

        $(document).on('submit', '#frm_add_reading_findings', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: form.serialize(),
                beforeSend: function() {

                }
            }).done(function(d){
                init_tbl_findings_list(tbl_findings_list);
            }).fail(function() {
                PECO.phpError();
            });
        });

        tbl_findings_list.on('click', '#btn_delete', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            var this_tr = this_.closest('tr');
            $.ajax({
                url: PECO.base_url() + 'mrd/deletefindingsmain',
                type: 'post',
                data: {'id': this_id},
                dataType: 'json',
            }).done(function(d) {
                if(d.qry==true) {
                    this_tr.fadeOut('fast');
                }else{
                    PECO.initAlerts(d.msg, 'MRD Menu', 'warning');
                }
            }).fail(function() {
                PECO.phpError();
            });
        });
    };





	var init_tbl_findings_list = function(tbl) {
	     $.ajax({
             url: PECO.base_url() + 'mrd/getfindingslist',
             data: {},
             type: 'post',
             dataType: 'json',
             beforeSend: function() {
                 PECO.DTphpLoading(tbl, 'Loading findings...');
             }
         }).done(function(d) {
             tbl.DataTable({
                 bDestroy: true,
                 bPaginate: false,
                 bFilter: true,
                 bInfo: true,
                 bStateSave: true,
                 scrollY: '350px',
                 pageLength: false,
                 aaData: d.list,
                 "searchHighlight": true,
                 aoColumns: [
                     {"data": "sysid", sClass: 'number', sWidth: '20px'},
                     {"data": "codes", sClass: 'number', sWidth: '20px'},
                     {"data": "descs", sClass: 'number', sWidth: '20px'},
                     {"data": "deptid", sClass: 'number', sWidth: '20px'},
                     {"data": "isrecheck", sClass: 'number', sWidth: '20px'},
                     {"data": "status", sClass: 'number', sWidth: '20px'},
                     {"data": "controls", sClass: 'number', sWidth: '20px'},
                 ],
                 language: PECO.DTEmptyMessage('No findings yet!'),
                 fnRowCallback: function(nRow, aData, index) {

                 }
             });
         }).fail(function() {
             PECO.DTphpError(tbl, '<h4 class="text-danger"><i class="fa fa-times"></i> Error PHP</h4>');
         });
    };

    return {

        init: function () {
            init_gdlb_select();
        },

		nearmtr: function(dataid) {
			init_near_meter(dataid);
		},

        selectgdlb: function(elem, val) {
            init_select_gdlb(elem, val);
        },

        findingsmain: function() {
            init_findings_main();
        }
		
    };
}();