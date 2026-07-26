<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
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