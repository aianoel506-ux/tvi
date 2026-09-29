var CWDO = function() {

    var init_plugins = function() {
        PECO.getSweetAlert();
        PECO.getSelect2Plugins();
        PECO.getHighlightsPlugin();
    };

    return {
        init: function() {
            init_plugins();
        }
    }
}();