var CUSTOMER = function() {

    var init_plugins = function() {
        PECO.getNumberFormatPlugin();
        PECO.getHighlightsPlugin();
    };

    var init_services = function() {

    };

    return {
        services: function() {
            init_services();
        }
    }

}();