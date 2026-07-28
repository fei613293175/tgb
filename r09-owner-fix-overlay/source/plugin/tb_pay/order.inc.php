<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require DISCUZ_ROOT . './source/plugin/tb_pay/common.php';

$modac = daddslashes($_GET['modac']);
if ($modac == 'myorder') {
    if (!$_G['uid']) {
        echo json_encode(array('total' => 0, 'code' => -1, 'list' => array()));
        exit;
    }
    $startlimit = max(0, intval($_GET['start']));
    $ppp = min(30, max(1, intval($_GET['pagesize'])));
    $wheres = 'where uid=' . intval($_G['uid']) . ' ORDER BY dateline desc';
    $total = C::t('#tb_pay#tb_pay')->count_all($wheres);
    $listdata = C::t('#tb_pay#tb_pay')->fetch_page_data($startlimit, $ppp, $wheres);
    $scan_status = array(0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 3 => '发放处理中', 4 => '发放异常');
    require_once DISCUZ_ROOT . './source/plugin/tb_pay/scan.config.php';
    $scan_channels = tbpay_scan_qrcodes();

    for ($i = 0; $i < count($listdata); $i++) {
        $listdata[$i]['dateline'] = dgmdate($listdata[$i]['dateline']);
        $listdata[$i]['ostatusstr'] = $paystatus[$listdata[$i]['ostatus']];
        $listdata[$i]['can_resubmit'] = 0;
        $listdata[$i]['resubmit_url'] = '';
        $listdata[$i]['resubmit_exhausted'] = 0;
        $listdata[$i]['resubmit_notice'] = '';
        $listdata[$i]['resubmit_data'] = null;
        if (!in_array(intval($listdata[$i]['paytype']), array(10, 11, 12))) {
            $listdata[$i]['shstatusstr'] = '';
        } elseif (in_array(intval($listdata[$i]['paytype']), array(11, 12))) {
            $review = DB::fetch_first('SELECT * FROM %t WHERE orderid=%s AND uid=%d', array(
                'tb_pay_scan_review', $listdata[$i]['orderid'], intval($_G['uid']),
            ));
            $listdata[$i]['shstatusstr'] = $review ? $scan_status[intval($review['status'])] : '待提交凭证';
            $listdata[$i]['liyou'] = $review && $review['reject_reason'] ? $review['reject_reason'] : $listdata[$i]['liyou'];
            if ($review && intval($review['status']) === 2 && intval($listdata[$i]['ostatus']) === 0) {
                if (intval($review['submit_count']) < 2) {
                    $paytype = intval($review['paytype']);
                    $qrcodes = array();
                    foreach (isset($scan_channels[$paytype]) ? $scan_channels[$paytype] : array() as $qrcode) {
                        $qrcodes[] = array(
                            'key' => $qrcode['key'],
                            'label' => $qrcode['label'],
                            'url' => $qrcode['url'],
                            'download_url' => 'plugin.php?id=tb_pay:scan_qr&paytype=' . $paytype . '&qr_key=' . rawurlencode($qrcode['key']),
                        );
                    }
                    $listdata[$i]['can_resubmit'] = $qrcodes ? 1 : 0;
                    $listdata[$i]['resubmit_notice'] = '仅剩1次重新提交机会，提交后无法再次修改';
                    $listdata[$i]['resubmit_data'] = array(
                        'paytype' => $paytype,
                        'channel_name' => $paytype === 11 ? '支付宝扫码' : '微信扫码',
                        'pluginid' => $listdata[$i]['plugin'],
                        'orderid' => $listdata[$i]['orderid'],
                        'zd' => $listdata[$i]['payextend'],
                        'qrcodes' => $qrcodes,
                    );
                } else {
                    $listdata[$i]['resubmit_exhausted'] = 1;
                    $listdata[$i]['resubmit_notice'] = '该订单的1次重新提交机会已用完，请重新下单';
                }
            }
        } else {
            $listdata[$i]['shstatusstr'] = $payshstatus[$listdata[$i]['shstatus']];
        }
    }
    echo json_encode(array('total' => $total, 'code' => 200, 'list' => $listdata));
    exit;
}

include template('tb_pay:payorder');
