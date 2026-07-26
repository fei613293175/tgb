<?php exit('Author: https://addon.dismall.com/?@xigua �������� �ͷ�QQ 1628585958 ΢�� wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->

<script src="source/plugin/xigua_hb/static/app.js" type="text/javascript"></script>
<style>.weui-switch:checked {border-color:$config[maincolor];background-color:$config[maincolor]}#new_popup.weui-popup__container{ display: none;position: fixed;height:100vh}#new_popup.weui-popup__container .weui-popup__modal,#new_popup.weui-popup__container .weui-popup__modal .fixpopuper{position: relative;height:100vh!important;}#new_popup.weui-popup__container--visible .weui-popup__overlay{background-color:#f8f8f8}</style>
<div id="new_popup" class="weui-popup__container" style="z-index:1000">
    <div class="weui-popup__modal">
        <form  action="$SCRITPTNAME?id=xigua_hb&ac=myaddr&do=add" method="post" id="form">
            <div class="fixpopuper">
                <input name="formhash" value="{FORMHASH}" type="hidden">
                <input name="form[oldid]" value="0" type="hidden">
                <input name="inajax" value="1" type="hidden">
                <div class="weui-cells__title" style="margin-top:2rem;margin-bottom:1rem;color:#255eed;font-size:16px;">如不购买实物商品，地址可随意填写</div>
                <div class="weui-cells weui-cells_form">
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><label class="weui-label">{lang xigua_hb:xm}</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input" type="text" name="form[realname]" placeholder="{lang xigua_hb:plzxm}">
                        </div>
                    </div>

                    <div class="weui-cell">
                        <div class="weui-cell__hd"><label for="" class="weui-label">{lang xigua_hb:mobile}</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input" type="tel" name="form[mobile]" placeholder="{lang xigua_hb:mobile_tip}" value="">
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><label for="name" class="weui-label">{lang xigua_hb:dist}</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input" id="dist" name="form[dist]" type="text" placeholder="{lang xigua_hb:plzdist}" value="" readonly>
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <textarea class="weui-textarea" name="form[address]" placeholder="{lang xigua_hb:plzxxdz}" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="weui-cell none">
                        <div class="weui-cell__hd"><label for="" class="weui-label">{lang xigua_hb:postcode}</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input" type="tel" name="form[postcode]" placeholder="{lang xigua_hb:plzpostcode}" value="">
                        </div>
                    </div>
                </div>
                <div class="weui-cells weui-cells_form" style="margin-top:10px">
                    <div class="weui-cell weui-cell_switch">
                        <div class="weui-cell__bd">{lang xigua_hb:swmr}</div>
                        <div class="weui-cell__ft">
                            <input class="weui-switch" name="form[dft]" value="1" type="checkbox">
                        </div>
                    </div>
                </div>
                <div class="fix-bottom mt10" style="position: relative">
                    <input type="submit" name="dosubmit" id="dosubmit" class="weui-btn weui-btn_primary" value="{lang xigua_hb:queding}" />
                    <a class="weui-btn weui-btn_default close-popup" >{lang xigua_hb:quxiao}</a>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="page__bd">
    <!--{template xigua_hb:common_nav}-->
    <div>
        <div class="weui-cells__title">{lang xigua_hb:shouhuo}</div>
        <!--{if $list}-->
        <!--{loop $list $k $v}-->
        <div class="weui-form-preview <!--{if $k>0}-->mt10<!--{/if}-->" id="li_{$v[id]}">
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item tl">
                    <span class="f24 c6">{$v[realname]} <em class="f15 main_color">{$v[mobile]}</em>
                    <!--{if $v[dft]}--><em class="f15 y c3">{lang xigua_hb:dft}</em><!--{/if}-->
                    </span>
                    <span class="weui-form-preview__value f15">{$v[dist1]}{$v[dist2]}{$v[dist3]} {$v[address]}</span>
                </div>
            </div>
            <div class="weui-form-preview__ft">
                <a class="weui-form-preview__btn weui-form-preview__btn_default f17" onclick="return confirm_del('{lang xigua_hb:delconfirm}', '$SCRITPTNAME?id=xigua_hb&ac=myaddr&do=del&formhash={FORMHASH}&delid={$v[id]}', '{$v[id]}');" href="javascript:">{lang xigua_hb:dodel}</a>
                <button type="submit" class="weui-form-preview__btn weui-form-preview__btn_primary f17" href="javascript:" onclick="return full_input(this);" data-dft="$v[dft]" data-oldid="$v[id]" data-realname="$v[realname]" data-mobile="$v[mobile]" data-address="$v[address]" data-dist="{$v[dist1]} {$v[dist2]} {$v[dist3]}" data-postcode="$v[postcode]">{lang xigua_hb:xiugai}</button>
                <!--{if 1||$back_to_overwrite}--><a onclick="return update_addr('$SCRITPTNAME?id=xigua_hb&ac=myaddr&do=update&formhash={FORMHASH}&oldid={$v[id]}')" class="weui-form-preview__btn <!--{if $v[dft]}--> weui-form-preview__btn_default<!--{else}-->weui-form-preview__btn_primary<!--{/if}--> f17">{lang xigua_hb:swmrdz}</a><!--{/if}-->
            </div>
        </div>
        <!--{/loop}-->
        <!--{else}-->
        <!--{template xigua_hb:loading}-->
        <script>
            $('#loading-show').addClass('hidden');
            $('#loading-none').removeClass('hidden');
        </script>
        <!--{/if}-->
    </div>
    <div class="footer_fix"></div>
    <div class="bottom_fix"></div>
    <div class="fix-bottom" style="border-radius:30px;">
        <a class="weui-btn weui-btn_primary" style="border-radius:30px;" data-dft="1" onclick="return full_input(this);">{lang xigua_hb:newaddr}</a>
    </div>
</div>
<!--{eval}-->
$_key = 'hbpubIdist' . intval($_GET['st']);
loadcache($_key);
if (!$_G['cache'][$_key]['variable'] || (TIMESTAMP - $_G['cache'][$_key]['expiration'] > 2592000) || defined('IN_ADMINCP')) {
    C::t('#xigua_hb#xigua_hb_district')->init(C::t('#xigua_hb#xigua_hb_district')->list_all());
    $jsary = C::t('#xigua_hb#xigua_hb_district')->get_tree_array(0);
    $jsary = array_values($jsary);
    savecache($_key, array('variable' => array($jsary), 'expiration' => TIMESTAMP));
} else {
    $jsary = $_G['cache'][$_key]['variable'][0];
}
$cityjson = json_encode($jsary);
<!--{/eval}-->
<script>
+function($){
    $.rawCitiesData = $cityjson;
}($);
</script>
<script type="text/javascript" src="source/plugin/xigua_hb/static/js/city-picker.js?{VERHASH}" charset="utf-8"></script>
<script>$("#dist").cityPicker({ title: "{lang xigua_hb:plzdist}" });
function full_input(ob){
    var fd = ['oldid','realname','mobile','dist', 'address', 'postcode'];
    for(var i in fd){
        var h = $(ob).data(fd[i]);
        if(typeof h === 'undefined' || h === 0){
            h = '';
        }
        $('[name="form['+fd[i]+']"]').text(h+'').val(h+'');
    }
    if($(ob).data('dft')){
        $('[name="form[dft]"]').prop("checked",true);
    }else{
        $('[name="form[dft]"]').prop("checked",false);
    }
    $('#new_popup').popup();
    setTimeout(function () {
        $('#new_popup').show();
    }, 500);
    return false;
}
function update_addr(url){
    $.showLoading();
    $.ajax({
        type: 'post',
        url: url+_URLEXT,
        data: {'formhash' :FORMHASH},
        dataType: 'xml',
        success: function (data) {
            $.hideLoading();
            if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
            var s = data.lastChild.firstChild.nodeValue;
            var msgar = tip_common(s);
        },
        error: function () {
            $.hideLoading();
        }
    });
}
</script>
<!--{eval $tabbar=0;}-->
