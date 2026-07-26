<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958'); ?>
<!--{template xigua_hb:common_header}-->
<link rel="stylesheet" href="source/plugin/xigua_hj/static/tgb-r05/report-light-grid-r05.css?20260727-r05-c2">
<div class="page__bd tgb-r05-report-page">
    <!--{template xigua_hb:common_nav}-->
    <div class="weui-cells__title">{lang xigua_hj:qtx}{lang xigua_hj:reson}</div>
<form id="form" action="$SCRITPTNAME?id=xigua_hj&ac=report" method="post">
    <input type="hidden" name="formhash" value="{FORMHASH}" />
    <input type="hidden" id="referer" name="form[referer]" value="{$link}" />
    <!--{if $jbyy = array_filter(explode("\n", trim($hj_config['jbyy'])))}-->
    <div class="weui-cells weui-cells_form before_none after_none">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <div class="post-tags cl" id="post-typeid">
                <!--{loop $jbyy $_k $_v}-->
                <a class="weui-btn weui-btn_mini weui-btn_default tagsids" href="javascript:;" onclick="return set_typeid({echo $_k+1}, this);">{echo trim($_v)}</a>
                <!--{/loop}-->
                <input name="form[tagid]" type="hidden" value="0" class="tagsids_input" />
            </div>
        </div>
    </div>
    </div>
    <!--{/if}-->
    <div class="weui-cells__title">{lang xigua_hj:desc}</div>
    <div class="weui-cells weui-cells_form before_none after_none">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea name="form[desc]" class="weui-textarea" placeholder="{lang xigua_hj:reson_tip}" rows="3"></textarea>
            </div>
        </div>
    </div>
    <div class="weui-cells__title">{lang xigua_hj:lxfs}</div>
    <div class="weui-cells weui-cells_form before_none after_none">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">{lang xigua_hj:name}</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="form[name]" type="text" placeholder="{lang xigua_hj:name_tip}">
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">{lang xigua_hj:mobile}</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="form[mobile]" type="tel" placeholder="{lang xigua_hj:qtx}{lang xigua_hj:mobile}">
            </div>
        </div>
    </div>
    <div class="weui-btn-area">
        <input class="weui-btn weui-btn_primary" type="submit" id="dosubmit" value="{lang xigua_hb:queding}" />
    </div>
</form>
</div>
<!--{eval $tabbar=0;}-->
<!--{template xigua_hb:common_footer}-->
<script>
function set_typeid(id, obj) {
    $('.tagsids').removeClass('tag-on');
    $('.tagsids_input').val('0');

    $(obj).addClass('tag-on');
    $('input[name="form[tagid]"]').val(id);
}
if(document.referrer){
    $('#referer').val(document.referrer);
}
</script>