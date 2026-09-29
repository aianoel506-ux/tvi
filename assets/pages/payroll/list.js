var LIST = function () {
    // INITIALIZE HIGHLIGHTS SEARCH IN TABLE
    PECO.getHighlightsPlugin();
    PECO.getNumberFormatPlugin();
    PECO.getiCheckPlugin();


    var payroll_tbl = $('#payroll_table');
    // CHANGE URL PARAMETER FOR SUB INFO

    var meter_reader_payroll = $('#meter_reader_payroll');
    var total_gdlbval = $('#total_gdlbval');
    var total_amountval = $('#total_amountval');

    var init_payroll_table = function(hashcode) {
        var payclass = $('#tabber', document).find('li.active a').attr('data-id');
        var ccid = $('#deptselect', document).val();
        
        // Convert payclass to integer for proper comparison
        payclass = parseInt(payclass);
        
        // Get paytype value
        var paytype = $('#select2paytype', document).val();
        
        // For tier1 (3077), tier2 (3078), and rank and file (128), use the selected paytype
        // For other payclasses, default to paytype 1
        if(payclass != 128 && payclass != 3077 && payclass != 3078) {
            paytype = 1;
        }

        $.ajax({
            url: PECO.base_url() + 'payroll/emplist',
            type: 'POST',
            dataType: 'json',
            data: {'modulehash': hashcode, 'class': payclass, 'dept': ccid, 'stat': 1, 'viewtype': 1, 'paytype': paytype},
        }).done(function (data) {
            $('#totalempnet').html(data.totalnet);
            $('#totaldeduction').html(data.totaldeduct);
            $('#count_male').html(data.malecnt);
            $('#count_female').html(data.femalecnt);
            $('#count_resign').html(data.resigncnt);

            payroll_tbl.dataTable().empty();
            payroll_tbl.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: false,
                bProcessing: true,
                aaData: data.list,
                aoColumns: [
                    {"data":"expand"},
                    {"data":"empid" , sClass:'', sWidth: ''},
                    {"data":"firstname" , sClass:'', sWidth: ''},
                    {"data":"lastname" , sClass:'', sWidth: ''},
                    {"data":"middlename" , sClass:'', sWidth: ''},
                    {"data":"department" , sClass:'', sWidth: ''},
                    {"data":"basic" , sClass:'', sWidth: ''},
                    {"data":"loans" , sClass:'', sWidth: ''},
                    {"data":"premiums" , sClass:'', sWidth: ''},
                    {"data":"tax" , sClass:'', sWidth: ''},
                    {"data":"deduct" , sClass:'', sWidth: ''},
                    {"data":"control" , sClass:'', sWidth: ''},
                    {"data":"netpay" , sClass:'', sWidth: ''}
                ],
                searchHighlight: true,
                fnRowCallback: function(nRow, data) {
                    PECO.dtExpandBtn(nRow, data.expand);
                    PECO.popOverRow($(nRow).find('.popovers'), true, true, 'popover-info');
                    PECO.iCheckRow($(nRow).find('input.icheck'), 'minimal', 'blue');
                }
            });

        });
    };
    var init_payroll_event = function (hashcode) {

        $('body').on('submit', '#frm_process_payroll', function (e)
        {
            e.preventDefault();
            var form = $(this);
            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                dataType: 'json',
                data: form.serialize(),
                success: function (res) {
                    console.log(res);
                    PECO.initAlerts(res.message, res.title, res.func)
                }
            });

        });
        //
        $('body').on('click', '#printpayslip', function (e)
        {
            e.preventDefault();
            $.ajax({
                url: PECO.base_url() + 'payroll/printpayslip',
                type: 'POST',
                dataType: 'json',
                data: {}
            }).done(function (data) {
                console.log(data);
            });
        });
        // add ajax submit form here
        $('body').on('submit', '#frm_insert_deduction_earnings', function (e) {
            e.preventDefault();
            var form = $(this);
            jQuery.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                dataType: 'json',
                data: form.serialize()
            }).done(function (d) {
                PECO.initAlerts(d.message, d.title, d.func);
                init_otherearninganddeducttable();
            });

        });
    };

    //get data from db
    var tbl_meter_reader_table = function(){
        $.ajax({
            url:PECO.base_url()+"payroll/fetchmeterreaderemp",
            type:"post",
            dataType:"json",
            beforeSend: function(){
                meter_reader_payroll.dataTable().empty();
                PECO.DTphpLoading(meter_reader_payroll, 'Loading... ');
            }
        }).done(function (d) {
            var prevamount = 0;
            prevamount = Number(d.totalamountsummary) + Number(d.totaldeduction);
            total_gdlbval.html(d.gdlbsum);
            $(document).find('#hiddenamountval').val(prevamount);
            $(document).find('#hiddentotal_deduction').val(d.totaldeduction);
            $(document).find('#hiddentotal_amountval').val(d.totalamountsummary);
            $(document).find('#amountval').text(prevamount).number(true, 2);
            $('#total_deduction').text(d.totaldeduction).number(true,2);
            total_amountval.text(d.overalltotal).number(true , 2);
            populate_meter_reader_table(d);
        });
    };

    //populate data to table
    var populate_meter_reader_table = function (data) {
        meter_reader_payroll.dataTable().empty();
        meter_reader_payroll.dataTable({
            bDestroy: true,
            bPaginate: true,
            bFilter: true,
            bInfo: true,
            bStateSave: true,
            bProcessing: true,
            aaData: data.meterreaderpayroll,
            aoColumns: [
                {"data":"num", sWidth: ''},
                {"data":"empid" , sClass: 'text-primary', sWidth: ''},
                {"data":"fullname", sWidth: '25%'},
                {"data":"gdlb"  , sClass: 'number gdlb', sWidth: ''},
                {"data":"regtotal", sClass: 'text-info number regtotal', sWidth: ''},
                {"data":"sptotal", sClass: 'text-info number sptotal', sWidth: ''},
                {"data":"regrate" ,  sClass: 'regrate', sWidth: ''},
                {"data":"sprate", sClass: 'sprate', sWidth: ''},
                {"data":"regdeduct", sClass: 'number text-danger regdeduct', sWidth: ''},
                {"data":"spdeduct", sClass: 'number text-danger spdeduct', sWidth: ''},
                {"data":"total", sClass: 'text-info number total' , sWidth: '20%'}
            ],
            searchHighlight: true,
            fnRowCallback: function(nRow, data) {
                if(data.done===true) {
                    $(nRow).find('td').addClass('success');
                }
            },
            fnDrawCallback: function() {
                var total_regreadings = 0;
                var total_spreadings = 0;
                var total_reading = 0;
                meter_reader_payroll.find('td.regtotal').each(function(){
                    total_regreadings += Number($(this).find('input').val());
                });
                meter_reader_payroll.find('td.sptotal').each(function(){
                    total_spreadings += Number($(this).find('input').val());
                });
                total_reading = total_regreadings + total_spreadings;
                $(document).find('#total_regval').text(total_regreadings).number(true, 2);
                $(document).find('#total_spval').text(total_spreadings).number(true, 2);
                $(document).find('#total_reading').text(total_reading).number(true , 2);
            }
        });
    };

    var init_mrd_payroll_compute = function() {
        meter_reader_payroll.on("keyup", "input", function (e) {
            e.preventDefault();
            var this_ = $(this);
            var overalltotal = 0;
            var this_tr = this_.closest('tr');
            var this_dataid_val = this_tr.find('#dataid').val();
            // regular and special column value
            var this_regular_val = this_tr.find('td.regtotal #reghiddenval').val();
            var this_special_val = this_tr.find('td.sptotal  #sphiddenval').val();
            // regular and special rate inputs
            var this_regular_valrates = this_tr.find('#regrateinputs').val();
            var this_special_valrates = this_tr.find('#sprateinputs').val();
            //deduction
            var this_totalregdeduct = this_tr.find('td.regdeduct #errorregularcount').val();
            var this_totalspdeduct = this_tr.find('td.spdeduct #errorspecialcount').val();
            var total_deduct = Number(this_totalregdeduct) + Number(this_totalspdeduct);
            var this_total = this_tr.find('td.total #totaltext');
            var this_total_input = this_tr.find('#totalinput');
            var total_amt_reg = Number(this_regular_valrates) * Number(this_regular_val);
            var total_amt_sp = Number(this_special_valrates) * Number(this_special_val);
           // if($('#regrateinputs').val() != ""){
                overalltotal = (Number(total_amt_reg) + Number(total_amt_sp)) - Number(total_deduct);
           // }
           this_total.text(overalltotal).number(true, 2);
           this_total_input.val(overalltotal);
        });
    };

    //initialize summary values
    var init_mrd_payroll_summary = function() {
        var total_amount = 0;
        var regdeduct = 0;
        var spdeduct = 0;
        var totaldeduction = 0;
         meter_reader_payroll.find('td.total').each(function () {
            total_amount += Number($(this).find('input').val());
         });
         meter_reader_payroll.find('td.regdeduct').each(function () {
             regdeduct += Number($(this).find('input').val());
         });
         meter_reader_payroll.find('td.spdeduct').each(function () {
             spdeduct += Number($(this).find('input').val());
         });
        totaldeduction = Number(regdeduct) + Number(spdeduct);
        $(document).find('#amount').text(total_amount).number(true, 2);
        total_amount = Number(total_amount) - Number(totaldeduction);
        $(document).find('#total_deduction').text(totaldeduction).number(true, 2);
        $(document).find('#total_amountval').text(total_amount).number(true, 2);
    };

    //update entered value when enter key hit
    var update_rate_totalamt = function(this_){

        var this_tr = this_.closest('tr');
        var this_dataid_val = this_tr.find('#dataid').val();
        var this_regrate_val_inputs = this_tr.find('#regrateinputs').val();
        var this_sprate_val_inputs = this_tr.find('#sprateinputs').val();
        var this_regdeduct_val_inputs = this_tr.find('#errorregularcount').val();
        var this_spdeduct_val_inputs = this_tr.find('#errorspecialcount').val();
        var this_reg_val = this_tr.find('#reghiddenval').val();
        var this_sp_val = this_tr.find("#sphiddenval").val();

        $.ajax({
            url: PECO.base_url()+ 'payroll/insertpayrolllogs',
            type:"post",
            dataType:"json",
            data:{"logsid":this_dataid_val,"regrateinput":this_regrate_val_inputs , "sprateinput":this_sprate_val_inputs,"regval":this_reg_val,"spval":this_sp_val , "regdeduct":this_regdeduct_val_inputs , "spdeduct":this_spdeduct_val_inputs }
        }).done(function(d){
           if(d.qry === true){
                this_tr.find('td').removeClass('info danger').addClass('success');
            }else{
                this_tr.find('td').removeClass('info success').addClass('danger');
            }

           $(document).find('#amountval').text(d.totalamount).number(true , 2);
           $(document).find('#total_deduction').text(d.totaldeduct).number(true , 2);
           $(document).find('#total_amountval').text(d.overalltotal).number(true , 2);
           $(document).find('#hiddenamountval').val(d.totalamount);
           $(document).find('#hiddentotal_deduction').val(d.totaldeduct);
           $(document).find('#hiddentotal_amountval').val(d.overalltotal);
        });
    };

    var init_payroll = function (hashcode) {

        $('#month').select2({
            "allowClear": true,
            "placeholder": 'Select Year'
        });
        $('#day').select2({
            "allowClear": true,
            "placeholder": 'Select Days'
        });

        init_payroll_event(hashcode);
       //PECO.dtSubDetails(table, 'payroll/payrollinfo');
        //initialize table
        tbl_meter_reader_table();
        //key press automatic compute
        init_mrd_payroll_compute();
        //for enter command update
        meter_reader_payroll.on('keydown', 'input', function (e) {
            var this_ = $(this);
            var key = (e.keyCode ? e.keyCode : e.which);

            if(e.keyCode==13){

                var this_index = this_.closest('td.regdeduct').index();
                var next_tr = this_.closest('tr').next();
                var next_input = next_tr.find('td.regrate').eq(this_index).find('input');
                next_input.focus();
                update_rate_totalamt(this_);
            }
            if(e.keyCode==40){
                var this_index = this_.closest('td').index();
                var next_tr = this_.closest('tr').next();
                var next_input = next_tr.find('td').eq(this_index).find('input');
                next_input.focus();
            }
            if(e.keyCode==38){
                var this_index = this_.closest('td').index();
                var next_tr = this_.closest('tr').prev();
                var next_input = next_tr.find('td').eq(this_index).find('input');
                next_input.focus();
            }
        });

        $('#processpayrollbtn').click(function (e) {
            e.preventDefault();
        });

        PECO.select2Basic($('#deptselect'), 'hris/select2dept', 'Select Department...', true);
        PECO.select2Basic($('#select2month'), 'systems/select2month', 'Select Month...');

        init_payroll_table(hashcode);

        $(document).on('change', '#detpselect', function(e){
            init_payroll_table(hashcode);
        });

        $(document).on('click', '#tabber li', function(e){
            // Mark clicked tab as active
            $('#tabber li').removeClass('active');
            $(this).addClass('active');
            
            // Get the payclass from the clicked tab
            var selectedPayclass = parseInt($(this).find('a').attr('data-id'));
            
            // Enable/disable paytype dropdown based on selected payclass
            if(selectedPayclass == 128 || selectedPayclass == 3077 || selectedPayclass == 3078) {
                $('#select2paytype').attr('disabled', false);
            } else {
                $('#select2paytype').attr('disabled', true).val('1');
            }
            
            init_payroll_table(hashcode);
        });
        
        // Handle paytype dropdown change
        $(document).on('change', '#select2paytype', function(e){
            init_payroll_table(hashcode);
        });
    };
    return {
        init: function (hashcode) {
            init_payroll(hashcode);
        }
    };
    // add ajax to add additional and earnings and deduction here
// end ajax to add additional and earnings and deduction here
}();