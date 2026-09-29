/* 
 * Author: Lucky John Faderon
 * 04-05-2017
 * CRM Scripts
 */

var CRM = function () {
    PECO.getSelectPlugins();
    PECO.getDataTablePlugin();
    PECO.getHighlightsPlugin();
    
    var init_crm_stats = function () {
        PECO.map('#map', '#frm_filter_customers');
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href")
            if (target === '#tab_map') {
                PECO.map('#map', '#frm_filter_customers');
            }
            $('#view_title').html($(this).text());
        });
        var active_li = $("ul.nav-tabs li.active");
        var active_tx = active_li.find('a').text();
        $('#view_title').html(active_tx);
        
        $('#tbl_cust_list').dataTable();

    };
    return {
        stats: function () {
            return init_crm_stats();
        }
    }
}(jQuery);