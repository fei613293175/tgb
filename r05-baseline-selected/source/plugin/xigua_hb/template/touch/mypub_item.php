<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>

<!--{if $_GET['newac']=="mypub"}-->

<!--{loop $list $kkkk $v}-->
<!--{eval
$calltel = $v[mobile] ? "tel:$v[mobile]" : '';
if($v[weixin]):
$calltel = "javascript:lxfs_tip(this, '$v[mobile]', '$v[weixin]', '$v[id]');";
endif;
$callfull = "showfull('$v[id]', '$v[mobile]', '','', '$v[weixin]');return false;";
if($v[wancheng]):
    $calltel = "javascript:$.toast('{lang xigua_hb:xinxiwc}','error');";
    $callfull = "showfull('$v[id]', '', '','', '');return false;";
else:
    $vcatid = $v[catid];
    $vtelp  = $cats[$vcatid]['telpri'];
    if($vtelp>0 && !($_G['uid']==$v[uid] || IS_ADMINID)):
        include DISCUZ_ROOT. 'source/plugin/xigua_hb/include/c_addon.php';
        if(!$viewtels[$v[id]] && !$ishk):
            $calltel = "javascript:hb_paytel('$v[id]','$vtelp','$vcatid');";
            $callfull = "showfull('$v[id]', '', '$vtelp','$vcatid', '$v[weixin]');return false;";
        endif;
    endif;
endif;
$hidegl = $_GET[stat]!='endts'&&!$v[wancheng];
if(!$users[$v[uid]][username] && $v[realname] && $v[realname]!='-'):
    $users[$v[uid]][username] = $v[realname];
endif;
}-->


<div class="li<!--{if $v[wancheng]}--> op6<!--{/if}-->" id="li_$v[id]" style="background-color:{$v[zdcolor]};box-shadow: 0 -1px 10px 0 rgba(0,0,0,0.1);" <!--{if $v[dig_on] && $v[zdcolor]}-->style="background-color:{$v[zdcolor]};box-shadow: 0 -1px 10px 0 rgba(0,0,0,0.1);"<!--{/if}-->>



<div class="po-avt-wrap" style="box-shadow: 0 -1px 10px 0 rgba(0,0,0,0.1);">
    <a href="javascript:;">
        <img style="height: 60px;
    width: 100px;top: 75px;border-radius:2px;" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[uid]');"
             onerror="this.error=null;this.src='source/plugin/xigua_hb/static/img/zhanwei.png'" class="po-avt"
             src="$v[img_preview][0]"></a>
</div>

<div>

    <div class="listdata-card-top" style="display: flex;  margin-top:0px;  align-items: center;">
        <img src="uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1" alt="" style="width:40px;vertical-align: middle;border-radius: 50%;margin-right: 5px;">
        <span style="color:#333;font-size:18px;margin-left:5px;">$v[realname]</span>

       
    
  <a href="plugin.php?id=xigua_hb&ac=view&pubid=$v[id]">

        <!--{if $v[dig_on]}-->
            <div class="mod-lv is-star" style="background-image: linear-gradient(90deg, #f1f4fb 1%, #f1f4fb 99%);border:1px solid #2888ff;color:#2888ff;font-size:10px;margin-top:-5px;margin-left:5px;"><!--{if $v[zdword]}-->$v[zdword]<!--{else}-->{lang xigua_hb:zhiding}<!--{/if}--><!--{if $_GET['is_my'] && ($_G[uid]==$v[uid]||IS_ADMINID)}-->{lang xigua_hb:shengyu}{eval echo intval(($v['dig_endts']-TIMESTAMP)/86400);}{lang xigua_hb:day}<!--{/if}--></div><!--{/if}-->
        <!--{if $v[hb_num]>$v[hb_sendnum]}--><div class="mod-lv is-hot view_jump"  style="background-image: linear-gradient(90deg, #f1f4fb 1%, #f1f4fb 99%);border:1px solid #3167ee;color:#3167ee;font-size:10px;" data-id="$v[id]" data-stid="$v[stid]">{lang xigua_hb:hb}</div><!--{/if}-->





    </div>
</div>

<div class="po-cmt">




    <div class="po-hd cl <!--{if $cats[$v[catid]]['hidereal']}-->h30hide <!--{/if}--><!--{if $v[wancheng]}-->wxexpired1<!--{/if}-->" <!--{if $v[wancheng] && $cats[$v[catid]]['closeicon']}-->style="background-image:url({$cats[$v[catid]]['closeicon']})"<!--{/if}-->>
    <!--{if !$_GET[hb]}-->
    <!--{if (!$v[pay_status] && $v[order_id] && $v[order_id]!=-1)&& $_GET['is_my']&& !$v[display]}-->
    <a target="_blank" class="abs b-color8 mod-feed-tag main_color" style="top:.05rem" href="$SCRITPTNAME?id=xigua_hb&ac=pay&catid=$v[catid]&pubid=$v[id]"><!--{if IS_ADMINID}-->{lang xigua_hb:wei} <!--{else}-->{lang xigua_hb:pay}<!--{/if}--></a>
    <!--{else}-->
    <!--{if $v[display]}-->
    <!--{if $v[endts]<TIMESTAMP}-->

    <!--{else}-->
    <!--{if $config[listcattype]!=2}-->
    <!--{/if}-->
    <!--{/if}-->
    <!--{elseif !$v[display] && $v[reason]}-->
    <div style="width: 170px;height:80px;z-index: 9999;background-image: linear-gradient(90deg, #f1f4fb 1%, #f1f4fb 99%)!important;border:1px solid #3167ee;color:#ff9900!important;margin-top:0px;font-size:10px;text-align:left;" class="abs  mod-feed-tag main_color">
        <div style="overflow-y: scroll;height: 95%;">
        <!--{if $v[uid] == $_G['uid'] }-->
        <a class=" weui-btn_mini mt0" style="border-radius:20px;color:#fff;background: linear-gradient(135deg, #2888ff 0%, #42ccfb 100%);width:40px!important;font-size:10px;margin-top:-10px;" href="plugin.php?id=xigua_hb&ac=pub&step=3&edit=$v[id]">立即修改 </a>
        <!--{/if}-->      
            </p>
        $v[reason]
</div>
    </div>
    <!--{else}-->

    <!--{/if}-->
    <!--{/if}-->
    <!--{else}-->
    <a class="abs color-red" href="javascript:;"><i class="iconfont icon-hot-02"></i> $v[views]</a>
    <!--{/if}-->


    <!--{if !$_GET[hb]}-->
    <div class="cl pr">

        <!--{if ($_G[uid] == $v[uid]||IS_ADMINID)  }-->
        <a <!--{if $hidegl}-->style="display:none"<!--{/if}--> class="a c_opt" href="javascript:;" id="pubitem_$v[id]" data-id="$v[id]" data-uid="$v[uid]" data-wc="{$v[wancheng]}" <!--{if $v[display]&&!$v[wancheng]}-->data-canzd="1"<!--{else}-->data-hidefx="1"<!--{/if}--> <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$v[display]&&$config[red]&&!$v[wancheng]}-->data-canhb="1"<!--{/if}--> <!--{if !$v[pay_status]}-->data-catid="$v[catid]"<!--{/if}--> onclick="return showansi(this);"><!--{if IS_ADMINID}-->{lang xigua_hb:guanli0}<!--{else}-->{lang xigua_hb:guanli}<!--{/if}--></a>
        <!--{/if}-->

    </div>
    <!--{else}-->
    <!--{if ($_G[uid] == $v[uid]||IS_ADMINID)  }-->
    <a <!--{if $hidegl}-->style="display:none"<!--{/if}--> class="a c_opt" href="javascript:;" id="pubitem_$v[id]" data-id="$v[id]" data-uid="$v[uid]" data-wc="{$v[wancheng]}" <!--{if $v[display]&&!$v[wancheng]}-->data-canzd="1"<!--{else}-->data-hidefx="1"<!--{/if}--> <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$v[display]&&$config[red]&&!$v[wancheng]}-->data-canhb="1"<!--{/if}--> <!--{if !$v[pay_status]}-->data-catid="$v[catid]"<!--{/if}--> onclick="return showansi(this);"><!--{if IS_ADMINID}-->{lang xigua_hb:guanli0}<!--{else}-->{lang xigua_hb:guanli}<!--{/if}--></a>
    <!--{/if}-->

    <!--{/if}-->



  <a href="plugin.php?id=xigua_hb&ac=view&pubid=$v[id]">
    <div class="post cl">

        <div id="view_jump_$v[id]" class="view_jump mod-feed-text is-three cl" data-id="$v[id]" data-stid="$v[stid]" style="font-weight: normal;width:90%;margin-bottom:10px;font-size:15px!important;margin-top:0px;">

           <!--{if $config[customfisrt]!=2}-->{echo nl2br(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))));}<!--{/if}-->
            
            <!--{eval $distvar = '';}-->
            <!--{loop $v[vars] $vr}-->
            <!--{if $vr[type]=='location' && is_array($vr[value]) && count($vr[value])==3 && is_numeric($vr[value][2])}-->
            <!--{eval $distvar = $vr;}-->
            <!--{elseif $vr[html] && $vr[type]=='linkurl' && $vr[html]!=' '}-->
            <span class="block"><span class="main_color">{$vr[title]}{lang xigua_hb:m}</span><a href="$vr[value]">{lang xigua_hb:gdxqqdj}</a></span>
            <!--{elseif $vr[html] && $vr[type]!='pics' && $vr[html]!=' '&& $vr[value]!==''}-->
            <span class="block"><span class="main_color">{$vr[title]}{lang xigua_hb:m}</span>$vr[html]</span>
            <!--{/if}-->
            <!--{/loop}-->
            <!--{if $config[customfisrt]==2}-->{echo nl2br(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))));}<!--{/if}-->
            <!--{if 0&& $v['video'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php')}-->
            <div class="video_set"><video poster="{$v['video_cover']}" src="{$v['video']}" controls="controls"  <!--{if !IN_PROG}-->x5-playsinline webkit-playsinline playsinline x-webkit-airplay="allow"<!--{/if}-->></video></div>
            <!--{eval $v[img_preview] = array();$hidev=1;}-->
            <!--{/if}-->
            <!--{if $v[realname] && $v[realname]!='-'}--><span class="block"><span class="main_color">{lang xigua_hb:lianxi}</span>$v[realname]</span><!--{/if}-->
        </div>
        <!--{if !$cats[$v[catid]][hidereal]}-->
        <!--{if $config[zjbd]}-->
        <!--{if $vtelp>0 && $vcatid&&!$viewtels[$v[id]] && !$ishk}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="javascript:hb_paytel('$v[id]','$vtelp',$vcatid);"><i class="iconfont icon-dianhua2 f14"></i>{lang xigua_hb:boda}</a>
        <!--{elseif $v[weixin]}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="javascript:;" onclick="lxfs_tip(this, '$v[mobile]', '$v[weixin]');">{lang xigua_hb:cklxfs}</a>
        <!--{elseif $v[mobile]}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="tel:$v[mobile]"><i class="iconfont icon-dianhua2"></i>{lang xigua_hb:boda}</a>
        <!--{/if}-->
        <!--{else}-->

        <!--{/if}-->
        <!--{/if}-->
        <!--{if $v[goodname]}-->
        <!--{eval $goodinfo = unserialize($v[goodinfo]);}-->
        <a class="weui-flex hs_inner" target="_blank" href="$SCRITPTNAME?id=xigua_sp&ac=view&gid={$v[goodid]}">
            <img src="{$goodinfo[fengmian]}">
            <div class="hs_titloc cl" style="width:calc(100% - 3.5rem)">
                <h3 style="line-height: 1.4rem;position: relative;height: 1.25rem;" class="da">{$goodinfo[title]}</h3>
                <b class="color-red2">{$goodinfo[dprice]}{lang xigua_hb:yuan}</b>
            </div>
        </a>
        <!--{elseif $v[sh]}-->
        <a class="weui-flex hs_inner sh_jump" target="_blank" href="javascript:;" data-id="{$v[sh]['shid']}">
            <img src="{$v[sh][logo]}">
            <div class="hs_titloc">
                <h3 class="da">{$v[sh][name]}</h3>
                <span><i class="iconfont icon-coordinates_fill f14 vm"></i>{$v[sh][addr]}</span>
            </div>
        </a>
        <!--{elseif 0&&$v[sh]&&$v[img_preview]}-->
        <a class="hs_inner_loc da" href="javascript:;" onclick="hb_jump('$SCRITPTNAME?id=xigua_hs&ac=view&shid={$v[sh][shid]}');"><i class="iconfont icon-coordinates_fill f14 "></i>{$v[sh][addr]}</a>
        <!--{elseif $distvar && is_array($distvar)}-->
        <a class="hs_inner_loc da" style="display:block" href="javascript:;" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=view&pubid=$v[id]');"><i class="iconfont icon-coordinates_fill f14 "></i>{$distvar[value][0]} <!--{if $v[distance]}--><span class="diskm">{lang xigua_hb:juwo}{$v[distance]}</span><!--{/if}--></a>
        <!--{/if}-->
    </div>
    <!--{if !$_GET[hb]}-->
    <!--{else}-->
    <!--{if ($_G[uid] == $v[uid]||IS_ADMINID)  }-->
    <a <!--{if $hidegl}-->style="display:none"<!--{/if}--> class="a c_opt" href="javascript:;" id="pubitem_$v[id]" data-id="$v[id]" data-uid="$v[uid]" data-wc="{$v[wancheng]}" <!--{if $v[display]&&!$v[wancheng]}-->data-canzd="1"<!--{else}-->data-hidefx="1"<!--{/if}--> <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$v[display]&&$config[red]&&!$v[wancheng]}-->data-canhb="1"<!--{/if}--> <!--{if !$v[pay_status]}-->data-catid="$v[catid]"<!--{/if}--> onclick="return showansi(this);"><!--{if IS_ADMINID}-->{lang xigua_hb:guanli0}<!--{else}-->{lang xigua_hb:guanli}<!--{/if}--></a>
    <!--{/if}-->
    <div class="weui-flex pr">
        <div class="weui-flex__item"><span class=" color-red" ><i class="iconfont icon-hongbao2 f18"></i> <span class="f12">&yen;</span><span class="f20">$v[hb_money]</span><span class="f12">{lang xigua_hb:yuan}</span></span></div>
        <!--{if $v[hb_num]>$v[hb_sendnum]}--><div class="color-red f12 hlisttip">{lang xigua_hb:qiangjinxingzhong}</div><!--{else}--><div class="color-gray f12 hlisttip">{lang xigua_hb:qaingwan}</div><!--{/if}-->
    </div>
    <!--{/if}-->
</div>





<!--{if !$_GET[hb] && !$_GET['is_my']}-->
<div class="r" id="r_$v[id]" <!--{if !$v[zanlist]&&!$v[commentlist]}-->style="display:none"<!--{/if}-->></div>
<div class="cmt-wrap" id="cmt_wrap_$v[id]" <!--{if !$v[zanlist]&&!$v[commentlist]}-->style="display:none"<!--{/if}-->>
<div class="like cl">
    <span class="likenum c9"><em id="praises_$v[id]">$v[votes]</em>{lang xigua_hb:votes}</span>
    <span class="likeuser z" id="praise_list_$v[id]">
<!--{loop $v[zanlist] $_v}-->
<a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$_v[uid]"><img class="uavatar" src="{echo avatar($_v[uid], 'middle', true);}" onerror="$(this).parent().remove();this.error=null" /></a>
        <!--{/loop}-->
</span>
</div>
<!--{if $config[showcomment]}-->
<div class="cmt-list border_top" id="cmt_list_$v[id]" data-id="$v[id]" <!--{if !$v[commentlist]}-->style="display:none"<!--{/if}-->>
<!--{eval $comment_simple = 1;}-->
<!--{eval $comments = $v[commentlist];}-->
<!--{template xigua_hb:comment_li}-->
<!--{if $v[comments]>5}-->
<p class="view_jump" data-id="$v[id]" data-stid="$v[stid]">{lang xigua_hb:viewall}$v[comments]{lang xigua_hb:tiaopinglun}</p>
<!--{/if}-->
</div>
<!--{/if}-->
</div>

<!--{/if}-->
<!--{if ($_G[uid] == $v[uid]||IS_ADMINID) && ($v[display]||$_GET[is_admin]) && $hidegl }-->
<div class="po-act" style="display:none;position:relative;top:.25rem;width:100%;padding-left:0;">


    <!--{if $v[dig_on]}--><div class="mod-lv is-star"><!--{if $v[zdword]}-->$v[zdword]<!--{else}-->{lang xigua_hb:zhiding}<!--{/if}--><!--{if $_GET['is_my'] && ($_G[uid]==$v[uid]||IS_ADMINID)}-->{lang xigua_hb:shengyu}{eval echo intval(($v['dig_endts']-TIMESTAMP)/86400);}{lang xigua_hb:day}<!--{/if}--></div><!--{/if}-->
    <!--{if $v[hb_num]>$v[hb_sendnum]}--><div class="mod-lv is-hot view_jump" data-id="$v[id]" data-stid="$v[stid]">{lang xigua_hb:hb}</div><!--{/if}-->



    <span style="float:right;margin-left:5px;color: #c6c6c6;font-size: 0.6rem;margin-top: 4px;"><em>浏览{echo hb_trans($v[views])}</em></span>

    <span style="float:right;color: #c6c6c6;font-size: 0.6rem;margin-top: 4px;">$v[time_u]<!--{if $v[refresh_times]}-->{lang xigua_hb:shuaxin}<!--{else}-->{lang xigua_hb:fabu0}<!--{/if}--></span>



</div>
<!--{elseif ($_G[uid] == $v[uid]||IS_ADMINID) && !$v[pay_status] && !$v[display]}-->
<div class="po-act">
    <a class="weui-btn weui-btn_mini p0 mt0" href="$SCRITPTNAME?id=xigua_hb&ac=pay&catid=$v[catid]&pubid=$v[id]">{lang xigua_hb:pay1} </a>
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="$('#pubitem_{$v[id]}').trigger('click');">{lang xigua_hb:more}</a>
</div>
<!--{/if}-->
</div>


            <div class="listdata-card-bottom" style="position:relative;padding-top:0px;margin-bottom:1px;color: #f1f1f1;">

               <span class="" style="vertical-align:middle;margin-right:5px;color: #000;font-size: 14px; font-weight:400;margin-top:35px!important;">
    <img style="width:21px;height:21px;margin-top:-3px;vertical-align: middle;" src="https://img.imehui.com/20240714/17209718226693f22e6bc51.png">
    已获得 {$v[views]} 流量
</span>

                           
                <span  style="padding: 2px 5px;float:right;font-weight: normal;font-size:14px;color: #ff7b00; overflow: hidden;">
                             {$v[time_u]}更新排名
                            </span>
            </div>



<div class="opqy" style="margin-top:0px;">
    <a class="weui-btn weui-btn_mini p0 mt0  btn-new01" style="border:1px solid #fff0;vertical-align:middle;border-radius:20px;background: linear-gradient(135deg, #ff7b00, #e63946);color:#fff!important;margin-left:5px;" href="javascript:;" onclick="hb_dig('$v[id]');"><!--{if $v[zdword]}-->$v[zdword]<!--{else}-->置顶<!--{/if}--></a>
    <a class="weui-btn weui-btn_mini p0 mt0  btn-new01" style="border:1px solid #fff0;vertical-align:middle; background: linear-gradient(135deg, #ff7b00, #e63946); color:#fff!important;" href="javascript:;" onclick="hb_shuaxin('$v[id]','$cats[$v[catid]][shuxin]');">刷新</a>
   
    <a  style="float: right" class=" weui-btn weui-btn_mini p0 mt0 btn-new01" style="background-image: linear-gradient(90deg, #3f3cff 1%, #5468ff 99%)!important;color:#fff!important;"  href="javascript:;" onclick="$('#pubitem_{$v[id]}').trigger('click');">更多管理</a>
</div>


</div>

<!--{template xigua_hb:ad}-->
<!--{/loop}-->
<!--{else}-->
<!--{loop $list $kkkk $v}-->
<!--{eval
$calltel = $v[mobile] ? "tel:$v[mobile]" : '';
if($v[weixin]):
$calltel = "javascript:lxfs_tip(this, '$v[mobile]', '$v[weixin]', '$v[id]');";
endif;
$callfull = "showfull('$v[id]', '$v[mobile]', '','', '$v[weixin]');return false;";
if($v[wancheng]):
    $calltel = "javascript:$.toast('{lang xigua_hb:xinxiwc}','error');";
    $callfull = "showfull('$v[id]', '', '','', '');return false;";
else:
    $vcatid = $v[catid];
    $vtelp  = $cats[$vcatid]['telpri'];
    if($vtelp>0 && !($_G['uid']==$v[uid] || IS_ADMINID)):
        include DISCUZ_ROOT. 'source/plugin/xigua_hb/include/c_addon.php';
        if(!$viewtels[$v[id]] && !$ishk):
            $calltel = "javascript:hb_paytel('$v[id]','$vtelp','$vcatid');";
            $callfull = "showfull('$v[id]', '', '$vtelp','$vcatid', '$v[weixin]');return false;";
        endif;
    endif;
endif;
$hidegl = $_GET[stat]!='endts'&&!$v[wancheng];
if(!$users[$v[uid]][username] && $v[realname] && $v[realname]!='-'):
    $users[$v[uid]][username] = $v[realname];
endif;
}--><div class="li<!--{if $v[wancheng]}--> op6<!--{/if}-->" id="li_$v[id]" <!--{if $v[dig_on] && $v[zdcolor]}-->style="background-color:{$v[zdcolor]}"<!--{/if}-->>
<div class="po-avt-wrap">
    <a href="javascript:;"><img onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[uid]');" onerror="this.error=null;this.src='source/plugin/xigua_hb/static/img/zhanwei.png'" class="po-avt" src="{avatar($v[uid], 'middle', true)}"></a>
</div>
<div class="po-cmt">
    <div class="po-hd cl <!--{if $cats[$v[catid]]['hidereal']}-->h30hide <!--{/if}--><!--{if $v[wancheng]}-->wxexpired1<!--{/if}-->" <!--{if $v[wancheng] && $cats[$v[catid]]['closeicon']}-->style="background-image:url({$cats[$v[catid]]['closeicon']})"<!--{/if}-->>
    <!--{if !$_GET[hb]}-->
    <!--{if (!$v[pay_status] && $v[order_id] && $v[order_id]!=-1)&& $_GET['is_my']&& !$v[display]}-->
    <a target="_blank" class="abs b-color8 mod-feed-tag main_color" style="top:.05rem" href="$SCRITPTNAME?id=xigua_hb&ac=pay&catid=$v[catid]&pubid=$v[id]"><!--{if IS_ADMINID}-->{lang xigua_hb:wei} <!--{else}-->{lang xigua_hb:pay}<!--{/if}--></a>
    <!--{else}-->
    <!--{if $v[display]}-->
    <!--{if $v[endts]<TIMESTAMP}-->
    <a class="abs" style="color:#999" href="javascript:;">$cats[$v[catid]][name]</a>
    <!--{else}-->
    <!--{if $config[listcattype]!=2}-->
    <a target="_blank" class="abs <!--{if $_GET[pinstyle]}-->pstyle1<!--{/if}-->" href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$v[catid]">$cats[$v[catid]][name]</a>
    <!--{/if}-->
    <!--{/if}-->
    <!--{elseif !$v[display] && $v[reason]}-->
    <div style="max-width: 7rem;z-index: 501;background-image: linear-gradient(90deg, #f1f4fb 1%, #f1f4fb 99%)!important;border:1px solid #3167ee;color:#3167ee;text-align: left;" class="abs  mod-feed-tag main_color">$v[reason]

        <!--{if $v[uid] == $_G['uid'] }-->
        <a class=" weui-btn_mini mt0" style="border-radius:5px;color:#fff;background-color:red!important;width:50px!important;font-size:12px;" href="plugin.php?id=xigua_hb&ac=pub&step=3&edit=$v[id]">立即修改 </a>
        <!--{/if}-->


    </div>

    <!--{else}-->
    <a class="abs b-color8 mod-feed-tag main_color">{lang xigua_hb:daishen} $cats[$v[catid]][name]</a>
    <!--{/if}-->
    <!--{/if}-->
    <!--{else}-->
    <a class="abs color-red" href="javascript:;"><i class="iconfont icon-hot-02"></i> $v[views]</a>
    <!--{/if}-->

    <div class="usr-name mod-usr-name lv cl">
        <!--{if $v[ipaddr]}-->
        <div class="ipn">
            <span class="name" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[uid]');">$users[$v[uid]][username]</span>
            <em class="ipadr"><!--{if $_G['cache']['hb_ext_config']['ipprefix']}-->$_G['cache']['hb_ext_config']['ipprefix']<!--{else}-->&#26469;&#33258;<!--{/if}-->$v[ipaddr]</em>
            <!--{else}-->
            <span class="name" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[uid]');">$users[$v[uid]][username]</span>
            <!--{/if}-->
            <!--{if $veris1[$v[uid]]}--><!--{if $_G[cache][plugin][xigua_hr][grtb]}--><img class="rzimg vm" src="$_G[cache][plugin][xigua_hr][grtb]" /> <!--{else}--><i class="iconfont icon-erified color-forest vm"></i><!--{/if}--><!--{/if}-->
            <!--{if $veris2[$v[uid]]}--><!--{if $_G[cache][plugin][xigua_hr][qytb]}--><img class="rzimg vm" src="$_G[cache][plugin][xigua_hr][qytb]" /> <!--{else}--><i class="iconfont icon-qiyerenzheng color-dropbox vm"></i><!--{/if}--><!--{/if}-->
            <!--{if $bao[$v[uid]]}--><!--{if !$bao[$v[uid]][icon]}--><!--{eval $bao[$v[uid]][icon] = $_G[cache][plugin][xigua_hr][bzjtb];}--><!--{/if}--><!--{if $bao[$v[uid]][icon]}--><img class="rzimg vm" src="$bao[$v[uid]][icon]" /> <!--{else}--><i class="iconfont icon-baozhengjinmoshi color-good vm" style="font-size:19px"></i><!--{/if}--><!--{if $_G[cache][plugin][xigua_hr][bzjed]}--><span class="f13 main_color">{$_G[cache][plugin][xigua_hr][lbbzjqz]}{$bao[$v[uid]][price]}{lang xigua_hb:yuan}</span><!--{/if}--><!--{/if}-->
            <!--{if $v[dig_on]}--><div class="mod-lv is-star"><!--{if $v[zdword]}-->$v[zdword]<!--{else}-->{lang xigua_hb:zhiding}<!--{/if}--><!--{if $_GET['is_my'] && ($_G[uid]==$v[uid]||IS_ADMINID)}-->{lang xigua_hb:shengyu}{eval echo intval(($v['dig_endts']-TIMESTAMP)/86400);}{lang xigua_hb:day}<!--{/if}--></div><!--{/if}-->
            <!--{if $v[hb_num]>$v[hb_sendnum]}--><div class="mod-lv is-hot view_jump" data-id="$v[id]" data-stid="$v[stid]">{lang xigua_hb:hb}</div><!--{/if}-->
            <!--{if $v[ipaddr]}--></div><!--{/if}-->
    </div>

    <div class="post cl">

        <!--{if array_filter($v[tags])}-->
        <div class="cl mt8 item_tags view_jump" data-id="$v[id]" data-stid="$v[stid]">
            <!--{loop $v[tags] $k $tag}-->
            <!--{if $tag}--><span class="mod-feed-tag b-color{$k}">$tag</span><!--{/if}-->
            <!--{/loop}-->
        </div>
        <!--{/if}-->
        <div id="view_jump_$v[id]" class="view_jump mod-feed-text is-three cl" data-id="$v[id]" data-stid="$v[stid]">
            <!--{if $config[listcattype]==2}--><a class="bftag b-color0 main_color" href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$v[catid]">$cats[$v[catid]][name]</a><!--{/if}-->
            <!--{if $config[customfisrt]!=2}-->{echo nl2br(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))));}<!--{/if}-->
            <!--{eval $distvar = '';}-->
            <!--{loop $v[vars] $vr}-->
            <!--{if $vr[type]=='location' && is_array($vr[value]) && count($vr[value])==3 && is_numeric($vr[value][2])}-->
            <!--{eval $distvar = $vr;}-->
            <!--{elseif $vr[html] && $vr[type]=='linkurl' && $vr[html]!=' '}-->
            <span class="block"><span class="main_color">{$vr[title]}{lang xigua_hb:m}</span><a href="$vr[value]">{lang xigua_hb:gdxqqdj}</a></span>
            <!--{elseif $vr[html] && $vr[type]!='pics' && $vr[html]!=' '&& $vr[value]!==''}-->
            <span class="block"><span class="main_color">{$vr[title]}{lang xigua_hb:m}</span>$vr[html]</span>
            <!--{/if}-->
            <!--{/loop}-->
            <!--{if $config[customfisrt]==2}-->{echo nl2br(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))));}<!--{/if}-->
            <!--{if 0&& $v['video'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php')}-->
            <div class="video_set"><video poster="{$v['video_cover']}" src="{$v['video']}" controls="controls"  <!--{if !IN_PROG}-->x5-playsinline webkit-playsinline playsinline x-webkit-airplay="allow"<!--{/if}-->></video></div>
            <!--{eval $v[img_preview] = array();$hidev=1;}-->
            <!--{/if}-->
            <!--{if $v[realname] && $v[realname]!='-'}--><span class="block"><span class="main_color">{lang xigua_hb:lianxi}</span>$v[realname]</span><!--{/if}-->
        </div>
        <!--{if !$cats[$v[catid]][hidereal]}-->
        <!--{if $config[zjbd]}-->
        <!--{if $vtelp>0 && $vcatid&&!$viewtels[$v[id]] && !$ishk}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="javascript:hb_paytel('$v[id]','$vtelp',$vcatid);"><i class="iconfont icon-dianhua2 f14"></i>{lang xigua_hb:boda}</a>
        <!--{elseif $v[weixin]}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="javascript:;" onclick="lxfs_tip(this, '$v[mobile]', '$v[weixin]');">{lang xigua_hb:cklxfs}</a>
        <!--{elseif $v[mobile]}-->
        <a class="mb8 h30 weui-btn weui-btn_mini" href="tel:$v[mobile]"><i class="iconfont icon-dianhua2"></i>{lang xigua_hb:boda}</a>
        <!--{/if}-->
        <!--{else}-->
        <a class="showfull main_color" id="showfull_$v[id]" onclick="{$callfull}">{lang xigua_hb:quanfen}</a>
        <!--{/if}-->
        <!--{/if}-->
        <div class="cl feed-preview-pic">
            <!--{if $v['video']&&!$hidev && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php')}-->
            <span class="imgloading view_jump" data-id="$v[id]">
    <em class="emvbg" style="background-image:url({$v['video_cover']})"></em><em class="emvdo"></em>
</span><!--{/if}-->
            <!--{eval $img_preview_cnt = count($v[img_preview])-1;}-->
            <!--{loop $v[img_preview] $k $img}--><!--{eval
$showzhang = ($k==$img_preview_cnt && $v[img_count]>3);
if($v[video]&&$img_preview_cnt==2):
    $showzhang = $k==1;
    if($k==$img_preview_cnt):
        continue;
    endif;
endif;
$img = $GLOBALS_QN ? $img.'?imageView2/1/q/80' : ( $GLOBALS_OSS ? $img.'?x-oss-process=image/resize,h_640,w_640/format,jpg/quality,q_80' : $img);
 }--><span <!--{if $config[picin]}-->class="imgloading view_jump" data-id="$v[id]" data-stid="$v[stid]"<!--{else}-->class="imgloading"<!--{/if}-->><img src="$img" onerror="this.error=null;$(this).parent().remove();">
            <!--{if $showzhang&&$v[img_count]>1}--><em class="num">$v[img_count]{lang xigua_hb:zhang}</em><!--{/if}-->
            </span>
            <!--{/loop}-->
        </div>
        <!--{if $v[goodname]}-->
        <!--{eval $goodinfo = unserialize($v[goodinfo]);}-->
        <a class="weui-flex hs_inner" target="_blank" href="$SCRITPTNAME?id=xigua_sp&ac=view&gid={$v[goodid]}">
            <img src="{$goodinfo[fengmian]}">
            <div class="hs_titloc cl" style="width:calc(100% - 3.5rem)">
                <h3 style="line-height: 1.4rem;position: relative;height: 1.25rem;" class="da">{$goodinfo[title]}</h3>
                <b class="color-red2">{$goodinfo[dprice]}{lang xigua_hb:yuan}</b>
            </div>
        </a>
        <!--{elseif $v[sh]}-->
        <a class="weui-flex hs_inner sh_jump" target="_blank" href="javascript:;" data-id="{$v[sh]['shid']}">
            <img src="{$v[sh][logo]}">
            <div class="hs_titloc">
                <h3 class="da">{$v[sh][name]}</h3>
                <span><i class="iconfont icon-coordinates_fill f14 vm"></i>{$v[sh][addr]}</span>
            </div>
        </a>
        <!--{elseif 0&&$v[sh]&&$v[img_preview]}-->
        <a class="hs_inner_loc da" href="javascript:;" onclick="hb_jump('$SCRITPTNAME?id=xigua_hs&ac=view&shid={$v[sh][shid]}');"><i class="iconfont icon-coordinates_fill f14 "></i>{$v[sh][addr]}</a>
        <!--{elseif $distvar && is_array($distvar)}-->
        <a class="hs_inner_loc da" style="display:block" href="javascript:;" onclick="hb_jump('$SCRITPTNAME?id=xigua_hb&ac=view&pubid=$v[id]');"><i class="iconfont icon-coordinates_fill f14 "></i>{$distvar[value][0]} <!--{if $v[distance]}--><span class="diskm">{lang xigua_hb:juwo}{$v[distance]}</span><!--{/if}--></a>
        <!--{/if}-->
    </div>
    <!--{if !$_GET[hb]}-->
    <div class="cl pr">
        <p class="time">
            <span><em>{echo hb_trans($v[views])}</em>{lang xigua_hb:liulandot}</span>
            <span><em>{echo hb_trans($v[shares])}</em>{lang xigua_hb:fenxiangdot}</span>
            <!--{if $v[dig_on] && $v[dig_crts]}-->
            <span>{echo date('Y-m-d',$v[dig_crts])}{lang xigua_hb:zhiding}</span>
            <!--{else}-->
            <span>$v[time_u]<!--{if $v[refresh_times]}-->{lang xigua_hb:shuaxin}<!--{else}-->{lang xigua_hb:fabu0}<!--{/if}--></span>
            <!--{/if}-->
            <!--{if $_G[uid]==$v[uid]||IS_ADMINID }--><span>{echo date('Y/m/d', $v[endts])}&#21040;&#26399;</span><!--{/if}-->
        </p>
        <!--{if ($_G[uid] == $v[uid]||IS_ADMINID)  }-->
        <a <!--{if $hidegl}-->style="display:none"<!--{/if}--> class="a c_opt" href="javascript:;" id="pubitem_$v[id]" data-id="$v[id]" data-uid="$v[uid]" data-wc="{$v[wancheng]}" <!--{if $v[display]&&!$v[wancheng]}-->data-canzd="1"<!--{else}-->data-hidefx="1"<!--{/if}--> <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$v[display]&&$config[red]&&!$v[wancheng]}-->data-canhb="1"<!--{/if}--> <!--{if !$v[pay_status]}-->data-catid="$v[catid]"<!--{/if}--> onclick="return showansi(this);"><!--{if IS_ADMINID}-->{lang xigua_hb:guanli0}<!--{else}-->{lang xigua_hb:guanli}<!--{/if}--></a>
        <!--{/if}-->
        <!--{if $hidegl}--><i class="c-icon iconfont icon-qunawanhuifu" id="c_icon_$v[id]" <!--{if $v[dig_on] && $v[zdcolor]}-->style="background-color:{$v[zdcolor]}"<!--{/if}-->></i><!--{/if}-->
        <div class="touch-panel animated opannel">
            <div class="touch-panel-c weui-flex">
                <a href="javascript:void(0)" class="weui-flex__item praise" data-id="$v[id]" data-href="$SCRITPTNAME?id=xigua_hb&ac=misc&do=vote&pubid=$v[id]&formhash={FORMHASH}"><i id="praise_$v[id]" class="iconfont <!--{if $vots[$v[id]]}-->icon-jinlingyingcaiwangtubiao24<!--{else}-->icon-jinlingyingcaiwangtubiao44<!--{/if}-->"></i>{lang xigua_hb:votes}</a>
                <!--{if $calltel && !$cats[$v[catid]][hidereal]}--><a href="{$calltel}" class="weui-flex__item"><i class="icon-dianhua2 iconfont"></i>{lang xigua_hb:tel}</a><!--{/if}-->
                <!--{if $config[showcomment]}--><a href="javascript:void(0)" class="weui-flex__item comment" id="comment_$v[id]" data-id="$v[id]"><i class="icon-xiaoxi iconfont"></i>{lang xigua_hb:comments}</a><!--{/if}-->
                <!--{if $config[showsixin]}--><a href="javascript:void(0)" data-id="$v[id]" data-uid="{$v[uid]}" class="weui-flex__item comment_to"><i class="icon-sixin2 iconfont"></i>{lang xigua_hb:sixin}</a><!--{/if}-->
            </div>
        </div>
    </div>
    <!--{else}-->
    <!--{if ($_G[uid] == $v[uid]||IS_ADMINID)  }-->
    <a <!--{if $hidegl}-->style="display:none"<!--{/if}--> class="a c_opt" href="javascript:;" id="pubitem_$v[id]" data-id="$v[id]" data-uid="$v[uid]" data-wc="{$v[wancheng]}" <!--{if $v[display]&&!$v[wancheng]}-->data-canzd="1"<!--{else}-->data-hidefx="1"<!--{/if}--> <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$v[display]&&$config[red]&&!$v[wancheng]}-->data-canhb="1"<!--{/if}--> <!--{if !$v[pay_status]}-->data-catid="$v[catid]"<!--{/if}--> onclick="return showansi(this);"><!--{if IS_ADMINID}-->{lang xigua_hb:guanli0}<!--{else}-->{lang xigua_hb:guanli}<!--{/if}--></a>
    <!--{/if}-->
    <div class="weui-flex pr">
        <div class="weui-flex__item"><span class=" color-red" ><i class="iconfont icon-hongbao2 f18"></i> <span class="f12">&yen;</span><span class="f20">$v[hb_money]</span><span class="f12">{lang xigua_hb:yuan}</span></span></div>
        <!--{if $v[hb_num]>$v[hb_sendnum]}--><div class="color-red f12 hlisttip">{lang xigua_hb:qiangjinxingzhong}</div><!--{else}--><div class="color-gray f12 hlisttip">{lang xigua_hb:qaingwan}</div><!--{/if}-->
    </div>
    <!--{/if}-->
</div>

<!--{if !$_GET[hb] && !$_GET['is_my']}-->
<div class="r" id="r_$v[id]" <!--{if !$v[zanlist]&&!$v[commentlist]}-->style="display:none"<!--{/if}-->></div>
<div class="cmt-wrap" id="cmt_wrap_$v[id]" <!--{if !$v[zanlist]&&!$v[commentlist]}-->style="display:none"<!--{/if}-->>
<div class="like cl">
    <span class="likenum c9"><em id="praises_$v[id]">$v[votes]</em>{lang xigua_hb:votes}</span>
    <span class="likeuser z" id="praise_list_$v[id]">
<!--{loop $v[zanlist] $_v}-->
<a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$_v[uid]"><img class="uavatar" src="{echo avatar($_v[uid], 'middle', true);}" onerror="$(this).parent().remove();this.error=null" /></a>
        <!--{/loop}-->
</span>
</div>
<!--{if $config[showcomment]}-->
<div class="cmt-list border_top" id="cmt_list_$v[id]" data-id="$v[id]" <!--{if !$v[commentlist]}-->style="display:none"<!--{/if}-->>
<!--{eval $comment_simple = 1;}-->
<!--{eval $comments = $v[commentlist];}-->
<!--{template xigua_hb:comment_li}-->
<!--{if $v[comments]>5}-->
<p class="view_jump" data-id="$v[id]" data-stid="$v[stid]">{lang xigua_hb:viewall}$v[comments]{lang xigua_hb:tiaopinglun}</p>
<!--{/if}-->
</div>
<!--{/if}-->
</div>

<!--{/if}-->
<!--{if ($_G[uid] == $v[uid]||IS_ADMINID) && ($v[display]||$_GET[is_admin]) && $hidegl }-->
<div class="po-act" style="position:relative;top:.25rem;width:100%;padding-left:0">
    <!--{if $config['digprices']&&$dig_prices}-->
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="hb_dig('$v[id]');"><!--{if $v[zdword]}-->$v[zdword]<!--{else}-->{lang xigua_hb:zhiding}<!--{/if}--></a>
    <!--{/if}-->
    <!--{if (!$v[hb_num]||$v[hb_num]==$v[hb_sendnum])&&$config[red]}-->
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="hb_hbchoice('$v[id]');">{lang xigua_hb:hb}</a>
    <!--{/if}-->
    <!--{if $config['refresh']>0 && !$needsafe}-->
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="hb_shuaxin('$v[id]','$cats[$v[catid]][shuxin]');">{lang xigua_hb:shuaxin}</a>
    <!--{/if}-->
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="$('#pubitem_{$v[id]}').trigger('click');">{lang xigua_hb:more}</a>
</div>
<!--{elseif ($_G[uid] == $v[uid]||IS_ADMINID) && !$v[pay_status] && !$v[display]}-->
<div class="po-act">
    <a class="weui-btn weui-btn_mini p0 mt0" href="$SCRITPTNAME?id=xigua_hb&ac=pay&catid=$v[catid]&pubid=$v[id]">{lang xigua_hb:pay1} </a>
    <a class="weui-btn weui-btn_mini p0 mt0" href="javascript:;" onclick="$('#pubitem_{$v[id]}').trigger('click');">{lang xigua_hb:more}</a>
</div>
<!--{/if}-->
</div>
</div>
<!--{template xigua_hb:ad}-->
<!--{/loop}-->



<!--{/if}-->



