<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{if strpos($_SERVER['HTTP_REFERER'], 'xigua_jy')!==false||$_GET['from']=='jy'}-->
<!--{eval $jy_config = $_G['cache']['plugin']['xigua_jy'];$_GET['from']='jy';
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $jy_config['dftcolor'];}-->
<!--{template xigua_jy:header}--><link rel="stylesheet" href="source/plugin/xigua_lt/static/lt.css?{VERHASH}" />
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_es')!==false||$_GET['from']=='es'}-->
<!--{eval $es_config = $_G['cache']['plugin']['xigua_es'];$_GET['from']='es';
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $es_config['dftcolor'];}-->
<!--{template xigua_es:header}--><link rel="stylesheet" href="source/plugin/xigua_lt/static/lt.css?{VERHASH}" />
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_job')!==false||$_GET[from]=='job'}-->
<!--{eval $job_config = $_G['cache']['plugin']['xigua_job'];$_GET['from']='job';
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $job_config['dftcolor'];
}--><!--{template xigua_job:header}--><link rel="stylesheet" href="source/plugin/xigua_lt/static/lt.css?{VERHASH}" />
<!--{else}-->
<!--{template xigua_lt:header}-->
<!--{/if}-->
<div class="page__bd" style="margin-top:70px;  background-color: #f8fafc!important; /* 浅色背景 */">
    <!--{template xigua_hb:common_nav}-->


<!--{if $newindex_list[99]}-->
<div class="swipe cl" data-speed="5fff">
    <div class="swipe-wrap" style="margin:35px;">
        <!--{loop $newindex_list[99] $__k $__v}-->
        <div><a href="$__v[adlink]"><img  src="$__v[icon]"></a></div>
        <!--{/loop}-->
    </div>
    <nav class="cl bullets bullets1">
        <ul class="position">
            <!--{loop $newindex_list[99] $__k $__v}-->
            <li <!--{if $__k==0}-->class="current"<!--{/if}-->></li>
            <!--{/loop}-->
        </ul>
    </nav>
</div>
<!--{/if}-->
<!--{loop $newindex_list $_k $_v}--><!--{if $_k!=99&&$_k!=7}-->
<div class="content-bottom" style="margin:15px;border-radius:5px;">
<!--{loop $_v $__k $__v}-->
    <a href="$__v[adlink]" class="menu-item">
        <div class="taro-img menu-img">
            <img style="width:30px;height:30px;margin-left:3px;" class="taro-img__mode-scaletofill" src="$__v[icon]">
        </div>
        <span class="menu-text " style="font-weight:400;font-size:30px;margin-top:-10px;margin-left:-5px;color:#1e293b!important;">$__v[name]</span>
    </a>
<!--{/loop}-->
</div>
<!--{elseif $_k==7}-->
<div class="weui-cells comment_ul_users bgf mt0 border_none" style="margin-bottom:.5rem">
    <!--{loop $_v $__k $__v}-->
    <a class="weui-cell  weui-cell_access " href="$__v[adlink]">
        <div class="weui-cell__hd">
            <img class="avatar_cert" src="{$__v[icon]}" alt="">
        </div>
        <div class="weui-cell__bd">
            <p class="c3 f16" style="font-weight:400;color:#1e293b!important;">$__v[name]</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
    <!--{/loop}-->
</div>
<!--{/if}-->
<!--{/loop}-->
<!--{if $top_users}-->
<div class="weui-cells comment_ul_users bgf mt0 border_none" style="margin-bottom:.5rem;  background-color: #f8fafc!important; /* 浅色背景 */">
    <!--{loop $top_users $_k $_vusers}-->
    <a class="weui-cell  weui-cell_access" href="$SCRITPTNAME?id=xigua_lt&ac=chat&touid={$_vusers[uid]}">
        <div class="weui-cell__hd">
            <img class="avatar_cert" src="uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1" alt="">
        </div>
        <div class="weui-cell__bd" style="margin-bottom:.5rem;  background-color: #f8fafc!important; /* 浅色背景 */">
            <p class="c3 f16" style="font-weight:400;color:#1e293b!important;">$_vusers[username]</p>
        </div>
        <div class="weui-cell__ft" style="margin-bottom:.5rem;  background-color: #f8fafc!important; /* 浅色背景 */"></div>
    </a>
    <!--{/loop}-->
</div>
<!--{/if}-->

    <div id="list" class="weui-panel__bd comment_ul p0 bgf" style="margin-top: 0px; background-color: #f8fafc!important; /* 浅色背景 */"></div>
    <!--{template xigua_hb:loading}-->
    
    <div style="  background-color: #f8fafc!important; height:500px;"> </div>
    
</div>
<div style="  background-color: #f8fafc!important;">
<script>
    var loadingurl = _APPNAME+'?id=xigua_lt&ac=chats_li&hidezw=1&inajax=1&type=$_GET[type]&from=$_GET[from]&multi=1&pagetype=page&page=';
    var loadingCallback = function () {
        $('.weui-cell_swiped').swipeout();
    };
    $(document).on('click','.delete-swipeout', function () {
        var that = $(this);
        that.parents('.weui-cell').remove();
        $.ajax({
            type: 'post',
            url: '$SCRITPTNAME?id=xigua_lt&ac=delchats&inajax=1',
            data:{formhash:'{FORMHASH}', touid : that.data('touid'), authorid : that.data('authorid')},
            dataType: 'xml',
            success: function (data) {
                $.hideLoading();
                if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                var s = data.lastChild.firstChild.nodeValue;
                tip_common(s);
            },
            error: function () {
                $.hideLoading();
            }
        });
    });
</script>
</div>
<!--{if $_GET[from]=='jy'}-->

<!--{template xigua_jy:footer}-->
<!--{elseif $_GET[from]=='es'}-->

<!--{template xigua_es:footer}-->
<!--{elseif $_GET[from]=='job'}-->

<!--{template xigua_job:footer}-->
<!--{else}-->

<!--{template xigua_lt:footer}-->
<!--{/if}-->