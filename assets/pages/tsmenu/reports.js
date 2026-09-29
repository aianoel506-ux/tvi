var TSREP = function() {
    var tbl_summary = $('#tbl_summary', document);
    var init_reports = function() {
        tbl_init_summary();
    };

    var tbl_init_summary = function() {
        $.ajax({
            url: PECO.base_url() + 'ts/drawsummarytable',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_summary, 'Table summary is being drawn...');
            }
        }).done(function(d) {
            var tbl_head = tbl_summary.find('thead');
            if(d.qry==true) {
                tbl_head.html(d.heads);
                setTimeout(function(){
                    PECO.DTDefault(tbl_summary, 'No Records found!');
                },250);
            }
        });
    };

    return {
        init: function() {
            init_reports();
        }
    }
}();