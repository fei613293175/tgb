<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信wxiguabbs'); ?>
<!--{eval $extdefaultvalueary2 = explode(',', $extdefaultvalue2);
$extdefaultvalueary2_lv1 = array();
foreach($extdefaultvalueary2 as $___v):
    list($___v1, $___v2) = explode('-', $___v);
    $extdefaultvalueary2_lv1[] = $___v1;
endforeach;
$c50 = hb_hex2rgb($config['maincolor'], .5);
}--><style>.opacity5{background-color:$c50!important;}.potr{margin-top:.5rem;margin-bottom:.3rem;color:#333;font-size:.65rem}.post_combgf{background:#fff;padding-left:.75rem;padding-right:.75rem;max-height:75vh;overflow-y:auto;-webkit-overflow-scrolling:touch;padding-bottom:1rem}._ontop.weui-btn:after{border:.3rem solid #ff9800}
.quanxuan {font-size: .7rem;margin-bottom: .5rem;display: block;float: right;}
.quanxuan img{width: 0.7rem;height: 0.7rem;vertical-align: middle;}
</style>
<div id="popup_twoselects" class="weui-popup__container popup-bottom">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal" style="z-index:503">
        <div class="toolbar">
            <div class="toolbar-inner">
                <a href="javascript:;" class="picker-button close-popup">{lang xigua_hb:close}</a>
                <h1 class="title"></h1>
            </div>
        </div>
        <div class="modal-content post_combgf">
<div style="margin-top:1rem">
    <a class="quanxuan checkallts_1"><img src="source/plugin/xigua_hb/static/img/qx.png"> {lang select_all}</a>
    <div class="post-tags cl" style="margin-bottom:.5rem;display: table;">
        <!--{loop $extratmp2[0] $_tmpk $_tmpv}-->
        <a class="check8 weui-btn weui-btn_mini <!--{if in_array($_tmpv['name'], $extdefaultvalueary2_lv1)}-->weui-btn_primary _nomove<!--{else}-->weui-btn_default <!--{/if}-->" data-title="{$_tmpv['index']}" data-id="twoselects_{$_tmpv['index']}" id="topindex_{$_tmpv['index']}">{$_tmpv['name']}</a>
        <!--{/loop}-->
    </div>
    <div id="check7_outer">
        <!--{loop $extratmp2[0] $_tmpk $_tmpv}-->
    <div class="twoselects2" id="twoselects_{$_tmpv['index']}" style="display:none">
        <a class="quanxuan checkallts_2"  ><img src="source/plugin/xigua_hb/static/img/qx.png"> {lang select_all}</a>
        <div class="post-tags cl" style=" display: table;">
            <!--{loop $_tmpv['sub'] $__tmpk $__tmpv}-->
            <a class="check7 weui-btn weui-btn_mini <!--{if in_array($_tmpv['name'].'-'.$__tmpv['name'], $extdefaultvalueary2)}-->weui-btn_primary<!--{else}-->weui-btn_default <!--{/if}-->" data-title="{$__tmpv['index']}" data-topindex="$_tmpv['index']" data-topval="{$_tmpv['name']}">{$__tmpv['name']}</a>
            <!--{/loop}-->
        </div>
    </div>
        <!--{/loop}-->
    </div>
</div>
        </div>
    </div>
</div>
<script>$(document).on('click','#{$extratmpid2}', function () {var that = $(this), popcm =$('#popup_twoselects');popcm.find('.title').html(that.attr('placeholder')?that.attr('placeholder'):that.data('title'));popcm.popup();popcm.show();setTimeout(function(){popcm.show();}, 500);return false;});
$(document).on('click','.check7', function(){
    var that = $(this), val = '';
    if(that.hasClass('weui-btn_primary')){
        that.addClass('weui-btn_default').removeClass('weui-btn_primary');
    }else{
        that.addClass('weui-btn_primary').removeClass('weui-btn_default');
    }
    $('.check8').removeClass('_nomove');
    $('#check7_outer').find('.check7.weui-btn_primary').each(function () {
        $('#topindex_'+$(this).data('topindex')).addClass('_nomove');
        val+=','+$(this).data('topval') +'-'+ $(this).text();
    });
    $('#{$extratmpid2}').val(val.substr(1));
    twoselect_fill();
});
$(document).on('click','.check8', function () {
    var that = $(this);
    that.parent().find('a.weui-btn_primary').removeClass('_ontop');
    that.parent().find('a.weui-btn_primary:not(._nomove)').removeClass('weui-btn_primary').addClass('weui-btn_default');
    that.addClass('weui-btn_primary').addClass('_ontop').removeClass('weui-btn_default');
    $('.twoselects2').hide();
    $('#'+that.data('id')).show();
});
twoselect_fill();
function twoselect_fill(){
    $('.twoselects2').each(function(){
        var z7 = $(this).find('.check7');
        if(z7.length>0 && z7.length!==$(this).find('.weui-btn_primary').length){
            $('#topindex_'+z7.data('topindex')).addClass('opacity5');
        }else{
            $('#topindex_'+z7.data('topindex')).removeClass('opacity5');
        }
    });
}
$(document).on('click','.checkallts_1', function () {
    $('.checkallts_2').trigger('click');
    $('.check8').trigger('click');
    if($(this).text()==' {lang select_all}') {
        $(this).html('<img src="source/plugin/xigua_hb/static/img/qx.png"> {lang xigua_hb:quxiao}');
    }else{
        $(this).html('<img src="source/plugin/xigua_hb/static/img/qx.png"> {lang select_all}');
    }
});
$(document).on('click','.checkallts_2', function () {
    if($(this).text()==' {lang select_all}'){
        $(this).parent().find('.check7').each(function(){
            if($(this).hasClass('weui-btn_default')){
                $(this).trigger('click');
            }else{
                $(this).trigger('click');
                $(this).trigger('click');
            }
        });
        $(this).html('<img src="source/plugin/xigua_hb/static/img/qx.png"> {lang xigua_hb:quxiao}');
    }else{
        $(this).parent().find('.check7').each(function(){
            if($(this).hasClass('weui-btn_primary')){
                $(this).trigger('click');
            }else{
                $(this).trigger('click');
                $(this).trigger('click');
            }
        });
        $(this).html('<img src="source/plugin/xigua_hb/static/img/qx.png"> {lang select_all}');
    }
});
</script>