<?php
/**
 * Created by PhpStorm.
 * User: fader
 * Date: 3/28/2019
 * Time: 3:50 PM
 */

$qry_user_shortcut = $this->db->select(
    '
    ums.sysid,
    sn.sysid AS moduleid,
    sn.name, 
    sn.desc,
    sn.type,
    sn.hashcode,
    sn.pagefile,
    sn.url,
    sn.icon,
    sn.htmlclass
    '
)
    ->from('prime_system_users_module_shortcut AS ums')
    ->join('prime_module_navigations_main AS sn', 'sn.sysid = ums.moduleid')
    ->where(array('ums.status' => 1, 'ums.userid' => user_id()))
    ->get();
?>
<div class="page-content-wrapper cad-new-account ">
    <div class="page-content  animated fadeInUp fast" id="">
        <h3 style="margin-top: -30px;" class=" font-red-flamingo bold "><i class="fa fa-mouse-pointer"></i> User's Shortcut</h3>


        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="row" id="shortcuts_container"></div>
            </div>
        </div>
    </div>
</div>


<script>
    var USERSHORTCUTS = function() {
        var init_user_shortcut = function() {
            init_user_shortcut_container();
            init_user_events();
        };

        var init_user_shortcut_container = function() {
            $.ajax({
                url: PECO.base_url() + 'user/getusershortcut',
                type: 'post',
                data: {},
                dataType: 'json',
                beforeSend: function() {
                    $('#shortcuts_container', document).html('<div class="col-md-12"><h4><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading shortcuts...</h4></div>');
                }
            }).done(function(d) {
                $('#shortcuts_container', document).html(d.html);
            });
        };

        var init_user_events = function() {
            $(document).on('click', '#btn_remove', function(d) {
                d.preventDefault();
                var this_ = $(this);
                var this_id = this_.attr('data-id');
                $.ajax({
                   url: PECO.base_url() + 'user/delshortuct',
                   type: 'post',
                   data: {'moduleid': this_id},
                }).done(function(d) {
                    this_.closest('#shortcut_item').addClass('animated bounceOut');
                    setTimeout(function() {
                        init_user_shortcut_container();
                    }, 1000);
                });
            });
        };

        return {
            init: function() {
                init_user_shortcut();
            }
        }
    }();

    USERSHORTCUTS.init();
</script>