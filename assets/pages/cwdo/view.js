var VIEW = function() {
    // initialize plugins
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    PECO.getSweetAlert();
    PECO.getSelect2Plugins();


    var init_view_event = function() {


    };



    return {
        init: function() {
            init_view_event();
        }
    }
}();