<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<div class="page__bd" style="margin-top:35px;">
    <!--{template xigua_hb:common_nav}-->
    <div class="weui-navbar">

        <a href="$SCRITPTNAME?id=xigua_hb&ac=manage&stat=display&display=0" class="weui-navbar__item <!--{if $_GET[stat]=='display'&&$_GET[display]==0 && !$_GET['new']}-->weui_bar__item_on<!--{/if}-->">
            <span>{lang xigua_hb:daishen}</span>
        </a>
        
        
          <a href="$SCRITPTNAME?id=xigua_hb&ac=manage&stat=display&display=0&reason=1&new=1" class="weui-navbar__item <!--{if $_GET[stat]=='display'&& $_GET[display]==0 && $_GET[reason]==1}-->weui_bar__item_on<!--{/if}-->">
            <span>审核失败</span>
            
            
            
        </a>
        
        <a href="$SCRITPTNAME?id=xigua_hb&ac=manage&stat=display&display=1" class="weui-navbar__item <!--{if $_GET[stat]=='display'&&$_GET[display]==1 && !$_GET['new']}-->weui_bar__item_on<!--{/if}-->">
            <span>{lang xigua_hb:yishen}</span>
        </a>
    </div>

    <div id="list" class="mod-post x-postlist p0">

    </div>

    <!--{template xigua_hb:loading}-->
</div>

<script>
    var loadingurl = window.location.href+'&ac=list_item&is_admin=1&is_my=1&inajax=1&page=';
    scrollto = 1;
</script>
<!--{eval $tabbar=1;}-->
<!--{template xigua_hb:common_footer}-->