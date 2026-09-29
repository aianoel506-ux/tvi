<?php
/**
 * Created by PhpStorm.
 * User: ITD-SE
 * Date: 11/13/2018
 * Time: 10:32 AM
 */

?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-bubble font-hide hide"></i>
            <span class="caption-subject font-hide bold uppercase">Chats</span>
        </div>
        <div class="actions">
            <div class="portlet-input input-inline">
                <div class="input-icon right">
                    <i class="icon-magnifier"></i>
                    <input type="text" class="form-control input-circle" placeholder="search..."> </div>
            </div>
        </div>
    </div>
    <div class="portlet-body" id="chats">
        <div class="scroller" style="height: 400px;" data-always-visible="1" data-rail-visible1="1">
            <ul class="chats">
                <li class="out">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar2.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Lisa Wong </a>
                        <span class="datetime"> at 20:11 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="out">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar2.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Lisa Wong </a>
                        <span class="datetime"> at 20:11 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="in">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar1.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Bob Nilson </a>
                        <span class="datetime"> at 20:30 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="in">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar1.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Bob Nilson </a>
                        <span class="datetime"> at 20:30 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="out">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar3.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Richard Doe </a>
                        <span class="datetime"> at 20:33 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="in">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar3.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Richard Doe </a>
                        <span class="datetime"> at 20:35 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="out">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar1.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Bob Nilson </a>
                        <span class="datetime"> at 20:40 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="in">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar3.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Richard Doe </a>
                        <span class="datetime"> at 20:40 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. </span>
                    </div>
                </li>
                <li class="out">
                    <img class="avatar" alt="" src="<?php echo base_url(); ?>assets/layouts/layout/img/avatar1.jpg" />
                    <div class="message">
                        <span class="arrow"> </span>
                        <a href="javascript:;" class="name"> Bob Nilson </a>
                        <span class="datetime"> at 20:54 </span>
                        <span class="body"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. sed diam nonummy nibh euismod tincidunt ut laoreet.
                                                            </span>
                    </div>
                </li>
            </ul>
        </div>
        <div class="chat-form">
            <div class="input-cont">
                <input class="form-control" type="text" placeholder="Type a message here..." />
            </div>
            <div class="btn-cont">
                <span class="arrow"> </span>
                <a href="" class="btn blue icn-only">
                    <i class="fa fa-check icon-white"></i>
                </a>
            </div>
        </div>
    </div>
</div>


<script>
    var itemContainer = $(".scroller", document);
    itemContainer.slimScroll({
        allowPageScroll: true, // allow page scroll when the element scroll is ended
        size: '7px',
        color: ($(this).attr("data-handle-color")  ? $(this).attr("data-handle-color") : '#bbb'),
        railColor: ($(this).attr("data-rail-color")  ? $(this).attr("data-rail-color") : '#eaeaea'),
        position: 'right',
        height: '400px',
        railVisible: true,
        disableFadeOut: true,
        alwaysVisible: true
    });
</script>