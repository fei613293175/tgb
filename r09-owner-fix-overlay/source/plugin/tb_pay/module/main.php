<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if(!$tbpayorder){
    tbpay_js_alert("支付订单不存在");
}
if($tbpayorder == -1){
    tbpay_js_alert("支付订单状态错误");
}
if($tbpayorder == -2){
    tbpay_js_alert("支付订单金额错误");
}
if(!$_G['uid']){
    tbpay_js_alert("请登录后支付");
}

//获取账号余额
$userMoney = getUserMoney($_G['uid']);
//获取账号余额
$userMoneynew = getUserMoneyNew($_G['uid']);

require_once DISCUZ_ROOT . './source/plugin/tb_pay/scan.config.php';
$scan_qr_channels = tbpay_scan_qrcodes();
$scan_qrcodes_11 = $scan_qr_channels[11];
$scan_qrcodes_12 = $scan_qr_channels[12];

// 原有根据插件屏蔽支付方式
if($_GET['pluginid'] == "tb_cus_crashfh"){
    unset($zftype_arr[20]);
}
// 原有根据插件屏蔽支付方式

if($_GET['pluginid'] == "duobao"){
    if($_GET['rtype']==2){
        foreach ($zftype_arr as $key=>$value){
            if($key != 20){
                  unset($zftype_arr[$key]);
            }
        }
    }else{
        foreach ($zftype_arr as $key=>$value){
            if(!in_array($key,array(20) )){
                  unset($zftype_arr[$key]);
            }
        }
    }
}

// ========== 新增：小于等于1元时隐藏支付方式10和12 ==========
// 获取订单金额（此处假设$tbpayorder_new已在全局赋值，若没有请使用订单查询）
$orderPrice = floatval($tbpayorder_new['price']);
if($orderPrice <= 2){
    unset($zftype_arr[10], $zftype_arr[12]);
}
// ============================================================

// ========== 新增：金额为98或138元时屏蔽支付方式3 ==========
if($orderPrice == 58 || $orderPrice == 68){
    unset($zftype_arr[3]);
}
// ============================================================

include template('tb_pay:main');
