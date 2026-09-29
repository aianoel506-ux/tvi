var MRDREP = function() {

    var init_reports = function() {
        $('.select2', document).each(function() {
            $(this).select2({'placeholder': 'Select Month..'});
        });
        $('.date-picker').datepicker({
            // rtl: PECO.isRTL(),
            orientation: "left",
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

    };

    var init_reading_reports = function() {
        var btn = $('#btn_get_reports', document);

        // init_reading_reports_tbl(btn);

        $(document).on('click', '#btn_get_reports', function(e) {
            e.preventDefault();
            var btn = $(this);
            init_reading_reports_tbl(btn);
        });

        $(document).on('click', '#btn_print_report', function(e) {
            e.preventDefault();
            var btn = $(this);
            init_reading_reports_tbl(btn, true, "Generating reports..");
        });

        $(document).on('click', '#btn_excel_report', function(e) {
            e.preventDefault();
            var btn = $(this);
            init_reading_reports_excel(btn, "Writing reports excel..");
        });
    };

    var init_reading_reports_excel = function(btn, msg) {
        var btn_html = btn.html();
        var dateend = $('#schedule_date_end', document).val();
        var datestart = $('#schedule_date_start', document).val();
        var billyr = $('#schedule_billyr', document).val();
        var billmo = $('#schedule_billmo', document).val();
        var ismsg = (msg) ? msg : 'Getting data..';

        PECO.btnLoading(btn, ismsg);
        window.location = PECO.base_url() + 'reports/getreadingreportsexcel/'+datestart+'/'+dateend+'/'+billmo+'/' +billyr;
        PECO.btnSuccess(btn,'Data loaded!', btn_html, 'btn-success');
    };

    var init_reading_reports_tbl = function(btn, print, msg) {
        var btn_html = btn.html();
        var tbl_tbody = $('#tbl_reading_reports tbody', document);
        var dateend = $('#schedule_date_end', document).val();
        var datestart = $('#schedule_date_start', document).val();
        var billyr = $('#schedule_billyr', document).val();
        var billmo = $('#schedule_billmo', document).val();
        var types = $('#schedule_types', document).val();
        var isprint = (print) ? true : false;
        var ismsg = (msg) ? msg : 'Getting data..';

        $.ajax({
            url: PECO.base_url() + 'reports/getreadingreports',
            type: 'post',
            data: {
                'dateend': dateend,
                'datestart': datestart,
                'billmo': billmo,
                'billyr': billyr,
                'types': types
            },
            dataType: 'json',
            beforeSend: function() {
                PECO.btnLoading(btn, ismsg);
            }
        }).done(function(d) {
            PECO.btnSuccess(btn,'Data loaded!', btn_html, 'btn-primary');
            if(isprint) {
                var content='';
                content += d.header;
                content += '<div style="width: 40%; display:inline-block; margin-bottom: 5px;">Reading Date Range: <b>'+d.daterange+'</b></div>';
                content += '<div style="width: 30%; display:inline-block; margin-bottom: 5px; text-align: right; float: right;">'+d.datenow+'</div>';
                content += '<hr style="border: 1px dashed #333; margin: 0px 0px;">';
                content += '<table class="table table-condensed tbl-sm print-table-standard" id="reading_reports_table">';
                content += '<thead>';
                content += '<th width="200px">READER</th>';
                content += '<th>DATE</th>';
                content += '<th>GDLB</th>';
                content += '<th class="number">READ</th>';
                content += '<th class="number">UNREAD</th>';
                content += '<th class="number">RECHECK</th>';
                content += '<th class="number">TOTAL</th>';
                content += '</thead>';
                content += '<tbody>';
                content += d.html;
                content += '</tbody>';
                content += '</table>';
                content += d.footer;
                PECO.pecoRepPrint('', content, false);
            }else {
                tbl_tbody.html(d.html);
            }
        }).fail(function() {
            PECO.phpError();
            PECO.btnErrorPHP(btn,'Error PHP', btn_html, 'btn-primary');
        });
    };

    return {
        init: function() {
            init_reports();
        },
        reading: function() {
            init_reading_reports();
        }
    }
}();