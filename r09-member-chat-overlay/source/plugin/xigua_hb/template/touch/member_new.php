<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{if $_GET['noheader'] && $_GET['uid']}-->
<!--{eval $_G['cache']['hb_ext_config']['open_incurr_mag']=1;}-->
<!--{/if}-->
<!--{if !$config[showsixin]}--><!--{eval $custom_side = array();}--><!--{/if}-->
<!--{if strpos($_SERVER['HTTP_REFERER'], 'xigua_jy')!==false || $_GET['from']=='jy'}-->
<!--{eval $jy_config = $_G['cache']['plugin']['xigua_jy'];$_GET['from']='jy';
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $jy_config['dftcolor'];}-->
<!--{template xigua_jy:header}-->
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_es')!==false || $_GET['from']=='es'}-->
<!--{eval $es_config = $_G['cache']['plugin']['xigua_es'];$_GET['from']='es';
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $es_config['dftcolor'];}-->
<!--{template xigua_es:header}--><style>.weui-navbar .weui-navbar__item{display:none}.weui-navbar .weui-navbar__item:nth-child(1),.weui-navbar .weui-navbar__item:last-child{display:block}</style>
<!--{else}--><!--{template xigua_hb:common_header}--><!--{/if}--><!--{if $_GET['noheader']}--><style>.hong .x_header,.my__head,.weui-navbar,.weui-tabbar{display:none}</style><!--{/if}--><!--{if IN_MAGAPP || IN_QIANFAN || IN_APPBYME||IN_PROG}--><style>.x_header a:first-child{display:none}</style><!--{/if}--><!--{eval
if($_G[uid]):
    $hasfave = C::t('#xigua_hb#xigua_hb_follow')->fetch_follow_by_favid_uid($uid, $_G['uid'], 'favuser');
endif;
$no_header_fix=1;$config[showheader] = 1;$hide_nav=0;
if($_G['uid']):
    @$member_blackList = C::t('home_blacklist')->fetch_all_by_uid_buid($_G['uid'], array($user['uid']));
endif;
}-->


<style>
    .listdata {

        background-color: #f1f4fb;
        background-repeat: no-repeat;

        width:100%!important;
        margin-top: 137px;
    }

    .listdata-card {
        background-color: #fff;
        box-shadow: 0px -1px 5px 0px rgba(0,0,0,0.1);
        border-radius: 15px;
        margin-bottom: 5px;
        height: 230px;
        margin-left: 15px;
        margin-right: 15px;

    }
    .listdata-card1 {
        background-color: #fff!important;
        box-shadow: 0 -1px 5px 0 rgba(0,0,0,0.1);
        border-radius: 15px;
        margin-bottom: 5px;
        height: 230px;

        margin-left: 15px;
        margin-right: 15px;}

    .listdata-card-top, .listdata-card-bottom {
        padding: 25px 15px;
        font-size: 0.75rem;
        color: #ff0000;
        font-weight: bold;
    }

    .listdata-card-top img {
        vertical-align: middle;
        width: 20px;
    }
       .font{
       color:#4b4b4b;font-weight:450;font-size:13px;

       top:2px;
       position: relative;
    }
</style>



 <!--{template xigua_hb:wdk_header}-->
<link rel="stylesheet" href="source/plugin/xigua_hb/static/css/member_new.css?{VERHASH}" />
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r09/member-detail-light-grid-r09.css?v=20260727-r09-3" />
<script>document.documentElement.classList.add('tgb-r09-member-detail-page');</script>
<!--{if $_GET['app']}--><style>.yu_sidectrl, .yu_weui, .yu_line,.yu_top,.x_header,.my__head,.yu_top_avatar{display:none}</style><!--{/if}-->
<div class="page__bd tgb-r09-member-detail">

<!--{if !$_GET['app']}-->
    <a class="yu_sidectrl mem_ctrl1" href="javascript:void(0)" aria-label="更多操作"><i class="iconfont icon-gengduo1" aria-hidden="true"></i></a>
  
<!--{/if}-->
 
        <div class="tgb-r09-member-cover" aria-hidden="true"><span></span></div>
      
    </div>
    <div>
        <img class="yu_top_avatar" src="{avatar($user[uid], 'middle', 1)}">
    </div>
    <div class="yu_top cl">
        <div class="yu_top_num cl">
            <div class="yt" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=fav&ty=user&uid=$user[uid]')">
                <b>$his_favuser</b>
                <span class="c9 f13">&#20851;&#27880;</span>
            </div>
            <div class="yt" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=fav&ty=myfans&uid=$user[uid]')">
                <b>$his_fans</b>
                <span class="c9 f13">&#31881;&#19997;</span>
            </div>
            <!--{if $_G[uid]!=$user[uid]}-->
        <!--{if $member_blackList[$user[uid]]}-->
            <div class="yt">
                <div class="yu_top_btn yu_ygz lahei" data-jiechu="1" data-uid="$user[uid]" data-username="{$user[username]}">&#24050;&#25289;&#40657;</div>
            </div>
        <!--{else}-->
            <div class="yt" style="">
                <div class="yu_top_btn <!--{if !$hasfave}-->main_bg<!--{else}-->yu_ygz<!--{/if}--> gzusebtn" id="gz{$uid}" data-to="{$uid}"><!--{if !$hasfave}-->{lang xigua_hb:jgz}<!--{else}-->{lang xigua_hb:ygz}<!--{/if}--></div>
            </div>
            <div class="yt">
                <div style="margin-left:.25rem;background-image: linear-gradient(90deg, #0099ff 1%, #0071fe 99%);font-weight:450;color:#fff;width:65px;" data-uid="$uid" class="yu_top_btn yu_ygz comment_to" style="">私信</div>
            </div>
        <!--{/if}-->
            <!--{else}-->
            <div class="yt">
                <div onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=fav&ty=myfans')" class="yu_top_btn main_bg">&#20851;&#27880;</div>
            </div>
            <div class="yt">
                <div style="margin-left:.25rem" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=mycomment&type=sx')" class="yu_top_btn yu_ygz">&#28040;&#24687;</div>
            </div>
            <!--{/if}-->
            <img style="display:none" src="source/plugin/xigua_hb/static/img/xl.png" class="yu_img_btn" alt="">
        </div>
        <div class="yu_username" style="display:flex;font-size:25px;">
            <b style="font-size:25px;">{$user[username]}</b>
 

        </div>
        <div class="yu_zuji f13 ">
            <div class="c9">最后在线：{$lastvisit}</div>
            <!--{if $ipaddr}-->
            <div class="mt10 cl">
                <div class="yu_button z"><!--{if $_G['cache']['hb_ext_config']['ipprefix']}-->$_G['cache']['hb_ext_config']['ipprefix']<!--{else}-->IP属地 <!--{/if}-->$ipaddr</div>
            </div>
            <!--{/if}-->
        </div>
    </div>
<div class="bgf">
    <div class="cl border_bottom yu_line"></div>
</div>
<div class="weui-navbar mt0 yu_weui">
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&from={$_GET['from']}" class="weui-navbar__item  <!--{if !$_GET[type]}-->weui_bar__item_on<!--{/if}-->">
        <span>{lang xigua_hb:fabu0} $his_totalpub</span>
    </a>
    <!--{if $_G['cache']['plugin']['xigua_hs']}-->
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&type=dianpu&from={$_GET['from']}" class="weui-navbar__item  <!--{if $_GET[type]=='dianpu'}-->weui_bar__item_on<!--{/if}-->">
        <span>{lang xigua_hb:dianpu} $his_totalsh</span>
    </a>
    <!--{/if}-->
    <!--{if $_G['cache']['plugin']['xigua_hp']}-->
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&type=mingpian&from={$_GET['from']}" class="weui-navbar__item  <!--{if $_GET[type]=='mingpian'}-->weui_bar__item_on<!--{/if}-->">
        <span>{lang xigua_hp:mp} $his_hp</span>
    </a>
    <!--{/if}-->
    <!--{if $_G['cache']['plugin']['xigua_dh']}-->
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&type=114&from={$_GET['from']}" class="weui-navbar__item  <!--{if $_GET[type]=='114'}-->weui_bar__item_on<!--{/if}-->">
        <span>&#30005;&#35805;</span>
    </a>
    <!--{/if}-->
    <!--{if $_G[uid]==$uid||IS_ADMINID}-->
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&type=viewlog&from={$_GET['from']}" class="weui-navbar__item <!--{if $_GET[type]=='viewlog'}-->weui_bar__item_on<!--{/if}-->">
        <span>{lang xigua_hb:fangke}</span>
    </a>
    <!--{/if}-->
    <!--{if $_G['cache']['plugin']['xigua_dp']}-->
    <a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$uid&type=dp&from={$_GET['from']}" class="weui-navbar__item <!--{if $_GET[type]=='dp'}-->weui_bar__item_on<!--{/if}-->">
        <span>&#35780;&#20215;</span>
    </a>
    <!--{/if}-->
</div>

<!--{if $_GET[type]=='viewlog'}-->
<div id="list" class="weui-cells p0 mt0 before_none"></div>
<script>
    var loadingurl = window.location.href+'&ac=member_li&inajax=1&page=';
</script>
<!--{elseif $_GET[type]=='dp'}-->
<link rel="stylesheet" href="source/plugin/xigua_dp/static/jx.css?{VERHASH}" /><style>.dp_list .gzusebtn{display:none}</style>
<div id="list" class="weui-cells p0 mt0 before_none dp_list"></div>
<script>
    var loadingurl = '$SCRITPTNAME?id=xigua_dp&ac=jingxuan&hidex=1&inajax=1&type=es&typelike={$uid}_0&pagesize=10&page=';
</script>
<!--{elseif $_GET[type]=='dianpu'}-->
<link href="source/plugin/xigua_hs/static/hs.css?{VERHASH}" rel="stylesheet" />
<div id="list" class="mod-post x-postlist pt0"></div>
<script>
    var loadingurl = "$SCRITPTNAME?id=xigua_hs&ac=myshop_li&uid=$uid&inajax=1&page=";
</script>
<!--{elseif $_GET[type]=='mingpian'}-->
<link rel="stylesheet" href="source/plugin/xigua_hp/static/css/hp.css?{VERHASH}" />
<div id="list" class="mod-post x-postlist pt0"></div>
<script>
    $(document).on('click', '.hp_glist', function () {
        var that = $(this);
        var jmpurl = _APPNAME +'?id=xigua_hp&ac=view&mpid='+that.data('id')+(typeof _URLEXT!=='undefined'? _URLEXT : '');
        if(that.data('cookie')){
            hb_setcookie(that.data('cookie')+that.data('id'), '', -1);
            jmpurl += '&d=1';
        }
        if(typeof mag !== 'undefined'){
            mag.newWin(GSITE+jmpurl);
            return false;
        }
        if(typeof wx !=='undefined'){
            if (window.__wxjs_environment === 'miniprogram') {
                GSITE = GSITE.replace(/http:\/\//, 'https://');
                wx.miniProgram.navigateTo({url:'/pages/index/index?url='+encodeURIComponent(GSITE+jmpurl)});
                return false;
            }
        }
        if(typeof QFH5 !== 'undefined'){
            QFH5.jumpNewWebview(GSITE+jmpurl);
            return false;
        }
        window.location.href = jmpurl;
        return false;
    });
    var loadingurl = "$SCRITPTNAME?id=xigua_hp&ac=good_li&orderby=&keyword=$uid&inajax=1&page=";
</script>
<!--{elseif $_GET[type]=='114'}-->
<link rel="stylesheet" href="source/plugin/xigua_dh/static/css/dh.css?{VERHASH}">
<div id="list" class="mod-post x-postlist pt0"></div>
<script>
    $(document).on('click', '.dh_jump', function () {
        var that = $(this);
        var jmpurl = _APPNAME +'?id=xigua_dh&ac=view&shid='+that.data('id')+(typeof _URLEXT!=='undefined'? _URLEXT : '');
        hb_jump(jmpurl);
    });
    var loadingurl = "$SCRITPTNAME?id=xigua_dh&ac=myshop_li&orderby=&uid=$uid&inajax=1&page=";
</script>
<!--{else}-->
<div id="list" class="mod-post x-postlist pt0"></div>
<script>
    var loadingurl = window.location.href+'&ac=list_item&inajax=1&uid=$uid&frommember=1&page=';
</script>
<!--{/if}-->
<!--{template xigua_hb:loading}-->
</div>
<div id="mem_ctrl" class="weui-popup__container popup-bottom" style="z-index:999">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal" style="border-radius:.75rem .75rem 0 0">
        <div class="modal-content bgf" style="padding-bottom:1rem;letter-spacing:2px">
            <div class="weui-cells border_none mt0 ">
                <a class="weui-cell weui-cell_access we_share border_none" href="javascript:;">
                    <div class="weui-cell__bd tc">&#20998;&#20139;</div>
                </a>
                <a class="weui-cell weui-cell_access border_none" href="$SCRITPTNAME?id=xigua_hj">
                    <div class="weui-cell__bd tc color-red2">&#20030;&#25253;</div>
                </a>
            <!--{if IS_ADMINID}-->
                <a class="weui-cell weui-cell_access pbbtn border_none" href="javascript:;">
                    <div class="weui-cell__bd tc"><!--{if $qi[pingbi]}-->{lang xigua_hb:jiechu}<!--{else}-->{lang xigua_hb:pb}<!--{/if}--></div>
                </a>
            <!--{/if}-->
                <!--{if $member_blackList[$user[uid]]}-->
                <a class="weui-cell weui-cell_access border_none lahei" data-jiechu="1" data-uid="$user[uid]" data-username="{$user[username]}" href="javascript:;">
                    <div class="weui-cell__bd tc">&#35299;&#38500;&#40657;&#21517;&#21333;</div>
                </a>
                <!--{else}-->
                <a class="weui-cell weui-cell_access border_none lahei"  data-jiechu="0" data-uid="$user[uid]" data-username="{$user[username]}" href="javascript:;">
                    <div class="weui-cell__bd tc">&#25289;&#40657;</div>
                </a>
                <!--{/if}-->
                <a class="weui-cell weui-cell_access border_none">
                    <div class="weui-cell__bd tc">&nbsp;</div>
                </a>
                <a class="weui-cell weui-cell_access close-popup border_none">
                    <div class="weui-cell__bd tc c9">&#21462;&#28040;</div>
                </a>
            </div>
        </div>
    </div>
</div>
<!--{if $_GET['from']=='jy'}-->
<!--{eval $tabbar=0;$jy_tabbar=1;}-->
<!--{template xigua_jy:footer}-->
<!--{elseif $_GET['from']=='es'}-->
<!--{eval $es_tabbar=1;$tabbar=0;}-->
<!--{template xigua_es:footer}-->
<!--{else}-->
<!--{eval $tabbar=1;}-->
<!--{template xigua_hb:common_footer}-->
<!--{/if}-->
<script>
    $(document).on('click','.pbbtn', function () {
        var that = $(this);
        $.modal({title: "{lang xigua_hb:pingbicyh}",text: "{lang xigua_hb:qdpb}",buttons: [{ text: "{lang xigua_hb:quxiao}", className: "default"},{ text: "{lang xigua_hb:pb}", onClick: function(){pingbi(that.data('uid'), 1);} },{ text: "{lang xigua_hb:jiechu}", onClick: function(){pingbi(that.data('uid'), 0);}}]})
    });
    $(document).on('click','.lahei', function () {
        var that = $(this);
        var btnss=[];
        btnss.push({text: "{lang xigua_hb:quxiao}", className: "default"} );
        if(that.data('jiechu')) {
            btnss.push( {  text: "{lang xigua_hb:jiechu}", onClick: function(){ lahei(that.data('uid'), 1); } }  );
            $.modal({title:'&#25289;&#40657;&#29992;&#25143;',text: "&#30830;&#23450;&#35201;&#35299;&#38500;&#40657;&#21517;&#21333; "+that.data('username'),buttons:btnss });
        }else {
            btnss.push( {  text: "&#25289;&#40657;", onClick: function(){ lahei(that.data('uid'), 0); } } );
            $.modal({title:'&#25289;&#40657;&#29992;&#25143;',text: "&#30830;&#23450;&#35201;&#25289;&#40657; "+that.data('username'),buttons:btnss });
        }
    });

    function lahei(uid, jiechu) {
        $.showLoading();
        $.ajax({
            type: 'post',
            url: window.location.href + '&ac=manage&inajax=1&uid=' + uid + '&lahei=' + (jiechu ? 'no' : 'yes'),
            data: {formhash: FORMHASH, 'dolahei': 1},
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false;
                }
                var s = data.lastChild.firstChild.nodeValue;
                tip_common(s);
            },
            error: function () {
                $.hideLoading();
            }
        });
    }
    $(document).on('click', '.gzusebtn', function () {
        var that = $(this);
        $.showLoading();
        $.ajax({
            type: 'post',
            url: _APPNAME + '?id=xigua_hb&ac=fav&fav=user&isbg=1&inajax=1',
            data: {'touid': that.data('to'), 'formhash': FORMHASH},
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                if (null == data) {
                    tip_common('error|' + ERROR_TIP);
                    return false
                }
                var s = data.lastChild.firstChild.nodeValue;
                tip_common(s)
            },
            error: function () {
                $.hideLoading()
            }
        })
    });
    $(document).on('click','.mem_ctrl1', function () {
        $('#mem_ctrl').popup();
    });
</script>
