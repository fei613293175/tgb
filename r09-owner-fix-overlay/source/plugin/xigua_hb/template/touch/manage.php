<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<style data-tgb-r05-lane-b="review">
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
.page__bd { box-sizing:border-box; min-height:100vh; padding:0 0 calc(76px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; }
.page__bd > .x_header_fix { display:none!important; height:0!important; }
.page__bd > .weui-navbar { width:auto; margin:0 16px 12px; overflow:hidden; border:1px solid #d8e1ec!important; border-radius:8px; background:#fff!important; box-shadow:none!important; }
.page__bd > .weui-navbar .weui-navbar__item { min-width:88px; min-height:44px; padding:0 10px; color:#718096!important; font-size:14px!important; line-height:44px!important; }
.page__bd > .weui-navbar .weui-navbar__item.weui_bar__item_on { background:#edf3fa!important; color:#2764ff!important; font-weight:600; }
.page__bd > .weui-navbar .weui-navbar__item.weui_bar__item_on span::after { background:#2764ff!important; background-color:#2764ff!important; opacity:1!important; }
.page__bd #list { box-sizing:border-box; padding:0 16px!important; background:transparent!important; }
.page__bd #list .li, .page__bd #list .listdata-1, .page__bd #list .listdata-card { box-sizing:border-box; height:auto!important; margin:0 0 12px!important; border:1px solid #d8e1ec; border-radius:8px!important; background:#fff!important; background-image:none!important; box-shadow:0 4px 14px rgba(12,27,51,.05); }
.page__bd #list .post { box-sizing:border-box; max-width:100%; border:0!important; background:transparent!important; box-shadow:none!important; }
.page__bd #list .mod-feed-text, .page__bd #list .mod-feed-text a { color:#0e1b2a!important; font-size:15px!important; line-height:23px!important; overflow-wrap:anywhere; }
.page__bd #list .mod-lv, .page__bd #list .item_tags span, .page__bd #list .bftag { border-color:#bfd0e3!important; border-radius:6px!important; background:#edf3fa!important; background-image:none!important; color:#2176c7!important; }
.page__bd #list .weui-btn, .page__bd #list .weui-btn_mini, .page__bd #list .btn-new01 { box-sizing:border-box; min-width:76px; min-height:44px!important; padding:0 12px!important; border:1px solid #bfd0e3!important; border-radius:8px!important; background:#fff!important; background-image:none!important; color:#2764ff!important; font-size:13px!important; line-height:42px!important; box-shadow:none!important; }
.page__bd #list .c_opt, .page__bd #list .showfull, .page__bd #list .c-icon { box-sizing:border-box; display:inline-flex; min-width:44px; min-height:44px; align-items:center; justify-content:center; color:#2764ff!important; }
.page__bd #list .touch-panel a, .page__bd #list .po-act a { box-sizing:border-box; min-height:44px; }
.page__bd #list img, .page__bd #list video { max-width:100%; }
.page__bd #list .mod-feed-text, .page__bd #list .time, .page__bd #list .ipadr, .page__bd #list .da { overflow-wrap:anywhere; word-break:break-word; }
.page__bd #list .tgb-r05-traffic-mark { display:inline-flex; width:24px; height:24px; margin-right:4px; align-items:flex-end; justify-content:center; gap:2px; vertical-align:middle; }
.page__bd #list .tgb-r05-traffic-mark:before { content:""; width:3px; height:8px; border-radius:2px 2px 0 0; background:#19b8a9; box-shadow:5px -4px 0 #2764ff,10px -9px 0 #7657ff; }
</style>
<div class="page__bd" style="margin-top:0;">
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
