<?php exit('Author: '); ?>

<!--{if $_GET[step]==3 && !$_GET[catid]}-->
<!--{eval dheader("Location: $SCRITPTNAME?id=xigua_hb&ac=pub".$urlext);}-->
<!--{/if}-->
<!--{eval
include_once DISCUZ_ROOT.'source/plugin/xigua_hb/include/ext.php';
include_once DISCUZ_ROOT.'source/plugin/xigua_hb/include/c_pub.php';
}-->
<!--{if $_G['cache']['plugin']['xigua_es'] && ($_G['cache']['plugin']['xigua_es']['esid']==$catinfo['pid']||$_G['cache']['plugin']['xigua_es']['esid']==$catinfo['id'])}-->
<!--{eval
$config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] =  $_G['cache']['plugin']['xigua_es']['dftcolor'];
$config['tcpub'] = '';}-->
<!--{/if}-->
<!--{template xigua_hb:common_header}-->

<link rel="stylesheet" href="source/plugin/tb_cus_base/static/bootstrapfont/bootstrap-icons.css">
<script src="source/plugin/tb_cus_base/static/layer/layer.js" type="text/javascript" charset="UTF-8"></script>

<link rel="stylesheet" href="source/plugin/xigua_hb/static/dist/cropper.css?{VERHASH}">
<style>
    /* ========== 趣赚汇统一风格 - 暖白珊瑚红渐变 ========== */
    :root {
        --bg: #fff9f5;
        --card-bg: rgba(255, 255, 255, 0.85);
        --primary: #ff7b00;
        --primary-dark: #e63946;
        --primary-gradient: linear-gradient(135deg, #ff7b00, #e63946);
        --gold-light: #ffb380;
        --gold-dark: #d35400;
        --text-primary: #3d2b1a;
        --text-secondary: #8b6f5c;
        --text-tertiary: #b08968;
        --border-light: rgba(255, 200, 120, 0.35);
        --border-card: rgba(255, 190, 90, 0.35);
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
        --shadow-md: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
        --shadow-red: 0 5px 15px rgba(255,50,0,0.25);
    }

    body {
        background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
        color: var(--text-primary) !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', 'Microsoft YaHei', sans-serif !important;
    }

    .tag-on {
        background: var(--primary-gradient) !important;
        color: #fff !important;
    }

    input::-webkit-input-placeholder {
        color: #b08968;
        font-size: 14px;
        font-weight: 400;
    }

    .infotype {
        width: 95%;
        height: 30px;
        margin: 10px;
        border-radius: 16px;
        border: 1px solid var(--border-light);
        background: rgba(255,255,255,0.7);
        color: var(--text-primary);
    }

    input:-moz-placeholder {
        color: #b08968;
    }

    input::-moz-placeholder {
        color: #b08968;
    }

    input:-ms-input-placeholder {
        color: #b08968;
    }

    .x_header {
        z-index: 10;
    }

    .weui-cells_form .weui-cell__ft {
        font-size: .85rem;
    }

    .weui-grid {
        padding: 1rem 0;
    }

    /* 头部样式 - 浅色毛玻璃 */
    header.x_header {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);
        border-bottom: 1px solid rgba(255,200,120,0.35) !important;
        height: 60px;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 2px 20px rgba(255,150,30,0.06);
    }

    .x_header a.z img {
        filter: none;
    }

    /* 发布按钮样式 - 红橙渐变 */
    a#submitnew {
        background: var(--primary-gradient) !important;
        color: white !important;
        border: none !important;
        box-shadow: var(--shadow-red);
        transition: all 0.3s ease;
    }

    a#submitnew:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,50,0,0.35);
    }

    /* 主体内容区域 */
    .page__bd {
        background: transparent !important;
        color: var(--text-primary) !important;
        margin-top: 70px;
    }

    /* 分类网格样式 */
    .weui-grids {
        background: var(--card-bg) !important;
        border-radius: 2rem;
        padding: 16px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 12px;
        border: 1px solid var(--border-card);
        box-shadow: var(--shadow-sm);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .weui-grid {
        background: rgba(255,255,255,0.7);
        border: 1px solid var(--border-light);
        border-radius: 1.5rem;
        padding: 12px 8px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .weui-grid:hover {
        transform: translateY(-2px);
        border-color: #ff7b00;
        box-shadow: 0 8px 20px rgba(255,100,30,0.15);
    }

    .weui-grid__icon img {
        width: 40px;
        height: 40px;
        border-radius: 16px;
        margin-bottom: 8px;
    }

    .weui-grid__label {
        color: var(--text-primary) !important;
        font-size: 14px !important;
        font-weight: 600;
    }

    /* 单元格样式 */
    .weui-cells {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 2rem;
        border: 1px solid var(--border-card);
        margin: 1px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
    }

    .weui-cells__title {
        color: var(--text-secondary);
        font-size: 14px;
        margin: 16px 16px 8px;
    }

    .weui-cell {
        background: transparent !important;
        border-bottom: 1px solid var(--border-light);
        padding: 16px;
    }

    .weui-label {
        color: var(--text-primary) !important;
        font-weight: 600;
    }

    .weui-input,
    .weui-textarea,
    .weui-select {
        background: rgba(255,245,235,0.5) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 16px !important;
        color: var(--text-primary) !important;
        padding: 10px 12px !important;
        font-size: 14px !important;
    }

    .weui-input:focus,
    .weui-textarea:focus,
    .weui-select:focus {
        border-color: #ff7b00 !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,123,0,0.15);
    }

    .weui-vcode-btn {
        background: var(--primary-gradient) !important;
        color: white !important;
        border-radius: 20px !important;
        padding: 6px 16px !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .weui-vcode-btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-red);
    }

    /* 上传组件样式覆盖 */
    .weui-uploader__files {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .weui-uploader__file {
        width: 100px !important;
        height: 100px !important;
        border-radius: 16px !important;
        border: 2px solid rgba(255,190,90,0.3) !important;
        background-size: cover !important;
        background-position: center !important;
    }

    .weui-uploader__input-box {
        background: rgba(255,245,235,0.5) !important;
        border: 2px dashed var(--border-light) !important;
        border-radius: 20px !important;
        padding: 40px 20px !important;
        text-align: center;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .weui-uploader__input-box:hover {
        border-color: #ff7b00 !important;
        background: rgba(255,220,180,0.3) !important;
    }

    /* 按钮样式 - 红橙渐变 */
    .weui-btn_primary {
        background: var(--primary-gradient) !important;
        border: none !important;
        border-radius: 30px !important;
        box-shadow: var(--shadow-red);
        transition: all 0.3s ease;
        color: #fff !important;
    }

    .weui-btn_primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,50,0,0.35);
    }

    .weui-btn_default {
        background: #fff !important;
        border: 1px solid var(--border-light) !important;
        color: var(--text-secondary) !important;
        border-radius: 30px !important;
    }

    /* 置顶红包弹窗样式 */
    .dig_pub_div,
    .redb_div {
        background: var(--card-bg) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 20px !important;
        transition: all 0.3s ease;
        color: var(--text-primary);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .dig_pub_div.active,
    .redb_div.active {
        border-color: #ff7b00 !important;
        box-shadow: 0 0 15px rgba(255,123,0,0.2);
    }

    .dig_pub_price,
    .redb_price {
        color: #d35400 !important;
    }

    .dig_pub_title,
    .redb_title {
        color: var(--text-primary) !important;
    }

    /* 通知样式 */
    .crypto-notice {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-light);
        border-radius: 2rem;
        padding: 20px;
        margin: 16px;
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .crypto-notice-warning {
        color: #e8553d !important;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .crypto-notice-agreement {
        color: var(--text-tertiary);
        font-size: 12px;
    }

    .crypto-notice-link {
        color: #d35400 !important;
        font-weight: 600;
        text-decoration: none;
    }

    .crypto-notice-link:hover {
        color: #ff7b00 !important;
    }

    /* VIP促销卡片 */
    .crypto-vip-promo {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-card);
        border-radius: 1.5rem;
        padding: 16px;
        margin: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        box-shadow: var(--shadow-sm);
    }

    .crypto-vip-title {
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 500;
    }

    .crypto-vip-btn {
        background: var(--primary-gradient) !important;
        color: white !important;
        padding: 6px 18px !important;
        border-radius: 60px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-red);
    }

    .crypto-vip-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,50,0,0.35);
    }



    .crypto-input,
    .crypto-textarea {
        width: 90% !important;
        background: rgba(255,245,235,0.7) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 1.5rem !important;
        padding: 14px 16px !important;
        color: var(--text-primary) !important;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .crypto-input:focus,
    .crypto-textarea:focus {
        border-color: #ff7b00 !important;
        outline: none;
        box-shadow: 0 0 0 2px rgba(255,123,0,0.15);
    }

    .crypto-input::placeholder,
    .crypto-textarea::placeholder {
        color: #b08968;
    }

    /* 弹窗样式 */
    .layui-layer-page {
        border-radius: 2rem !important;
        background: var(--card-bg) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-card);
        box-shadow: 0 20px 45px rgba(255,140,30,0.10) !important;
    }

    .layui-layer-content {
        color: var(--text-primary) !important;
    }

    .layui-layer-title {
        background: rgba(255,245,235,0.7) !important;
        border-bottom: 1px solid var(--border-light) !important;
        color: var(--text-primary) !important;
        font-weight: 600;
    }

    .layui-layer-btn .layui-layer-btn0 {
        background: var(--primary-gradient) !important;
        border: none !important;
        color: #fff !important;
        border-radius: 30px !important;
    }

    .layui-layer-btn .layui-layer-btn1 {
        background: rgba(255,245,235,0.7) !important;
        border: 1px solid var(--border-light) !important;
        color: var(--text-secondary) !important;
        border-radius: 30px !important;
    }

    /* 选择器 */
    .choose_selet {
        cursor: pointer;
    }

    .masker {
        background: rgba(0, 0, 0, 0.5) !important;
        backdrop-filter: blur(4px);
    }

    /* 底部固定按钮区域 */
    .fix-bottom {
        position: fixed;
        bottom: 20px;
        left: 0;
        right: 0;
        text-align: center;
        z-index: 100;
    }

    .fix-bottom .weui-btn {
        width: 80%;
        margin: 0 auto;
    }

    /* 其他细节 */
    .weui-switch {
        background: rgba(255,245,235,0.7) !important;
        border: 1px solid var(--border-light) !important;
    }

    .weui-switch:checked {
        background: var(--primary-gradient) !important;
    }

    .weui-cell__ft i.bi-x {
        color: var(--text-tertiary);
        font-size: 24px;
        cursor: pointer;
    }

    .weui-cell__ft i.bi-x:hover {
        color: var(--text-primary);
    }

    /* 响应式调整 */
    @media (max-width: 768px) {
        .weui-grids {
            grid-template-columns: repeat(3, 1fr);
        }
        .crypto-vip-promo {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }
    }

    /* 覆盖内联绿色样式为红色渐变 */
    .dig_pub_subtag.main_bg,
    .redb_subtag.main_bg {
        background: var(--primary-gradient) !important;
    }
    .popbtn {
        border: 1.5px solid #ff7b00;
        background-color: #fff9f5;
        color: var(--text-primary);
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s;
    }
    .popbtn:hover {
        background-color: #ffefe5;
    }
    .pubdigc {
        border-bottom-color: #ff7b00 !important;
    }
    .layui-layer-btn a.layui-layer-btn0 {
        background: var(--primary-gradient) !important;
    }
    
    /* 上传区样式复用 */
    .crypto-upload-container {
        background: var(--card-bg) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 2rem;
        padding: 20px;
        margin: 16px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-card);
    }
    .crypto-upload-file {
        border: 2px solid rgba(255,190,90,0.3) !important;
        border-radius: 16px !important;
    }
    .crypto-upload-input {
        background: rgba(255,245,235,0.5) !important;
        border: 2px dashed var(--border-light) !important;
        border-radius: 20px !important;
    }
    .crypto-upload-input:hover {
        border-color: #ff7b00 !important;
    }
</style>
<header class="x_header bgcolor_11 cl f15" style="background:#fff!important;backdrop-filter:none;border-bottom:1px solid #d8e1ec!important;height:60px;box-shadow:0 4px 14px rgba(12,27,51,.05);">
    <div class="tgb-publish-header-spacer" style="margin-top:15px;"> </div>
    <a class="z " style="height:50px;line-height:25px;" href="javascript:window.history.go(-1);"><i class="iconfont icon-fanhuijiantou" aria-hidden="true"></i></a>
    <div class="tgb-publish-header-title" style="font-weight:700;font-size:20px;text-align:center;color:#0e1b2a;margin-top:2px;">发布项目</div>
    <a style="position:fixed;right:0; background: linear-gradient(135deg, #ff7b00, #e63946); width:50px; color: #fff; height:35px; border:none; padding:-15px 10px -10px 10px; margin-top:-40px; font-weight: 700; border-radius: 30px; font-size: 0.75rem; margin-right: 15px; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(255,50,0,0.25); transition: all 0.3s ease;" id="submitnew" href="javascript:void(-1)">
        <div class="tgb-publish-submit-label" style="vertical-align:middle;margin-top:-4px;">发布</div>
    </a>
    <div class="navtitle" style="height:0px;"></div>
</header>

<div class="page__bd" style="margin-top:70px;border-radius:0!important;margin:0;background-color:transparent;font-size:15px;font-weight:400;color:#405166!important;">
<!--{template xigua_hb:ad_in2}-->
<!--{if !$_GET[step]}-->
<!--{if $config[pubtip]}--><div class="weui-cells__title" style="margin-top:55px;font-weight:400;font-size:12px!important;"> </div><!--{/if}-->
<div class="weui-cells border_none" style="font-size:12px!important;"><!--{eval $pubmodule = unserialize($config[pubmodule]);}-->
<!--{if $_G['cache']['plugin']['xigua_hs']&& in_array('1', $pubmodule)}-->
<a class="weui-cell weui-cell_access" href="$SCRITPTNAME?id=xigua_hs&ac=enter&from=hb">
<div class="weui-cell__hd" style="font-size:12px!important;"><i class="iconfont icon-shoplight color-red f30 mr15"></i></div>
<div class="weui-cell__bd" style="font-size:12px!important;">
<p>
<span class="f15" style="font-size:12px!important;">{lang xigua_hs:enter_guide1}</span>
<br>
<span class="c9 f13" style="font-size:12px!important;">{lang xigua_hs:enter_guide2}</span>
</p>
</div>
<div class="weui-cell__ft"></div>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_hp'] && in_array('3', $pubmodule)}-->
<a class="weui-cell weui-cell_access" href="$SCRITPTNAME?id=xigua_hp&ac=join&mobile=2{$urlext}">
<div class="weui-cell__hd"><i class="iconfont icon-mingpian main_color f30 mr15"></i></div>
<div class="weui-cell__bd">
<p>
<span class="f15">{lang xigua_hb:wsgr}</span>
<br>
<span class="c9 f13">{lang xigua_hb:wsgrdesc}</span>
</p>
</div>
<div class="weui-cell__ft"></div>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_dh'] && in_array('2', $pubmodule)}-->
<a class="weui-cell weui-cell_access" href="$SCRITPTNAME?id=xigua_dh&ac=join&mobile=2{$urlext}">
<div class="weui-cell__hd"><i class="iconfont icon-dianhuaben main_color f30 mr15"></i></div>
<div class="weui-cell__bd">
<p>
<span class="f15">&#25105;&#26159;&#21830;&#23478;&#44;&#28857;&#20987;&#20837;&#39547;&#49;&#49;&#52;&#30005;&#35805;&#26412;</span>
<br>
<span class="c9 f13">&#20837;&#39547;&#21040;&#49;&#49;&#52;&#30005;&#35805;&#26412;&#44;&#31435;&#21363;&#33719;&#24471;&#28023;&#37327;&#21830;&#26426;</span>
</p>
</div>
<div class="weui-cell__ft"></div>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_hf'] && in_array('4', $pubmodule)}-->
<a class="weui-cell weui-cell_access" href="$SCRITPTNAME?id=xigua_hf&ac=add{$urlext}">
<div class="weui-cell__hd"><i class="iconfont icon-weixin3 main_color f28 mr10"></i></div>
<div class="weui-cell__bd">
<p>
<span class="f15">{lang xigua_hf:dotpub}</span>
<br>
<span class="c9 f13">{lang xigua_hb:dotpubdesc}</span>
</p>
</div>
<div class="weui-cell__ft"></div>
</a>
<!--{/if}-->
</div>
<div class="weui-cells__title">{lang xigua_hb:xuanlei}</div>
<div class="weui-grids bgf weui-grids-nob weui-grids-nob2 border_none" style="border-radius:1.5rem;">
<!--{loop $list $cat}-->
<!--{if $cat[pub_link]}-->
<!--{eval $cat[cat_link]=$cat[pub_link];}-->
<!--{/if}-->
<!--{if ($config[pubhide] && $cat[cat_link]) || $cat[yxff]}--><!--{eval continue;}--><!--{/if}-->
<a <!--{if $config['numi2']!=4&&$config['numi2']>0}--> {eval echo "style='width:".(100/$config['numi2'])."%!important'";}<!--{/if}--> <!--{if  !$cat[cat_link]||$cat[yxff]}-->onclick="return showcat('$cat[id]', '$cat[name]');"<!--{else}-->href="$cat[cat_link]"<!--{/if}--> class="weui-grid js_grid">
<div class="weui-grid__icon"><img style="width:40px; height:40px;padding:0px;" src="$cat[icon]" alt=""></div>
<p class="weui-grid__label" style="font-size:14px!important;margin-left:12px;margin-top:20px;"> $cat[name] </p>
</a>
<!--{/loop}-->
</div>
<!--{if $_G['cache']['plugin']['xigua_hs']['showinpub']}-->
<!--{eval $sh=C::t('#xigua_hs#xigua_hs_shanghu')->fetch_all_by_where(array('uid='.$_G['uid'].' and display=1 and endts>='.TIMESTAMP));}-->
<!--{if $sh && ($_G['cache']['plugin']['xigua_hm']||$_G['cache']['plugin']['xigua_hd']||$_G['cache']['plugin']['xigua_hk']||$_G['cache']['plugin']['xigua_pt']||$_G['cache']['plugin']['xigua_sp'])}-->
<!--{eval $vips = C::t('#xigua_hs#xigua_hs_vip')->fetch_all_by_page(0 , 99, 'id'); }-->
<div class="weui-cells__title">{lang xigua_hb:fbhd} </div>
<div class="weui-grids bgf weui-grids-nob weui-grids-nob2 border_none shp">
<!--{if $_G['cache']['plugin']['xigua_hm']}-->
<!--{if in_array('qianggou', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_hm&ac=my_seckill&auto=1&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-qianggou main_color f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_hm:new_seckill}</p>
</a>
<!--{/if}-->

<!--{if in_array('youhui', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_hm&ac=my_seckill&do=quan&auto=1&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-icozhekouquan color-rss f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_hs:ac_youhui}</p>
</a>
<!--{/if}-->
<!--{/if}-->

<!--{if $_G['cache']['plugin']['xigua_hd'] && in_array('jianjia', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_hd&ac=my_evt&auto=1&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-40kanjia color-paypal f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_hs:ac_jianjia}</p>
</a>
<!--{/if}-->


<!--{if $_G['cache']['plugin']['xigua_hk'] && in_array('heika', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_hk&ac=add&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-huiyuan2 color-red f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_hs:ac_heika}</p>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_pt'] && in_array('pt', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_pt&ac=add&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-fenlei color-rss f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_pt:fqpt}</p>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_sp'] && in_array('tcsp', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_sp&ac=add&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-gouwuche color-rss f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_sp:fbsp}</p>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_he'] && in_array('huodong', $vips[$sh[0]['viptype']]['access'])}-->
<a href="$SCRITPTNAME?id=xigua_he&ac=chosecat&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-huodongxiangqu1  color-success f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_he:new_pub}</p>
</a>
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_dh']}-->
<a href="$SCRITPTNAME?id=xigua_dh&ac=join&mobile=2{$urlext}" class="weui-grid js_grid">
<div class="weui-grid__icon">
<i class="iconfont icon-dianhuaben main_color f24"></i>
</div>
<p class="weui-grid__label">{lang xigua_dh:fabu}</p>
</a>
<!--{/if}-->
</div>
<!--{/if}-->

<!--{/if}-->
<script>var CAT = [];<!--{loop $list $cat}--><!--{if $cat[child]}--><!--{loop $cat[child] $c}--><!--{if $c[yxff]}--><!--{eval continue;}--><!--{/if}-->
CAT.push({pid:'$c[pid]', id:'$c[id]', name:'$c[name]', cat_link:'{echo $c[pub_link] ?$c[pub_link]:$c[cat_link]}', icon:'$c[icon]'});
<!--{/loop}--><!--{/if}--><!--{/loop}-->
<!--{if $_GET[ct]}-->var ct = '';for(var i =0; i<CAT.length; i++){if(CAT[i].id=='$_GET[ct]'){ct = CAT[i].name}}
showcat('$_GET[ct]', ct);
<!--{eval $pub0 = "$SCRITPTNAME?id=xigua_hb&ac=pub&ct=$_GET[ct]";}-->
<!--{/if}-->
function showcat(id, name){var act = [];for(var i =0; i<CAT.length; i++){
if(CAT[i].pid==id){var surl = '$SCRITPTNAME?id=xigua_hb&ac=pub&step=3&catid='+CAT[i].id+_URLEXT;
if(CAT[i].cat_link){surl = CAT[i].cat_link;}
act.push({text: '<a class="sel_a" href="'+surl+'">'+(CAT[i].icon ? '<img class="pubcimg" src="'+CAT[i].icon+'" />' :'')+CAT[i].name+'</a>'});}}
if(act.length==0){window.location.href = '$SCRITPTNAME?id=xigua_hb&ac=pub&step=3&catid='+id+_URLEXT;return false;}
$.actions({title: '{lang xigua_hb:fabu0}'+name+'', actions: act});
$('#weui-actionsheet .weui-actionsheet__cell').each(function(){if($(this).find('.sel_a').length>0){$(this).css('padding', 0);$(this).find('.sel_a').css('margin', 0).css('padding', 0).css('height', '2.4rem').css('line-height', '2.4rem');}});return false;}
<!--{if $_GET[st]<=0 && !$_GET[chs] && ($config[allowfzpub]==2||$config[allowfzpub]==3) && $_G['cache']['plugin']['xigua_st']}-->
<!--{eval $hotcity = DB::fetch_all("select * from %t WHERE status=1 ORDER BY displayorder DESC", array('xigua_st'), 'stid');}-->
var ststr = '', stlink = window.location.href;
<!--{if $config[allowfzpub]==3}-->
ststr+="<a style='width:calc(33.3333% - 1rem);margin-bottom:.75rem' href=\""+stlink+"&chs=1&st=0\" class=\"z main_bg\">{$_G['cache']['plugin']['xigua_st']['zongname']}</a>";
<!--{/if}-->
<!--{loop $hotcity $v}-->
ststr+= "<a style='width:calc(33.3333% - 1rem);margin-bottom:.75rem' href=\""+stlink+"&chs=1&st={$v[stid]}\" class=\"z main_bg\">{eval $_stname = $v[name2] ? $v[name2] :$v[name];echo strip_tags($_stname);}</a>";
<!--{/loop}-->
$.modal({title: "{lang xigua_hb:qingxuan}{lang xigua_st:st}",text: "<div class='postsite cl'>"+ststr+"</div>",buttons: [{text:"{lang xigua_hb:close}",className:"default", onClick: function(){window.location.href=stlink} }]});
<!--{/if}-->
</script>
<!--{eval $tabbar=1;}-->
<!--{elseif $_GET[step]==3}-->
<form action="$SCRITPTNAME?id=xigua_hb&ac=pub&step=$_GET[step]&catid=$_GET[catid]" method="post" id="forms" enctype="multipart/form-data" onsubmit="return false;">
    <input type="hidden" name="dig_pub" id="set_dig_pub" value="">
    <input type="hidden" name="redb" id="set_redb" value="">
    <input type="hidden" name="closecomment" id="set_closecomment" value="">

    <!--{loop $_GET $key $item}-->
<input type="hidden" name="$key" value="{$item}">
<!--{/loop}-->
<input type="hidden" name="formhash" value="{FORMHASH}">
<input type="hidden" name="form[catid]" value="{$_GET[catid]}">
<!--{if $old_data}--><input type="hidden" name="pubid" value="$old_data[id]" /><!--{/if}-->
<input type="hidden" id="dist1" name="form[dist1]" value="{echo $_GET[province] ? $_GET[province] : $old_data[dist1]}">
<input type="hidden" id="dist2" name="form[dist2]" value="{echo $_GET[city] ? $_GET[city] : $old_data[dist2]}">
<input type="hidden" id="dist3" name="form[dist3]" value="{echo $_GET[district] ? $_GET[district] : $old_data[dist3]}">
<!--{if ($catinfo[price]>0||trim($catinfo[multiprice])) && $needsafe}-->
<script>$.alert('{lang xigua_hb:clmzbzc}', function() {window.location.href='$SCRITPTNAME?id=xigua_hb$urlext';});</script>
<!--{/if}-->


<!--{if $_G['cache']['plugin']['xigua_hk'] && $_G['cache']['plugin']['xigua_hk']['xinzhe']> 0 && $_G['cache']['plugin']['xigua_hk']['xinzhe']<=10}-->
<!--{eval $card = C::t('#xigua_hk#xigua_hk_card')->fetch_online_card($_G[uid]); }-->
<style>.c0{color:#FAE4E0}.kclink{padding: 0 .75rem;font-size:.65rem;background:linear-gradient(90deg,#755204 0,#373737 100%);margin:.75rem 0;border-radius:.5rem;box-shadow: 0 0 .05rem #373737;line-height:2rem;height:2rem}</style>

<!--{/if}-->

<div class="weui-cells weui-cells_form before_none after_none vars_cells" style="margin-top:50px;">
<!--{loop $vars $var}--><!--{eval
$defaultvalue = $old_data?$old_data[vars][$var[pluginvarid]][value]:$var[extra];
$var[placehd] = $var[placehd] ? $var[placehd] : $var[title];
}-->
<!--{if $var[type]=='number'}-->
<div class="weui-cell<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->">
<div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
<div class="weui-cell__bd">
<input class="weui-input" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> type="tel" placeholder="{$var[placehd]}" value="{$defaultvalue}" onkeyup="this.value=this.value.replace(/[^\d.]/g,'');" onafterpaste="this.value=this.value.replace(/[^\d.]/g,'');" <!--{if $var[maxlen]}-->maxlength="$var[maxlen]"<!--{/if}-->>
</div>
<!--{if $var[unitnew]}--><div class="weui-cell__ft">$var[unitnew]</div><!--{/if}-->
</div>
<!--{elseif $var[type]=='text' || $var[type]=='linkurl' || $var[type]=='hidefield'}-->
<div class="weui-cell<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->">
<div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
<div class="weui-cell__bd">
<input class="weui-input" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> type="text" placeholder="{$var[placehd]}" value="{$defaultvalue}"  <!--{if $var[maxlen]}-->maxlength="$var[maxlen]"<!--{/if}-->>
</div>
<!--{if $var[unitnew]}--><div class="weui-cell__ft">$var[unitnew]</div><!--{/if}-->
</div>
<!--{elseif $var[type]=='textarea'}-->
<div class="weui-cell">
<div class="weui-cell__bd">
<textarea class="weui-textarea" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> placeholder="{$var[placehd]}" rows="3" value="{$defaultvalue}"  <!--{if $var[maxlen]}-->maxlength="$var[maxlen]"<!--{/if}-->>{$defaultvalue}</textarea>
</div>
</div>
<!--{elseif $var[type]=='area'}-->
<div class="weui-cell weui-cell_access<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->">
<div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
<div class="weui-cell__bd">
<input class="weui-input" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> id="city_picker_{$var[pluginvarid]}" type="text" placeholder="{$var[placehd]}" value="{echo $defaultvalue ? $defaultvalue : ''}">
<script>
+function($){
$.rawCitiesData = $cityjson;
}($);
</script>
<script type="text/javascript" src="source/plugin/xigua_hb/static/js/city-picker.js?{VERHASH}" charset="utf-8"></script>
<script>
$("#city_picker_{$var[pluginvarid]}").cityPicker({/*showDistrict: false,*/
title: "{lang xigua_hb:qingxuan}{$var[title]}"
});
</script>
</div>
<div class="weui-cell__ft"></div>
</div>
<!--{elseif $var[type]=='datetime'}-->
<div class="weui-cell<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
<div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
<div class="weui-cell__bd">
<input class="weui-input" id="datetime_picker{$var[pluginvarid]}" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> type="text" placeholder="{$var[placehd]}" value="{$defaultvalue}">
</div>
<script>$("#datetime_picker{$var[pluginvarid]}").datetimePicker();</script>
</div>
<!--{elseif $var[type]=='date'}-->
<div class="weui-cell<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
<div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
<div class="weui-cell__bd">
<input class="weui-input" id="date_picker{$var[pluginvarid]}" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> type="text" placeholder="{$var[placehd]}" value="{$defaultvalue}">
</div>
<script>$("#date_picker{$var[pluginvarid]}").calendar();</script>
</div>
<!--{elseif $var[type]=='select'}-->
<!--{eval $extra1 = C::t('#xigua_hb#xigua_hb_cat')->get_tree($var[extra], $old_data[vars][$var[pluginvarid]][value]);}-->
<!--{if $extra1[1]}-->
<div class="weui-cell weui-cell_access<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
<div class="weui-cell__hd">
<label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
</div>
<div class="weui-cell__bd">
<input class="weui-input" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}--> id="select_picker_{$var[pluginvarid]}" type="text" placeholder="{$var[placehd]}" value="$extra1[4]" />
<input type="hidden" name="form[vars][{$var[pluginvarid]}]" id="sel_picker_{$var[pluginvarid]}" value="{echo $old_data ? $old_data[vars][$var[pluginvarid]][value] : $extra1[5][0]}" />
<script type="text/javascript" src="source/plugin/xigua_hb/static/js/city-picker.js?{VERHASH}" charset="utf-8"></script>
<script>$("#select_picker_{$var[pluginvarid]}").cityPicker({
showDistrict:<!--{if $extra1[1]==1}-->false<!--{else}-->true<!--{/if}-->,
citiesData:{echo json_encode($extra1[2]);},
title: "{lang xigua_hb:qingxuan}{$var[title]}",
onChange:function(v, values){
var len1 = values[values.length-1] ? values.length - 1 : values.length - 2;
var indexdata = {echo json_encode($extra1[5]);};
$('#sel_picker_{$var[pluginvarid]}').val(indexdata[values[len1]]);
}
});</script>
</div>
<div class="weui-cell__ft"></div>
</div>
<!--{else}-->
<div class="weui-cell weui-cell_select weui-cell_select-after<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
<div class="weui-cell__hd">
<label for="" class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
</div>
<div class="weui-cell__bd">
<select class="weui-select" name="form[vars][{$var[pluginvarid]}]" <!--{if $old_data && $var[unchangeable]}-->disabled="disabled"<!--{/if}-->>
<!--{loop $extra1[0] $tmp1 $tmp2}-->
<!--{eval $tmp2 = $tmp2['name']}-->
<option value="$tmp1" <!--{if {$old_data[vars][$var[pluginvarid]][value]}==$tmp1}-->selected="selected"<!--{/if}-->>$tmp2</option>
<!--{/loop}-->
</select>
</div>
</div>
<!--{/if}-->
<!--{elseif $var[type]=='twoselects'}--><!--{eval $extra2 = C::t('#xigua_hb#xigua_hb_cat')->parse_extra($var[extra], $old_data[vars][$var[pluginvarid]][value], $var[type]);}-->
<!--{if $extra2[1]}-->
<!--{eval $extratmp2 = $extra2;$extdefaultvalue2 = $defaultvalue;$extratmpid2 = 'select_picker_'.$var['pluginvarid'];}-->
<div class="weui-cell weui-cell_access<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
    <div class="weui-cell__hd">
        <label for="" class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
    </div>
    <div class="weui-cell__bd">
        <input class="weui-input" name="form[vars][{$var[pluginvarid]}]" readonly="readonly" id="select_picker_{$var[pluginvarid]}" type="text" data-title="$var[title]" placeholder="{$var[placehd]}" value="{echo $old_data[vars][$var[pluginvarid]][value]?$old_data[vars][$var[pluginvarid]][value] : ''}" <!--{if $old_data && $var[unchangeable]}-->data-noedit="1" disabled="disabled"<!--{/if}--> />
    </div>
    <div class="weui-cell__ft"></div>
</div>
<!--{/if}-->
<!--{elseif $var[type]=='mselects'}--><!--{eval $extra1 = C::t('#xigua_hb#xigua_hb_cat')->get_tree($var[extra], $old_data[vars][$var[pluginvarid]][value]);}-->
<!--{if $extra1[1]}--><!--{eval $extratmp = $extra1;$extdefaultvalue = $defaultvalue;$extratmpid = 'select_picker_'.$var['pluginvarid'];}-->
<div class="weui-cell weui-cell_access<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
    <div class="weui-cell__hd">
        <label for="" class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
    </div>
    <div class="weui-cell__bd">
        <input class="weui-input" name="form[vars][{$var[pluginvarid]}]" readonly="readonly" id="select_picker_{$var[pluginvarid]}" type="text" data-title="$var[title]" placeholder="{$var[placehd]}" value="{echo $old_data[vars][$var[pluginvarid]][value]?$old_data[vars][$var[pluginvarid]][value] : ''}" <!--{if $old_data && $var[unchangeable]}-->data-noedit="1" disabled="disabled"<!--{/if}--> />
    </div>
    <div class="weui-cell__ft"></div>
</div>
<!--{else}-->
<div class="weui-cell weui-cell_access<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
<div class="weui-cell__hd">
    <label for="" class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
</div>
<div class="weui-cell__bd">
    <input class="weui-input" name="form[vars][{$var[pluginvarid]}]" readonly="readonly" id="select_picker_{$var[pluginvarid]}" type="hidden" data-title="$var[title]" placeholder="{$var[placehd]}" value="{echo $old_data[vars][$var[pluginvarid]][value]?$old_data[vars][$var[pluginvarid]][value] : ''}"  />
    <!--{eval $extdefaultvalueary = explode(',', $defaultvalue);}-->
    <div class="post-tags cl">
    <!--{loop $extra1[0] $__tmpv}-->
<a data-id="select_picker_{$var['pluginvarid']}" class="check6 weui-btn weui-btn_mini <!--{if in_array($__tmpv['name'], $extdefaultvalueary)}-->weui-btn_primary<!--{else}-->weui-btn_default <!--{/if}-->" data-title="{$__tmpv['index']}">{$__tmpv['name']}</a>
    <!--{/loop}-->
    </div>
</div>
</div>
<!--{/if}-->
<!--{elseif $var[type]=='selects'}-->
<!--{eval $extra = trim($var[extra]);$extra = explode("\n", $extra);}-->
<div class="weui-cell weui-cell_select weui-cell_select-after<!--{if $var[jiaoyi]>200}--> group_200 group_{$var[jiaoyi]}{eval $group_key[$var[jiaoyi]]++;}<!--{/if}-->" >
    <div class="weui-cell__hd">
        <label for="" class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
    </div>
    <div class="weui-cell__bd">
        <select multiple="multiple" class="weui-select" name="form[vars][{$var[pluginvarid]}][]" <!--{if $old_data && $var[unchangeable]}-->disabled="disabled"<!--{/if}--> >
        <!--{loop $extra $extra_string}-->
        <!--{eval list($tmp1, $tmp2) = explode('=', trim($extra_string));}-->
        <option value="$tmp1" <!--{if in_array($tmp1, $old_data[vars][$var[pluginvarid]][value]) }-->selected="selected"<!--{/if}--> >$tmp2</option>
        <!--{/loop}-->
        </select>
    </div>
</div>
<!--{elseif $var[type]=='location' && $_G['cache']['plugin']['xigua_hs']}-->
<!--{eval $defaultvalue = $old_data?$old_data[vars][$var[pluginvarid]][value][0]:'';$needpopmap = 1;}-->
<div class="weui-cell weui-cell_vcode">
<div class="weui-cell__hd">
<label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label>
</div>
<div class="weui-cell__bd enter_addr">
<input class="weui-input" id="location_{$var[pluginvarid]}" name="form[vars][{$var[pluginvarid]}][]" type="text" placeholder="{$var[placehd]}" value="{echo $_GET[addr] ? $_GET[addr] : $defaultvalue}" <!--{if $old_data && $var[unchangeable]}-->readonly="readonly"<!--{/if}-->>
<input type="hidden" id="location_lat_{$var[pluginvarid]}" name="form[vars][{$var[pluginvarid]}][]" value="{echo $getlat ? $getlat: $old_data[vars][$var[pluginvarid]][value][1]}">
<input type="hidden" id="location_lng_{$var[pluginvarid]}" name="form[vars][{$var[pluginvarid]}][]" value="{echo $getlng ? $getlng: $old_data[vars][$var[pluginvarid]][value][2]}">
</div>
<div class="weui-cell__ft">
<!--{if $cannewmap}-->
<a href="$newmapurl" class="weui-vcode-btn" type="button">{lang xigua_hb:dingwei}</a>
<!--{else}-->
<button class="weui-vcode-btn openlocation" data-id="{$var[pluginvarid]}" type="button">{lang xigua_hb:dingwei}</button>
<!--{/if}-->
</div>
</div>
<!--{elseif $var[type]=='pics'}-->
<!--{eval $var[placehd] = intval( $var[placehd]);}-->
<div class="weui-cell">
<div class="weui-cell__bd">
<div class="weui-uploader">
<div class="weui-uploader__hd">
<p class="weui-uploader__title">{$var[title]}<!--{if $v[required][$__k]}--><em class="color-red">*</em><!--{/if}--></p>
<div class="weui-uploader__info">{echo $var[placehd] ? str_replace('n', $var[placehd], lang_hb('zuiduozhao',0)) : ''} 4343433434</div>
</div>
<div class="weui-uploader__bd">
<ul class="weui-uploader__files" data-max="{$var[placehd]}" data-maxtip="{echo str_replace('n', $var[placehd], lang_hb('zuiduozhao',0))}">
<!--{loop $old_data[vars][$var[pluginvarid]][value] $img}-->
<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url($img)">
<input type="hidden" name="form[vars][{$var[pluginvarid]}][]" value="$img"/>
<div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div>
</li>
<!--{/loop}-->
</ul>
<div class="weui-uploader__input-box">
<!--{if (HB_INWECHAT&&$config[multiupload]) || IN_MAGAPP}-->
<a class="weui-uploader__input" data-name="form[vars][{$var[pluginvarid]}]" data-multi="1"></a>
<!--{else}-->
<input class="weui-uploader__input" data-name="form[vars][{$var[pluginvarid]}]" type="file" data-multi="1" accept="image/*">
<!--{/if}-->
</div>
</div>
</div>
</div>
</div>



<!--{elseif !$hastw && $var[type]=='tuwen'}-->
<!--{eval $hastw = $var;}-->
<div class="weui-cell weui-cell_access tu_wen_input"  data-req="$var[required]" data-id="{$var[pluginvarid]}" data-placeholder="{$var[title]}">
    <div class="weui-cell__hd"><label class="weui-label">{$var[title]}<!--{if $var[required]}--><em class="color-red">*</em><!--{/if}--></label></div>
    <div class="weui-cell__bd">
        <input id="tuwen_form" class="weui-input" type="text" name="tuwen_form" readonly placeholder="{$var[placehd]}">
    </div>
    <div class="weui-cell__ft"></div>
</div>
<!--{/if}-->
<!--{/loop}-->

{eval}

$tb_cus_base_config  =  $_G['cache']['plugin']['tb_cus_base'];
        
    $onlinecount = explode("~",$tb_cus_base_config['onlinecount']);
    
    $showonlinecount = rand($onlinecount[0],$onlinecount[1]);
    
    



    $showonlinecount = 0;

    $cache_file_left = DISCUZ_ROOT.'./data/sysdata/cache_tb_cus_base.php';
    if(($_G['timestamp'] - @filemtime($cache_file_left)) > $tb_cus_base_config['cachetime']*60) {

            $showonlinecount = rand($onlinecount[0],$onlinecount[1]);
            
     

            $contents[]=$showonlinecount;
            $cacheArray .= "\$contents=".arrayeval($contents).";\n";
            writetocache('tb_cus_base', $cacheArray);
    }else{

            include_once $cache_file_left;
            $showonlinecount=$contents[0];
            

    }
 
$onlinenum = DB::result_first("SELECT count(*) FROM ".DB::table('common_session')) * 25;

$user = DB::result_first("SELECT count(*) FROM ".DB::table('common_member')) * 11;

if($_G['uid']){

$uid = $_G['uid'];
$usermoney = DB::fetch_first('SELECT money FROM %t WHERE uid=%d', array("xigua_hb_user", $uid));
$usermoney = $usermoney['money'];
$usermfsxnum = DB::fetch_first('SELECT mfsxnum FROM %t WHERE uid=%d', array("xigua_hb_user", $uid));
$usermfsxnum = $usermfsxnum['mfsxnum'];


$software = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d', array("tb_cus_taojing_software", $uid));
$software = $software['sc']?$software['sc']:0;


    $card = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d', array("tb_cus_card", $uid));
    $card = $card['sc']?$card['sc']:0;

//用户

$totalmypub = C::t('#xigua_hb#xigua_hb_pub')->count_by_uid($_G['uid']);

//悬赏
$xuanshang = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d', array("tb_xuanshang", $uid));
$xuanshang = $xuanshang['sc']?$xuanshang['sc']:0;


$xuanshang = $xuanshang['sc']?$xuanshang['sc']:0;

//头条
$toutiao = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d AND endtime>'.time(), array("tb_toutiao", $uid));
$toutiao = $toutiao['sc']?$toutiao['sc']:0;

$xiguahh_user = C::t("#tb_cus_xiguahh#tb_cus_xiguahh_user")->fetch_first_field_data("*","where uid={$uid}");

$ext2 = $xiguahh_user['money']?$xiguahh_user['money']:0.00;


$myextcredits = getuserprofile('extcredits' . $config['credit_type']);

// $ext2 = getuserprofile('extcredits2');


}

if($hhme['status'] == 1){
    $showhhmename = $hhme[joininfo][name];
}else{

    $oldback  = $hhme['oldback'];
    $oldjoin = unserialize($oldback);
    $oldjoin = unserialize($oldjoin['joininfo']);
    $showhhmename = $oldjoin['name'];
}

if($hhme['endts']){
    $hhendts = date("Y-m-d",$hhme['endts']);
}

 $uidfollow = DB::result_first("select count(id) from %t  WHERE favtype='favuser' and favid=%d", array('xigua_hb_follow', $_G['uid']));

$uidfollow1 = DB::result_first("select count(id) from %t  WHERE favtype='favuser' and uid=%d", array('xigua_hb_follow', $_G['uid']));

   $uidvote = DB::result_first("select count(id) from %t  WHERE uid=%d", array('xigua_hb_votelog', $_G['uid']));

$sj = DB::result_first("select count(id) from %t  WHERE uid=%d", array('xigua_hs_follow', $_G['uid']));

require DISCUZ_ROOT . './source/plugin/tb_cus_mobilereg/common.php';
$yqmcode = getUserInviteCode($_G['uid']);


$commentnew = DB::result_first("select cid from %t  WHERE new=1 AND touid=%d", array('xigua_hb_comment', $_G['uid']));



{/eval}

{if $hhme[joininfo][name] == "签米会员" || $hhme['joininfo']['name'] == "商业会员" || $hhme['joininfo']['name'] == "星益会员"}


<div class="crypto-upload-container" style="background-color: transparent;">
    <div class="tgb-publish-form-spacer" style="height:50px;"> </div>

    <div class="weui-cell crypto-upload-cell" <!--{if $catinfo['zdtps']<0}-->style="display:none"<!--{/if}-->>
    <div class="weui-cell__bd">
        <div class="weui-uploader crypto-uploader">
            <div class="weui-uploader__bd">
                <ul class="weui-uploader__files crypto-upload-files" data-max="{echo intval($config['maximg'])}" data-maxtip="{echo str_replace('n', $config['maximg'], lang_hb('zuiduozhao',0))}" id="uploaderFiles">
                    <!--{loop $old_data[imglist] $img}-->
                    <li class="weui-uploader__file weui-uploader__file_status crypto-upload-file" style="background-image:url($img)">
                        <input type="hidden" name="form[imglist][]" value="$img"/>
                        <div class="weui-uploader__file-content crypto-file-content">
                            <i class="weui-icon-warn iconfont icon-shanchu crypto-delete-icon"></i>
                        </div>
                    </li>
                    <!--{/loop}-->
                </ul>
                <!--{if is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php')&&is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/video.php')}-->
                <!--{eval
                $showv = ($config['qn_ak'] && $config['qn_sk'] && $config['qn_bk'] && $config['qn_url']) || ($config['ACCESS_ID'] && $config['ACCESS_KEY'] && $config['ENDPOINT'] && $config['BUCKET']);
                }-->
                <div class="weui-uploader__input-box crypto-upload-input">
                    <!--{if (HB_INWECHAT&&$config[multiupload]) || IN_MAGAPP}-->
                    <a id="uploaderInput" class="weui-uploader__input crypto-upload-btn" data-name="form[imglist]" data-multi="1"></a>
                    <!--{else}-->
                    <input id="uploaderInput" class="weui-uploader__input crypto-upload-btn" data-name="form[imglist]" type="file" data-multi="1" accept="image/*" <!--{if $config[closecj]}-->multiple="multiple"<!--{/if}-->>
                    <!--{/if}-->
                 
                
                </div>
                <!--{if IN_MAGAPP && $_G['cache']['hb_ext_config']['showpzmg']}--><!--{template xigua_hb:fixpub_mag}--><!--{/if}-->
         
                <!--{else}-->
                <div class="weui-uploader__input-box crypto-upload-input">
                    <!--{if (HB_INWECHAT&&$config[multiupload]) || IN_MAGAPP}-->
                    <a id="uploaderInput" class="weui-uploader__input crypto-upload-btn" data-name="form[imglist]" data-multi="1"></a>
                    <!--{else}-->
                    <input id="uploaderInput" class="weui-uploader__input crypto-upload-btn" data-name="form[imglist]" type="file" data-multi="1" accept="image/*" <!--{if $config[closecj]}-->multiple="multiple"<!--{/if}-->>
                    <!--{/if}-->
                </div> 
                <!--{if IN_MAGAPP && $_G['cache']['hb_ext_config']['showpzmg']}--><!--{template xigua_hb:fixpub_mag}--><!--{/if}-->
                <!--{/if}-->
            </div>
        </div>
    </div>
</div>
{else}
<div class="crypto-upload-container" style="padding:30px 0 0 0;margin-top:-20px">
    
        <div class="tgb-publish-form-spacer" style="height:50px;"> </div>


    
    
    
<div class="crypto-vip-promo" style="border-radius:16px; margin:15px; text-align:center;">
   
    <div class="crypto-vip-text">
        <span class="crypto-vip-title">推广宝会员，解锁上传图片特权 <a href="plugin.php?id=xigua_hb&ac=vip" class="crypto-vip-btn">
            立即开通
        </a></span>
        
    </div>
</div>
{/if}

{if $hhme[joininfo][name] == "签米会员" || $hhme['joininfo']['name'] == "商业会员" || $hhme['joininfo']['name'] == "星益会员"}
    <div class="crypto-input-group crypto-contact-input" style="margin-top:15px; margin-bottom:15px;">
      
        <input name="form[new_lianxi]" class="crypto-input crypto-contact-field" value="{$old_data[new_lianxi]}" placeholder="你的联系方式，例如: 微信号: XX">
    </div>
    {else}
<div class="crypto-vip-promo" style="border-radius:16px; margin:15px; text-align:center;">

    <div class="crypto-vip-text">
        <span class="crypto-vip-title">推广宝会员，解锁留联系方式特权 <a href="plugin.php?id=xigua_hb&ac=vip" class="crypto-vip-btn">
            立即开通
        </a></span>
        
    </div>
</div>
{/if}

<div class="crypto-input-group crypto-title-input" style="margin-top:15px; margin-bottom:15px;">
  
    <input name="form[title]" class="crypto-input crypto-title-field" value="{$old_data[title]}" placeholder="请编辑一个吸引人的项目标题">
</div>

<!--{if !$hastw}-->
<div class="crypto-input-group crypto-description-input" style="margin-top:15px; margin-bottom:25px;">
  
    <textarea name="form[description]" class="crypto-textarea crypto-description-field" placeholder="请输入项目介绍，言简意赅能吸引更多人，内容不能包含表情包，否则无法发布。" rows="6">$old_data[description]</textarea>
</div> 
<!--{/if}-->

<!--{if $showv&&!$catinfo[a_video]}-->
<!--{template xigua_hb:video_field}-->
<!--{/if}-->

<style>
    /* 上传区域轻奢风格 */
    .crypto-upload-container {
        background: rgba(255,255,255,0.85) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 2rem;
        border: 1px solid rgba(255,190,90,0.35);
        box-shadow: var(--shadow-md);
        margin: 16px;
        padding: 20px;
    }
    .crypto-uploader {
        background: transparent !important;
    }
    .crypto-upload-files {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }
    .crypto-upload-file {
        width: 100px !important;
        height: 100px !important;
        border-radius: 16px !important;
        border: 2px solid rgba(255,190,90,0.3) !important;
        background-size: cover !important;
        background-position: center !important;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .crypto-file-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .crypto-upload-file:hover .crypto-file-content {
        opacity: 1;
    }
    .crypto-delete-icon {
        color: #fff !important;
        font-size: 20px !important;
        cursor: pointer;
    }
    .crypto-upload-input {
        background: rgba(255,245,235,0.5) !important;
        border: 2px dashed rgba(255,200,120,0.35) !important;
        border-radius: 20px !important;
        padding: 40px 20px !important;
        text-align: center !important;
        transition: 0.2s;
        margin-top: 20px !important;
    }
    .crypto-upload-input:hover {
        border-color: #ff7b00 !important;
        background: rgba(255,220,180,0.3) !important;
    }
    .crypto-upload-btn {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        opacity: 0 !important;
        cursor: pointer !important;
    }
    .crypto-vip-promo {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-card);
        border-radius: 1.5rem;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        box-shadow: var(--shadow-sm);
    }
    .crypto-vip-text {
        flex: 1;
        text-align: left;
    }
    .crypto-vip-title {
        color: var(--text-primary) !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        display: block;
        margin-bottom: 0px;
    }
    .crypto-vip-btn {
        color: white !important;
        padding: 4px 14px !important;
        border-radius: 60px !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        display: inline-block !important;
        background: linear-gradient(135deg, #ff7b00, #e63946) !important;
        box-shadow: 0 5px 15px rgba(255,50,0,0.25) !important;
        transition: 0.2s !important;
    }
    .crypto-vip-btn:active { transform: scale(0.96); }
    .crypto-input-group {
        margin: 0px 30px 0px 0px;
        position: relative;
    }
    .crypto-input,
    .crypto-textarea {
        width: 100% !important;
        background: rgba(255,245,235,0.7) !important;
        border: 1px solid rgba(255,200,120,0.35) !important;
        border-radius: 1.5rem !important;
        padding: 14px 16px !important;
        color: var(--text-primary) !important;
        font-size: 14px !important;
        transition: 0.2s !important;
    }
    .crypto-input:focus,
    .crypto-textarea:focus {
        outline: none !important;
        border-color: #ff7b00 !important;
        box-shadow: 0 0 0 2px rgba(255,123,0,0.15) !important;
    }
    .crypto-input::placeholder,
    .crypto-textarea::placeholder {
        color: var(--text-tertiary) !important;
    }
    .crypto-textarea {
        min-height: 180px !important;
        resize: vertical !important;
        line-height: 1.6 !important;
    }
    .crypto-notice {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-card);
        border-radius: 2rem;
        padding: 20px;
        box-shadow: var(--shadow-sm);
    }
    .crypto-notice-warning {
        color: #d35400 !important;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .crypto-notice-agreement {
        color: var(--text-tertiary);
        font-size: 12px;
    }
    .crypto-notice-link {
        color: #ff7b00 !important;
    }
    .crypto-glass {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--border-card);
        box-shadow: var(--shadow-sm);
        border-radius: 2rem;
    }
</style>
   
   
   
   
<!--{if $sh}-->
<div class="weui-cell weui-cell_access">
<div class="weui-cell__hd"><label for="" class="weui-label">{lang xigua_hs:guanlian}</label></div>
<div class="weui-cell__bd">
<input id="choose_sh" class="weui-input choose_selet" name="form[shname]" readonly type="text" value="{echo $old_data ? $old_data['shname'] : ''}" placeholder="{lang xigua_hs:qxzguanlian}">
</div>
<div class="weui-cell__ft"></div>
</div>
<!--{/if}-->
<!--{if $splist}-->
<div class="weui-cell weui-cell_access">
<div class="weui-cell__hd"><label class="weui-label">&#20851;&#32852;&#21830;&#21697;</label></div>
<div class="weui-cell__bd">
<input id="choose_sp" class="weui-input choose_selet" name="form[goodname]" readonly type="text" value="{echo $old_data['goodname'] ? $old_data['goodname'] : ''}" placeholder="&#35831;&#36873;&#25321;&#20851;&#32852;&#21830;&#21697;">
</div>
<div class="weui-cell__ft"></div>
</div>
<!--{/if}-->


<!--{if array_filter($catinfo['tag'])}-->
<div class="weui-cell" style="padding-bottom:.3rem">
<div class="weui-cell__bd">
<div class="post-tags cl" id="post-typeid">

</div>
</div>
</div>
<!--{/if}-->
</div>
<!--{if $group_key}--><style>.group_200{float: left;line-height: 2.5rem;display:block;padding: 0!important;text-align:center}
.group_200 .weui-label{width:100%}.group_200.weui-cell:before{display:none}.group_200 .weui-input{text-align:center}.group_200 .weui-select{text-align:center;padding:0!important}
.group_200.weui-cell_select .weui-cell__bd:after{display:none}.group_200 .weui-label{margin-bottom:-.25rem;line-height:2rem;margin-top:.25rem}<!--{loop $group_key $__class $__class_num}-->.group_{$__class}{ width:{echo 100/$__class_num;}%}<!--{/loop}--></style><!--{/if}-->

<!--{eval
$m = unserialize($config['mustbind']);
$lastmobile = $lastmobile?$lastmobile:'';
}-->



<!--{if $config[allowfzpub]==1 && $_G['cache']['plugin']['xigua_st']}-->
<!--{eval $hotcity = DB::fetch_all("select * from %t WHERE status=1 ORDER BY displayorder DESC", array('xigua_st'), 'stid');}-->
<a class="weui-cell weui-cell_access" href="javascript:;">
    <div class="weui-cell__hd"><i class="iconfont icon-iconfenxiao color-dribbble"></i></div>
    <div class="weui-cell__hd"><label class="weui-label">{lang xigua_hb:fenzhanx}</label></div>
    <div class="weui-cell__bd"><input class="weui-input" id="site" readonly name="form[pubstids]" placeholder="{lang xigua_hb:qxzzd}" type="text" value="{$old_data[pubstids]}"></div><div class="weui-cell__ft"></div>
</a>
<!--{/if}-->
</div>






<!--{if $old_data['endts']<TIMESTAMP && $catinfo[multiprice]}-->
<!--{eval
$multiprice = array();
foreach(array_filter(explode("\n", trim($catinfo[multiprice]))) as $__k => $__v):
$multiprice[] = explode("|", trim($__v));
endforeach;
}-->
<div class="weui-cells__title">{lang xigua_hb:qxzmultiprice} </div>
<link rel="stylesheet" href="source/plugin/xigua_hb/static/css/taocan.css?4{VERHASH}" /><style>.car-type .car-year-season{background: linear-gradient(135deg, #ff7b00, #e63946);padding:0;max-width:4.5rem;width:auto}.car-type .type-item-active .car-active-c{border-bottom-color:#ff7b00}.car-type .type-item-active:after{border-color:#ff7b00}.car-type .type-item-active {background:{echo hb_hex2rgb('#ff7b00', 0.06)}}
.car-type .type-item{float:left;width:calc((100vw - 2.5rem) / 3);max-width:6rem;margin-right:.5rem;margin-top:.5rem;min-height:4.7333rem;height:auto;padding-left:.5rem}.type-discount + .car-year-season {margin-top: .4134rem;}.car-type .type-item:nth-child(3n){margin-right:0}.car-type .type-item:nth-child(1),.car-type .type-item:nth-child(2),.car-type .type-item:nth-child(3){margin-top:0}.car-type .car-tip{height:auto;min-height:1.0667rem;line-height:1.2;padding-right:.5rem}.car-type .car-year-season {max-width:4.2rem}
.longcard.type-item{width:100%;max-width:100%;margin-bottom:.5rem;height:auto;padding:.5rem;margin-right:0;min-height:.2rem}
.longcard.type-item .type-title{float:right;padding-top: .2rem;font-size: .64rem;margin-right:1rem}
.longcard.type-item .type-discount{float:left;padding-right:.25rem;min-height:1.2rem;line-height:1.2rem}
.longcard.type-item .type-discount .car-price{font-size:.9rem}
.longcard.type-item .car-tip{color:#3d2b1a;font-weight:600}
.longcard.type-item .car-year-season{float:left;margin-left:.5rem;margin-top:.2rem;max-width:100%;padding:.25rem .5rem}
</style><div id="dftvip" class="cl car-type"><div class="type-item-box" style="display:block"><!--{loop $multiprice $___k $___v}--><!--{eval
if((IN_APPBYME||IN_MAGAPP||IN_QIANFAN||IN_MOCUZ||$CMAPP) && is_numeric($___v[5])):
    $___v[0] = $___v[5];
endif;
}--><label for="s{$___k}" class="<!--{if $config[longcard]==2}-->longcard<!--{/if}--> type-item J_ping <!--{if $___k==0}-->type-item-active<!--{else}-->type-item-gray<!--{/if}-->"><!--{if $___v[4]}--><img class="tcbgjb" src="{$___v[4]}" onerror="this.error=null;$(this).remove()"><!--{/if}-->
<input type="radio" class="none" name="form[multiprice]" value="{echo $___k+1}" id="s{$___k}" <!--{if $___k==0}-->checked="checked"<!--{/if}-->>
<div class="type-title">{lang xigua_hb:lastts}{$___v[1]}{lang xigua_hb:day}</div><div class="car-sku-year cl"><div class="type-discount"><span class="car-price">{echo floatval($___v[0])}</span><span class="car-unit">{lang xigua_hb:yuan} <!--{if $___v[2] && $config[longcard]==2}-->&nbsp;&nbsp;$___v[2]<!--{/if}--></span></div><!--{if $___v[2]}--><div class="{echo dstrlen($___v[2])>18?'car-tip':'car-year-season'}" style="{if $config[longcard]==2}display:none{else}transform:scale(.95);{/if}">$___v[2]</div><!--{/if}-->
</div><div class="car-active-c"><i class="iconfont icon-xuanzhong"></i></div></label><!--{/loop}-->
</div></div><script>$(document).on('click','.J_ping', function () {$('.J_ping').addClass('type-item-gray').removeClass('type-item-active');$(this).addClass('type-item-active').removeClass('type-item-gray');});</script><!--{/if}-->

<div class="footer_fix" style="height:20px;"></div>
<div id="agree__text" class="weui-popup__container">
<div class="weui-popup__overlay"></div>
<div class="weui-popup__modal">
<div class="fixpopuper">
<article class="weui-article">
<h1>{lang xigua_hb:xiyi}</h1>
<section>
<section>
$config[xieyi]
</section>
</section>
</article>
<div class="footer_fix"></div>
<div class="bottom_fix"></div>
</div>
<div class="fix-bottom" >
<a class="weui-btn weui-btn_primary close-popup" href="javascript:;">{lang xigua_hb:woyi}</a>
</div>
</div>
</div>
<input type="hidden" id="location_lat" name="form[lat]" value="{echo $getlat ? $getlat: $old_data[lat]}">
<input type="hidden" id="location_lng" name="form[lng]" value="{echo $getlng ? $getlng: $old_data[lng]}">
<div class="fix-bottom" style="display:none;position: initial;margin-bottom:30px;">
<!--{if !$catinfo[width_hb] && !$old_data[id] && $realpubcnt<=0 && !$catinfo[multiprice] && !$needsafe && $catinfo[price]>0 && $catinfo[price]!='0.00'}-->
<!--{eval $catinfo_price = floatval($catinfo[price]);}-->
<!--<input type="button" class="weui-btn weui-btn_primary" name="dosubmit" id="submitnew" value="<!--{if $qianbaomfxxnum>0}-->{lang xigua_hb:queren}<!--{else}-->{lang xigua_hb:pay1}{$catinfo_price}{lang xigua_hb:yuan}{lang xigua_hb:fabu0}<!--{/if}-->" data-price="$catinfo_price" />
-->

    <script>if($('.dig_pub_div').length>0){
$(document).on('click','.dig_pub_div', function () {
var dpri = $('.dig_pub_div.active').find('.dig_pub_price').data('dpr');if(typeof dpri==='undefined'){dpri = 0;}
var showdpri = dpri+$catinfo_price;
if(showdpri>0){$('#submitnew').val('{lang xigua_hb:pay1}'+showdpri+'{lang xigua_hb:yuan}{lang xigua_hb:fabu0}');
}else{$('#submitnew').val('{lang xigua_hb:queren}');
}});}</script><!--{else}-->
<!--<input type="button" style="border-radius:30px;width: 60%;" class="weui-btn weui-btn_primary" name="dosubmit" id="submitnew" value="{lang xigua_hb:queren}" />
--><!--{/if}-->
</div>
<!--{if $hastw}--><!--{template xigua_hb:edit_jieshao_html}--><!--{/if}-->
</form>
<!--{/if}-->
</div>
<div id="popctrl" class="weui-popup__container">
<div class="weui-popup__overlay"></div>
<div class="weui-popup__modal">
<div style="height: 100vh"><img id="photo"></div>
<div class="pub_funcbar">
<a class="weui-btn close-popup weui-btn_primary" data-method="confirm">{lang xigua_hb:queding}</a>
<a class="weui-btn close-popup weui-btn_default" data-method="destroy">{lang xigua_hb:quxiao}</a>
</div>
</div>
</div>
<!--{if $extratmp}--><!--{template xigua_hb:pub_selects}--><!--{/if}-->
<!--{if $extratmp2}--><!--{template xigua_hb:pub_twoselects}--><!--{/if}-->

<!--{if $needpopmap && $_G['cache']['plugin']['xigua_hs']}-->
<script type="text/javascript" src="https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js?{VERHASH}"></script>
<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key={$_G['cache']['plugin']['xigua_hs']['mkey']}"></script>
<!--{if $_G['cache']['plugin']['xigua_hs']['baidusdk']}--><script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak={$_G['cache']['plugin']['xigua_hs']['baidusdk']}"></script><!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_hs']['google']}--><script src="https://maps.googleapis.com/maps/api/js?key={$hs_config[google]}&sensor=false"></script><!--{/if}-->
<div id="mapouter" style="z-index:999" class="weui-popup__container">
<div class="weui-popup__modal">
<div id="mapcontainer" style="position:absolute;width:100%;bottom:0;height:100vh"></div>
<div class="fix-bottom" style="background-color:transparent;padding-top:0">
<div class="weui-flex">
<a class="mt0 half weui-btn weui-btn_default close-popup" href="javascript:;">{lang xigua_hb:close}</a>
<a class="mt0 ml15 half weui-btn weui-btn_primary confirm-popup popupL" href="javascript:;">{lang xigua_hb:queding}</a>
</div>
</div>
</div>
</div>
<!--{/if}-->
<div class="masker" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);display:none;z-index:1000" onclick='$(".choose_selet").select("close")'></div>
<!--{template xigua_hb:common_footer}-->



<script>
    function showrule(){
        var dialog_h = $("#showrule").height()+80;
        layeropen = layer.open({
            type: 1,
            anim:2,
            shade:0.65,
            content: $("#showrule").html(),
        });
    }

    function colselayui(){
        layer.closeAll();
    }

    function showpubdig(){


        layeropen = layer.open({
            type: 1,
            anim:2,
            shade:0.65,
            content: $("#pubdig"),
        });
    }

    function showpubred(){
        layeropen = layer.open({
            type: 1,
            anim:2,
            shade:0.65,
            content: $("#pubred"),
        });
    }

    function showpubset(){
        layeropen = layer.open({
            type: 1,
            anim:2,
            shade:0.65,
            content: $("#pubset"),
        });
    }

</script>

<script>
    $(document).on('click', '.dig_pub_div', function () {

        var thisck = $(this).find('input[name="dig_pub"]');
        var s = thisck.is(':checked');
        if (s) {
            $(this).siblings().removeClass('active');
            $(this).siblings().find('input[name="dig_pub"]').removeAttr('checked');
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
        console.log(thisck);
        $("#set_dig_pub").val(thisck.val());

        var thistitle = $(this).find('.dig_pub_title');
        var thisprice = $(this).find('.dig_pub_price');
        $("#set_dig_pub_tips").html("【"+thisprice.text()+"】");


    });

    $(document).on('click', '.redb_div', function () {
        var thisck = $(this).find('input[name="redb"]');
        var s = thisck.is(':checked');
        if (s) {
            $(this).siblings().removeClass('active');
            $(this).siblings().find('input[name="redb"]').removeAttr('checked');
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
        $("#set_redb").val(thisck.val());
        var thistitle = $(this).find('.redb_title');
        var thisprice = $(this).find('.redb_price');
        $("#set_redb_tips").html("【"+thisprice.text()+"】");
    });

    $(document).on('click', '.weui-switch', function () {
        var checkzdpub = document.getElementById('checkzdpub');
        if(checkzdpub.checked){
            $("#set_closecomment").val(1);
        }else{
            $("#set_closecomment").val("");
        }

    });
</script>



<style>
    .layui-layer-title,.layui-layer-setwin{
        display: none;
    }
    .layui-layer-page{
        border-radius: 2rem;

    }

    .pop-up{
        border-radius: 2rem;
        width: 350px;
    }

    .popbtn{
        border: 1.5px solid #ff7b00;
        padding: 8px 15px;
        border-radius: 30px;
        color: #3d2b1a;
        text-align: center;
        width: 120px;margin: auto;
        font-weight: 600;
        background: #fff9f5;
    }

    .dig_pub_exts{padding:.25rem .75rem 0}.dig_pub_div{float:left;width:calc(33.33333333% - .46rem);background:#fff;font-size:.7rem;border-radius:1rem;position:relative;text-align:center;border:1px solid #f5f5fd;margin:.5rem .5rem .5rem 0}
    .dig_pub_div:nth-child(3n){margin-right:0}.dig_pub_div .dig_pub_subtag{position:absolute;top:-.5rem;border-radius:.25rem .25rem .25rem 0;left:-1px;padding:0 .5rem;color:#fff;box-shadow:0 0 10px rgba(0,0,0,.05)}.dig_pub_div .dig_pub_title{color:#3d2b1a;font-size:.7rem;margin-top:.4rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.dig_pub_div .dig_pub_yuan{color:#8b6f5c;font-size:.7rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;height:1.1rem}
    .dig_pub_div .dig_pub_price{font-size:1.4rem;line-height:1.4rem}.dig_pub_div .dig_pub_price em{font-size:.7rem;margin-right:.2rem}.dig_pub_div.active{border-color:#ff7b00}.pubdigc{background-position: -3.04rem -1.3333rem;position: absolute;background-repeat: no-repeat;background-size: 14.4533rem 6.7467rem;display: block;border-bottom: 1.2rem solid #ff7b00;border-left: 1.2rem solid transparent;width: 0;height: 0;bottom: 0;right: 0;display:none;}
    .pubdigc i{position: absolute;color: #fff;top: .3rem;font-size: .7rem;right: -0.1rem;z-index:9;transform: scale(.5);}.active .pubdigc{display:block}.pubdigo{overflow: hidden;padding: .45rem .5rem .3rem;border-radius: .1rem;position: relative;}.dig_pub_div.longdig{width:100%;text-align:left;margin:.45rem 0}
    .dig_pub_div.longdig .pubdigo{line-height:1.4rem}.dig_pub_div.longdig .dig_pub_title{float:left;margin-top:0}.dig_pub_div.longdig .dig_pub_price{float:right;margin-left:.75rem;font-size:1rem;margin-right:.5rem}.dig_pub_div.longdig .dig_pub_yuan{float:right;height:auto}

    .redb_exts{padding:.25rem .75rem 0}.redb_div{float:left;width:calc(33.33333333% - .46rem);background:#fff;font-size:.7rem;border-radius:1rem;position:relative;text-align:center;border:1px solid #f5f5fd;margin:.5rem .5rem .5rem 0}
    .redb_div:nth-child(3n){margin-right:0}.redb_div .redb_subtag{position:absolute;top:-.5rem;border-radius:.25rem .25rem .25rem 0;left:-1px;padding:0 .5rem;color:#fff;box-shadow:0 0 10px rgba(0,0,0,.05)}.redb_div .redb_title{color:#3d2b1a;font-size:.7rem;margin-top:.4rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}.redb_div .redb_yuan{color:#8b6f5c;font-size:.7rem;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;height:1.1rem}
    .redb_div .redb_price{font-size:1.4rem;line-height:1.4rem}.redb_div .redb_price em{font-size:.7rem;margin-right:.2rem}.redb_div.active{border-color:#ff7b00}.pubdigc{background-position: -3.04rem -1.3333rem;position: absolute;background-repeat: no-repeat;background-size: 14.4533rem 6.7467rem;display: block;border-bottom: 1.2rem solid #ff7b00;border-left: 1.2rem solid transparent;width: 0;height: 0;bottom: 0;right: 0;display:none;}
    .pubdigc i{position: absolute;color: #fff;top: .3rem;font-size: .7rem;right: -0.1rem;z-index: 9;transform: scale(.5);}.active .pubdigc{display:block}.pubdigo{overflow: hidden;padding: .45rem .5rem .3rem;border-radius: .1rem;position: relative;}.redb_div.longdig{width:100%;text-align:left;margin:.45rem 0}
    .redb_div.longdig .pubdigo{line-height:1.4rem}.redb_div.longdig .redb_title{float:left;margin-top:0}.redb_div.longdig .redb_price{float:right;margin-left:.75rem;font-size:1rem;margin-right:.5rem}.redb_div.longdig .redb_yuan{float:right;height:auto}
</style>
<div id="showrule" style="display: none">
    <div class="pop-up">
        <div style="padding:20px 0px;font-size:20px;color:#3d2b1a;font-weight: bold;font-weight: 700;text-align: center;">
            <span style="padding-left:20px;">发布须知</span>
            <span onclick="colselayui()" style="float: right;padding-right: 10px;margin-top: -5px;">
                    <i class="bi bi-x" style="font-size:30px;"></i>
            </span>
        </div>
        <div style="padding:0px 20px;">
            $config[xieyi]
        </div>
        <div style="margin-top: 20px;margin-bottom: 20px;">
            <div  onclick="colselayui()" class="popbtn">我知道了</div>
        </div>
    </div>
</div>
<!--{eval include DISCUZ_ROOT.'source/plugin/xigua_hb/include/c_pub_dig.php';}-->
<div id="pubdig" style="display:none;">
    <div class="weui-cells weui-cells_form before_none after_none" style="border-radius: 2rem;">
        <div class="weui-cell weui-cell_switch before_none">
            <div class="weui-cell__hd">
                <span class="tgb-r05-modal-mark tgb-r05-modal-mark--pin" aria-hidden="true"></span>
            </div>
            <div class="weui-cell__bd" style="font-size:16px;font-weight:600;color:#3d2b1a;">选择置顶时间</div>
            <div class="weui-cell__ft" onclick="colselayui()">
                <i class="bi bi-x" style="font-size:20px;"></i>
            </div>
        </div>
        <div class="dig_pub_exts cl" style="border-radius:20px;">
            <!--{loop $tmp_dig_prices $_k $tmp_dig}-->
            <!--{eval $i++;}-->
            <label class=" <!--{if $tmp_dig[price]<=0}-->none1 <!--{/if}--><!--{if $config[longcard]==2}-->longdig<!--{/if}--> dig_pub_div cl <!--{if !$actv_has && $tmp_dig[price]>=0}-->active<!--{eval $actv_has=1;}--><!--{/if}--> " for="dig_pub_$_k" style="border-radius:20px 20px 0px 20px;">
                <!--{if $tmp_dig[tgg]}-->
                <div class="dig_pub_subtag main_bg" style="background:#2764ff!important;">{$tmp_dig[tgg]}</div>
                <!--{/if}-->
                <div class="pubdigo">
                    <div class="dig_pub_title" style="font-size:13px;font-weight:600;">{$tmp_dig[shorttitle]}</div>
                    <div class="dig_pub_price main_color" style="font-size:19px;font-weight:700;color:#2764ff!important;" data-dpr="{$tmp_dig[price]}"><em>&yen;</em>{$tmp_dig[price]}</div>
                    <div class="dig_pub_yuan" style="font-size:13px;"><!--{if $tmp_dig[yuanjia]}-->{$tmp_dig[yuanjia]}<!--{/if}--></div>
                    <input type="checkbox" name="dig_pub" value="{$tmp_dig[type]}" id="dig_pub_$_k" style="display:none" />
                    <div class="pubdigc"><i class="iconfont icon-xuanzhong"></i></div>
                </div>
            </label>
            <!--{/loop}-->
        </div>
    </div>

    <div style="margin-top: 20px;margin-bottom: 20px;">
        <div  onclick="colselayui()" class="popbtn" style="border:1.5px solid #ff7b00;font-weight:600;background:#ff7b00;color:#fff;">选好了</div>
    </div>
</div>

<div id="pubred" style="display:none;">
    <div class="weui-cell weui-cell_switch">
        <div class="weui-cell__hd">
            <i class="iconfont icon-hongbao2 tgb-r05-modal-icon" aria-hidden="true"></i>
        </div>
        <div class="weui-cell__bd" style="font-size:16px;font-weight:600;color:#3d2b1a;">发红包</div>
        <div class="weui-cell__ft" onclick="colselayui()">
            <i class="bi bi-x" style="font-size:30px;"></i>
        </div>
    </div>
    <div class="redb_exts cl" ><!--{eval $actv_has2=0;}-->
        <!--{loop $red $_k $item}-->
        <!--{eval $i++;}-->
        <label class="<!--{if $config[longcard]==2}-->longdig<!--{/if}--> redb_div cl <!--{if !$actv_has2}-->active<!--{eval $actv_has2=1;}--><!--{/if}--> " for="redb_$_k"  style="border-radius:20px 20px 0px 20px;">
            <div class="pubdigo">
                <div class="redb_title" style="font-size:11.5px;font-weight:600;">{$item[title]}</div>
                <div class="redb_price main_color" style="font-size:19px;font-weight:700;color:#2764ff!important;" data-dpr="{$item[price]}"><em>&yen;</em>{$item[price]}</div>
                <input type="checkbox" name="redb" value="{$item[price]}" id="redb_$_k" style="display:none" />
                <div class="pubdigc"><i class="iconfont icon-xuanzhong"></i></div>
            </div>
        </label>
        <!--{/loop}-->
    </div>

    <div style="margin-top: 20px;margin-bottom: 20px;">
        <div  onclick="colselayui()" class="popbtn" style="border:1.5px solid #ff7b00;font-weight:600;background:#ff7b00;color:#fff;">选好了</div>
    </div>
</div>

<div id="pubset" style="display:none;">
    <div class="weui-cell weui-cell_switch">
        <div class="weui-cell__bd" style="font-size:16px;font-weight:600;color:#3d2b1a;">更多设置</div>
        <div class="weui-cell__ft" onclick="colselayui()">
            <i class="bi bi-x" style="font-size:30px;"></i>
        </div>
    </div>

    <div class="weui-cell weui-cell_switch before_none"  style="width: 300px;margin-top:15px;margin-bottom:15px;vertical-align: middle;">
        <div class="weui-cell__bd" style="font-size:15px;font-weight:600;color:#3d2b1a;"> <i class="bi bi-chat-square-dots" style="vertical-align: middle;"></i> 不允许评论<em style="color:#e63946;">(收费20元)</em></div>
        <div class="weui-cell__ft">
            <input class="weui-switch" type="checkbox" id="checkzdpub">
        </div>
    </div>

</div>


<script src="source/plugin/xigua_hb/static/dist/cropper.min.js"></script>



<script>
var formlocknew = 0;
$(document).on('click','#submitnew', function () {
var that = $('#forms');
if (formlocknew===1) {console.log(formlocknew);return false;}
$.showLoading();
formlocknew = 1;
$.ajax({
type: 'post',
url: that.attr('action') + '&inajax=1' + _URLEXT,
data: that.serialize(),
dataType: 'xml',
success: function (data) {
$.hideLoading();
formlocknew = 0;
if (null == data) {
tip_common('error|' + ERROR_TIP);
return false;
}
var s = data.lastChild.firstChild.nodeValue;
tip_common(s);
},
error: function () {$.hideLoading();formlocknew = 0;}
});
return false;
});

var loadingImg ='<li id="loadingimg" class="weui-uploader__file weui-uploader__file_status"><div class="weui-uploader__file-content"><img src="source/plugin/xigua_hb/static/img/loading.gif"/></div></li>';
var photoct = $('#photo');
var imgpop = $('#popctrl');var uploadinput_obj;
var uploadinput = $('.weui-uploader__input');
var boxer = null, filedname = '', ICnt = '{echo $tuwen_array?count($tuwen_array):0}', canmulti = 0, max_upload_num = 10, max_upload_maxtip = '';

$(function () {
var URL = window.URL || window.webkitURL;
var blobURL;
var file;
<!--{if HB_INWECHAT&&$config[multiupload]}-->
canmulti = 1;
uploadinput.on("touchstart", function () {
uploadinput_obj = $(this);
boxer = $(this).parent().prev();
max_upload_num = boxer.data('max');
max_upload_maxtip = boxer.data('maxtip');
if(boxer.find('li').length>=max_upload_num){ $.toast(max_upload_maxtip, 'error'); return false; }
wx_upload(max_upload_num);
return false;
});
<!--{elseif IN_MAGAPP}-->
uploadinput.on('touchstart', function(){
uploadinput_obj = $(this);
boxer = $(this).parent().prev();
max_upload_num = boxer.data('max');
max_upload_maxtip = boxer.data('maxtip');
if(boxer.find('li').length>=max_upload_num){ $.toast(max_upload_maxtip, 'error'); return false; }
magPicPick();
return false;
});
<!--{else}-->
if(<!--{eval echo IN_QIANFAN ?1:0}-->){
uploadinput.removeAttr('type').attr('readonly', '1');
uploadinput.on('touchstart', function(){
uploadinput_obj = $(this);
boxer = $(this).parent().prev();
max_upload_num = boxer.data('max');
max_upload_maxtip = boxer.data('maxtip');
if(boxer.find('li').length>=max_upload_num){ $.toast(max_upload_maxtip, 'error'); return false; }
magPicPick();
return false;
});
}else{
uploadinput.on("change", function () {
uploadinput_obj = $(this);
boxer = $(this).parent().prev();
if(boxer.data('max')){
if(boxer.children('li').length>=boxer.data('max')){ $.toast(boxer.data('maxtip'), 'error'); return false; }
}
filedname = $(this).data('name');
photoct.cropper('destroy').cropper({
minContainerHeight: 320,
autoCropArea:1
});
var files = this.files;
if (!photoct.data('cropper')) {
return;
}
if (files && files.length) {
file = files[0];
if (/^image\/\w+$/.test(file.type)) {
<!--{if !$config[closecj]}-->
blobURL = URL.createObjectURL(file);
photoct.one('built.cropper', function () {
URL.revokeObjectURL(blobURL);
}).cropper('reset').cropper('replace', blobURL);
uploadinput_obj.val('');
imgpop.popup();
<!--{else}-->
for(var i=0;i<files.length; i++){
file = files[i];
compress(file, function(TMP){
var img = TMP.split(',')[1];
img = window.atob(img);
var ia = new Uint8Array(img.length);
for (var i = 0; i < img.length; i++) {
ia[i] = img.charCodeAt(i);
}
var blob = new Blob([ia], {type:"image/jpeg"});
var formdata=new FormData();
formdata.append('file',blob);
$.ajax({
type: 'post',
url: '$SCRITPTNAME?id=xigua_hb&ac=uploader&inajax=1&formhash={FORMHASH}',
data :  formdata,
processData : false,
contentType : false,
dataType: 'xml',
success: function (data) {
if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
var s = data.lastChild.firstChild.nodeValue;
if(s.indexOf('success')!==-1){
var html = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + TMP + ')"><input type="hidden" name="'+ uploadinput_obj.data('name')+'[]" value="' + s.split('|')[1] + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
$('#loadingimg').remove();
boxer.append(html);
} else {
tip_common(s);
}
}
});
});
boxer.append('<li id="loadingimg" class="weui-uploader__file weui-uploader__file_status"><div class="weui-uploader__file-content"><img src="source/plugin/xigua_hb/static/img/loading.gif"/></div></li>');
}
<!--{/if}-->
} else {
$.toptip('Please choose an image file.', 'error');
}
}
});
}
<!--{/if}-->
$('.pub_funcbar a').each(function () {
var btn = $(this);
var mtd = btn.attr('data-method');
btn.on('click', function () {
if (mtd == 'destroy') {
} else if (mtd == 'confirm') {
result = photoct.cropper('getCroppedCanvas');
photo = result.toDataURL('image/jpeg');
var img=photo.split(',')[1];
img=window.atob(img);
var ia = new Uint8Array(img.length);
for (var i = 0; i < img.length; i++) {
ia[i] = img.charCodeAt(i);
}
var bfile = new Blob([ia], {type:"image/jpeg"});
compress(bfile, function(cmp_photo){
var img = cmp_photo.split(',')[1];
img = window.atob(img);
var ia = new Uint8Array(img.length);
for (var i = 0; i < img.length; i++){
ia[i] = img.charCodeAt(i);}
var blob = new Blob([ia], {type:"image/jpeg"});
var formdata=new FormData();
formdata.append('file',blob);
$.ajax({type: 'post',url: '$SCRITPTNAME?id=xigua_hb&ac=uploader&inajax=1&formhash={FORMHASH}',data:formdata,
processData : false,contentType : false,dataType: 'xml',success: function(data){
if(null==data){ tip_common('error|'+ERROR_TIP);return false;}
var s = data.lastChild.firstChild.nodeValue;
if(s.indexOf('success')!==-1){
var html = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + cmp_photo + ')"><input type="hidden" name="'+ uploadinput_obj.data('name')+'[]" value="' + s.split('|')[1] + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
$('#loadingimg').remove();boxer.append(html);} else {tip_common(s);}}});});
boxer.append('<li id="loadingimg" class="weui-uploader__file weui-uploader__file_status"><div class="weui-uploader__file-content"><img src="source/plugin/xigua_hb/static/img/loading.gif"/></div></li>');
} else {var opt = btn.attr('data-option');photoct.cropper(mtd, opt);}});});});
function compress(file, callback){
var reader = new FileReader();
reader.onload = function (e) {
var image = $('<img/>');
image.on('load', function () {
var square = 640;
var canvas = document.createElement('canvas');
if (this.width > this.height) {canvas.width = Math.round(square * this.width / this.height);canvas.height = square;
} else {canvas.height = Math.round(square * this.height / this.width);canvas.width = square;}
var context = canvas.getContext('2d');
context.clearRect(0, 0, square, square);
var imageWidth = canvas.width;
var imageHeight = canvas.height;
var offsetX = 0;
var offsetY = 0;
context.drawImage(this, offsetX, offsetY, imageWidth, imageHeight);
var data = canvas.toDataURL('image/jpeg', 0.8);
console.log([imageWidth,imageHeight]);
callback(data);});
image.attr('src', e.target.result);};
reader.readAsDataURL(file);
}
<!--{if HB_INWECHAT&&$config[multiupload]}-->
var hideLodsyc = 0;
var syncUpload = function (localIds) {
var localId = localIds.shift();
wx.uploadImage({
localId: localId, isShowProgressTips:0, success: function (res) {
var serverId = res.serverId;if(localIds.length<=1){ hideLodsyc=1; }else{hideLodsyc=0;}
do_download("&serverId[]=" + serverId); if (localIds.length > 0) { syncUpload(localIds); }
}
});
};
function do_download(serverId) {
if (max_upload_num > 0 && (boxer.find('li').length >= max_upload_num) && !boxer.data('only')) {
$.toptip(max_upload_maxtip, 'warning');
return false;
}
$.ajax({
type: "POST", url: "$SCRITPTNAME?id=xigua_hb&ac=uploader&do=download&inajax=1",
data: serverId, async: false, dataType: "xml", success: function (data) {
if(hideLodsyc){$.hideLoading();}
if (null == data) {
tip_common('error|' + ERROR_TIP);
return false;
}
var s = data.lastChild.firstChild.nodeValue;
var sar = s.split('|');
if (sar[0] == 'success') {
var imgary = sar[1].split('((()))');
var html_imga = '';
for (var j = 0; j < imgary.length; j++) {
html_imga = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + imgary[j] + ')"><input type="hidden" name="' + uploadinput_obj.data('name') + '[]" value="' + imgary[j] + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
}
boxer.append(html_imga);
} else {
tip_common(s);
}
}
});
}
function wx_upload(max_num) {
if (max_num < 1 || typeof max_num === 'undefined') {
max_num = 9;
}
if (max_num > 9) {
max_num = 9
}
wx.chooseImage({
count: max_num, complete:function(){ $('input[type="text"],input[type="tel"],textarea').blur(); }, success: function (res) {
var localIds = res.localIds;
$.showLoading();
syncUpload(localIds);
}
});
}
<!--{/if}-->
<!--{if IN_MAGAPP}-->
function magPicPick(){mag.picPick({preview: function(res){$.showLoading();},
success: function(res){$.hideLoading();var imgu = typeof res.url!=='undefined' ? res.url : ('{$config[magapp_url]}/core/attachment/attachment/attach?aid='+res.aid);
var html_imga = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + imgu + ')"><input type="hidden" name="'+ uploadinput_obj.data('name')+'[]" value="' + imgu + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
boxer.append(html_imga);},fail: function(res){$.hideLoading();}});}
<!--{elseif IN_QIANFAN}-->
function magPicPick(){var maxqf = boxer.data('only') ? 1 : 9;var type = 0;var jsUploadOptions = {'picFormat': 1,
'picMaxSize': 1200,'compressOption':100,'uploadNum': maxqf,'uploadType': 0,'showCamera':false};
QFH5.uploadImageOrVideo(type, JSON.stringify(jsUploadOptions), function (state, data) {
if (state == 1) {
for(var i in data){
var imgu = data[i].url;
if(boxer.data('only')){
var html = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + imgu + ')"><input type="hidden" name="'+ uploadinput_obj.data('name')+'[]" value="' + imgu + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
boxer.html(html);
}else{
var html_imga = '<li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(' + imgu + ')"><input type="hidden" name="'+ uploadinput_obj.data('name')+'[]" value="' + imgu + '"/><div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div></li>';
boxer.append(html_imga);
}
}
} else {
}
});
}
<!--{/if}-->
function sms_time() { var o = $('#vcodebtn'); if (SMS_WAIT_TIME <= 0) { o.removeAttr("disabled"); o.html("{lang xigua_hb:vcode_get}"); SMS_WAIT_TIME = 120;} else { o.attr("disabled", true);
o.html("{lang xigua_hb:vcode_again}(" + SMS_WAIT_TIME + ")"); SMS_WAIT_TIME--; setTimeout(function() { sms_time(); }, 1000); } }
$('#vcode_tel').on('keyup', function () { var vcode_btn = $('#vcode_btn'), vcode_area = $('#vcode_area'), that =$(this); var ombi = that.data('old'), omval = that.val();
if(omval == ombi){ vcode_btn.addClass('w0');vcode_area.addClass('none'); }else{ vcode_btn.removeClass('w0');vcode_area.removeClass('none'); } });
<!--{if $sh}--> var itar = []; <!--{loop $sh $_sh}--> itar.push({title:'{$_sh[name]}'}); <!--{/loop}-->
$("#choose_sh").select({ title: "{lang xigua_hs:qxzguanlian}", items: itar, onOpen:function () {$('.masker').fadeIn();}, beforeClose:function () {$('.masker').fadeOut(150);return true;} });
<!--{/if}-->
<!--{if $splist}-->
var itar2 = [];<!--{loop $splist $_sp}-->itar2.push({title:'{$_sp[title]}    ({$_sp[shname]})'});<!--{/loop}-->
$("#choose_sp").select({ title: "&#35831;&#36873;&#25321;&#20851;&#32852;&#21830;&#21697;", items: itar2, onOpen:function () {$('.masker').fadeIn();}, beforeClose:function () {$('.masker').fadeOut(150);return true;} });
<!--{/if}-->
<!--{if HB_INWECHAT}-->
$(function () { var isPageHide = false; window.addEventListener('pageshow', function () { if (isPageHide) { window.location.reload(); } }); window.addEventListener('pagehide', function () { isPageHide = true; }); });
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_hs']}-->
var HB_INWECHAT = '{HB_INWECHAT}',mkey = "{$_G['cache']['plugin']['xigua_hs'][mkey]}",HS_MULTIUPLOAD = "{$_G['cache']['plugin']['xigua_hb'][multiupload]}";
var GOOGLE = "{$_G['cache']['plugin']['xigua_hs']['google']}", PUB_VARID = 0, IGNORETIP = 0;
$('.openlocation').on('click', function(){ var pot = []; PUB_VARID = $(this).data('id'); IGNORETIP = 0;
pot.push({ text: "{lang xigua_hb:locacurrt}", onClick: function() { he_getlocation(setPoint); } });
pot.push({text: "{lang xigua_hb:xuandian}", onClick: function() { he_getlocation(chooseMap); } });
$.actions({ actions: pot}); });
<!--{if !$_G['cache']['plugin']['xigua_hs']['baidusdk']}-->
$('.popupL').on('click', function(){ setForm(chooseMapRes.detail.location.lat, chooseMapRes.detail.location.lng, 0); });
<!--{/if}-->
$(document).on('click', '.obj_', function(){ var k = parseInt($(this)[0].classList[2].replace('obj_', '')); setFormField(OBJ[k]); });
var chooseMapRes = [], OBJ = {};
function setForm(lat, lng, deft){ $.showLoading(); $.ajax({ type: 'GET', url: _APPNAME + '?id=xigua_hs&ac=getloc&lat='+lat+'&lng='+lng+'&inajax=1',
dataType: 'xml', success: function (data) { $.hideLoading(); if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
var s = data.lastChild.firstChild.nodeValue; if(s.indexOf('error')!=-1){ tip_common(s); }else{ var _actions = []; OBJ = jQuery.parseJSON(s.split('|')[1]); if(deft){
setFormField(OBJ[0]); return true; }
for(var j in OBJ){ _actions.push({ text: OBJ[j].address, className:'obj_ obj_'+j, onClick: function() {  $.closePopup(); } }); }
$.actions({ actions:_actions }); } }, error: function () { $.hideLoading(); } }); }
function setFormField(subj){
$('#location_'+PUB_VARID).val((!!subj.address_component.city ? subj.address_component.city: '')+subj.address);
$('#location_lat_'+PUB_VARID).val(subj.location.lat);
$('#location_lng_'+PUB_VARID).val(subj.location.lng);
$('#location_lat').val(subj.location.lat);
$('#location_lng').val(subj.location.lng);
$('#dist1').val(subj.address_component.province);
$('#dist2').val(subj.address_component.city);
$('#dist3').val(subj.address_component.district);
}
function setPoint(position){
if(typeof position.type != 'undefined'){
if(position.type == 'ip'){if(IGNORETIP){}else {chooseMap(position);}return false;}}
setForm((position.latitude||position.lat), (position.longitude||position.lng), 1);}
function chooseMap(position) {if(typeof mag != 'undefined') {mag.mapPick(function (res) {
setForm(res.lat, res.lng, 0);});return false;}
<!--{if $_G['cache']['plugin']['xigua_hs']['google']}-->
var myCenter=new google.maps.LatLng((position.latitude||position.lat), (position.longitude||position.lng));
var Gmap=new google.maps.Map(document.getElementById("mapcontainer"),{
center:myCenter,zoom:15,mapTypeId:google.maps.MapTypeId.ROADMAP});
var Gmarker=new google.maps.Marker({position:myCenter});
Gmarker.setMap(Gmap);$("#mapouter").popup();
google.maps.event.addListener(Gmap, 'click', function(event) {
var center = event.latLng;
var centerlat = center.lat();
var centerlng = center.lng();
Gmarker.setMap(null);
Gmarker=new google.maps.Marker({
position:new google.maps.LatLng(centerlat, centerlng)
});
Gmarker.setMap(Gmap);
setForm(centerlat, centerlng, 0);
$.closePopup();
});
<!--{elseif $_G['cache']['plugin']['xigua_hs']['baidusdk']}-->
var map = new BMap.Map("mapcontainer");
var geoc = new BMap.Geocoder();
var point = new BMap.Point((position.longitude||position.lng), (position.latitude||position.lat));
map.centerAndZoom(point, 15);
var marker = new BMap.Marker(point);
map.addOverlay(marker);
map.addControl(new BMap.MapTypeControl());
map.addControl(new BMap.NavigationControl());
map.addEventListener("click", function (e){
var marker = new BMap.Marker(new BMap.Point(e.point.lng, e.point.lat));
map.addOverlay(marker);
var pt = e.point;
geoc.getLocation(pt, function(rs){
var addComp = rs.addressComponents;
$('#location_'+PUB_VARID).val(addComp.province+addComp.city+addComp.district+addComp.street+addComp.streetNumber);
$('#location_lat_'+PUB_VARID).val(e.point.lat);
$('#location_lng_'+PUB_VARID).val(e.point.lng);
$('#location_lat').val(e.point.lat);
$('#location_lng').val(e.point.lng);
$('#dist1').val(addComp.province);
$('#dist2').val(addComp.city);
$('#dist3').val(addComp.district);
$.closePopup();
});
});
$("#mapouter").popup();
<!--{else}-->
var center = new qq.maps.LatLng((position.latitude||position.lat), (position.longitude||position.lng));
var mapinit = function () {
geocoder = new qq.maps.Geocoder({
complete: function (result) {
chooseMapRes = result;
}
});
geocoder.getAddress(center);
map = new qq.maps.Map(document.getElementById("mapcontainer"), {center: center, zoom: 13});
marker = new qq.maps.Marker({
position: center, map: map
});
qq.maps.event.addListener(map, 'click', function (event) {
var tmpcenter = new qq.maps.LatLng(event.latLng.getLat(), event.latLng.getLng());
marker.setPosition(tmpcenter);
geocoder.getAddress(tmpcenter);
});
$("#mapouter").popup();
};
mapinit();
<!--{/if}-->
}
function he_getlocation(callback){ if(0&&typeof mag != 'undefined'){ mag.getLocation(function(res){callback(res);}); }else if(typeof sq != 'undefined'){sq.getLocation(function(res){callback(res);});
}else if(typeof QFH5 != 'undefined') {QFH5.getLocation(function (state, data) { if (state == 1) {callback(data);} else {alert(data.error);}}); }else if((HB_INWECHAT&& {echo intval($config[multiupload])})==1) {
wx.getLocation({type: 'gcj02',success: function (res) {callback(res);},cancel: function (res) {}}); }else{var geolocation = new qq.maps.Geolocation(mkey, "myapp");geolocation.getLocation(callback, function () {}, {timeout:4000, failTipFlag:true});}}
<!--{/if}-->
if($('.openlocation').length>0){PUB_VARID = $('.openlocation').data('id');setTimeout(function () {IGNORETIP = 1;he_getlocation(setPoint);if(typeof wx!=='undefined'){wx.ready(function () {he_getlocation(setPoint);});}}, 300);}
<!--{if $config[wxinput]}-->
function syncweixin(){if($('#weuiAgree2').length>0){if($('#weuiAgree2:checked').val()){var mob = $('input[name="form[mobile]"]');var wx = $('input[name="form[weixin]"]');wx.val(mob.val());}}}
$(document).on('keyup', 'input[name="form[mobile]"]', function () {var that = $(this);console.log(that.val());syncweixin();});
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_st'] && $hotcity}-->
$("#site").select({title: "{lang xigua_hb:fbdz}",multi: true,<!--{if $config['maxfz']}-->max:$config['maxfz'],<!--{/if}-->
items: [{title:"{$_G['cache']['plugin']['xigua_st']['zongname']}", value:0},<!--{loop $hotcity $v}-->{title: "{echo $v[name2]?$v[name2]:$v[name]}",value: "{$v[stid]}",},<!--{/loop}-->],
onOpen:function () {$('.masker').fadeIn();},beforeClose:function () {$('.masker').fadeOut(150);return true;}});
<!--{/if}-->$(document).on("click",".check6",function(){var that=$(this),val="";var prtnt=that.parent().parent();if(that.hasClass("weui-btn_primary")){that.addClass("weui-btn_default").removeClass("weui-btn_primary")}else{that.addClass("weui-btn_primary").removeClass("weui-btn_default")}prtnt.find(".check6.weui-btn_primary").each(function(){val+=","+$(this).text()});$("#"+that.data("id")).val(val.substr(1))});</script>
<!--{if $showv&&!$catinfo[a_video]}--><!--{template xigua_hb:video}--><!--{/if}-->
<!--{if $hastw}--><!--{template xigua_hb:edit_jieshao}--><!--{/if}-->
<!--{template xigua_hb:autosave}-->
<script>function pop_4(){<!--{if !IN_PROG}-->window.history.pushState({ title: "title",  url: "#"}, "title", "#"); window.addEventListener("popstate", function(e) { $.closePopup(); }, false);<!--{/if}-->
$("#agree__text").popup();}</script>
<!--{if $catinfo[allowmp3] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/voice.php')}--><!--{template xigua_hb:voice}--><!--{/if}-->
<style data-tgb-r05-lane-b="publish">
:root {
    --bg:#f4f7fb; --card-bg:#fff; --primary:#2764ff; --primary-dark:#1847cc;
    --primary-gradient:#2764ff; --gold-light:#19b8a9; --gold-dark:#2176c7;
    --text-primary:#0e1b2a; --text-secondary:#405166; --text-tertiary:#718096;
    --border-light:#d8e1ec; --border-card:#d8e1ec; --shadow-sm:none;
    --shadow-md:0 8px 24px rgba(12,27,51,.08); --shadow-red:none;
}
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
*, *:before, *:after { box-sizing:border-box; }
img, video, iframe, table { max-width:100%; }
header.x_header { box-sizing:border-box; min-height:52px; border-bottom:1px solid #d8e1ec!important; background:rgba(255,255,255,.98)!important; box-shadow:0 4px 14px rgba(12,27,51,.05)!important; -webkit-backdrop-filter:none!important; backdrop-filter:none!important; }
.x_header a.z, .x_header a#submitnew { min-width:44px!important; min-height:44px!important; }
.x_header a.z { display:flex; align-items:center; justify-content:center; color:#0e1b2a!important; }
.x_header a.z .icon-fanhuijiantou { color:#0e1b2a!important; font-size:20px; line-height:1; }
.page__bd { box-sizing:border-box; min-height:100vh; padding-bottom:calc(88px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; color:#405166!important; }
.weui-grids, .weui-cells, .weui-panel, .crypto-container, .crypto-form-card { box-sizing:border-box; border:1px solid #d8e1ec!important; border-radius:8px!important; background:#fff!important; background-image:none!important; box-shadow:none!important; -webkit-backdrop-filter:none!important; backdrop-filter:none!important; }
.weui-grids { margin:12px 16px!important; padding:8px!important; gap:8px!important; }
.weui-grid { box-sizing:border-box; min-height:88px; padding:12px 8px!important; border:1px solid transparent!important; border-radius:8px!important; background:#fff!important; box-shadow:none!important; transform:none!important; }
.weui-grid:active { border-color:#bfd0e3!important; background:#edf3fa!important; }
.weui-grid:hover, .weui-grid:focus-visible { border-color:#bfd0e3!important; background:#edf3fa!important; box-shadow:none!important; transform:none!important; }
.weui-grid__icon img { border-radius:8px!important; }
.weui-grid__label, .weui-cell__bd, .weui-cell__bd p, .weui-label, .crypto-label { color:#405166!important; }
.weui-cells { margin:12px 16px!important; overflow:hidden; }
.weui-cell { box-sizing:border-box; min-height:52px; padding:10px 12px!important; border-color:#d8e1ec!important; background:#fff!important; }
.weui-cell:before, .weui-cells:before, .weui-cells:after { border-color:#d8e1ec!important; }
.weui-input:not([type="hidden"]), .weui-textarea, .crypto-input, .crypto-textarea, input[type="text"], input[type="tel"], input[type="number"], input[type="password"], textarea, select { box-sizing:border-box; min-height:48px; border:1px solid #d8e1ec!important; border-radius:8px!important; background:#fff!important; color:#0e1b2a!important; font-size:16px!important; line-height:24px!important; box-shadow:none!important; }
textarea, .weui-textarea, .crypto-textarea { min-height:120px; padding:12px!important; resize:vertical; }
input::-webkit-input-placeholder, textarea::-webkit-input-placeholder { color:#718096!important; font-size:14px!important; }
.weui-input:focus, .weui-textarea:focus, .weui-select:focus, .crypto-input:focus, .crypto-textarea:focus, input:focus, textarea:focus, select:focus, a:focus-visible, button:focus-visible { outline:2px solid #2764ff!important; outline-offset:2px; border-color:#2764ff!important; box-shadow:none!important; }
input:disabled, textarea:disabled, select:disabled, .weui-btn_disabled { background:#edf3fa!important; color:#718096!important; opacity:1!important; }
.post-tags .weui-btn, .post-tags a, .check5, .check6, .check7, .check8, .quanxuan { box-sizing:border-box; min-height:44px; margin:4px!important; padding:0 12px!important; border:1px solid #bfd0e3!important; border-radius:6px!important; background:#fff!important; color:#405166!important; font-size:13px!important; line-height:42px!important; box-shadow:none!important; }
.post-tags .weui-btn_primary, .post-tags .tag-on, .check5.weui-btn_primary, .check6.weui-btn_primary, .check7.weui-btn_primary, .check8.weui-btn_primary, .tag-on { border-color:#2764ff!important; background:#edf3fa!important; color:#2764ff!important; }
.weui-btn, .picker-button, .popbtn, .pub_funcbar a, a#submitnew { box-sizing:border-box; min-height:44px; border-radius:8px!important; box-shadow:none!important; transition:background-color 180ms ease,border-color 180ms ease!important; transform:none!important; }
.weui-btn_primary, a#submitnew, .popbtn { border:1px solid #2764ff!important; background:#2764ff!important; background-image:none!important; color:#fff!important; }
.weui-btn_default, .picker-button { border:1px solid #bfd0e3!important; background:#fff!important; color:#2764ff!important; }
.weui-vcode-btn { box-sizing:border-box; min-height:44px; border-radius:8px!important; background:#2764ff!important; background-image:none!important; color:#fff!important; }
a#submitnew { min-width:88px!important; height:44px!important; padding:0 14px!important; font-size:14px!important; line-height:44px!important; }
.weui-uploader__file, .weui-uploader__input-box { box-sizing:border-box; border:1px solid #d8e1ec!important; border-radius:8px!important; background-color:#edf3fa!important; }
.weui-uploader__input-box { min-width:72px; min-height:72px; }
.weui-uploader__input-box:hover, .crypto-upload-input:hover { border-color:#2764ff!important; background:#edf3fa!important; }
.weui-uploader__file-content, .crypto-file-content { min-width:44px; min-height:44px; }
.crypto-file-content { top:0!important; right:0!important; left:auto!important; width:44px!important; height:44px!important; opacity:1!important; background:rgba(12,27,51,.72)!important; }
.crypto-delete-icon { display:flex; width:44px; height:44px; align-items:center; justify-content:center; }
.toolbar, .toolbar-inner, .post_combgf, .weui-popup__modal, .fixpopuper, .layui-layer-page, .pop-up, #pubdig .weui-cells, #pubred .weui-cell, #pubset .weui-cell { border-color:#d8e1ec!important; border-radius:12px!important; background:#fff!important; color:#405166!important; box-shadow:0 8px 24px rgba(12,27,51,.08)!important; }
.toolbar-inner { min-height:52px; }
.toolbar .title, .weui-article h1, .pop-up > div:first-child, #pubdig .weui-cell__bd, #pubred .weui-cell__bd, #pubset .weui-cell__bd { color:#0e1b2a!important; }
.weui-popup__modal, .pop-up { max-width:100%!important; overflow-x:hidden; }
.pop-up { width:calc(100vw - 32px)!important; }
.weui-cell__ft[onclick], .pop-up > div:first-child span[onclick] { display:flex; min-width:44px; min-height:44px; align-items:center; justify-content:center; }
.tgb-r05-modal-mark, .tgb-r05-modal-icon { display:inline-flex; width:24px; height:24px; align-items:center; justify-content:center; color:#2764ff!important; font-size:22px; }
.tgb-r05-modal-mark--pin:before { content:""; width:10px; height:16px; border:2px solid #2764ff; border-radius:6px 6px 2px 2px; transform:rotate(35deg); }
.layui-layer-title { border-color:#d8e1ec!important; background:#edf3fa!important; color:#0e1b2a!important; }
.layui-layer-btn a { box-sizing:border-box; min-height:44px; border-radius:8px!important; font-size:14px; line-height:42px!important; }
.layui-layer-btn .layui-layer-btn0 { border:1px solid #2764ff!important; background:#2764ff!important; color:#fff!important; }
.layui-layer-btn .layui-layer-btn1 { border:1px solid #bfd0e3!important; background:#fff!important; color:#2764ff!important; }
.car-type, .type-item, .dig_pub_div, .redb_div { border-color:#d8e1ec!important; border-radius:8px!important; background:#fff!important; background-image:none!important; color:#405166!important; box-shadow:none!important; }
.type-item-active, .dig_pub_div.active, .redb_div.active { border-color:#2764ff!important; background:#edf3fa!important; }
.car-price, .dig_pub_price, .redb_price { color:#2764ff!important; }
.dig_pub_subtag, .car-year-season { border-radius:6px!important; background:#19b8a9!important; background-image:none!important; color:#fff!important; }
.dig_pub_title, .redb_title, .longcard.type-item .car-tip { color:#0e1b2a!important; }
.dig_pub_yuan, .redb_yuan { color:#718096!important; }
.pubdigc { border-bottom-color:#2764ff!important; }
.car-type .type-item-active .car-active-c { border-bottom-color:#2764ff!important; }
.car-type .type-item-active:after { border-color:#2764ff!important; }
.infotype { min-height:48px!important; border-color:#d8e1ec!important; border-radius:8px!important; background:#fff!important; color:#0e1b2a!important; font-size:16px!important; }
.kclink { min-height:44px!important; border:1px solid #bfd0e3!important; border-radius:8px!important; background:#edf3fa!important; background-image:none!important; color:#2176c7!important; font-size:14px!important; line-height:44px!important; box-shadow:none!important; overflow-wrap:anywhere; }
.crypto-upload-container, .crypto-vip-promo, .crypto-notice, .crypto-glass { border:1px solid #d8e1ec!important; border-radius:8px!important; background:#fff!important; background-image:none!important; box-shadow:none!important; -webkit-backdrop-filter:none!important; backdrop-filter:none!important; }
.crypto-vip-btn { display:inline-flex!important; min-height:44px; align-items:center; border:1px solid #2764ff!important; border-radius:8px!important; background:#2764ff!important; background-image:none!important; color:#fff!important; font-size:13px!important; box-shadow:none!important; transform:none!important; }
.crypto-notice-warning { color:#d99000!important; }
.crypto-notice-link { color:#2764ff!important; }
.crypto-notice-link:hover, .crypto-notice-link:focus { color:#1847cc!important; }
.weui-switch { border-color:#bfd0e3!important; background:#edf3fa!important; }
.weui-switch:checked { border-color:#2764ff!important; background:#2764ff!important; background-image:none!important; }
.fix-bottom { box-sizing:border-box; padding-bottom:env(safe-area-inset-bottom,0px); border-top:1px solid #d8e1ec; background:#fff!important; }
.masker, .weui-popup__overlay { background:rgba(12,27,51,.42)!important; }
/* TGB-R09-PUBLISH-VISUAL-FIX:START */
.tgb-light-grid header.x_header {
    display:flex!important;
    position:relative!important;
    height:60px!important;
    min-height:60px!important;
    padding:0 14px!important;
    align-items:center!important;
    justify-content:center!important;
}
.tgb-publish-header-spacer, .tgb-publish-form-spacer, header.x_header .navtitle { display:none!important; }
.tgb-publish-header-title { height:auto!important; margin:0!important; line-height:24px!important; }
header.x_header a.z {
    position:absolute!important;
    top:8px!important;
    left:14px!important;
    width:44px!important;
    height:44px!important;
    padding:0!important;
    line-height:1!important;
}
header.x_header a#submitnew {
    position:absolute!important;
    top:8px!important;
    right:14px!important;
    width:64px!important;
    min-width:64px!important;
    height:44px!important;
    margin:0!important;
    padding:0 12px!important;
    overflow:visible!important;
    line-height:1!important;
    white-space:nowrap;
}
header.x_header a#submitnew .tgb-publish-submit-label {
    width:auto!important;
    height:auto!important;
    margin:0!important;
    line-height:20px!important;
}
.vars_cells .crypto-upload-container {
    margin-top:0!important;
    padding-top:16px!important;
}
/* TGB-R09-PUBLISH-VISUAL-FIX:END */
@media (max-width:374px) {
    .weui-grids { grid-template-columns:repeat(2,minmax(0,1fr))!important; }
    .dig_pub_div, .redb_div { width:calc(50% - 8px)!important; }
    .weui-cell { align-items:flex-start; }
    .weui-label { width:88px!important; overflow-wrap:anywhere; }
}
@media (prefers-reduced-motion:reduce) {
    .weui-btn, .weui-grid, .picker-button, .popbtn { transition:none!important; }
}
</style>
