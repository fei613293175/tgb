<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r07/membership-light-grid-r07.css?v=20260727-r07">
<div id="refresh_ctrl" style="z-index:999" class="weui-popup__container popup-bottom">
<form  action="$SCRITPTNAME?id=xigua_hb&ac=refresh&do=auto&st={$_GET['st']}" method="post" id="form1">
    <input name="formhash" value="{FORMHASH}" type="hidden">
    <input name="inajax" value="1" type="hidden">
    <input name="reshid" class="reshid" value="0" type="hidden">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal">
        <div class="toolbar">
            <div class="toolbar-inner">
                <a href="javascript:;" class="picker-button close-popup">{lang xigua_hb:quxiao}</a>
                <h1 class="title">{lang xigua_hb:shuaxinxinxi} ID: <em class="reshid"></em></h1>
            </div>
        </div>
        <div class="modal-content">
            <div class="weui-cells before_none after_none">
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">设置间隔</label></div>
                    <div class="weui-cell__bd">
                        <input name="form[jiange]" class="weui-input" style="margin-left:-20px;background-color:#f1f4fb;padding:5px 0px 5px 5px;border-radius:5px;" type="tel" placeholder="{lang xigua_hb:qtxsj}" value="0">
                    </div>
                    <div class="weui-cell__ft">
                        <span class="color-red"> {lang xigua_hb:fz}<em class="color-gray">自动刷新1次</em></span>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">最多刷新</label></div>
                    <div class="weui-cell__bd">
                        <input name="form[jiangemax]" class="weui-input" style="margin-left:-20px;background-color:#f1f4fb;padding:5px 0px 5px 5px;;border-radius:5px;" type="tel" placeholder="{lang xigua_hb:jiangemax_tip}" value="{$old_data['jiangemax']}">
                    </div>
                    <div class="weui-cell__ft">
                        <span class="color-red"> {lang xigua_hb:c}<em class="color-gray">{lang xigua_hb:htzsx}</em></span>
                    </div>
                </div>

<div style="padding:10px 15px" class="color-gray" style="font-size:12px;">目前{lang xigua_hb:yishuaxin}
    <em class="main_color" id="refresh_time_num"> {$old_data['refresh_times']} </em>{lang xigua_hb:c},
    <b class="main_color">{lang xigua_hb:mczdkc}{$config[refresh]}{lang xigua_hb:yuan}或1张刷新卡</b>
    <p>{lang xigua_hb:yebzhzdtz}</p>
 

    <div>
        <a class="color-red bold" href="$SCRITPTNAME?id=xigua_hb&ac=refresh&do=sxtc">{lang xigua_hb:shuaxin3}</a>
        <p>{lang xigua_hb:shuaxin4}<em class="main_color" id="refresh_tc">0</em>{lang xigua_hb:c}</p>
    </div>
</div>
            </div>
            <div style="background-color:#fff;border-radius:5px 5px 0px 0px;margin:10px 0px;text-align:left;font-size:12px;margin-bottom:10px;padding:10px;">
   本平台刷新逻辑采用先进的智能刷新，当平台人流较小或深夜时，系统会为您自动降低刷新频率来节约刷新卡，也就是人流量较大时按照您设置的频率刷新，人流少时只有用户访问时才会刷新给用户。
</div>
            <div class="fix-bottom" style="position: relative">
                <input type="submit" id="dosubmit1" href="javascript:;" class="weui-btn weui-btn_primary" value="{lang xigua_hb:queding}">
            </div>
        </div>
    </div>
</form>
</div>
