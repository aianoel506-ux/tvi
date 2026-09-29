<link href="<?php echo base_url(); ?>assets/admin/pages/css/timeline.css" rel="stylesheet" type="text/css"/>
<style>
    .timeline-body {
        padding: 10px !important;
    }
    .timeline .timeline-badge {
        height: 60px;
        width: 60px;
    }
</style>

<div class="row">
    <div class="col-md-6">
        <?php
            customer_application_basicinfo($dataid);
        ?>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-comments"></i> <span class="caption-subject font-green-sharp bold uppercase">Comments</span> <span class="caption-helper"></span> </div>
                <div class="actions">
                    <a id="btn_comment_refresh" class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                        <i class="icon-refresh"></i>
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <form class="form-horizontal" id="frm_submit_comment" action="<?php echo base_url(); ?>query/subtmitrncomment" method="post">
                    <input type="hidden" value="<?php echo $trnid; ?>" name="trnid"/>
                    <input type="hidden" value="<?php echo $trailid; ?>" name="trailid"/>
                    <div class="input-group input-icon left">
                        <i class="fa fa-comment"></i>
                        <input name="remarks" class="form-control" rows="6" placeholder="Write a comments"/>
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default">Post</button>
                        </span>
                    </div>
                </form>
                <hr>
                <div class="timeline" id="comment_list"></div>

                <hr>
                <button type="button" class="btn btn-default">View more..</button>
            </div>
        </div>
    </div>
</div>


<!--
    <div class="timeline-item">
        <div class="timeline-badge">
            <img class="timeline-badge-userpic" src="<?php echo base_url(); ?>uploads/images/users/lucky-john-faderon/lucky-default.png">
        </div>
        <div class="timeline-body">
            <div class="timeline-body-arrow">
            </div>
            <div class="timeline-body-head">
                <div class="timeline-body-head-caption">
                    <a href="javascript:;" class="timeline-body-title font-blue-madison">Lucky John F. Faderon</a>
                    <span class="timeline-body-time font-grey-cascade">Comment at 3:45 PM</span>
                </div>
                <div class="timeline-body-head-actions">
                </div>
            </div>
            <div class="timeline-body-content">
                <span class="font-grey-cascade">
                I send back the process to customer's application department because the requirement(s) are not all complied.
                </span>
            </div>
        </div>
    </div>
-->

<script>
    var COMMENTS = function() {
        var comment_list = $('#comment_list', document);
        var fn_comments = function(trnid) {

            int_comment_list(trnid);

            comment_list.on('click', '#btn_delete_comment', function(e) {
               var this_ = $(this);
               var this_list = this_.closest('.timeline-item');
               var this_id = this_.attr('data-id');
               var conf = confirm('Are you sure, you want to delete this comment?');
               if(conf == true) {
                   $.ajax({
                       url: PECO.base_url() + 'query/deletetrncomment',
                       type: 'post',
                       data: {id: this_id},
                       dataType: 'json',
                   }).done(function (d) {
                       this_list.addClass('animated fadeOutRight fast');
                       setTimeout(function () {
                           this_list.remove();
                           if($('.timeline-item', comment_list).length == 0) {
                               $('#nav_comments_cnt', document).text('');
                               comment_list.html('<h3 id="list_no_comment" style="margin-left: 40px;"><i class="fa fa-warning text-warning"></i> No comment found!</h3>');
                           }
                       }, 500);

                   }).fail(function () {
                       PECO.phpError();
                   });
               }
            });

            $(document).on('click', '#btn_comment_refresh', function(e) {
                e.preventDefault();
                int_comment_list(trnid);
            });


            $(document).on('submit', '#frm_submit_comment', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    dataType: 'json',
                }).done(function(d) {
                    $('#list_no_comment', comment_list).remove();
                    comment_list.prepend(handler_comment(d));
                }).fail(function() {
                    PECO.phpError();
                });
                return false;
            });

        };

        var int_comment_list = function(trnid) {
            $.ajax({
                url: PECO.base_url() + 'query/gettrncomments',
                type: 'post',
                data: {trnid: trnid},
                dataType: 'json',
                beforeSend: function() {
                    comment_list.html('<h3 style="margin-left: 40px"><i class="text-info fa fa-spinner fa-spin fa-pulse"></i> Loading comments...</h3>');
                }
            }).done(function(d) {
                if(typeof d.list !== 'undefined' && d.list.length > 0) {
                    comment_list.html('');
                    for(var i = 0; i<d.list.length; i++) {
                        comment_list.append(handler_comment(d.list[i]));
                    }
                } else {
                    comment_list.html('<h3 id="list_no_comment" style="margin-left: 40px;"><i class="fa fa-warning text-warning"></i> No comment found!</h3>');
                }
            });
        };

        var handler_comment = function(data) {
            var html = '';
            html += '<div class="timeline-item animated fadeInDown fast">';
            html += '<div class="timeline-badge">';
            html += '<img alt="PECO" class="timeline-badge-userpic" src="'+data.pic+'">';
            html += '</div>';
            html += '<div class="timeline-body">';
            if(data.del == true) {
                html += '<a href="javascript:;" id="btn_delete_comment" data-id="' + data.id + '" class="text-danger pull-right"><i class="fa fa-times"></i></a>';
            }
            html += '<div class="timeline-body-arrow">';
            html += '</div>';
            html += '<div class="timeline-body-head">';
            html += '<div class="timeline-body-head-caption">';
            html += '<a href="javascript:;" class="timeline-body-title font-blue-madison">'+data.name+'</a>';
            html += '<span class="timeline-body-time font-grey-cascade">'+data.date+'</span>';
            html += '</div>';
            html += '<div class="timeline-body-head-actions">';
            html += '</div>';
            html += '</div>';
            html += '<div class="timeline-body-content">';
            html += '<span class="font-grey-cascade">' + data.message + '</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            return html;
        };

        return {
            init: function(trnid) {
                fn_comments(trnid);
            }
        }
    }();

    COMMENTS.init(<?php echo $trnid; ?>);

</script>