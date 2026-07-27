<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_lt:header}-->

<link rel="stylesheet" href="source/plugin/xigua_hb/static/dist/cropper.css?{VERHASH}">
<style>.weui-search-bar__cancel-btn{font-size:.8rem}.weui-search-bar {background-color: #f8f8f8;}
.search_bar_btn{margin-left:.5rem;line-height:1.4rem;white-space:nowrap;display:none;font-size:.8rem}
.weui-search-bar.weui-search-bar_focusing .search_bar_btn{display:block}
.weui-search-bar__box {background: url(source/plugin/xigua_hb/static/img/s.png) .5rem center no-repeat;background-size: .8rem;}
.me-talk .content-talk{border:$_c6 solid 1px; background:$_c6 }<!--{if $dftcolor2}-->.me-talk .content-talk{color:$dftcolor2}<!--{/if}-->
.me-talk .content-talk::before{border-top:$_c6 solid 1px; border-right:$_c6 solid 1px; background:$_c6 }
.consult_cell{width: 100%;height: auto;display:block}.position li, .position1 li{background:$_c6}
.chat___goodsCard{width:100%;box-sizing:border-box;padding:.65rem;margin:0 auto;background:#fff;border-radius:.5rem;display:-webkit-flex;display:flex;-webkit-align-items:flex-start;align-items:flex-start;position:relative}.taro_img img{width:100%;height:100%}.taro_img{display:inline-block;overflow:hidden;position:relative;font-size:0;-webkit-flex-shrink:0;flex-shrink:0;width:4rem;height:4rem;border-radius:.25rem}.chat___goodsInfo___2DueR{margin-left:.5rem;width:calc(100% - 7.5rem)}.chat___name___10ZSG{font-size:.7rem;color:#222;line-height:1rem;display:-webkit-box;-webkit-line-clamp:2;overflow:hidden;word-break:break-all;max-height:2rem}.chat___price___2AE1P{margin-top:.3rem;font-size:.7rem;color:#fe0036;line-height:1rem}.chat___hosName___jk80L{font-size: .65rem;color: #777;line-height: 1rem;margin-top: .3rem;max-height: 2rem;overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 2;word-break: break-all;}
.chat___reserveBtn___2Fq9t{width: 3rem;height:1.2rem;background:$_c6;border-radius: 1rem;font-size: .6rem;color: #000000;line-height: 1.2rem;text-align: center;position: absolute;color:#fff;right: .75rem;top: 2rem;}</style>
<!--{eval $no_header_fix=1;}-->
    <!--{template xigua_hb:common_nav}-->
<div class="page__bd" style="margin-top:35px;">
    <a href="$SCRITPTNAME?id=xigua_hj" style="margin-top:55px;text-align: center; font-size: .6rem; width: 100%; display: block; color: #999;">
  
        <i class="color-red2 iconfont icon-jubao f12"></i> &#22914;&#36935;&#26080;&#25928;&#12289;&#34394;&#20551;&#12289;&#35784;&#39575;&#20449;&#24687;&#65292;&#35831;<em class="color-red2">&#28857;&#27492;&#20030;&#25253;&#65281;</em></a>
    <div id="chat_widget">
        <div class="widget-list">
            <ul class="talk-msg" id="list1">
            </ul>
        </div>
    </div>
    <div class="none">
        <!--{template xigua_hb:loading}-->
    </div>
    <div class="fix-bottom in_bottom" style="padding:.5rem .75rem 0;z-index:502">
        <div class="weui-cells mt0 before_none after_none" style="margin-bottom:.5rem">
            <div class="weui-cell p0">
                <div class="weui-cell__hd"><a href="{eval echo $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:'javascript:window.history.go(-1);'}"><i class="iconfont icon-fanhuijiantou f24 c3 bold mr0"></i></a></div>
                <div class="weui-cell__bd">
                <form action="" method="post" onsubmit="do_send_sx('', 1);return false;">
                    <input enterkeyhint="send" class="weui-input" id="commentinput" type="text" name="mesasgae" placeholder="{lang xigua_hb:sendplc}" value="">
                </form>
                    <img class="emotion_chat emotion_chat_a1" src="source/plugin/xigua_lt/static/img/a1.png"  >
                    <img class="emotion_chat emotion_chat_a2" src="source/plugin/xigua_lt/static/img/a2.png"  >
                </div>
                <div class="weui-cell__ft">
                    <input type="button" style="opacity:.6"  class="weui-btn weui-btn_primary weui-btn_mini" name="smsdosubmit" id="smsdosubmit" value="{lang xigua_hb:send}">
                </div>
            </div>
        </div>
        <div class="indexDmatk cl" style="display:none">
            <!--{if in_array('img', $opens)}-->
            <div class="indexDmatk_item">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/photo-new.png">
                        <div id="box_avatar">
                            <!--{if HB_INWECHAT && $config[multiupload]}-->
                            <a class="weui-uploader__input" data-name="form[avatar]" data-boxer="box_avatar" data-only="1" data-multi="0" type="file"></a>
                            <!--{else}-->
                            <input class="weui-uploader__input" data-name="form[avatar]" data-boxer="box_avatar" data-only="1" data-multi="0" type="file" accept="image/*">
                            <!--{/if}-->
                        </div>
                    </div>
                </div>
                <div class="indexDmatk_item_text">&#22270;&#29255;</div>
                <ul class="weui-uploader__files none" data-only="1" data-max="1" data-maxtip=""></ul>
            </div>
            <!--{/if}-->
            <!--{if in_array('video', $opens) && $lt_config[maxvideo]>0}-->
            <div class="indexDmatk_item">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/take-photo-new.png">
                        <input class="weui-uploader__input_video" data-name="form[video]" type="file" accept="video/*">
                    </div>
                </div>
                <div class="indexDmatk_item_text">&#35270;&#39057;</div>
            </div>
            <!--{/if}-->
            <!--{if in_array('red', $opens)}-->
            <div class="indexDmatk_item redclick">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/hb.png">
                    </div>
                </div>
                <div class="indexDmatk_item_text">{lang xigua_lt:hb}</div>
            </div>
            <!--{/if}-->
            <!--{if in_array('order', $opens)}-->
            <div class="indexDmatk_item orderclick">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/dd.png">
                    </div>
                </div>
                <div class="indexDmatk_item_text">{lang xigua_lt:dd}</div>
            </div>
            <!--{/if}-->
            <!--{if in_array('mp3', $opens)}-->
            <div class="indexDmatk_item recclick">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/yy.png">
                    </div>
                </div>
                <div class="indexDmatk_item_text">{lang xigua_lt:mp3}</div>
            </div>
            <!--{/if}-->
            <!--{if in_array('gift', $opens)}-->
            <div class="indexDmatk_item giftclick">
                <div class="indexDmatk_item_block">
                    <div class="indexDmatk_item_img">
                        <img src="source/plugin/xigua_lt/static/img/lw.png?123">
                    </div>
                </div>
                <div class="indexDmatk_item_text">{lang xigua_lt:gift}</div>
            </div>
            <!--{/if}-->
        </div>
        <!--{template xigua_lt:face}-->
    </div>
    <div id="popctrl" class="weui-popup__container" style="z-index:1001">
        <div class="weui-popup__modal">
            <div style="height: 100vh"><img id="photo"></div>
            <div class="pub_funcbar">
                <a class="weui-btn close-popup weui-btn_primary" data-method="confirm">{lang xigua_hb:queding}</a>
                <a class="weui-btn close-popup weui-btn_default" data-method="destroy">{lang xigua_hb:quxiao}</a>
            </div>
        </div>
    </div>
    <div class="bottom_fix heigt8"></div>
</div>

<!--{template xigua_lt:red}-->
<!--{template xigua_lt:order}-->
<!--{template xigua_lt:gift}-->
<!--{template xigua_lt:recorder}-->

<!--{template xigua_lt:enter_up}-->
<!--{template xigua_lt:footer}-->
<script>
    var loadingurl1 = '$SCRITPTNAME?id=xigua_lt&ac=chat&do=chat_li&inajax=1&touid=$touid&page=';
    scrollto = 1;
    setInterval(function(){
        if($('#commentinput').val()){
            $('#smsdosubmit').css('opacity', '1');
        }else{
            $('#smsdosubmit').css('opacity', '.8');
        }
    }, 500);

    $(document).on('click','.emotion_chat_a1', function () {
        $('.emotion_panel').hide();
        $(this).toggleClass('deg45');
        $('.indexDmatk').toggle();
    });
    var swipinit=0;
    $(document).on('click','.emotion_chat_a2', function () {
        $('.indexDmatk').hide();
        $('.emotion_chat_a1').removeClass('deg45');
        $(this).toggleClass('deg360');
        $('.emotion_panel').toggle();
        if(!swipinit){
            $('div.swipe2').each(function () {
                hb_slider($(this), 0); $(this).css('height', 'auto');
            });
            swipinit = 1;
        }
    });
    $(document).on('click','#smsdosubmit', function () {
        var input = $('#commentinput').val();
        if(!input){
            return false;
        }
        do_send_sx(input, 1);
    });
    var oldpdm = '';
    $(document).on('focus','#commentinput', function () {
        oldpdm = $('.in_bottom').css('padding-bottom');
        $('.in_bottom').css('padding-bottom', 0);
    });
    $(document).on('blur','#commentinput', function () {
        $('.in_bottom').css('padding-bottom', oldpdm);
    });
    function do_send_sx(input, type){
        if(!input){
            input = $('#commentinput').val();
        }
        $.showLoading();
        $.ajax({
            type: 'post',
            url: _APPNAME + '?id=xigua_lt&ac=chatcmt&do=comment&inajax=1&type='+type+'&pubid=0',
            data: {'comment': input, 'touid': '$touid', 'formhash': FORMHASH},
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
            
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                
                /*if(data.code==-1){
                    layer.msg(data.msg,{shade:0.01});
                    return false;
                }*/
                
                
                
                var s = data.lastChild.firstChild.nodeValue;
                var msgar = s.split('|');
                
            
                
                if (msgar[0] === 'success') {
                    $('#commentinput').val('');
                    showcmt(msgar[3]);
                }else{
                      tip_common(s);
                }
            }
        });
    }
    function showcmt(cid){
        if(cid<1){
            return false;
        }
        $.ajax({
            type: 'get',
            url: _APPNAME + '?id=xigua_lt&ac=chat&do=fetch&inajax=1&cid='+cid,
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                var s = data.lastChild.firstChild.nodeValue;
                if(s){
                    $('#list1').append(s);
                    $('html,body').animate({scrollTop: $('.bottom_fix').offset().top},800);
                }
            }
        });
    }
    if ($("#list1").length > 0) {
        load_toplist();
        setTimeout(function(){
            $('html,body').animate({scrollTop: $('.bottom_fix').offset().top}, 800);
        }, 500);
    }
    $(window).on('scroll', function () {
        if($(document).scrollTop()<=0){
            load_toplist();
        }
    });

    var lasts = '';
    function load_toplist() {
        if (page <= 0) {
            return;
        }
        $.ajax({
            type: 'get', url: loadingurl1 + '' + page + _URLEXT, dataType: 'xml', success: function (data) {
                var s = $.trim(data.lastChild.firstChild.nodeValue);
                if (!s) { page = -1; return; }
                if(lasts===s){
                    return;
                }
                $("#list1").prepend(s);
                lasts = s;
                page++;
            }
        });
    }

    var times =0, sleep_ts=10000;
    setInterval(function () {
        $.ajax({
            type: 'get',
            url: _APPNAME + '?id=xigua_lt&ac=chat&do=fetchpm&inajax=1&touid=$touid',
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                var s = data.lastChild.firstChild.nodeValue;
                if(lasts===s){
                    return;
                }
                if(times>0 && s){
                    $('#list1').append(s);
                    $('html,body').animate({scrollTop: $('.bottom_fix').offset().top},800);
                    sleep_ts = 10000;
                }else{
                    sleep_ts += 500;
                }
                times=1;
            }
        });
    }, sleep_ts);

    $(document).on('click','.swipein b', function () {
        var span =$(this).find('span');
        var face = span.attr('data-face');
        var inputt = $('#commentinput');
        var old = inputt.val();
        if(!face){
            var pos = strrpos(old, '/');
            if(pos===false){
                pos = old.length-1;
            }else if(old.length-pos>4){
                pos = old.length-1;
            }
            var n= old.substr(0,pos);
            inputt.val(n);
        }else{
            inputt.val(old+face);
        }
    });
    function strrpos(haystack, needle, offset) {
        var i = -1;
        if (offset) {
            i = (haystack + '')
                .slice(offset)
                .lastIndexOf(needle);
            if (i !== -1) {
                i += offset;
            }
        } else {
            i = (haystack + '')
                .lastIndexOf(needle);
        }
        return i >= 0 ? i : false;
    }
    $(document).on('click','.redclick', function () {
        $("#redouter").popup();
    });
    $(document).on('click','.orderclick', function () {
        $("#orderouter").popup();
        load_common_lofing('');
    });
    var formlocklt = 0;
    $(document).on('submit', '#formlt', function () {
        var dosubbtn = $('#dosubmitlt');
        var that = $(this);
        if (formlocklt === 1) {
            return false;
        }
        $.showLoading();
        formlocklt = 1;
        $.ajax({
            type: 'post',
            url: that.attr('action') + '&inajax=1' + _URLEXT,
            data: that.serialize(),
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                formlocklt = 0;
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                var s = data.lastChild.firstChild.nodeValue;
                tip_common(s);
            },
            error: function () {
                $.hideLoading();
                formlocklt = 0;
            }
        });
        return false;
    });
    function sync_hbmoney(obj){
        obj.value=obj.value.replace(/[^\d.]/g,'');
        var s = obj.value;
        if(s<=0){
            s = '0.00';
        }
        $('#hbmony').text(s);
    }
    var curpg = 1, lofinglock = 0;
    var lofingurl = 'plugin.php?id=xigua_hb&ac=myorder_li&inajax=1&page=';
    function load_common_lofing(kwd) {
        if (curpg <= 0 || typeof lofingurl == 'undefined') {
            return;
        }
        if(lofinglock){
            return;
        }
        lofinglock = true;
        $.ajax({
            type: 'get', url: lofingurl + '' + curpg+(kwd?'&keyword='+encodeURIComponent(kwd) : '') + _URLEXT, dataType: 'xml', success: function (data) {
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                var s = $.trim(data.lastChild.firstChild.nodeValue);
                if (!s) {
                    $("#list_lofing").html('<div class="hs_empty" style="margin-top:0!important"><i class="icon iconfont icon-zanwuwenda"></i><p>{lang xigua_hb:zanwugengduo}</p></div>');
                }else{
                    $("#list_lofing").html(s);
                }
                lofinglock = false;
                console.log('curpage:' + curpg);
                curpg++;
            }, error: function () {
                lofinglock = false;
            }
        });
    }
    $(document).on('click','#dosearch', function () {
        if($('#searchInput').val()){
            load_common_lofing($('#searchInput').val());
        }
    });
    $(document).on('click','.hb_orderid', function () {
        $.closePopup('#orderouter');
        do_send_sx($(this).data('orderid'), 'order');
    });
    $(document).on('click','.giftclick', function () {
        $('#gift_ctrl').popup();
    });
</script>
<!--{if $lt_config[maxvideo]>0}--><!--{template xigua_lt:video}--><!--{/if}-->