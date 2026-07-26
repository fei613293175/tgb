<?php exit('Author: https://addon.dismall.com/?@xigua �������� �ͷ�QQ 1628585958 ΢�� wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<!--{if $_GET[ty]=='sh'}-->
<!--{template xigua_hs:header}-->
<!--{elseif $_GET[ty]=='dhb'}-->
<link rel="stylesheet" href="source/plugin/xigua_dh/static/css/dh.css?{VERHASH}" />
<!--{elseif $_GET[ty]=='huodong'}-->
<link rel="stylesheet" href="source/plugin/xigua_he/static/he.css?{VERHASH}" />
<style>.main_color2{color:{$_G['cache']['plugin']['xigua_he'][mainc2]}!important;}
    .index_re_List_tip{border-color:$_G['cache']['plugin']['xigua_he'][mainc2];color:$_G['cache']['plugin']['xigua_he'][mainc2];background-color:{$_G['cache']['plugin']['xigua_he'][mainc2]}0f}</style>
<!--{/if}-->


<style>

    #list{
        background: #fafafa;
    }
    .listdata-card {
        background-color: #fff;
        border-radius: 10px;
        margin-bottom: 12px;
        height: 230px;
        margin-left: 10px;
        margin-right: 10px;
    }

    .listdata-card-top img {
        vertical-align: middle;
        width: 20px;
    }

    .listdata-card-top, .listdata-card-bottom {
        padding: 20px 10px;
        font-size: 0.75rem;
        color: #ff0000;
        font-weight: bold;
    }
</style>
<style data-tgb-r05-lane-b="favorites">
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
.page__bd { box-sizing:border-box; min-height:100vh; padding:12px 0 calc(76px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; }
.page__bd > .weui-navbar { width:auto; margin:0 16px 12px; border:1px solid #d8e1ec!important; border-radius:8px; background:#fff!important; box-shadow:none!important; }
.page__bd > .weui-navbar > .weui-navbar { position:static; overflow-x:auto; border:0!important; background:transparent!important; white-space:nowrap; -webkit-overflow-scrolling:touch; }
.page__bd .weui-navbar__item { min-width:76px; min-height:44px; padding:0 12px; color:#718096!important; font-size:14px!important; line-height:44px!important; }
.page__bd .weui-navbar__item.weui_bar__item_on { background:#edf3fa!important; color:#2764ff!important; font-weight:600; }
.page__bd #list { box-sizing:border-box; margin:0 16px; padding:0!important; background:transparent!important; }
.page__bd #list > .weui-cell, .page__bd #list > a.weui-cell, .page__bd #list .favuser { box-sizing:border-box; min-height:64px; margin-bottom:10px; padding:10px 12px; border:1px solid #d8e1ec; border-radius:8px; background:#fff!important; color:#405166; box-shadow:0 4px 14px rgba(12,27,51,.05); }
.page__bd #list .weui-cell__hd img { width:44px!important; height:44px!important; border:2px solid #edf3fa; }
.page__bd #list .weui-cell__bd p { color:#0e1b2a; font-size:14px; line-height:22px; overflow-wrap:anywhere; }
.page__bd #list .weui-cell__ft { color:#718096; font-size:12px; }
.page__bd #list .listdata-card { height:auto!important; min-height:180px; margin:0 0 12px!important; overflow:hidden; border:1px solid #d8e1ec; border-radius:8px!important; background:#fff!important; box-shadow:0 4px 14px rgba(12,27,51,.05); }
.page__bd #list .listdata-card-top, .page__bd #list .listdata-card-bottom { padding:12px 16px!important; color:#405166!important; font-size:14px!important; font-weight:500!important; }
.page__bd #list, .page__bd #list a, .page__bd #list p, .page__bd #list span { overflow-wrap:anywhere; word-break:break-word; }
.page__bd .weui-loadmore, .page__bd .weui-loadmore__tips { color:#718096!important; }
.page__bd a:focus-visible { outline:2px solid #2764ff; outline-offset:2px; }
.weui-dialog, .weui-actionsheet { border-radius:12px 12px 0 0; background:#fff; color:#405166; }
.weui-dialog__btn, .weui-actionsheet__cell { min-height:44px; color:#2764ff!important; font-size:16px; line-height:44px; }
</style>


<div class="page__bd">
    <!--{template xigua_hb:common_nav}-->
    <div class="weui-navbar border_none <!--{if $member}-->none<!--{/if}-->">
        <div class="weui-navbar border_none">
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav" class="weui-navbar__item <!--{if !$_GET[ty]}-->weui_bar__item_on<!--{/if}-->">
                <span>{lang xigua_hb:xinxi}</span>
            </a>
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav&ty=user" class="weui-navbar__item <!--{if $_GET[ty]=='user'}-->weui_bar__item_on<!--{/if}-->">
                <span>&#20851;&#27880;</span>
            </a>
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav&ty=myfans" class="weui-navbar__item <!--{if $_GET[ty]=='myfans'}-->weui_bar__item_on<!--{/if}-->">
                <span>&#31881;&#19997;</span>
            </a>
            <!--{if $_G['cache']['plugin']['xigua_hs']}-->
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav&ty=sh" class="weui-navbar__item <!--{if $_GET[ty]=='sh'}-->weui_bar__item_on<!--{/if}-->">
                <span>{lang xigua_hb:shj}</span>
            </a>
            <!--{/if}-->
            <!--{if $_G['cache']['plugin']['xigua_dh']}-->
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav&ty=dhb" class="weui-navbar__item <!--{if $_GET[ty]=='dhb'}-->weui_bar__item_on<!--{/if}-->">
                <span>{lang xigua_hb:dhb}</span>
            </a>
            <!--{/if}-->
            <!--{if $_G['cache']['plugin']['xigua_he']}-->
            <a href="$SCRITPTNAME?id=xigua_hb&ac=fav&ty=huodong" class="weui-navbar__item <!--{if $_GET[ty]=='huodong'}-->weui_bar__item_on<!--{/if}-->">
                <span>{lang xigua_hb:huodong}</span>
            </a>
            <!--{/if}-->
        </div>
    </div>

    <!--{if $_GET[ty]=='huodong'}-->
    <ul class="helist mt0" id="list" style="padding-top:0"> </ul>
    <!--{else}-->
    <div  id="list" class="weui-cells p0 border_none"></div>
    <!--{/if}-->
    <!--{template xigua_hb:loading}-->
</div>

<script>
    <!--{if $_GET[ty]=='dhb'}-->
    var loadingurl = _APPNAME+'?id=xigua_dh&ac=myshop_li&fav=1&inajax=1&page=';

    $(document).on('click', '.dh_jump', function () {
        var that = $(this);
        var jmpurl = _APPNAME +'?id=xigua_dh&ac=view&shid='+that.data('id')+(typeof _URLEXT!=='undefined'? _URLEXT : '');
        dh_jump(jmpurl);
    });
    function dh_jump(jmpurl){
        if(typeof mag !== 'undefined'){
            mag.newWin(GSITE+jmpurl);
            return false;
        }
        if(typeof sq !== 'undefined'){
            sq.urlRequest(GSITE+jmpurl);
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
    }

    <!--{elseif $_GET[ty]=='sh'}-->
    var loadingurl = _APPNAME+'?id=xigua_hs&ac=myshop_li&fav=1&inajax=1&page=';
    <!--{elseif $_GET[ty]=='myfans'}-->
    var loadingurl = _APPNAME+'?id=xigua_hb&ac=fav&fav=fans&uid=$_GET[uid]&inajax=1&pagesize=40&page=';
    $(document).on('click','.favuser', function () {
        var that = $(this);
        window.location.href=that.data('href');
    });
    <!--{elseif $_GET[ty]=='user'}-->
    var loadingurl = _APPNAME+'?id=xigua_hb&ac=fav&fav=user&uid=$_GET[uid]&inajax=1&page=';
    $(document).on('click','.favuser', function () {
        var that = $(this);
        $.modal({
            title: '{lang xigua_hb:gzgl}',
            text: that.data('username'),
            buttons: [
            { text: "{lang xigua_hb:close}", className: "default", onClick: function(){ } },
            { text: "{lang xigua_hb:qxgz}", className: "default", onClick: function(){
$.showLoading();
$.ajax({
    type: 'post',
    url: _APPNAME + '?id=xigua_hb&ac=fav&fav=user&ref=1&inajax=1',
    data: {
        'touid': that.data('uid'),
        'formhash': FORMHASH
    },
    dataType: 'xml',
    success: function(data) {
        $.hideLoading();
        if (null == data) {
            tip_common('error|' + ERROR_TIP);
            return false;
        }
        var s = data.lastChild.firstChild.nodeValue;
        tip_common(s);
    },
    error: function() {
        $.hideLoading();
    }
});
                } },
            { text: "{lang xigua_hb:ck}", onClick: function(){ window.location.href=that.data('href')} }
            ]
        });
    });
    <!--{elseif $_GET[ty]=='huodong'}-->
    var loadingurl = _APPNAME+'?id=xigua_he&ac=he_li&fav=1&inajax=1&page=';
    $(document).on('click', '.jump_he', function () {
        var that = $(this);
        var jmpurl = _APPNAME +'?id=xigua_he&ac=view&hid='+that.data('id')+(typeof _URLEXT!=='undefined'? _URLEXT : '');
        if(typeof mag !== 'undefined'){
            mag.newWin(GSITE+jmpurl);
            return false;
        }
        if(typeof wx !=='undefined'){
            if (window.__wxjs_environment === 'miniprogram') {
                wx.miniProgram.navigateTo({url:'/pages/index/index?url='+encodeURIComponent(GSITE+jmpurl)});
                return false;
            }
        }
        window.location.href = jmpurl;
        return false;
    });
    <!--{else}-->
    var loadingurl = _APPNAME+'?id=xigua_hb&ac=list_item&fav=1&inajax=1&page=';
    <!--{/if}-->
</script>
<!--{eval $tabbar=1;$ac = 'myfav';}-->
<!--{template xigua_hb:common_footer}-->
<!--{if $_GET[ty]=='sh'}-->
<!--{template xigua_hs:footer}-->
<!--{/if}-->
