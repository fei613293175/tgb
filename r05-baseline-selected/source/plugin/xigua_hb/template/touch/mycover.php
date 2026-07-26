<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<div class="page__bd">
    <!--{template xigua_hb:common_nav}-->
    <div class="p15 mycover bgf">
        <!--{eval $cover = explode("\n", $config[toppics]);}-->
        <!--{loop $cover $k $v}-->
        <span><img src="{echo trim($v);}"></span>
        <!--{/loop}-->
    </div>
</div>
<!--{eval $tabbar=0;}-->
<!--{template xigua_hb:common_footer}-->
<script>
$(function () {
   $('.mycover span').on('click', function () {
       var sc = $(this).find('img').attr('src');
       hb_setcookie('coversrc', sc);
       window.location.href =_APPNAME+'?id=xigua_hb&ac=my'+_URLEXT;
   });
});
</script>