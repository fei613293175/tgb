<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>

<style>
/* ========== 趣赚汇 · 暖白珊瑚红渐变风格 (会员升级页) ========== */
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

.page__bd {
    background: transparent;
    margin-bottom: 50px;
    margin-top: 30px;
    padding: 0 15px;
}

.crypto-vip-container {
    max-width: 100%;
    margin: 0 auto;
}

.crypto-vip-card {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    padding: 0;
    box-shadow: var(--shadow-md) !important;
    overflow: hidden;
    margin-top: 20px;
}

.crypto-vip-header {
    background: var(--primary-gradient) !important;
    padding: 24px;
    text-align: center;
    border-bottom: 1px solid var(--border-card);
}

.crypto-vip-title {
    color: white;
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
}

.crypto-vip-subtitle {
    color: rgba(255, 255, 255, 0.95);
    font-size: 14px;
}

.crypto-vip-option {
    padding: 20px;
    border-bottom: 1px solid var(--border-light);
    transition: all 0.2s;
    cursor: pointer;
}

.crypto-vip-option:last-child {
    border-bottom: none;
}

.crypto-vip-option:hover {
    background: rgba(255, 123, 0, 0.03);
}

.crypto-vip-option.active {
    background: rgba(255, 123, 0, 0.05);
    border-left: 4px solid #ff7b00;
}

.crypto-radio {
    display: flex;
    align-items: flex-start;
    width: 100%;
}

.crypto-radio-input {
    display: none;
}

.crypto-radio-custom {
    width: 24px;
    height: 24px;
    border: 2px solid var(--border-light);
    border-radius: 50%;
    margin-right: 16px;
    position: relative;
    flex-shrink: 0;
    margin-top: 4px;
    background: rgba(255,245,235,0.7);
}

.crypto-radio-input:checked + .crypto-radio-custom {
    border-color: #ff7b00;
    background: #ff7b00;
}

.crypto-radio-input:checked + .crypto-radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background: white;
    border-radius: 50%;
}

.crypto-vip-details {
    flex: 1;
}

.crypto-vip-name {
    color: var(--text-primary);
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
}

.crypto-vip-desc {
    color: var(--text-tertiary);
    font-size: 14px;
    line-height: 1.5;
}

.crypto-vip-price {
    color: var(--primary-dark);
    font-size: 20px;
    font-weight: 700;
    margin-left: 16px;
    flex-shrink: 0;
}

.crypto-purchase-btn {
    width: 85%;
    background: var(--primary-gradient) !important;
    color: white;
    border: none;
    border-radius: 60px !important;
    padding: 18px;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-red);
    display: block;
    margin: 24px auto;
    text-align: center;
    text-decoration: none;
    animation: none;
}

.crypto-purchase-btn:active {
    transform: scale(0.96);
}

.crypto-purchase-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,50,0,0.35);
}

.crypto-info-panel {
    background: var(--card-bg) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border: 1px solid var(--border-card) !important;
    border-radius: var(--radius-lg) !important;
    padding: 20px;
    margin: 20px 15px 35px;
    box-shadow: var(--shadow-md) !important;
}

.crypto-info-text {
    color: var(--text-secondary);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 12px;
}

.crypto-highlight {
    color: var(--primary-dark) !important;
    font-weight: 600;
}

.crypto-link {
    color: #ff7b00 !important;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
}

.crypto-link:hover {
    color: var(--primary-dark) !important;
}

/* 隐藏原有样式 */
.weui-cells {
    display: none !important;
}

/* 覆盖可能的内联样式 */
.weui-cells_form {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-card) !important;
}
</style>
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r07/membership-light-grid-r07.css?v=20260727-r07-v3">

<!--{template xigua_hb:common_header}-->

<div class="page__bd crypto-vip-container tgb-r07-refresh-pack">
    <!--{template xigua_hb:common_nav}-->
    
    <div class="crypto-vip-card" style="margin-top:30px;">
        <div class="crypto-vip-header">
            <div class="crypto-vip-title">升级推广宝会员</div>
            <div class="crypto-vip-subtitle">5折购买刷新次数，降低推广成本</div>
        </div>
        
        <form action="$SCRITPTNAME?id=xigua_hb&ac=refresh&do=sxtc" method="post" id="form" enctype="multipart/form-data">
            <input type="hidden" name="formhash" value="{FORMHASH}">
            <input type="hidden" name="couponid" id="couponid" value="0">
            
            <!--{loop $vips $k $v}-->
            <!--{eval $i++;}-->
            <div class="crypto-vip-option <!--{if $i==1}-->active<!--{/if}-->" data-price="{$v[id]}">
                <label class="crypto-radio">
                    <input type="radio" class="crypto-radio-input" name="form[viptype]" value="{$v[id]}" id="s{$v[id]}" <!--{if $i==1}-->checked="checked"<!--{/if}-->>
                    <span class="crypto-radio-custom"></span>
                    
                    <div class="crypto-vip-details">
                        <div class="crypto-vip-name">{$v[name]}</div>
                        <div class="crypto-vip-desc">{$v[desc]}</div>
                    </div>
                    
                    <!--{if $v[price]}-->
                    <div class="crypto-vip-price">¥{$v[price]}</div>
                    <!--{/if}-->
                </label>
            </div>
            <!--{/loop}-->
            
            <input type="hidden" name="dosubmit" value="1">
            <button type="submit" class="crypto-purchase-btn" id="dosubmit">
                {lang xigua_hb:ljgm}
            </button>
        </form>
    </div>
    
    <div class="crypto-info-panel">
        <p class="crypto-info-text">
            {lang xigua_hb:ndqysy}<span class="crypto-highlight">{$qianbao[sysxnum]}</span>{lang xigua_hb:haisy}
            <span class="crypto-highlight">{$qianbao[mfsxnum]}</span>{lang xigua_hb:c}
        </p>
        <p class="crypto-info-text">
            <a href="$SCRITPTNAME?id=xigua_hb&ac=mypub" class="crypto-link">{lang xigua_hb:ljsy}</a>
        </p>
    </div>
</div>

<script>
    (function () {
        var purchaseForm = document.getElementById('form');
        var purchaseButton = document.getElementById('dosubmit');
        if (!purchaseForm || !purchaseButton) return;

        purchaseButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            HTMLFormElement.prototype.submit.call(purchaseForm);
        }, true);
    })();

    $(document).ready(function(){
        $(".crypto-vip-option").click(function(){
            // 移除所有选项的active类
            $(".crypto-vip-option").removeClass("active");
            // 给点击的选项添加active类
            $(this).addClass("active");
            // 选中对应的单选按钮
            $(this).find(".crypto-radio-input").prop("checked", true);
            
            let price = $(this).attr("data-price");
            getcoupon(price);
        });
        
        // 初始化获取第一个选项的优惠券
        let firstPrice = $(".crypto-vip-option:first").attr("data-price");
        getcoupon(firstPrice);
    });

    function getcoupon(kyprice){
        let resdata = "";
        var formData = new FormData();
        formData.append("submodac",'getone');
        formData.append("ctype",2);
        formData.append("kyprice",kyprice);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: formData,
            url: "plugin.php?id=tb_coupon:mycoupon",
            processData : false,
            contentType : false,
            async: false,
            success: function(data) {
                resdata = data.data;
                if(resdata.id){
                    $("#couponid").val(resdata.id);
                    // 可以在这里显示优惠券信息，如果需要的话
                    // console.log("已优惠：￥" + resdata.price);
                }else{
                    $("#couponid").val(0);
                }
            },
            error: function(xhr) {}
        })
    }
</script>

<!--{eval $tabbar = 0;}-->
<!--{template xigua_hb:common_footer}-->
