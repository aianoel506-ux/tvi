var BILLING = function() {
    var handler_billing = function() {
        handler_tbl_billing();
    }
    var handler_tbl_billing = function() {
        $('#tbl_billing', document).DataTable();
    };
    return {
        init: function() {
            handler_billing();
        }
    }
}();