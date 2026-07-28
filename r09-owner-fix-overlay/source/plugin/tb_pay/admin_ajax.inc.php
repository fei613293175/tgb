<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require DISCUZ_ROOT . './source/plugin/tb_pay/common.php';

if($_G['groupid'] != 1){
    echo json_encode(array('code'=>-1,"msg"=>"非法操作"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $_GET['formhash'] != FORMHASH) {
    echo json_encode(array('code' => -1, 'msg' => '请求校验失败，请刷新后台后重试'));
    exit;
}

if ($_GET['ac'] == 'scan_delete') {
    $review_id = intval($_GET['review_id']);
    $review = DB::fetch_first('SELECT * FROM %t WHERE id=%d', array('tb_pay_scan_review', $review_id));
    if (!$review) {
        echo json_encode(array('code' => -1, 'msg' => '扫码审核订单不存在或已删除'));
        exit;
    }
    if (in_array(intval($review['status']), array(1, 3))) {
        echo json_encode(array('code' => -1, 'msg' => '审核通过或正在发放的订单禁止删除'));
        exit;
    }
    $payorder = C::t('#tb_pay#tb_pay')->getPayById(intval($review['pay_id']));
    if ($payorder && intval($payorder['ostatus']) === 1) {
        echo json_encode(array('code' => -1, 'msg' => '已支付订单禁止删除'));
        exit;
    }
    $proof_path = trim($review['proof_path']);
    DB::delete('tb_pay_scan_review', 'id=' . $review_id . ' AND status NOT IN (1,3)');
    if (DB::affected_rows() !== 1) {
        echo json_encode(array('code' => -1, 'msg' => '订单状态已变化，请刷新后台重试'));
        exit;
    }
    if ($payorder && intval($payorder['ostatus']) === 0) {
        DB::delete('tb_pay', 'id=' . intval($review['pay_id']) . ' AND ostatus=0');
    }
    if ($proof_path && strpos($proof_path, 'data/attachment/tb_pay_scan/') === 0) {
        $proof_file = DISCUZ_ROOT . './' . $proof_path;
        if (is_file($proof_file)) @unlink($proof_file);
    }
    echo json_encode(array('code' => 200, 'msg' => '扫码审核订单已删除'));
    exit;
}

if ($_GET['ac'] == 'scan_review') {
    $review_id = intval($_GET['review_id']);
    $decision = intval($_GET['decision']);
    $reason = trim(daddslashes($_GET['reason']));
    $review = DB::fetch_first('SELECT * FROM %t WHERE id=%d', array('tb_pay_scan_review', $review_id));
    if (!$review || !in_array(intval($review['paytype']), array(11, 12))) {
        echo json_encode(array('code' => -1, 'msg' => '扫码审核记录不存在'));
        exit;
    }
    if (intval($review['status']) === 1) {
        echo json_encode(array('code' => 200, 'msg' => '该订单已经审核通过'));
        exit;
    }
    if (intval($review['status']) !== 0) {
        echo json_encode(array('code' => -1, 'msg' => '该记录当前不可审核，请刷新页面'));
        exit;
    }

    if ($decision === 2) {
        if ($reason === '') {
            echo json_encode(array('code' => -1, 'msg' => '驳回时必须填写原因'));
            exit;
        }
        DB::update('tb_pay_scan_review', array(
            'status' => 2,
            'reject_reason' => $reason,
            'reviewer_uid' => intval($_G['uid']),
            'reviewtime' => TIMESTAMP,
            'updateline' => TIMESTAMP,
        ), 'id=' . $review_id . ' AND status=0');
        C::t('#tb_pay#tb_pay')->update_by_id(array('shstatus' => 2, 'liyou' => $reason, 'updateline' => TIMESTAMP), intval($review['pay_id']));
        echo json_encode(array('code' => 200, 'msg' => '已驳回，用户可以重新提交凭证'));
        exit;
    }

    if ($decision !== 1) {
        echo json_encode(array('code' => -1, 'msg' => '审核操作无效'));
        exit;
    }

    $lock_name = 'tbpay_scan_review_' . $review_id;
    if (discuz_process::islocked($lock_name, 30)) {
        echo json_encode(array('code' => -1, 'msg' => '该订单正在审核，请勿重复操作'));
        exit;
    }
    DB::query('UPDATE %t SET status=3,reviewer_uid=%d,reviewtime=%d,updateline=%d WHERE id=%d AND status=0', array(
        'tb_pay_scan_review', intval($_G['uid']), TIMESTAMP, TIMESTAMP, $review_id,
    ));
    if (!DB::affected_rows()) {
        discuz_process::unlock($lock_name);
        echo json_encode(array('code' => -1, 'msg' => '订单状态已变化，请刷新页面'));
        exit;
    }

    $payorder = C::t('#tb_pay#tb_pay')->getPayById(intval($review['pay_id']));
    if (!$payorder || $payorder['orderid'] !== $review['orderid'] || intval($payorder['uid']) !== intval($review['uid'])) {
        DB::update('tb_pay_scan_review', array('status' => 4, 'reject_reason' => '支付主订单校验失败', 'updateline' => TIMESTAMP), 'id=' . $review_id);
        discuz_process::unlock($lock_name);
        echo json_encode(array('code' => -1, 'msg' => '支付主订单校验失败，未发放服务'));
        exit;
    }

    if (intval($payorder['ostatus']) !== 1) {
        notify_res($payorder['orderid'], $payorder['orderid'] . '_scan_' . $review_id);
        $payorder = C::t('#tb_pay#tb_pay')->getPayById(intval($review['pay_id']));
    }
    if ($payorder && intval($payorder['ostatus']) === 1) {
        DB::update('tb_pay_scan_review', array(
            'status' => 1,
            'reject_reason' => '',
            'reviewer_uid' => intval($_G['uid']),
            'reviewtime' => TIMESTAMP,
            'updateline' => TIMESTAMP,
        ), 'id=' . $review_id);
        C::t('#tb_pay#tb_pay')->update_by_id(array('shstatus' => 1, 'liyou' => '', 'updateline' => TIMESTAMP), intval($review['pay_id']));
        discuz_process::unlock($lock_name);
        echo json_encode(array('code' => 200, 'msg' => '审核通过，用户服务已即时发放'));
        exit;
    }

    DB::update('tb_pay_scan_review', array(
        'status' => 4,
        'reject_reason' => '业务发放未成功，请技术人员核查，禁止重复审核',
        'updateline' => TIMESTAMP,
    ), 'id=' . $review_id);
    discuz_process::unlock($lock_name);
    echo json_encode(array('code' => -1, 'msg' => '业务发放未成功，订单未标记为审核通过，请技术人员核查'));
    exit;
}

if($_GET['ac']=='sh'){
    $sstatus = intval($_GET['sstatus']);
    $jfid= intval($_GET['jfid']);
    if($sstatus == 1){
      $ostatus = 1;
      //回调可以notify
        $result = C::t('#tb_pay#tb_pay')->getPayById($jfid);
        if($result){
            notify_res($result['orderid'],$result['orderid']."_".$result['paytype']);
            $arr=array(
                'ostatus'=>$ostatus,
                'shstatus'=>$sstatus,
                'liyou'=> daddslashes($_GET['liyou']),
                'updateline'=> time(),
                'odateline'=> time(),
            );
            C::t('#tb_pay#tb_pay')->update($jfid,$arr);
        }

    }else{

        $ostatus = 0;
        $arr=array(
            'ostatus'=>$ostatus,
            'shstatus'=>$sstatus,
            'liyou'=> daddslashes($_GET['liyou']),
            'updateline'=> time(),
            'odateline'=> time(),
        );
        C::t('#tb_pay#tb_pay')->update($jfid,$arr);
    }
    echo json_encode(array('code'=>200,"msg"=>"已操作成功"));
    exit;
}


?>
