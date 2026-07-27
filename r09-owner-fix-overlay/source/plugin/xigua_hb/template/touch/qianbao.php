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
<!--{elseif $_GET[from]=='lg'}-->
<!--{eval $lg_config = $_G['cache']['plugin']['xigua_lg'];$_GET['from']='lg';$config['tname'] = $lg_config['pdmc']?$lg_config['pdmc']:$config['tname'];
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $lg_config['color'];}-->
<!--{template xigua_lg:header}-->
<!--{else}-->
<!--{template xigua_hb:common_header}-->
<!--{/if}--><!--{eval $no_header_fix=1;
$sxfstring = str_replace('n', $config[txsxf].'%', lang_hb('txsxf',0));
if(0 && $hh_config = $_G['cache']['plugin']['xigua_hh']):
    $joininfo = DB::result_first('SELECT joininfo FROM %t WHERE uid = %d', array('xigua_hh_member', $_G['uid']));
    $joininfo = unserialize($joininfo);
    foreach (explode("\n", trim($hh_config['price_join'])) as $index => $item):
        list($name, $price, $percentage, $lazy, $subpct, $hide, $canti) = explode('#', trim($item));
        if($name == $joininfo['name']):
            $info['notallowtx'] = $canti;
        endif;
    endforeach;
endif;
}-->
{template tb_cus_adv:myadvshow}
<!--{eval}-->

$user = DB::result_first("SELECT count(*) FROM ".DB::table('common_member')) * 11;

// ===== 修改点：直接在代码中配置提现额度（id => 金额） =====
$txprice = [
    1 => 20,
    2 => 100,
    3 => 300,
    4 => 500,
    5 => 1000,
    6 => 2000
];
// =========================================================

    if($_G['uid']){
    $xigua_user = DB::fetch_first('SELECT realname,alipay_account,bank_card FROM %t WHERE uid IN (%n)', array('xigua_hb_user',$_G['uid'] ), 'uid');
    }

    $myextcredits = getuserprofile('extcredits' . $config['credit_type']);

    $creaditstitle = $_G['setting']['extcredits'][$config['credit_type']['title']];

    $myextcredits = intval($myextcredits);
<!--{/eval}-->

<link rel="stylesheet" href="source/plugin/tb_cus_base/static/bootstrapfont/bootstrap-icons.css">
<script src="source/plugin/tb_cus_base/static/layer/layer.js" type="text/javascript" charset="UTF-8"></script>

<style>
/* ========== 趣赚汇 · 暖白珊瑚红渐变风格 (我的资产页) ========== */
:root {
    --bg: #fff9f5;
    --card-bg: rgba(255, 255, 255, 0.85);
    --primary: #ff7b00;
    --primary-dark: #e63946;
    --primary-gradient: linear-gradient(135deg, #ff7b00, #e63946);
    --text-primary: #3d2b1a;
    --text-secondary: #8b6f5c;
    --text-tertiary: #b08968;
    --border-light: rgba(255, 200, 120, 0.35);
    --border-card: rgba(255, 190, 90, 0.35);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
    --shadow-md: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
    --shadow-red: 0 5px 15px rgba(255,50,0,0.25);
    --radius-sm: 8px;
    --radius-md: 16px;
    --radius-lg: 24px;
    --radius-xl: 32px;
}

body {
    background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
    color: var(--text-primary) !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', sans-serif;
}

/* 覆盖原有深色样式 */
.selection {
    margin: 15px;
    padding: 16px;
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    color: var(--text-secondary);
    box-shadow: var(--shadow-md) !important;
}

.selection2 {
    margin: 15px;
    padding: 16px;
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    height: auto;
    box-shadow: var(--shadow-md) !important;
}

.transfer-list-box {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-top: -5px;
    margin-bottom: 5px;
}

.transfer-item {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 11px;
    width: 30%;
    height: 48px;
    background: rgba(255,245,235,0.7);
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 500;
    color: var(--text-secondary);
    border: 1px solid var(--border-light);
    transition: all 0.2s;
    cursor: pointer;
}

.transfer-item.active {
    background: var(--primary-gradient) !important;
    color: white !important;
    font-weight: 700 !important;
    border: 1px solid #ff7b00 !important;
    box-shadow: var(--shadow-red);
}

.transfer-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.u-button {
    text-align: center;
    width: 85%;
    color: white !important;
    background: var(--primary-gradient) !important;
    border: none;
    height: 56px;
    line-height: 56px;
    border-radius: 60px !important;
    font-weight: 700;
    font-size: 18px;
    margin: 24px auto 0;
    display: block;
    box-shadow: var(--shadow-red);
    transition: all 0.2s;
    cursor: pointer;
}

.u-button:active {
    transform: scale(0.96);
}

.price-desc-btn {
    animation: none;
}

/* 头部样式 */
#header {
    z-index: 999;
    background: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(22px) !important;
    -webkit-backdrop-filter: blur(22px) !important;
    border-bottom: 1px solid rgba(255,200,120,0.35) !important;
    width: 100%;
    height: 80px;
    font-size: 18px;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    box-sizing: border-box;
    box-shadow: 0 2px 20px rgba(255,150,30,0.06);
}

#back-button {
    margin-top: 150px;  
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 600;
}

#header-title {
    color: var(--text-primary);
    font-weight: 800;
    font-size: 20px;
    margin: 0;
    margin-top: 15px;
}

/* 钱包余额卡片 - 红橙渐变 */
.selection1 {
    margin-top: 95px;
    border-radius: var(--radius-lg) !important;
    color: white !important;
    background: var(--primary-gradient) !important;
    padding: 24px;
    margin: 15px;
    box-shadow: var(--shadow-red);
}

.selection1 .pricedesc {
    color: rgba(255, 255, 255, 0.9);
    margin-top: 15px;
    font-size: 14px;
}

.selection1 .price {
    color: white !important;
    font-weight: 700 !important;
    font-size: 2.5rem !important;
    margin: 10px 0;
}

/* 提现记录区域 */
.transfer-log-box {
    margin: 15px;
}

.weui-navbar {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    border-bottom: 1px solid var(--border-light);
    padding: 0;
}

.weui-navbar__item {
    flex: 1;
    text-align: center;
    padding: 16px 0;
    color: var(--text-tertiary);
    font-weight: 500;
    border-bottom: 3px solid transparent;
}

.weui-navbar__item.weui_bar__item_on {
    color: var(--primary-dark);
    border-bottom: 3px solid var(--primary);
    background: rgba(255,220,180,0.3);
}

#list.weui-cells {
    background-color: transparent !important;
    padding: 0;
}

/* 规则说明 */
div[style*="text-align: left;margin-top: 10px"] {
    text-align: left !important;
    margin: 15px !important;
    font-size: 14px !important;
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    padding: 20px !important;
    color: var(--text-secondary) !important;
    box-shadow: var(--shadow-md) !important;
}

div[style*="text-align: left;margin-top: 10px"] p {
    margin-bottom: 10px;
    line-height: 1.6;
    color: var(--text-primary) !important;
}

/* 绑定按钮 */
a[style*="background: linear-gradient(135deg, #22c55e"] {
    background: var(--primary-gradient) !important;
    color: white !important;
    font-size: 14px !important;
    padding: 8px 18px !important;
    border-radius: 60px !important;
    text-decoration: none !important;
    font-weight: 700 !important;
    box-shadow: var(--shadow-red) !important;
    margin-left: 10px !important;
    display: inline-block;
}
a[style*="background: linear-gradient(135deg, #22c55e"]:active {
    transform: scale(0.96);
}

/* 图片样式 */
img {
    border-radius: var(--radius-sm);
}

/* 链接样式 */
a {
    color: var(--primary);
    text-decoration: none;
    transition: color 0.2s;
}

a:hover {
    color: var(--primary-dark);
}

/* 覆盖内联渐变按钮 */
.u-button a, .u-button a:visited {
    color: white !important;
    display: block;
}
.u-button a:hover {
    color: white !important;
}

/* 未绑定提示文字 */
div[style*="text-align:center;margin-top:20px;color:#8e9aaf"] {
    color: var(--text-secondary) !important;
}

/* 遮盖原有的深色背景片段 */
.selection a, .selection2 a {
    color: var(--primary);
}
.selection a:hover, .selection2 a:hover {
    color: var(--primary-dark);
}

/* 修正 weui-cells 内边框 */
.weui-cells {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-card);
    box-shadow: var(--shadow-md);
}
</style>

<!-- 头部栏 -->
<div id="header" class="tgb-r07-wallet-header" style="margin-top:0px;">
    <!-- 返回按钮 -->
    <a href="javascript:window.history.go(-1);">
        <i class="bi bi-chevron-left" aria-hidden="true"></i>
    </a>
    <!-- 标题 -->
    <h1 id="header-title">我的资产</h1>
    <div style="width: 20px;"></div>
</div>

<div>
    <div class="selection1" style="margin-top: 95px;border-radius:24px;color:#fff;background: linear-gradient(135deg, #ff7b00, #e63946);padding:5px 15px;">
        <div style="margin-bottom: 10px;">
            <div class="pricedesc" style="color:#fff;margin-top:15px;">账户余额</div>
            <div class="price" id="njum1" style="color:#fff;font-weight:700;">{echo floatval($info[money]);}</div>
        </div>
    </div>
</div>

<div class="selection">
    <a href='plugin.php?id=xigua_hb&ac=mytx&from=&idu=1'>
        <div style="font-size:14px;color:#8b6f5c;">
            <i class="bi bi-wallet2" aria-hidden="true"></i>
            提现到
            <!--{if $xigua_user['alipay_account']}-->
                支付宝：{$xigua_user['alipay_account']}
            <!--{else}-->
                未绑定提现方式
            <!--{/if}-->
            <span style="float:right;color:#ff7b00;">
                <!--{if  !$xigua_user['alipay_account']}-->
                立即绑定
                <!--{/if}-->
            </span>
        </div>
    </a>
</div>

<div class="selection2">
    <div class="transfer-list-box">
        {eval $akey= 1;}          <!-- 默认选中 id=1（20元） -->
        <!--{loop $txprice $key $value}-->
        <div class="transfer-item {if $key==$akey} active {/if}" data-id="{$key}">
            <span><font style="font-size:13px;">¥ </font>{$value}</span>
        </div>
        <!--{/loop}-->
        
        <input type="hidden" id="txid" value="{$akey}">
    </div>
</div>

<!--{if $xigua_user['alipay_account']}-->
<div class="u-button price-desc-btn" id="tixianbtn" style="border-radius:60px;width:85%;">
    <a style="color: #fff; display: block;">
        立即提现
    </a>
</div>
<!--{else}-->
<div class="tgb-r07-wallet-unbound" style="text-align:center;margin-top:20px;color:#8b6f5c;vertical-align:middle;">
    未绑定提现方式
    <a href="plugin.php?id=xigua_hb&ac=mytx&from=&idu=1" style="vertical-align:middle;font-size:14px;color:#fff; background: linear-gradient(135deg, #ff7b00, #e63946); padding:8px 18px;border-radius:60px; margin-left: 10px; font-weight: 700;">
        立即绑定
    </a>
</div>
<!--{/if}-->

<div class="tgb-r07-wallet-notice" style="text-align: left;margin-top: 10px;font-size: 14px;background: rgba(255,255,255,0.85);backdrop-filter: blur(20px);-webkit-backdrop-filter: blur(20px);border-radius:24px;padding:20px;margin:15px;color:#8b6f5c;border:1px solid rgba(255,190,90,0.35);box-shadow: 0 20px 45px rgba(255,140,30,0.10);">
    <p>1、推广宝会员提现服务费永久7%，推荐开通会员提现更划算；</p>
    <p>2、提现24小时内审核到账，节假日顺延</p>
    <p>3、请在每天10点~18点提现</p>
</div>

<div class="tgb-r07-wallet-tabs" style="margin-left: 15px;margin-right: 15px;margin-bottom:-20px;">
    <div class="weui-navbar border_none prtop2" style="border-radius:24px 24px 0 0;background: rgba(255,255,255,0.85);backdrop-filter: blur(20px);-webkit-backdrop-filter: blur(20px);border:1px solid rgba(255,190,90,0.35);border-bottom:none;">
        <a href="$SCRITPTNAME?id=xigua_hb&ac=qianbao&type=in&from=$_GET['from']" class="weui-navbar__item <!--{if $_GET[type]!='out'}-->weui_bar__item_on<!--{/if}-->">
            <span>收益记录</span>
        </a>
        <a href="$SCRITPTNAME?id=xigua_hb&ac=qianbao&type=out&from=$_GET['from']" class="weui-navbar__item <!--{if $_GET[type]=='out'}-->weui_bar__item_on<!--{/if}-->">
            <span>{lang xigua_hb:tixian}记录</span>
        </a>
    </div>
</div>

<div style="border-bottom: 5px solid transparent;margin:0px 50px;"></div>

<div class="transfer-log-box" style="margin-left: 15px;margin-right: 15px;">
    <div style="border-bottom: 1px solid transparent;margin:0px 0px;"></div>
    <div id="list" class="weui-cells p0 border_none prtop2" style="background-color:transparent;">
    </div>
</div>

{template tb_cus_adv:myadvshow}

<script src="source/plugin/xigua_hb/static/countUp.js"></script>
<script>
    var loadingurl = window.location.href+'&ac=qianbao_li&inajax=1&type={$_GET[type]}&page=';
</script>

<!--{if strpos($_SERVER['HTTP_REFERER'], 'xigua_jy')!==false}-->
<!--{eval $tabbar=0;$jy_tabbar=1;}-->
<!--{template xigua_jy:footer}-->
<!--{elseif strpos($_SERVER['HTTP_REFERER'], 'xigua_es')!==false}-->
<!--{eval $es_tabbar=1;$tabbar=0;}-->
<!--{template xigua_es:footer}-->
<!--{elseif $_GET[from]=='lg'}-->
<!--{eval $lg_tabbar=1;$tabbar=0;}-->
<!--{template xigua_lg:footer}-->
<!--{elseif $_GET[from]=='job'}-->
<!--{eval $job_tabbar=1;$tabbar=0;}-->
<!--{template xigua_job:footer}-->
<!--{else}-->
<!--{eval $tabbar=0;}-->
<!--{template xigua_hb:common_footer}-->
<!--{/if}-->

<script>
    var options = {useEasing : true,useGrouping : true,separator : '',decimal : '.',prefix : '',suffix : ''};
    new countUp("njum1", 0, $('#njum1').text(), 2, 2.5, options).start();
    if($('#njum2').length>0){
        new countUp("njum2", 0, $('#njum2').text(), 0, 2.5, options).start();
    }
    
    var taccount = 0;
    
    $('#tixianbtn').on('click', function(){
        var txid = $("#txid").val();
        
        $.ajax({
            type: 'post',
            url: '$SCRITPTNAME?id=xigua_hb&ac=tixian&inajax=1',
            data:{formhash:'{FORMHASH}', txid : txid, amount : 8888},
            dataType: 'xml',
            success: function (data) {
                console.log(data);
                $.hideLoading();
                if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                var s = data.lastChild.firstChild.nodeValue;
                var msgar = s.split('|');
                if(msgar[3]){
                    localStorage.setItem('verifyOrderidtixian', msgar[3]);
                }
                tip_common(s);
            },
            error: function () {
                $.hideLoading();
            }
        });
    });
    
    $('#tixianbtn_111').on('click', function(){
        <!--{if $config['autoinapp'] && (IN_MAGAPP||IN_QIANFAN) && $config[qbguide] && $config[qbguidelink]}-->
            <!--{if IN_QIANFAN}-->
            QFH5.jumpMyPackage();return false;
            <!--{elseif IN_MAGAPP}-->
            mag.newWin('/mag/user/v1/user/wallet');return false;
            <!--{/if}-->
        <!--{/if}-->
        <!--{if $config[subscribe] && HB_INWECHAT}-->
        //var unscb = 0;
        
        $.ajax({
            type: 'get',
            url: _APPNAME + '?id=xigua_hb&ac=checksub&inajax=1',
            data: {formhash: FORMHASH},
            dataType: 'xml', async:false,
            success: function (data) {
                if (null == data) {
                    return false;
                }
                var s = $.trim(data.lastChild.firstChild.nodeValue);
                if (s.split('|')[1] != 'subscribe') {
                    unscb = 1;
                }
            }
        });
        if(unscb){
            $.alert("<img src=$config[qrcode] /><br>{lang xigua_hb:caewm}", "&#20851;&#27880;&#20844;&#20247;&#21495;&#33719;&#24471;&#23454;&#26102;&#25552;&#29616;&#21040;&#36134;&#36890;&#30693;");
            return false;
        }
        <!--{/if}-->
        $.prompt("{lang xigua_hb:shurutixian}", function(text) {
            $.showLoading();
            $.ajax({
                type: 'post',
                url: '$SCRITPTNAME?id=xigua_hb&ac=tixian&inajax=1',
                data:{formhash:'{FORMHASH}', amount : text},
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
        }, function() {
        });
        $('#weui-prompt-input').replaceWith('<input class="weui-prompt-input needsclick weui-input needsclick_input" type="text" id="weui-prompt-input" placeholder="{lang xigua_hb:shurutixian}<!--{if $config[txsxf]}-->,{$sxfstring}<!--{/if}-->" />');
        document.getElementById('weui-prompt-input').focus();
    });
    
    $('#czbtn').on('click', function(){
        $.prompt("{lang xigua_hb:srczje}", function(text) {
            $.showLoading();
            $.ajax({
                type: 'post',
                url: '$SCRITPTNAME?id=xigua_hb&ac=cz&inajax=1',
                data:{formhash:'{FORMHASH}', amount : text},
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
        }, function() {
        });
        $('#weui-prompt-input').replaceWith('<input class="weui-prompt-input needsclick weui-input needsclick_input" type="tel" id="weui-prompt-input" placeholder="{lang xigua_hb:srczje}" />');
        document.getElementById('weui-prompt-input').focus();
    });
    
    <!--{if !(IN_MAGAPP || IN_QIANFAN)&&$config[qbguide]&&$config[qbguidelink]}-->
    $.confirm("$config[qbguide]", function() {
        window.location.href = '$config[qbguidelink]';
    }, function() {
    });
    <!--{else}-->
    <!--{if IN_QIANFAN && $config['autoinapp']}-->
    setTimeout(function () {
        QFH5.jumpMyPackage();
    }, 400);
    <!--{elseif IN_MAGAPP&&$config['autoinapp']}-->
    setTimeout(function () {
        mag.newWin('/mag/user/v1/user/wallet');
    }, 400);
    <!--{/if}-->
    <!--{/if}-->
    
    $(document).on('click', ".transfer-item", function () {
        $(this).siblings('.transfer-item').removeClass('active');
        $(this).addClass('active');
        var txid = $(this).attr("data-id");
        $("#txid").val(txid);
    });
    
    $(document).on('click', ".buytxsxf", function () {
        jdfee();
    });
    
    <!--{if $_GET['jd']==1}-->
    jdfee();
    <!--{/if}-->
    
    function jdfee(){
        layer.confirm("确认要消耗{$config['tixianflprice']}财富值随机降低1%~1%兑换服务费吗？", {
            //取消操作
            cancel: function() {
            }
        }, function() {
            var formdata=new FormData();
            formdata.append('submodac', "buytxsxf");
            jQuery.ajax({
                type: 'post',
                url: 'plugin.php?id=xigua_hb&ac=tixian',
                data :  formdata,
                processData : false,
                contentType : false,
                dataType: 'json',
                success: function (data) {
                    if(data.rs == 200){
                        window.location.reload();
                    }else{
                        layer.msg(data.msg);
                    }
                }
            });
        });
    }
    
    checkres()
    function checkres(){
        intervalId = setInterval(() => {
            var verifyorderid = localStorage.getItem('verifyOrderidtixian');
            if(verifyorderid){
                $.get(
                    '{$_G["siteurl"]}plugin.php?id=xiaomy_certification:verifyResApp&submodac=check',
                    {orderid: verifyorderid},
                    function (data) {
                        if (data.extinfo == 1 || data.extinfo == 2) {
                            localStorage.removeItem('verifyOrderidzz');
                            clearInterval(intervalId);
                            window.location.reload();
                        }
                    }, "json");
            }
        }, 1000);
    }
</script>
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r07/finance-light-grid-r07.css?20260728-owner-v5">
