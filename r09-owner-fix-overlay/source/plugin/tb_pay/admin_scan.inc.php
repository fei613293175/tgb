<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

if (!empty($_GET['tbpay_ajax'])) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=' . CHARSET);
    require DISCUZ_ROOT . './source/plugin/tb_pay/admin_ajax.inc.php';
    exit;
}

require DISCUZ_ROOT . './source/plugin/tb_pay/common.php';

$formhash = FORMHASH;
$status_text = array(0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 3 => '发放处理中', 4 => '发放异常');
$rows = DB::fetch_all('SELECT r.*,p.username,p.subject,p.price,p.ostatus FROM %t r LEFT JOIN %t p ON p.id=r.pay_id ORDER BY r.id DESC LIMIT 100', array(
    'tb_pay_scan_review', 'tb_pay',
));

showtableheader('扫码支付审核（最近100条）');
showsubtitle(array('订单', '用户/商品', '金额/渠道', '付款信息', '支付凭证', '状态', '提交时间', '审核操作'));
if (!$rows) {
    showtablerow('', array('colspan="8"'), array('暂无扫码支付审核记录'));
}
foreach ($rows as $row) {
    $review_id = intval($row['id']);
    $channel = intval($row['paytype']) === 11 ? '支付宝扫码' : '微信扫码';
    $proof_url = $_G['siteurl'] . ltrim($row['proof_path'], '/');
    $proof = '<a href="' . dhtmlspecialchars($proof_url) . '" target="_blank"><img src="' . dhtmlspecialchars($proof_url) . '" style="width:90px;max-height:110px;object-fit:cover;border-radius:4px;border:1px solid #ddd" alt="支付凭证"></a>';
    $status = isset($status_text[intval($row['status'])]) ? $status_text[intval($row['status'])] : '未知';
    if (intval($row['status']) === 0) {
        $action = '<div style="min-width:230px"><input id="scan_reason_' . $review_id . '" class="txt" style="width:210px;margin-bottom:6px" placeholder="驳回时填写原因"><br>'
            . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',1)">审核通过并发放</button> '
            . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',2)">驳回</button></div>';
    } else {
        $action = intval($row['status']) === 2 ? '等待用户重新提交' : '不可重复审核';
    }
    showtablerow('', array(), array(
        dhtmlspecialchars($row['orderid']),
        '<b>' . dhtmlspecialchars($row['username']) . '</b><br>' . dhtmlspecialchars($row['subject']),
        '<b>￥' . dhtmlspecialchars($row['price']) . '</b><br>' . $channel,
        '网名：' . dhtmlspecialchars($row['payer_nickname']) . '<br>姓名末字：' . dhtmlspecialchars($row['realname_last']) . '<br>收款码：' . dhtmlspecialchars($row['qr_key']),
        $proof,
        '<b>' . $status . '</b>' . ($row['reject_reason'] ? '<br><span style="color:#c33">' . dhtmlspecialchars($row['reject_reason']) . '</span>' : ''),
        dgmdate($row['updateline'] ? $row['updateline'] : $row['dateline']),
        $action,
    ));
}
showtablefooter();

echo <<<EOT
<script src="source/plugin/tb_pay/static/js/jquery-3.3.1.min.js"></script>
<script>jQuery.noConflict();
function reviewScan(reviewId, decision) {
    var reason = jQuery('#scan_reason_' + reviewId).val() || '';
    if (decision === 2 && !reason) {
        alert('驳回时必须填写原因');
        return;
    }
    var message = decision === 1 ? '确认审核通过并立即向用户发放对应服务？' : '确认驳回该支付凭证？';
    if (!window.confirm(message)) return;
    jQuery.ajax({
        type: 'post',
        url: 'admin.php?action=plugins&operation=config&do={$pluginid}&identifier=tb_pay&pmod=admin_scan&tbpay_ajax=1',
        dataType: 'text',
        data: {ac:'scan_review', review_id:reviewId, decision:decision, reason:reason, formhash:'{$formhash}'},
        success: function(response) {
            var data;
            try {
                data = JSON.parse(String(response).split(/\r?\n/)[0].trim());
            } catch (error) {
                alert('审核响应异常，请刷新后台重试');
                return;
            }
            alert(data.msg);
            if (data.code === 200) window.location.reload();
        },
        error: function() { alert('网络异常，请稍后重试'); }
    });
}
</script>
EOT;
