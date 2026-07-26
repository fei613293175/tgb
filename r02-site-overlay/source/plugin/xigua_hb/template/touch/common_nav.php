<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{if $ac=='index' && $config[showheader]}--><!--{eval $hide_nav = 0;}--><!--{/if}-->
<!--{if IN_PROG && ($ac=='index'||$ac=='view2'||$ac=='album')}--><!--{eval $hide_nav = 0;}--><!--{/if}-->
<!--{if $_G['cache']['hb_ext_config']['top_prog']&&IN_PROG}--><!--{eval $hide_nav = 0;}--><!--{/if}-->
<!--{if !$hide_nav}-->
<!--{if $ac=='index'}-->
<header style=" border-bottom-left-radius: 20px;  height: 750px;background-image: linear-gradient(180deg, #FF5321 0%, #FF3632 37%);

background-color:#fff0!important;padding: calc(0px + 5px) 15px 5px 15px;" class="x_header bgcolor_11 cl   f15" <!--{if $config[intopindex]}-->style="background:transparent!important;position:absolute"<!--{/if}-->>
<!--{if $_G['cache']['plugin']['xigua_st']['showfz']}-->
<span onclick="window.location.href='$SCRITPTNAME?id=xigua_st&ac=city&app={$_GET[app]}{$urlext}'" class="fzopen">{echo $stinfo['name2']?$stinfo['name2']:$_G['cache']['plugin']['xigua_st']['zongname']} <i class="iconfont icon-xiangxia f13"></i></span>
<!--{else}-->
   <!--{/if}-->

<style>


.search-box {
    width: 100%;
    height: 35px;

}

.search-outer-box {
    height: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    position: relative;


    margin-right: 5px;
}

.headerlable {
    min-height: 30px;
    background: #fff;
    border-radius: 20px;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    position: relative;
    margin-right: 8px;
}

.icon.icon-search {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyFpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQyIDc5LjE2MDkyNCwgMjAxNy8wNy8xMy0wMTowNjozOSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2NEU0OEM1NTZBMzIxMUU4OEUxQjlGMENENThDRDVDMyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2NEU0OEM1NjZBMzIxMUU4OEUxQjlGMENENThDRDVDMyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY0RTQ4QzUzNkEzMjExRTg4RTFCOUYwQ0Q1OENENUMzIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY0RTQ4QzU0NkEzMjExRTg4RTFCOUYwQ0Q1OENENUMzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+tQL2tgAAAr9JREFUeNq8l01IVFEUx+9oGG6cROhDXOQqVHCQIGhrEyQtnCYhcmqjlEMfS80EW1ZKKytNhUzQbKHNRCWiTsuSKEjFiBAMCqyNH9NCsI/pf+I/cIt3nTfje3Pgxztv3r3n/t+8e+851xOJRFQK2wWC4Ag4CPYCL4iDZfAWxMAYWE8VLBAI/HO/I8XAV8FlkG/xvIAcAPXgDugCN8Gasmk5ht9DYBG0gJ1gGlwAVaAQeHit4u/TbHeF/UKZCpDAnWAIFIFxUAmOgh7wTnu7Nd738Hkl2xexfwfjpSXgOmgGmyAMjoMFmy+zwPZh9m9hPNsC5Du2snMt6FWZWS/7bzJevR0Bu8Fd+jLpJtT2bIJxFOPuSSXgGme9fMM+5Yz1MZ7Ebd9KgDRoAL/53Zy0ZsZt4DiWAoJc5y/SmHB27T3j5nMcSwF++o+VO5aM6zcJ8NF/5ZKAZFyfSUAJ/U8uCVjitcQkoEDb2dywdS13WAqIa8nHDfPy+t0k4Av9/S4JKOX1s0nALP3DLglIxp01CYjRD7ok4ASvMZMAqWQ2QDWocHjwclZSGxzHUoDM/gH6HQ4L6GTcAdMq87AmlGz1gSvhPOh3YPBzTEgycBn4ulU2/AYu0pfa7tg2B68B3fQvmQb/vyB5yIIyDzwBTRkOLv2esuCV5PYonZKsDdyiiHvgWRoTs5ztpV8uKyLp+4D3tgQkmMPPgBXWeHNgirWeT9vZvLwP8/k826+wfzV3WfEHTSJM54Jh8Fw7F/hN6VQzWWq3wQ1txtewKgrxZc+CX3bOBcnkJHV+MWgEI+CjljvivB/h831sry+3l5zQkpBO88Vy7Z6MdCH3SSY2QxFSqJ7iS0ul/PPvPpBIJBzdeaLRqOnRITDJuTNKET9yVPbsNeeR/KN1/HR52RQg9oYiVsFJWR3ZFqB4nPdzdxz9I8AA4lScvkCa9QYAAAAASUVORK5CYII=);
}
i.icon {
    display: inline-block;
    vertical-align: middle;
    background-size: 100% auto;
    background-position: center;
    background-repeat: no-repeat;
    font-style: normal;
    position: relative;
}

input.empty {
    padding-left: 28px;
}
input {
    width: 100%;
    height: 24px;
    line-height: 24px;
    padding-right: 25px;
    vertical-align: middle;
    background: none;
    border: none;
    opacity: 1;
    font-size: 0.7rem;
        color: #9499a6;
}


    .icon-search {
    left: 10px;
    display: inline-block;
    width: 16px;
    height: 16px;
    vertical-align: middle;
    background-size: 100% auto;
    background-position: 50%;
    background-repeat: no-repeat;
}
input::-webkit-input-placeholder { /* WebKit browsers */
  color: #9499a6;;
}
input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
  color: #9499a6;;
}
input::-moz-placeholder { /* Mozilla Firefox 19+ */
  color: #9499a6;;
}
input:-ms-input-placeholder { /* Internet Explorer 10+ */
  color: #9499a6;;
}
.mr30{
    margin-right: 15px;
    font-size:0.7rem;

}
.activehg{

   position: absolute;
    background: #fff;
    height: 3px;
    width: 32px;
    top: 33px;
    left: 0px;
    border-radius: 15px;
   background-image: linear-gradient(90deg, #fd775c 0%, #ffffff 100%);





}
.x_header a{
    width: 30px;

}
</style>


   <div class="weui-flex tgb-header-main">

    <div class="tgb-header-action" role="button" aria-label="进入聊天" onclick="javascript:location.href='plugin.php?id=xigua_lt'" >

        <img src="source/plugin/xigua_hb/static/tgb-r02/chat-r02.svg?v=20260726-r02c" alt="">
   </div>


     <div class="search-box"  >
                    <div class="search-outer-box">
                        <form action="$SCRITPTNAME" method="get" id="searchForm" target="_blank"  style="width:100%;">

                            <label  style="height: 30px;
background:#fff;" class="headerlable"><i class="icon icon-search"></i><input style="width: 80%;padding-left: 18px;height:30px;font-size:0.7rem;" type="search" placeholder="$config[sousuoinput]" autocomplete="off" maxlength="20" name="keyword" <!--{if $keyword}-->data-value="$keyword"<!--{/if}-->  autocorrect="autocorrect" value="" class="empty">

                         <span style="color:#FE412B;font-size:0.8rem;margin:0px 2px;font-weight:300">|</span>
                                <span onclick="dosearch()"style="font-size:0.65rem;width:40px;color:#f96142;margin-right:10px;margin-top:3px;font-weight:700;" >搜一搜</span>


                        </label>

                         <input type="hidden" name="id" value="xigua_hb">
                <input type="hidden" name="ac" value="cat">
                <!--                <input type="hidden" name="cat_id" value="$_GET[cat_id]">-->
                <input type="hidden" name="st" value="$_GET[st]">
                <input type="hidden" name="idu" value="$_GET[idu]">




                        </form>

                        <script>

                            function dosearch(){

                                $("#searchForm").submit();
                            }

                        </script>

                       </div>






      </div>






                 <div class="tgb-header-action" role="button" aria-label="进入我的" onclick="javascript:location.href='plugin.php?id=xigua_hb&ac=my'" >

        <img class="tgb-avatar" src="uc_server/avatar.php?uid=$_G['uid']&size=middle&ts=1" alt="">

   </div>



   </div>

   <div class="weui-flex tgb-channel-row">

        <span class="mr30" style="margin-left: 0.6rem;position:relative;font-size:0.8rem;font-weight:bold;" onclick="javascript:location.href='plugin.php?id=xigua_hb'">
           推荐

            <div class="activehg"></div>
        </span>
        <span class="mr30" onclick="javascript:location.href='plugin.php?id=bphp_prize:index&pid=1'">大转盘</span>
        <span class="mr30" onclick="javascript:location.href='plugin.php?id=guiigo_signin'">签到</span>
        <span class="mr30" onclick="javascript:location.href='plugin.php?id=aljtc_jy'">盲盒引流
</span>
        <span class="mr30" onclick="javascript:location.href='plugin.php?id=aljol&act=talk&friendid=0'">聊天</span>
        <span class="mr30" onclick="javascript:location.href='plugin.php?id=tb_cus_base:rank'">项目榜单</span>
   </div>
<div style="position: absolute; top: 0px; right: 0px; width: 10px; height: 10px; border-radius: 50%; background-color: black;"></div>


</header>

<div class="tgb-header-spacer" aria-hidden="true"></div>



<!--{else}-->
<!--{eval $back_to = "$SCRITPTNAME?id=xigua_hb";}-->
<!--{if $_SERVER[HTTP_REFERER]}-->
<!--{eval $back_to = "javascript:window.history.go(-1);";}-->
<!--{/if}-->
<!--{if $ac=='pub'&&$_GET[step]==3}-->
<!--{eval $back_to = "$SCRITPTNAME?id=xigua_hb&ac=pub";}-->
<!--{/if}-->
<!--{if in_array($ac, array('mypub', 'myorder'))}-->
<!--{eval $back_to = "$SCRITPTNAME?id=xigua_hb&ac=my";}-->
<!--{/if}-->
<!--{if $back_to_overwrite}-->
<!--{eval $back_to = $back_to_overwrite;}-->
<!--{/if}--><!--{if $_GET['hide_side']}--><!--{eval $custom_side = array();}--><!--{/if}-->
<header class="x_header bgcolor_11 cl f15" style="background: rgba(255, 255, 255, 0.88)!important;
        backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(200, 200, 210, 0.25);
        height:60px!important;
        margin-bottom:50px;">
    <span style="color:#000;">
    <a class="z f14" style="color:#000;font-weight:500;margin-top:0px;" href="$back_to"><i class="iconfont icon-fanhuijiantou w15" style="color:#000;font-weight:650;"></i><!--{if !$hidebackfont}-->{lang xigua_hb:back}<!--{/if}--></a>
    <!--{if $need_side}--><a class="y sidectrl view_ctrl" style="color:#000;font-weight:650;"><i class="iconfont icon-gengduo1 f22" style="color:#000;"></i></a><!--{/if}-->
    <!--{if $custom_side}--><a class="y sidectrl $custom_side[2]" style="color:#000;margin-top:0px;" href="$custom_side[0]">$custom_side[1]</a><!--{/if}-->
    <!--{if !$hidenav}--><div class="navtitle" style="font-weight:600;font-size:20px;margin-top:10px;color:#000;">{echo $anavtitle ? $anavtitle: $navtitle}</div><!--{/if}-->
    </span>
</header>
<div style="height:30px;"></div>
<!--{/if}-->




<div id="srh_popup" class="weui-popup__container" style="z-index:1000">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal">
        <div class="fixpopuper">
            <form action="$SCRITPTNAME" method="get" id="searchForm" target="_blank">
                <input type="hidden" name="id" value="xigua_hb">
                <input type="hidden" name="ac" value="cat">
                <!--                <input type="hidden" name="cat_id" value="$_GET[cat_id]">-->
                <input type="hidden" name="st" value="$_GET[st]">
                <input type="hidden" name="idu" value="$_GET[idu]">
                <div class="weui-cells weui-cells_form"  id="searchBar">

                    <div class="weui-cell weui-cell_vcode">
                        <div class="weui-cell__hd">
                            <label class="weui-label" style="width:auto"><i class="c9 iconfont icon-sousuo vm"></i></label>
                        </div>
                        <div class="weui-cell__bd">
                            <input type="search" class="weui-input" id="searchInput" placeholder="$config[sousuoinput]" required="required" name="keyword" <!--{if $keyword}-->data-value="$keyword"<!--{/if}-->>
                        </div>
                        <div class="weui-cell__ft">
                            <button class="weui-vcode-btn" type="submit">{lang xigua_hb:sousuo}</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="footer_fix"></div>
            <div class="bottom_fix"></div>
        </div>
        <div class="fix-bottom">
            <a class="weui-btn weui-btn_default close-popup" >{lang xigua_hb:quxiao}</a>
        </div>
    </div>
</div>




<!--{if !$no_header_fix && $_GET['ac']}-->


<div class="x_header_fix" <!--{if $config[intopindex]&&$ac=='index'}-->style="display:none"<!--{/if}-->>

</div>
<!--{/if}-->

<!--{/if}-->
