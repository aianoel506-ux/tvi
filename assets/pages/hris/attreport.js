var ATTENDANCEREPORT = function(){

    var init_report = function(){
        PECO.select2Basic($('#month' , document) , "systems/select2month" , 'Select Month' , false,false,false);
        PECO.select2Basic($('#year' , document) , "systems/select2year" , 'Select Year' , false,false,false);
        events();
    };

    var pecoRepPrint = function (reptitle, content) {
        // Open a new window for the printable table
        var win = window.open('', '');
        var head = '<title>' + reptitle + '</title>';
        win.document.title = reptitle;
        win.document.body.innerHTML =
            '<head>' +
            //'<title>'+reptitle+'</title>'+
            '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important;  font-family: arial; background: #fff;}</style>' +
            '</head>' +
            '<div style="position: absolute; left: 0px; width: 100%;">' + content + '</div>';
        setTimeout(function () {
            //   win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);

    };
    var events = function(){
        $(document).on('submit','#submitdtr',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (d) {
                pecoRepPrint("Attendance Records",d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit' , '#submitattlogs' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json'
            }).done(function (data) {
                $(document).find('#timelogshtml').html(data.html);
            }).fail(function () {
                PECO.phpError();
            });
        });
        $(document).on('submit','#submitdtrreport' , function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                type:this_.attr("method"),
                data:this_.serialize(),
                dataType:'json',
                beforeSend:function(){
                    $(document).find('#genloading').removeClass('hidden');
                }
            }).done(function (data) {
                printmonthlyattreport("Monthly Report" , data.html);
                $(document).find('#genloading').addClass('hidden');
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var printmonthlyattreport = function(reptitle , content){
        // Open a new window for the printable table
        var win = window.open('', '');
        var head = '<title>' + reptitle + '</title>';
        win.document.title = reptitle;
        win.document.body.innerHTML =
            '<head>' +
            //'<title>'+reptitle+'</title>'+
        '<link href="' + PECO.base_url() + 'assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/components.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>' +
            '<link href="' + PECO.base_url() + 'assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>' +
            '<style>body{margin: 0px 0px !important; font-family: arial; background: #fff;}</style>' +
            '</head>' +
            /*     '<img  style="display: inline-block; height: 80px; float: left; z-index: 2 !important; position: absolute; left: 0px;" src="' + PECO.base_url() + 'assets/global/img/PECO_LEFT_HEAD.png" /><img style="display: inline-block; height: 80px; width: 100%; position: absolute; top 0px; right: 0px; z-index: 0;" src="' + PECO.base_url() + 'assets/global/img/PECO_REP_HEAD.png" />' +
             '<h4 style="position: absolute; top: 50px; right: 0px; width: auto; text-align: right; padding-right: 10px">' + reptitle + '</h4>' +*/
            '<div style="position: absolute; top:0px; left: 0px; width: 100%;font-size: 10px !important;">' + content + '</div>';
        setTimeout(function () {
            //  win.print(); // blocking - so close will not
            //  win.close(); // execute until this is done
        }, 250);
    };

    return{
        init:function(){
            init_report();
        }
    }
}();