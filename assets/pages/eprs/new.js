$(function () {
    //TODO Duplicate with bos.js replaceBackslashes function
    var replaceBackslashes = function(k){
        return k.replace(/\\/g, "");
    };
    //TODO Duplicate with bos.js loadCostCenterSelect function
    var loadCostCenterSelect = function(){
        $.ajax({
            url: PECO.base_url() + 'purchasing/getCostCenters',
            datatype: 'json'
        }).done(function (d) {
            var selectOptions = replaceBackslashes(d);
            $('#cost_centers').select2().append(selectOptions);
        }).fail(function () {
            PECO.initAlerts('loadCostCenterSelect ajax failed', 'Information', 'warning');
        });
    };
    loadCostCenterSelect();
    var loadUnitSelect = function(){
        $.ajax({
            url: PECO.base_url() + 'purchasing/getUnit',
            datatype: 'json'
        }).done(function (d) {
            var selectOptions = replaceBackslashes(d);
            $('.item_unit_class').select2().append(selectOptions);
        }).fail(function () {
            PECO.initAlerts('loadUnitSelect ajax failed', 'Information', 'warning');
        });
    };
    loadUnitSelect();
    var loadAccountCodeSelect = function(){
        $.ajax({
            url: PECO.base_url() + 'purchasing/getAccountCodes',
            datatype: 'json'
        }).done(function (d) {
            var selectOptions = replaceBackslashes(d);
            $('.account_code_class').select2().append(selectOptions);
        }).fail(function () {
            PECO.initAlerts('loadAccountCodeSelect ajax failed', 'Information', 'warning');
        });
    };
    loadAccountCodeSelect();
    
    $("#exttemp").select2({placeholder: 'Select template..'});

    $("#ccids").select2().change(function () {
        compute_total();
    });
    $("#reqtype").select2().change(function () {
        var this_val = $(this).val();
        if (this_val == 2) {
            $('#capex_table').removeClass('hidden').find('.table').dataTable();
            $('#opex_table').addClass('hidden');
            $("#budget").attr('disabled', true).select2({placeholder: 'N/A'});
        } else {
            $("#budget").attr('disabled', false).select2({placeholder: 'N/A'});
            $('#capex_table').addClass('hidden');
            $('#opex_table').removeClass('hidden').find('.table').dataTable();
        }
    });
    $("#budget").select2().change(function () {
        compute_total();
    });
    $("#qrter").select2().change(function () {
        compute_total();
    });


    setInterval(function () {
        $('#qry-stat').toggleClass('hidden');
    }, 5000);


    $('#templatebtn').click(function (e) {
        $(this).closest('.form-group').toggleClass('has-success');
        $('#exttemp').toggleAttr('disabled');
    });

    $('#budgetamt').keyup(function () {
        var this_val = $(this).val();
        if (this_val > 0) {
            $("#terms").attr('disabled', false);
            $("#terms").select2({placeholder: "Select.."});
            compute_total();
            console.log(this_cc);
        } else {
            compute_total();
            $("#terms").val('').attr('disabled', true);
            $("#terms").select2({placeholder: "Add Budget Amount first!"});
        }
    });
    $("#terms").select2({placeholder: "Add Budget Amount first!"}).change(function () {
        compute_total();
        console.log(this_length);
    });

    function compute_total() {
        var cc_length = $("#ccids").select2('val').length;
        var qtr_length = $("#qrter").select2('val').length;
        var total_budget_bal = (Number(10200) * Number(qtr_length)) * cc_length;
        $('#budgetbal').text(total_budget_bal.toFixed(2)).digits();
    }
    $.fn.digits = function () {
        return this.each(function () {
            $(this).text($(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
        });
    };

    $.fn.toggleAttr = function (attr) {
        return this.each(function () {
            var $this = $(this);
            $this.attr(attr) ? $this.removeAttr(attr) : $this.attr(attr, attr);
        });
    };

});
