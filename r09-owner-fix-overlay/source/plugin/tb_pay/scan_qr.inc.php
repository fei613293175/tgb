<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require DISCUZ_ROOT . './source/plugin/tb_pay/scan.config.php';

$paytype = intval($_GET['paytype']);
$qr_key = trim($_GET['qr_key']);
$url = '';
$channels = tbpay_scan_qrcodes();
if (isset($channels[$paytype])) {
    foreach ($channels[$paytype] as $qrcode) {
        if ($qrcode['key'] === $qr_key) {
            $url = $qrcode['url'];
            break;
        }
    }
}
if (!$url || !function_exists('curl_init')) {
    showmessage('二维码不可用，请返回后重试');
}

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($curl, CURLOPT_TIMEOUT, 15);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'Tuiguangbao QR Downloader');
if (defined('CURLOPT_PROTOCOLS') && defined('CURLPROTO_HTTPS')) {
    curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
}
$image = curl_exec($curl);
$http_code = intval(curl_getinfo($curl, CURLINFO_HTTP_CODE));
curl_close($curl);
if ($image === false || $http_code !== 200 || strlen($image) > 8 * 1024 * 1024) {
    showmessage('二维码下载失败，请稍后重试');
}

$info = @getimagesizefromstring($image);
$types = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp');
if (!$info || empty($info['mime']) || !isset($types[$info['mime']])) {
    showmessage('二维码文件格式无效');
}

$filename = 'tuiguangbao-' . ($paytype === 11 ? 'alipay-' : 'wechat-') . $qr_key . '.' . $types[$info['mime']];
header('Content-Type: ' . $info['mime']);
header('Content-Length: ' . strlen($image));
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: private, no-store, max-age=0');
header('X-Content-Type-Options: nosniff');
echo $image;
exit;
