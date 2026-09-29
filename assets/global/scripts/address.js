var ADDRESS = function() {
    var handler_select2_region = function() {
        PECO.select2Basic($('#select2_region', document), 'query/select2region', 'Select region', true, false, false);

        handler_select2_province();
        handler_select2_citymun();
        $('#select2_region', document).change(function() {
            handler_select2_province();
            handler_select2_citymun();
        });
        $('#select2_province', document).change(function() {
            handler_select2_citymun();
        });
    };

    var handler_select2_province = function() {
        var regcode = $('#select2_region', document).val();
        if(regcode > 0) {
            $.ajax({
                url: PECO.base_url() + 'query/select2province',
                dataType: 'json',
                type: "POST",
                data: {regionid: regcode}
            }).done(function (d) {
                if (typeof d.list != 'undefined' && d.list.length > 0) {
                    if  ($.fn.select2) {
                        $('#select2_province', document).select2({
                            allowClear: true,
                            placeholder: 'Select province...',
                            data: d.list,
                            formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
                            formatSelection: formatDataSelection, // omitted for brevity, see the source of this page
                            width: '100%', // 100% or resolve
                            //dropdownCssClass: bigdrop_,
                        });
                        PECO.select2_slimscroller();

                        $('#select2_province', document).attr('disabled', false).val('');
                    }
                }
            });
        } else {
            $('#select2_province', document).attr('disabled', true).val('');
            $('#select2_province', document).select2("destroy").trigger('change');
        }
    };

    var handler_select2_citymun = function() {
        var provcode = $('#select2_province', document).val();
        if(provcode > 0) {
            $.ajax({
                url: PECO.base_url() + 'query/select2citymun',
                dataType: 'json',
                type: "POST",
                data: {provid: provcode}
            }).done(function (d) {
                if (typeof d.list != 'undefined' && d.list.length > 0) {
                    if  ($.fn.select2) {
                        $('#select2_citymun', document).select2({
                            allowClear: true,
                            placeholder: 'Select province...',
                            data: d.list,
                            formatResult: formatDataListBasic, // omitted for brevity, see the source of this page
                            formatSelection: formatDataSelection, // omitted for brevity, see the source of this page
                            width: '100%', // 100% or resolve
                            //dropdownCssClass: bigdrop_,
                        });
                        PECO.select2_slimscroller();

                        $('#select2_citymun', document).attr('disabled', false).val('');
                    }
                }
            });
        } else {
            $('#select2_citymun', document).attr('disabled', true).val('');
            $('#select2_citymun', document).select2("destroy").trigger('change');
        }
    };


    var formatDataListBasic = function (data) {
        if (data.loading)
            return data.name;
        var text = data.text.split(' - ');
        var text2 = (text[1]) ? text[1] : '';
        var markup = '<span class="select2-list"><i class="fa fa-circle-o text-info"></i> <b>' + text[0] + '</b> ' + text2 + '</span>';
        return markup;
    };


    var formatDataSelection = function (data) {
        return data.text.split(',', 1);
    };


    return {
        init: function() {
            PECO.select2Basic($('#select2_country', document), 'query/select2country', 'Select country', true, false, 175);
            handler_select2_region();
        }
    }
}();