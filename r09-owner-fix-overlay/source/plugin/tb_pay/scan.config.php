<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

// Add or remove entries here to maintain the QR-code carousel for pay_ID 11/12.
function tbpay_scan_qrcodes() {
    return array(
        11 => array(
            array('key' => 'alipay_1', 'label' => '支付宝收款码 1', 'url' => 'https://img.imehui.cn/20250126/1737893539679626a33d186.png'),
            array('key' => 'alipay_2', 'label' => '支付宝收款码 2', 'url' => 'https://img.imehui.cn/20250126/1737893556679626b4c5f84.png'),
            array('key' => 'alipay_3', 'label' => '支付宝收款码 3', 'url' => 'https://img.imehui.cn/20250126/1737893586679626d23891a.png'),
        ),
        12 => array(
            array('key' => 'wechat_1', 'label' => '微信收款码 1', 'url' => 'https://img.imehui.cn/20260528/17799620866a1810e6ae888.jpg'),
            array('key' => 'wechat_2', 'label' => '微信收款码 2', 'url' => 'https://img.imehui.cn/20260517/17789962246a095400f3855.jpg'),
        ),
    );
}

function tbpay_scan_qrcode_exists($paytype, $qrkey) {
    $channels = tbpay_scan_qrcodes();
    if (empty($channels[$paytype])) {
        return false;
    }
    foreach ($channels[$paytype] as $qrcode) {
        if ($qrcode['key'] === $qrkey) {
            return true;
        }
    }
    return false;
}

