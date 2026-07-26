<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->

<link rel="stylesheet" href="source/plugin/xigua_hb/static/dist/cropper.css?{VERHASH}">

<style>
/* ========== 趣赚汇 · 暖白珊瑚红渐变风格 (收款绑定页) ========== */
:root {
    --bg: #fff9f5;
    --card-bg: rgba(255, 255, 255, 0.85);
    --primary-gold: #ff7b00;
    --gold-light: #ffb380;
    --gold-dark: #e63946;
    --gold-gradient: linear-gradient(135deg, #ff7b00, #e63946);
    --gold-gradient-soft: linear-gradient(135deg, #fef9f0, #fdf3e0);
    --text-primary: #3d2b1a;
    --text-secondary: #8b6f5c;
    --text-tertiary: #b08968;
    --border-light: rgba(255, 200, 120, 0.35);
    --border-card: rgba(255, 190, 90, 0.35);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
    --shadow-md: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
    --shadow-lg: 0 8px 28px rgba(0, 0, 0, 0.08), 0 3px 10px rgba(0, 0, 0, 0.05);
    --shadow-gold: 0 5px 15px rgba(255,50,0,0.25);
    --radius-sm: 8px;
    --radius-md: 14px;
    --radius-lg: 18px;
    --radius-xl: 22px;
}

body {
    background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
    color: var(--text-primary) !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', sans-serif;
}

/* 覆盖原深色背景类 */
.page__bd {
    margin-top: 35px;
    background: transparent;
    padding: 0 15px;
}

.crypto-card-form {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    padding: 24px;
    box-shadow: var(--shadow-md) !important;
    margin-top: 50px;
}

.crypto-form-title {
    color: var(--text-primary);
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 8px;
    text-align: center;
}

.crypto-form-subtitle {
    color: var(--text-tertiary);
    font-size: 14px;
    text-align: center;
    margin-bottom: 24px;
}

.crypto-input-group {
    margin-bottom: 20px;
}

.crypto-input-label {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-size: 15px;
    font-weight: 600;
}

.crypto-input-label i {
    color: var(--primary-gold);
    margin-right: 8px;
    font-size: 16px;
}

.crypto-input {
    width: 88%;
    background: rgba(255,245,235,0.7);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    padding: 14px 16px;
    color: var(--text-primary);
    font-size: 16px;
    transition: all 0.2s;
}

.crypto-input:focus {
    outline: none;
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 2px rgba(255, 123, 0, 0.2);
}

.crypto-input:read-only {
    background: rgba(255,245,235,0.5);
    color: var(--text-tertiary);
    cursor: not-allowed;
}

.crypto-input::placeholder {
    color: var(--text-tertiary);
}

.crypto-upload-area {
    background: rgba(255,245,235,0.7);
    border: 2px dashed var(--border-light);
    border-radius: var(--radius-md);
    padding: 20px;
    text-align: center;
    transition: all 0.2s;
}

.crypto-upload-area:hover {
    border-color: var(--primary-gold);
    background: rgba(255, 123, 0, 0.05);
}

.crypto-upload-icon {
    color: var(--text-tertiary);
    font-size: 40px;
    margin-bottom: 12px;
}

.crypto-upload-text {
    color: var(--text-tertiary);
    font-size: 14px;
    margin-bottom: 8px;
}

.crypto-submit-btn {
    width: 100%;
    background: var(--gold-gradient);
    color: white;
    border: none;
    border-radius: 60px;
    padding: 18px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-gold);
    margin-top: 30px;
}

.crypto-submit-btn:active {
    transform: scale(0.97);
}

.crypto-warning-note {
    background: rgba(255, 245, 235, 0.7);
    border: 1px solid rgba(255, 200, 120, 0.35);
    border-radius: var(--radius-md);
    padding: 12px 16px;
    margin-bottom: 20px;
    text-align: center;
}

.crypto-warning-note i {
    color: var(--primary-gold);
    margin-right: 8px;
}
.crypto-warning-note span {
    color: var(--gold-dark);
    font-size: 13px;
}

/* 模态框轻奢风格 */
.weui-popup__modal {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    overflow: hidden;
}

.pub_funcbar {
    background: rgba(255, 245, 235, 0.7) !important;
    border-top: 1px solid var(--border-light);
    padding: 16px;
    display: flex;
    gap: 12px;
}

.weui-btn_primary {
    background: var(--gold-gradient) !important;
    border: none !important;
    border-radius: 60px !important;
    color: white !important;
    font-weight: 600 !important;
    flex: 1;
    box-shadow: var(--shadow-gold);
}
.weui-btn_primary:active {
    transform: scale(0.96);
}

.weui-btn_default {
    background: rgba(255,245,235,0.7) !important;
    border: 1px solid var(--border-light) !important;
    border-radius: 60px !important;
    color: var(--text-secondary) !important;
    font-weight: 600 !important;
    flex: 1;
}
.weui-btn_default:active {
    background: rgba(255,220,180,0.8) !important;
}

/* 上传组件样式覆盖 */
.weui-uploader__file {
    border-radius: var(--radius-md);
    border: 1px solid var(--border-light);
    background-color: rgba(255,245,235,0.7);
}
.weui-uploader__input-box {
    border-radius: var(--radius-md);
    background: rgba(255,245,235,0.7);
    border: 1px dashed var(--border-light);
}
.weui-uploader__input-box:active {
    background: rgba(255,220,180,0.8);
}
.weui-uploader__title {
    color: var(--text-primary);
}
</style>

<div class="page__bd crypto-form-container">
    <!--{template xigua_hb:common_nav}-->
    
    <div class="crypto-card-form" style="margin-top:0px;">
        <div class="crypto-form-title">绑定收款账户</div>
        <div class="crypto-form-subtitle">请填写真实信息以便顺利提现</div>
        
        <form action="$SCRITPTNAME?id=xigua_hb&ac=mytx" method="post" id="form">
            <input type="hidden" name="formhash" value="{FORMHASH}" />
            <input type="hidden" name="referer" value="$referer">
            
            <!-- 二维码上传区域（隐藏） -->
            <div class="weui-cell" style="display:none">
                <div class="weui-cell__bd">
                    <div class="weui-uploader">
                        <div class="weui-uploader__hd">
                            <p class="weui-uploader__title">{lang xigua_hb:qsczfbskm}</p>
                            <div class="weui-uploader__info"></div>
                        </div>
                        <div class="weui-uploader__bd">
                            <ul class="weui-uploader__files" data-only="1">
                                <!--{if $user[alipay_qr]}-->
                                <li class="weui-uploader__file weui-uploader__file_status" style="background-image:url({$user[alipay_qr]})">
                                    <input type="hidden" name="form[alipay_qr][]" value="{$user[alipay_qr]}"/>
                                    <div class="weui-uploader__file-content"><i class="weui-icon-warn iconfont icon-shanchu"></i></div>
                                </li>
                                <!--{/if}-->
                            </ul>
                            <div class="weui-uploader__input-box">
                                <!--{if (HB_INWECHAT&&$config[multiupload]) || IN_MAGAPP}-->
                                <a class="weui-uploader__input" data-name="form[alipay_qr]" type="file"></a>
                                <!--{else}-->
                                <input class="weui-uploader__input" data-name="form[alipay_qr]" type="file">
                                <!--{/if}-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 警告提示 -->
            <div class="crypto-warning-note">
                <i class="fa fa-exclamation-triangle"></i>
                <span>*收款账号必须和账号实名认证人一致</span>
            </div>
            
            <!-- 实名信息 -->
            <div class="crypto-input-group">
                <div class="crypto-input-label">
                    <i class="fa fa-user-circle"></i>
                    <span>{lang xigua_hb:realname}：</span>
                </div>
                <input class="crypto-input" type="text" name="form[realname]" placeholder="真实姓名" value="{$user[realname]}" readonly>
            </div>
            
            <!-- 支付宝账号 -->
            <div class="crypto-input-group">
                <div class="crypto-input-label">
                    <i class="fa fa-credit-card"></i>
                    <span>支付宝账号：</span>
                </div>
                <input class="crypto-input" type="text" name="form[alipay_account]" placeholder="请输入支付宝账号或手机号" value="{$user[alipay_account]}">
            </div>
            
            <!-- 提交按钮 -->
            <button type="submit" class="crypto-submit-btn" name="dosubmit" id="dosubmit">
                {lang xigua_hb:queding}
            </button>
        </form>
    </div>
</div>

<!-- 图片裁剪模态框 -->
<div id="popctrl" class="weui-popup__container" style="z-index:1001">
    <div class="weui-popup__modal">
        <div style="height: 100vh; background: #f0f2f5; display: flex; align-items: center; justify-content: center;">
            <img id="photo" style="max-width: 100%; max-height: 100%;">
        </div>
        <div class="pub_funcbar">
            <a class="weui-btn close-popup weui-btn_primary" data-method="confirm">{lang xigua_hb:queding}</a>
            <a class="weui-btn close-popup weui-btn_default" data-method="destroy">{lang xigua_hb:quxiao}</a>
        </div>
    </div>
</div>

<!--{eval $tabbar=0;}-->
<!--{template xigua_hb:common_footer}-->
<!--{template xigua_hb:enter_up}-->