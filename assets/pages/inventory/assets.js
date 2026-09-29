var ASSETS = function() {
    PECO.getHighlightsPlugin();
    PECO.getSelect2Plugins();
    var assets_tbl = $('#tbl_assets');

    var init_assets_fn = function() {
        //auto select meter asset type
        $(document).find('#meter_no_input').removeClass('hidden');
        $(document).find('#assettype').hide();
        //  alert($(document).find('#assettype').val());

        var originid = $(document).find('#moduleid').val();
        var dataid = $(document).find('#dataid').attr('data-id');

        //check if already tagged
           $.ajax({
            url:base_url + "assets/checkuseriftagged",
            method:"post",
            data:{"originid" : originid  ,  "dataid":dataid},
            dataType:'json'
            }).done(function (d) {
                if(d.tagged == true){
                    $(document).find('#assetcode').text(d.mtrserial);
                    $(document).find('#brand').text(d.brand);
                    $(document).find('#amp').text(d.amps);
                    $(document).find('#volts').text(d.volts);
                    $(document).find('#desc').text(d.desc);
                    $(document).find('#assetid').val(d.assetid);

                    $(document).find('#tag').attr("disabled", true);
                    $(document).find('#untag').attr("disabled", false);

                }else{
                    $(document).find('#tag').attr("disabled", false);
                    $(document).find('#untag').attr("disabled", true);
                }
            }).fail(function () {
                alert("Failed checking tagged");
            });

        $(document).on('submit','#save_new_asset', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action"),
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:"json"
            }).done(function (d) {
                PECO.initAlerts(d.msg , "Item" , d.func , 6000);
                $(document).find('#assettype').select2("val","");
                $(document).find('#meterno').val('');
                $(document).find('#meteramps').val('');
                $(document).find('#metervolts').val('');
                $(document).find('#newbrand').select2("val","");
                $(document).find('#serial').val('');
                $(document).find('#description').val('');
                init_assets_tbl(1);
            });
        });
        $(document).on('click' ,'#cancelpopover' , function () {
            $(document).find('.popovers').popover('hide');
        });
        $(document).on('click' , '.md-radiobtn' , function () {
           $(document).find('.popovers').popover('hide');
           $('.nav-tabs a[href="#details"]').trigger('click');
           var this_ = $(this);
           var id = this_.attr("data-id");
           $(document).find('#hiddenid').val(id); ///
            $(document).find('#assetid').val(id);
           $.ajax({
               url: base_url+'assets/getitemdetails',
               method:"post",
               data:{"id":id},
               dataType:"json"
           }).done(function (d) {
               $(document).find('#assetcode').text(d.assetcode);
               $(document).find('#brand').text(d.brand);
               $(document).find('#amp').text(d.amp);
               $(document).find('#volts').text(d.volts);
               $(document).find('#desc').text(d.descriptions);

           }).fail(function () {
               PECO.phpError();
           });
        });
        $(document).on('click','#tag' , function (e) {
            e.preventDefault();
            var id = $(document).find('#hiddenid').val(); ////
            var originid = $(document).find('#moduleid').val();
            var assetcode = $(document).find('#assetcode').text();
            var dataid = $(document).find('#dataid').attr('data-id');

                if(assetcode === 'N/A'){
                    PECO.initAlerts("Please select available asset to tag" , "Items" , "info" , 6000);
                }else{
                    $.SmartMessageBox({
                        title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to tag this asset?</span>",
                        content: 'Please confirm action taken!',
                        buttons: '[Yes][No]'
                    }, function (ButtonPressed) {
                        if (ButtonPressed === "Yes") {
                            $.ajax({
                                url:base_url+"assets/updatetagitem",
                                method:"post",
                                data:{"id":id, "originid":originid,"dataid":dataid},
                                dataType:"json"
                            }).done(function (d){
                                PECO.initAlerts(d.msg , "Tag" , d.func , 6000);
                                /*  setTimeout(function () {
                                    assets_tbl.find('tr').find('input[name=assetid]:checked').closest('tr').fadeOut('fast');
                                },500); */
                                init_assets_tbl(1);
                                $(document).find('#tag').attr("disabled", true);
                                $(document).find('#untag').attr("disabled", false);
                            });
                        }
                    });
                }
        });
        $(document).on('click' , '#untag' , function (e) {
            e.preventDefault();
            var originid = $(document).find('#moduleid').val();
            var dataid = $(document).find('#dataid').attr('data-id');
            var assetid = $(document).find('#assetid').val();
            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i>Are you sure you want to untag this asset?</span>",
                content: 'Please confirm action taken!',
                buttons: '[Yes][No]'
            }, function (ButtonPressed) {
                if (ButtonPressed === "Yes"){
                    $.ajax({
                        url:base_url+"assets/untagasset",
                        method:"post",
                        data:{"assetid":assetid , "originid":originid,"dataid":dataid},
                        dataType:'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg , "Tag" , d.func , 6000);
                        // $(document).find('#tag').attr("disabled", true);
                        init_assets_tbl(1);
                        $(document).find('#tag').attr("disabled", false);
                        $(document).find('#untag').attr("disabled", true);
                        $(document).find('#assetcode').text("N/A");
                        $(document).find('#brand').text("N/A");
                        $(document).find('#amp').text("N/A");
                        $(document).find('#volts').text("N/A");
                        $(document).find('#desc').text("N/A");
                    });
                }
            });
        });
        $(document).on('click' , '#renew' , function (e) {
            e.preventDefault();
            var assetid = $(document).find('#assetid').val();
        });
        PECO.DTDefault(assets_tbl, 'No Assets Record Found!');
        init_assets_tbl(1);
        PECO.dtSubDetails(assets_tbl, 'assets/getassetdetails');
        init_select2_brand();
        init_select2_assettype();
        $(document).find('#btn_add_brand').popover({'html': true});
        $(document).find('.asset-table-tab').on('click', 'a', function(){
            var this_val = $(this).attr('data-val');
            if(this_val == 2){
                $(document).find('#assetcode').text("N/A");
                $(document).find('#brand').text("N/A");
                $(document).find('#amp').text("N/A");
                $(document).find('#volts').text("N/A");
                $(document).find('#desc').text("N/A");
            }
            init_assets_tbl(this_val);
        });
        $(document).find('#assettype').select2().on('change', function(){
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val==320) {
                $(document).find('#meter_no_input').removeClass('hidden');
            }else{
                $(document).find('#meter_no_input').addClass('hidden');
            }
        });
        $(document).on('submit','#frm_add_brand', function(e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url:this_.attr("action") ,
                method:this_.attr("method"),
                data:this_.serialize(),
                dataType:"json"
            }).done(function (d) {
                PECO.initAlerts(d.msg , "Add Brand" ,d.func , 6000);
                if(d.qry==true) {
                    init_select2_brand();
                    setTimeout(function () {
                        $(document).find('.popovers').popover('hide');
                    }, 2000);
                }
            }).fail(function () {
                PECO.initAlerts("There was an error adding the brand", "Error" ,"error",6000);
            });
        });
    };
    var init_select2_brand = function() {
        PECO.select2Basic($(document).find('#newbrand'), 'query/getselect2brand', 'Select Brand..', false, false);
    };
    var init_select2_assettype = function() {
        PECO.select2Basic($(document).find('#assettype'), 'admin/get_types/ASSET', 'Select Asset Type..', false, false, 320);
        $('#assettype').on('change', function(){
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val == 320) {
                $(document).find('#meter_no_input').removeClass('hidden');
            }else{
                $(document).find('#meter_no_input').addClass('hidden');
            }
        });
    };
    var init_assets_tbl = function(stats) {
        var dataid = $(document).find('#dataid').attr('data-id');
        $.ajax({
            url: PECO.base_url() + 'assets/getassettbl',
            type: 'post',
            data: {'stats': stats , "dataid":dataid},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(assets_tbl, 'Loading assets lists..');
            }
        }).done(function(d){
            assets_tbl.dataTable().empty();
            var dt = assets_tbl.dataTable({
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i>  No assets record found!</h4>'
                },
                aoColumns: [
                    {"data": "expand", sWidth: '20px', sClass: ''},
                    {"data": "codes", sWidth: '', sClass: ''},
                    {"data": "descs", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        });

    };

    return {
        init: function() {
            init_assets_fn();
        }
    }
}();