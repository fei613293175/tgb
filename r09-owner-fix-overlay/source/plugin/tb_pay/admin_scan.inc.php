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
$approved_summary = DB::fetch_first('SELECT COUNT(*) AS total_count,COALESCE(SUM(p.price),0) AS total_money FROM %t r LEFT JOIN %t p ON p.id=r.pay_id WHERE r.status=1', array(
    'tb_pay_scan_review', 'tb_pay',
));
$rows = DB::fetch_all('SELECT r.*,p.username,p.subject,p.price,p.ostatus FROM %t r LEFT JOIN %t p ON p.id=r.pay_id ORDER BY CASE WHEN r.status=0 THEN 0 ELSE 1 END ASC,r.id DESC LIMIT 100', array(
    'tb_pay_scan_review', 'tb_pay',
));

showtableheader('扫码支付审核（最近100条）｜已通过 ' . intval($approved_summary['total_count']) . ' 笔，累计金额 ￥' . number_format(floatval($approved_summary['total_money']), 2));
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
    $can_delete = !in_array(intval($row['status']), array(1, 3)) && intval($row['ostatus']) !== 1;
    $delete_button = $can_delete ? '<button type="button" class="btn" style="margin-left:4px;color:#b42318" onclick="deleteScanOrder(' . $review_id . ')">删除订单</button>' : '';
    if (intval($row['status']) === 0) {
        $action = '<div style="min-width:230px"><input id="scan_reason_' . $review_id . '" class="txt" style="width:210px;margin-bottom:6px" placeholder="驳回时填写原因"><br>'
            . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',1)">审核通过并发放</button> '
            . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',2)">驳回</button>' . $delete_button . '</div>';
    } else {
        if (intval($row['status']) === 2) {
            $action = intval($row['submit_count']) < 2 ? '等待用户重新提交（剩余1次）' : '重新提交机会已用完';
        } else {
            $action = '不可重复审核';
        }
        $action .= $delete_button;
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
                var responseText = String(response);
                var footerIndex = responseText.indexOf('</div>');
                if (footerIndex >= 0) responseText = responseText.slice(0, footerIndex);
                data = JSON.parse(responseText.trim());
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
function deleteScanOrder(reviewId) {
    if (!window.confirm('确认删除该扫码审核订单、未支付主订单和支付凭证？删除后无法恢复。')) return;
    jQuery.ajax({
        type: 'post',
        url: 'admin.php?action=plugins&operation=config&do={$pluginid}&identifier=tb_pay&pmod=admin_scan&tbpay_ajax=1',
        dataType: 'text',
        data: {ac:'scan_delete', review_id:reviewId, formhash:'{$formhash}'},
        success: function(response) {
            var data;
            try {
                var responseText = String(response);
                var footerIndex = responseText.indexOf('</div>');
                if (footerIndex >= 0) responseText = responseText.slice(0, footerIndex);
                data = JSON.parse(responseText.trim());
            } catch (error) {
                alert('删除响应异常，请刷新后台重试');
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
