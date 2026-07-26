<?php exit('Author: https://addon.dismall.com/?@xigua �������� �ͷ�QQ 1628585958 ΢�� wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<style data-tgb-r05-lane-b="cover">
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
.page__bd { box-sizing:border-box; min-height:100vh; padding:12px 16px calc(24px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; }
.mycover { box-sizing:border-box; margin:0!important; padding:12px!important; overflow:hidden; border:1px solid #d8e1ec; border-radius:8px; background:#fff!important; box-shadow:0 4px 14px rgba(12,27,51,.05); }
.mycover span { box-sizing:border-box; float:left; width:calc(50% - 6px); min-height:112px; margin:0 12px 12px 0; overflow:hidden; border:2px solid transparent; border-radius:8px; background:#edf3fa; cursor:pointer; }
.mycover span:nth-child(2n) { margin-right:0; }
.mycover span:focus, .mycover span:focus-within, .mycover span:active { border-color:#2764ff; outline:2px solid #2764ff; outline-offset:2px; }
.mycover img { width:100%; height:112px; display:block; object-fit:cover; }
.mycover:after { content:""; display:table; clear:both; }
@media (max-width:374px) { .mycover span, .mycover span:nth-child(2n) { width:100%; margin-right:0; } }
</style>
<div class="page__bd">
    <!--{template xigua_hb:common_nav}-->
    <div class="p15 mycover bgf">
        <!--{eval $cover = explode("\n", $config[toppics]);}-->
        <!--{loop $cover $k $v}-->
        <span tabindex="0" role="button"><img src="{echo trim($v);}"></span>
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
