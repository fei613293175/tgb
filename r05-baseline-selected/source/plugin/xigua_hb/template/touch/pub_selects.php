<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信wxiguabbs'); ?>
<style>.potr{margin-top:.5rem;margin-bottom:.3rem;color:#333;font-size:.65rem}.post_combgf{background:#fff;padding-left:.75rem;padding-right:.75rem;max-height:75vh;overflow-y:auto;-webkit-overflow-scrolling:touch;padding-bottom:1rem}</style>
<div id="popup_pubselects" class="weui-popup__container popup-bottom">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal" style="z-index:503">
        <div class="toolbar">
            <div class="toolbar-inner">
                <a href="javascript:;" class="picker-button close-popup">{lang xigua_hb:close}</a>
                <h1 class="title"></h1>
            </div>
        </div>
        <div class="modal-content post_combgf">
<!--{eval $extdefaultvalueary = explode(',', $extdefaultvalue);}-->
<!--{loop $extratmp[0] $_tmpk $_tmpv}-->
<div class="potr">{$_tmpv['name']}</div>
<div class="post-tags cl">
    <!--{loop $_tmpv['sub'] $__tmpk $__tmpv}-->
    <a class="check5 weui-btn weui-btn_mini <!--{if in_array($__tmpv['name'], $extdefaultvalueary)}-->weui-btn_primary<!--{else}-->weui-btn_default <!--{/if}-->" data-title="{$__tmpv['index']}">{$__tmpv['name']}</a>
    <!--{/loop}-->
</div>
<!--{/loop}-->
            <div class="potr c9">&#23567;&#25552;&#31034;&#65306;&#28857;&#20987;&#36873;&#20013;&#65292;&#20877;&#27425;&#28857;&#20987;&#21487;&#20197;&#21462;&#28040;&#36873;&#20013;</div>
        </div>
    </div>
</div>
<script>$(document).on('click','#{$extratmpid}', function () {var that = $(this), popcm =$('#popup_pubselects');popcm.find('.title').html(that.attr('placeholder')?that.attr('placeholder'):that.data('title'));popcm.popup();popcm.show();setTimeout(function(){popcm.show();}, 500);return false;});$(document).on('click','.check5', function(){var that = $(this), val = '';var prtnt = that.parent().parent();if(that.hasClass('weui-btn_primary')){that.addClass('weui-btn_default').removeClass('weui-btn_primary');}else{
that.addClass('weui-btn_primary').removeClass('weui-btn_default');}prtnt.find('.check5.weui-btn_primary').each(function () {  val+=',' + $(this).text(); });
$('#{$extratmpid}').val(val.substr(1));});</script>