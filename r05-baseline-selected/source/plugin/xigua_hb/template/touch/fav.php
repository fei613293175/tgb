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