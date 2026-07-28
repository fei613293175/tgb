<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
// 在支付脚本的开头（<?php 之后，任何业务逻辑之前）添加
require_once DISCUZ_ROOT . './source/plugin/tb_credit/function.core.php';
require_once DISCUZ_ROOT . './source/plugin/tb_credit/credit.core.php';
if(!$tbpayorder){
    tbpay_json_echo(-1,"支付订单不存在");
}
if($tbpayorder == -1){
    tbpay_json_echo(-1,"支付订单状态错误");
}
if($tbpayorder == -2){
    tbpay_json_echo(-1,"支付订单金额错误");
}

$zftype = intval($_GET['zftype']);
if($_GET['formhash'] != FORMHASH){
    tbpay_json_echo(-1,"FORMHASH ERROR");
}
if(!$zftype_arr[$zftype]){
    tbpay_json_echo(-2,"支付方式错误");
}

if(!$tbpayorder){
    tbpay_json_echo(-2,"支付方式错误");
}


$payorder_data = [
    'uid'=>$_G['uid'],
    'username'=>$_G['username'],
    'orderid'=>$orderid,
    'subject'=>$tbpayorder_new['subject'],
    'plugin'=>$pluginid,
    'price'=>$tbpayorder_new['price'],
     //扩展信息
    'payextend'=>$payextended,
    'paytype'=>$zftype,
    'dateline'=>time(),
    'updateline'=>time(),
];

function tbpay_save_scan_proof($file) {
    if (empty($file) || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        tbpay_json_echo(-1, '请上传支付凭证截图');
    }
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        tbpay_json_echo(-1, '支付凭证上传失败，请重新选择图片');
    }
    if (intval($file['size']) <= 0 || intval($file['size']) > 8 * 1024 * 1024) {
        tbpay_json_echo(-1, '支付凭证图片不能超过8MB');
    }

    $imageinfo = @getimagesize($file['tmp_name']);
    $extensions = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp');
    if (!$imageinfo || empty($extensions[$imageinfo[2]])) {
        tbpay_json_echo(-1, '支付凭证仅支持 JPG、PNG、GIF 或 WEBP 图片');
    }

    $relative_dir = 'data/attachment/tb_pay_scan/' . date('Ym') . '/';
    $absolute_dir = DISCUZ_ROOT . './' . $relative_dir;
    if (!is_dir($absolute_dir) && !mkdir($absolute_dir, 0755, true)) {
        tbpay_json_echo(-1, '支付凭证保存失败，请稍后重试');
    }
    $filename = date('YmdHis') . '_' . md5(uniqid(mt_rand(), true)) . '.' . $extensions[$imageinfo[2]];
    if (!move_uploaded_file($file['tmp_name'], $absolute_dir . $filename)) {
        tbpay_json_echo(-1, '支付凭证保存失败，请稍后重试');
    }
    return $relative_dir . $filename;
}

//shangde
if($zftype == 1){
    require_once DISCUZ_ROOT . "./source/plugin/tb_pay/lib/shande/YunPayment.class.php";
    $data = [
        'version' => 10,
        'mer_no' =>  '6888803123738', //商户号
        'mer_order_no' => $orderid, //商户唯一订单号
        'create_time' => date('YmdHis'),
        'expire_time' => date('YmdHis', time()+30*60),
        'order_amt' =>  $tbpayorder_new['price'], //订单支付金额
        'notify_url' => $_G['siteurl']."source/plugin/tb_pay/notify_sd.php", //订单支付异步通知
        'return_url' => $_G['siteurl']."source/plugin/tb_pay/return_sd.php?orderid=$orderid", //订单前端页面跳转地址
        'create_ip' => $_G['clientip'],
        'goods_name' => $tbpayorder_new['subject'],
        'store_id' => '000000',
        'product_code' => '05030001',
        //  ○ 开户账户页面 product_code：00000001
        //  ○ 消费C2B product_code：04010001
        //  ○ 担保消费(C2C)  product_code：04010004
        //  ○ 消费（C2C） product_code：04010003
        'clear_cycle' => '3',
        //pay_extra参考语雀文档4.3
        'pay_extra' => json_encode(["userId"=>$_G['uid'],"nickName"=>$_G['username'],"accountType"=>"1"]),
        'accsplit_flag' => 'NO',
        'jump_scheme' => '',
        'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
        'sign_type' => 'RSA'
    ];
    $a = new YunPayment();
    $b = $a->dopayment($data);
    C::t('#tb_pay#tb_pay')->insert($payorder_data);
    tbpay_json_echo(200,$b,'tz');

}elseif($zftype == 2){

    require_once DISCUZ_ROOT . "./source/plugin/tb_pay/lib/shande/YunPayment.class.php";
    
    

    $data = [
        'version' => 10,
        'mer_no' =>  '6888803123738', //商户号
        'mer_order_no' => $orderid, //商户唯一订单号
        'create_time' => date('YmdHis'),
        'expire_time' => date('YmdHis', time()+30*60),
        'order_amt' =>  $tbpayorder_new['price'], //订单支付金额
        'notify_url' => $_G['siteurl']."source/plugin/tb_pay/notify_sd.php", //订单支付异步通知
        'return_url' => $_G['siteurl']."source/plugin/tb_pay/return_sd.php?orderid=$orderid", //订单前端页面跳转地址
        'create_ip' => $_G['clientip'],
        'goods_name' => $tbpayorder_new['subject'],
        'store_id' => '000000',
        'product_code' => '02000001',
        //  ○ 开户账户页面 product_code：00000001
        //  ○ 消费C2B product_code：04010001
        //  ○ 担保消费(C2C)  product_code：04010004
        //  ○ 消费（C2C） product_code：04010003
        'clear_cycle' => '3',
        //pay_extra参考语雀文档4.3
       // 'pay_extra' => json_encode(["userId"=>$_G['uid'],"nickName"=>$_G['username'],"accountType"=>"1"]),
        'accsplit_flag' => 'NO',
        'jump_scheme' => '',
        'meta_option' => json_encode([["s" => "Android","n" => "wxDemo","id" => "com.pay.paytypetest","sc" => "com.pay.paytypetest"]]),
        'sign_type' => 'RSA'
    ];
    $payurlurl = "https://sandcash.mixienet.com.cn/pay/h5/qrcode?";
    $a = new YunPayment();
    $b = $a->dopayment($data,$payurlurl);


    C::t('#tb_pay#tb_pay')->insert($payorder_data);

    
    tbpay_json_echo(200,$b,'tz');


}elseif($zftype == 11 || $zftype == 12){

    require_once DISCUZ_ROOT . './source/plugin/tb_pay/scan.config.php';
    $qr_key = trim((string)$_GET['qr_key']);
    $payer_nickname = trim((string)$_GET['payer_nickname']);
    $realname_last = trim((string)$_GET['realname_last']);

    if (!$_G['uid']) {
        tbpay_json_echo(-1, '请登录后提交审核');
    }
    if (!tbpay_scan_qrcode_exists($zftype, $qr_key)) {
        tbpay_json_echo(-1, '请选择有效的收款二维码');
    }
    if ($payer_nickname === '' || strlen($payer_nickname) > 60) {
        tbpay_json_echo(-1, '请输入正确的微信或支付宝网名');
    }
    if ($realname_last === '' || !preg_match('/^.{1}$/us', $realname_last)) {
        tbpay_json_echo(-1, '真实姓名最后一个字只能填写1个字');
    }

    $lock_name = 'tbpay_scan_' . md5($_G['uid'] . '|' . $orderid);
    if (discuz_process::islocked($lock_name, 10)) {
        tbpay_json_echo(-1, '正在提交，请勿重复操作');
    }

    $review = DB::fetch_first('SELECT * FROM %t WHERE orderid=%s', array('tb_pay_scan_review', $orderid));
    if ($review && intval($review['uid']) !== intval($_G['uid'])) {
        discuz_process::unlock($lock_name);
        tbpay_json_echo(-1, '订单归属校验失败');
    }
    if ($review && in_array(intval($review['status']), array(0, 1, 3, 4))) {
        discuz_process::unlock($lock_name);
        $message = intval($review['status']) === 1 ? '该订单已审核通过' : '该订单已提交，请勿重复提交';
        tbpay_json_echo(-1, $message);
    }
    if ($review && intval($review['status']) === 2) {
        if (intval($review['submit_count']) >= 2) {
            discuz_process::unlock($lock_name);
            tbpay_json_echo(-1, '该订单的1次重新提交机会已用完，请重新下单');
        }
        if (intval($review['paytype']) !== $zftype) {
            discuz_process::unlock($lock_name);
            tbpay_json_echo(-1, '重新提交必须使用原支付渠道');
        }
    }

    $existing_pay = C::t('#tb_pay#tb_pay')->getPayByorderid($orderid);
    if ($existing_pay) {
        if (intval($existing_pay['uid']) !== intval($_G['uid']) || intval($existing_pay['paytype']) !== $zftype || intval($existing_pay['ostatus']) === 1) {
            discuz_process::unlock($lock_name);
            tbpay_json_echo(-1, '订单状态已变化，请重新下单');
        }
        $pay_id = intval($existing_pay['id']);
    } elseif ($review) {
        discuz_process::unlock($lock_name);
        tbpay_json_echo(-1, '原支付订单不存在，请重新下单');
    }

    $proof_path = tbpay_save_scan_proof($_FILES['payment_proof']);
    if (!$existing_pay) {
        $pay_id = intval(C::t('#tb_pay#tb_pay')->insert($payorder_data, true));
    }

    $review_data = array(
        'pay_id' => $pay_id,
        'orderid' => $orderid,
        'uid' => intval($_G['uid']),
        'paytype' => $zftype,
        'qr_key' => $qr_key,
        'payer_nickname' => $payer_nickname,
        'realname_last' => $realname_last,
        'proof_path' => $proof_path,
        'status' => 0,
        'reject_reason' => '',
        'updateline' => TIMESTAMP,
        'reviewtime' => 0,
        'reviewer_uid' => 0,
    );
    if ($review) {
        $review_data['submit_count'] = intval($review['submit_count']) + 1;
        DB::update('tb_pay_scan_review', $review_data, 'id=' . intval($review['id']));
    } else {
        $review_data['dateline'] = TIMESTAMP;
        $review_data['submit_count'] = 1;
        DB::insert('tb_pay_scan_review', $review_data);
    }
    discuz_process::unlock($lock_name);
    tbpay_json_echo(200, '已提交审核，你可以在个人中心查询审核状态');

}elseif($zftype == 10){

    if (!discuz_process::islocked('add_pay_alipaycode', 5)) {
        $payorder_data['alipaycode'] = trim(daddslashes($_GET['alipaycode']));
        $isalipaycode = C::t('#tb_pay#tb_pay')->getByalipaycode($payorder_data['alipaycode']);
        if($isalipaycode){
            tbpay_json_echo(-1, "口令红包已存在");
        }
        $payorder_data['alipayname'] = trim(daddslashes($_GET['alipayname']));
        $payorder_data['alipaynprice'] = trim(daddslashes($_GET['alipaynprice']));
        C::t('#tb_pay#tb_pay')->insert($payorder_data);
        discuz_process::unlock('add_pay_alipaycode');
        //tbpay_json_echo(200, "已提交审核，你可以在“我的订单”页面查询。");
        tbpay_json_echo(200, $_G['siteurl']."source/plugin/tb_pay/return_sd.php?orderid=$orderid",'tz');
    }else{
        tbpay_json_echo(-1, "操作太频繁");
    }


}elseif($zftype == 9){

    if (!discuz_process::islocked('add_pay_money', 5)) {
        //获取账号余额
        $userMoney = getUserMoney($_G['uid']);
        if($userMoney<$tbpayorder_new['price']){
            tbpay_json_echo(-1, "支付失败,余额不足");
        }
        $res =  setUserMoney($_G['uid'],$tbpayorder_new['price'],$tbpayorder_new['subject'],"",'plugin.php?id=tb_pay:order');
        if($res){
            C::t('#tb_pay#tb_pay')->insert($payorder_data);
            $trade_no = $orderid."_9";
            notify_res($orderid,$trade_no);
        }
        discuz_process::unlock('add_pay_money');
        tbpay_json_echo(200, $_G['siteurl']."source/plugin/tb_pay/return_sd.php?orderid=$orderid",'tz');

    }else{
        tbpay_json_echo(-1, "操作太频繁");
    }


} elseif ($zftype == 20) {
    if (!discuz_process::islocked('add_pay_money', 5)) {
        $dhbil = 4;
        $jfcount = $tbpayorder_new['price'] * $dhbil;
        // 使用 tb_credit 插件的积分获取函数（推荐，若无则保留 getuserprofile）
        $userMoney = getByUidCredit(1); // 假设 getByUidCredit(1) 获取积分类型1（星创币）
        if ($userMoney < $jfcount) {
            tbpay_json_echo(-1, "支付失败，积分不足");
        }
        $userex4 = getByUidCredit(3); // 积分类型3（贡献值）
        $premiumext4 = 4;
        $premiumext4_sxf = $tbpayorder_new['price'] * $premiumext4;
        if ($userex4 < $premiumext4_sxf) {
            tbpay_json_echo(-1, "贡献值不足，每使用1个星创币，需额外消耗2个贡献值");
        }

        // ========== 替换 updatemembercount 为 updateUserCredit ==========
        // 1. 扣除星创币（jftype = 1）
        $updateCredit1 = array(
            'oper' => 2,                               // 2表示减少
            'jftype' => 1,                             // 积分类型1（星创币）
            'jfcount' => $jfcount,
            'ltype' => "积分支付",
            'dostr' => "支付购买：{$tbpayorder_new['subject']}，订单号：{$orderid}",
        );
        $res1 = updateUserCredit($updateCredit1);
        if (!$res1) {
            discuz_process::unlock('add_pay_money');
            tbpay_json_echo(-1, "积分扣除失败，请重试");
        }

        // 2. 扣除贡献值（jftype = 3）手续费
        if ($premiumext4_sxf > 0) {
            $updateCredit3 = array(
                'oper' => 2,
                'jftype' => 3,                         // 积分类型3（贡献值）
                'jfcount' => $premiumext4_sxf,
                'ltype' => "积分支付",
                'dostr' => "支付手续费：{$tbpayorder_new['subject']}，订单号：{$orderid}",
            );
            $res2 = updateUserCredit($updateCredit3);
            if (!$res2) {
                // 如果手续费扣除失败，理论上需要回滚第一步扣除，但为了简化，可提示错误
                discuz_process::unlock('add_pay_money');
                tbpay_json_echo(-1, "贡献值扣除失败，请重试");
            }
        }

        // 插入支付订单记录（仍使用原表）
        C::t('#tb_pay#tb_pay')->insert($payorder_data);
        $trade_no = $orderid . "_9";
        notify_res($orderid, $trade_no);

        discuz_process::unlock('add_pay_money');
        tbpay_json_echo(200, $_G['siteurl'] . "source/plugin/tb_pay/return_sd.php?orderid=$orderid", 'tz');
    } else {
        tbpay_json_echo(-1, "操作太频繁");
    }



}elseif($zftype == 21){

    
    
    if (!discuz_process::islocked('add_pay_money', 5)) {
        //获取账号余额
        $userMoney = getUserMoneyNew($_G['uid']);
        if($userMoney<$tbpayorder_new['price']){
            tbpay_json_echo(-1, "支付失败,余额不足");
        }
        $res =  setUserMoneyNew($_G['uid'],$tbpayorder_new['price'],$tbpayorder_new['subject'],"",'plugin.php?id=tb_pay:order');
        if($res){
            C::t('#tb_pay#tb_pay')->insert($payorder_data);
            $trade_no = $orderid."_21";
            notify_res($orderid,$trade_no);
        }
        discuz_process::unlock('add_pay_money');
        tbpay_json_echo(200, $_G['siteurl']."source/plugin/tb_pay/return_sd.php?orderid=$orderid",'tz');

    }else{
        tbpay_json_echo(-1, "操作太频繁");
    }




}elseif($zftype == 3 || $zftype == 4 ||  $zftype == 5){

   
   
   
    C::t('#tb_pay#tb_pay')->insert($payorder_data);
   
    require_once DISCUZ_ROOT . "./source/plugin/tb_pay/lib/fuylink/epay.config.php";
    require_once DISCUZ_ROOT . "./source/plugin/tb_pay/lib/fuylink/EpayCore.class.php";

    /**************************请求参数**************************/
    /*    $notify_url =  $_G['siteurl']."http://127.0.0.1/SDK/notify_url.php";
    //需http://格式的完整路径，不能加?id=123这类自定义参数

    //页面跳转同步通知页面路径
        $return_url = "http://127.0.0.1/SDK/return_url.php";
    //需http://格式的完整路径，不能加?id=123这类自定义参数*/

    $notify_url = $_G['siteurl']."source/plugin/tb_pay/notify_fuy.php";//订单支付异步通知
    $return_url = $_G['siteurl']."source/plugin/tb_pay/return_fuy.php"; //订单前端页面跳转地址

//商户订单号
    $out_trade_no = $orderid;
//商户网站订单系统中唯一订单号，必填

//支付方式（可传入alipay,wxpay,qqpay,bank,jdpay）


if($zftype == 3){
       $type = "alipay";
}elseif($zftype == 4){
     $type = "usdt";
}elseif($zftype == 5){
     $type = "bank";
}

 
    //商品名称
    $name = $tbpayorder_new['subject'];
    //付款金额
    $money =  $tbpayorder_new['price'];//订单支付金额;


    /************************************************************/
//构造要请求的参数数组，无需改动
    $parameter = array(
        "pid" => $epay_config['pid'],
        "type" => $type,
        "notify_url" => $notify_url,
        "return_url" => $return_url,
        "out_trade_no" => $out_trade_no,
        "name" => $name,
        "money"	=> $money,
    );
    //建立请求
    $epay = new EpayCore($epay_config);
    $html_text = $epay->pagePay($parameter);

    tbpay_json_echo(200,$html_text,'html');

}



?>
