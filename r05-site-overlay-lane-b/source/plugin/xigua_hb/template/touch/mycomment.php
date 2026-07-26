<?php exit('Author: https://addon.dismall.com/?@xigua �������� �ͷ�QQ 1628585958 ΢�� wxiguabbs'); ?>
<!--{if strpos($_SERVER['HTTP_REFERER'], 'xigua_jy')!==false}-->
<!--{eval $jy_config = $_G['cache']['plugin']['xigua_jy'];
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $jy_config['dftcolor'];}-->
<!--{template xigua_jy:header}-->
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_es')!==false}-->
<!--{eval $es_config = $_G['cache']['plugin']['xigua_es'];
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $es_config['dftcolor'];}-->
<!--{template xigua_es:header}-->
<!--{elseif $_GET[from]=='job'}-->
<!--{eval $job_config = $_G['cache']['plugin']['xigua_job'];
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $job_config['dftcolor'];
}--><!--{template xigua_job:header}-->
<!--{else}-->
<!--{template xigua_hb:common_header}-->
<!--{/if}-->
<style>.comment_ul .weui-media-box_appmsg .weui-media-box__hd{width:2rem;height:2rem;line-height:2rem}</style>
<style data-tgb-r05-lane-b="comments">
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
.page__bd { box-sizing:border-box; min-height:100vh; padding:12px 0 calc(76px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; }
.page__bd > .weui-navbar { width:auto; margin:0 16px 12px; overflow:hidden; border:1px solid #d8e1ec!important; border-radius:8px; background:#fff!important; box-shadow:none!important; }
.page__bd > .weui-navbar .weui-navbar__item { min-height:44px; padding:0 12px; color:#718096!important; font-size:14px!important; line-height:44px!important; }
.page__bd > .weui-navbar .weui-navbar__item.weui_bar__item_on { background:#edf3fa!important; color:#2764ff!important; font-weight:600; }
.comment_ul_users { margin:0 16px 12px!important; overflow:hidden; border:1px solid #d8e1ec!important; border-radius:8px; background:#fff!important; }
.comment_ul_users .weui-cell { min-height:64px; padding:8px 12px; }
#list.comment_ul { margin:0 16px; background:transparent!important; }
#list.comment_ul > a, #list.comment_ul .weui-cells { color:#405166!important; }
#list.comment_ul .weui-cells { margin:0 0 10px; overflow:hidden; border:1px solid #d8e1ec; border-radius:8px; background:#fff!important; box-shadow:0 4px 14px rgba(12,27,51,.05); }
#list.comment_ul .weui-cell { box-sizing:border-box; min-height:56px; padding:10px 12px!important; align-items:center!important; }
#list.comment_ul .weui-cell__hd img { width:40px!important; height:40px!important; margin-right:10px!important; border:2px solid #edf3fa; }
#list.comment_ul .bigtxt { height:auto!important; margin:0!important; color:#0e1b2a!important; font-size:14px!important; line-height:22px!important; }
#list.comment_ul .smalltxt { color:#718096!important; font-size:12px!important; line-height:18px!important; }
#list.comment_ul .view-content-comment-text { margin:0 12px 12px; padding:10px 12px; border-radius:6px!important; background:#edf3fa!important; color:#405166; font-size:14px; line-height:22px; overflow-wrap:anywhere; }
#list.comment_ul .cmt_p { margin:0 0 8px; padding:10px 12px; border:1px solid #d8e1ec; border-radius:6px; background:#fff; color:#405166; font-size:14px; line-height:22px; overflow-wrap:anywhere; }
#list.comment_ul .lybox a { box-sizing:border-box; width:160px!important; min-height:44px; border-radius:8px!important; background:#2764ff!important; box-shadow:none!important; font-size:14px!important; line-height:44px!important; }
#comment_ul_more { min-height:44px; border:1px solid #d8e1ec; border-radius:8px; background:#fff; }
.page__bd .weui-loadmore, .page__bd .weui-loadmore__tips { color:#718096!important; }
.page__bd a:focus-visible { outline:2px solid #2764ff; outline-offset:2px; }
</style>
<div class="page__bd">
    <!--{template xigua_hb:common_nav}-->
    <!--{if $_GET[type]!='sx'}-->
    <div class="weui-navbar">
        <a href="$SCRITPTNAME?id=xigua_hb&ac=mycomment&type=tome" class="weui-navbar__item <!--{if $_GET[type]!='tother'}-->weui_bar__item_on<!--{/if}-->">
            <span>{lang xigua_hb:comments}{lang xigua_hb:wode}</span>
        </a>
        <a href="$SCRITPTNAME?id=xigua_hb&ac=mycomment&type=tother" class="weui-navbar__item <!--{if $_GET[type]=='tother'}-->weui_bar__item_on<!--{/if}-->">
            <span>{lang xigua_hb:wode}{lang xigua_hb:comments}</span>
        </a>
    </div>
    <!--{/if}-->

<!--{if $_G['cache']['hb_ext_config']['sxkf']}-->
<!--{eval
if(!$_G['cache']['hb_ext_config']):
    loadcache('hb_ext_config');
endif;
$_sxkf = array_filter(explode(";", trim($_G['cache']['hb_ext_config']['sxkf'])));
$_stid_ary = array();
foreach($_sxkf as $_sxkf1):
    list($_stid_, $_uids_) = explode(':', trim($_sxkf1));
    $_stid_ary[$_stid_] = trim($_uids_);
endforeach;
$_getst = intval($_GET[st]);
$_uids = $_stid_ary[$_getst];
if($_stid_ary[-1]):
    $_uids = $_uids . ','.$_stid_ary[-1];
endif;
if($_uids = explode(',', trim($_uids))):
    $_users = DB::fetch_all('select * from %t where uid in (%n)', array('common_member', $_uids));
endif;
}-->
<!--{if $_users}-->
    <div class="weui-cells comment_ul_users bgf mt0 border_none" style="margin-bottom:.5rem">
    <!--{loop $_users $_k $_vusers}-->
        <a class="weui-cell  weui-cell_access " href="$SCRITPTNAME?id=xigua_hb&ac=chat&touid={$_vusers[uid]}">
            <div class="weui-cell__hd">
                <img style="width:2rem;height:2rem;display:block;margin-right:.5rem;border-radius:2rem" src="{avatar($_vusers[uid], 'middle', 1)}" alt="">
            </div>
            <div class="weui-cell__bd">
                <p class="c3 f16">$_vusers[username]</p>
            </div>
            <div class="weui-cell__ft"></div>
        </a>
    <!--{/loop}-->
    </div>
<!--{/if}-->
<!--{/if}-->
    <a href="$SCRITPTNAME?id=xigua_hj" style="display:none;margin:.75rem 0;text-align: center; font-size: .6rem; width: 100%; color: #999;"><i class="color-red2 iconfont icon-jubao f12"></i> &#22914;&#36935;&#26080;&#25928;&#12289;&#34394;&#20551;&#12289;&#35784;&#39575;&#20449;&#24687;&#65292;&#35831;<em class="color-red2">&#28857;&#27492;&#20030;&#25253;&#65281;</em></a>
    <div id="list" class="weui-panel__bd comment_ul p0 bgf"></div>
    <!--{template xigua_hb:loading}-->
</div>
<script>
    var loadingurl = window.location.href+'&ac=comment_li&hidezw=1&inajax=1&type=$_GET[type]&multi=1&pagetype=page&page=';
</script>
<!--{if strpos($_SERVER['HTTP_REFERER'], 'xigua_jy')!==false}-->
<!--{eval $tabbar=0;$jy_tabbar=1;}-->
<!--{template xigua_jy:footer}-->
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_es')!==false}-->
<!--{eval $es_tabbar=1;$tabbar=0;}-->
<!--{template xigua_es:footer}-->
<!--{elseif $_GET[from]=='job'}-->
<!--{eval $job_tabbar=1;$tabbar=0;}-->
<!--{template xigua_job:footer}-->
<!--{else}-->
<!--{eval $tabbar=1;}-->
<!--{template xigua_hb:common_footer}-->
<!--{/if}-->
