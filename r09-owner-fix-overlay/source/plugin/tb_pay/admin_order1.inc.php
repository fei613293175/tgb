<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require DISCUZ_ROOT . './source/plugin/tb_pay/common.php';

$scan_status_text = array(0 => '待审核', 1 => '审核通过', 2 => '审核驳回', 3 => '发放处理中', 4 => '发放异常');
$scan_rows = DB::fetch_all('SELECT r.*,p.username,p.subject,p.price,p.ostatus FROM %t r LEFT JOIN %t p ON p.id=r.pay_id ORDER BY r.id DESC LIMIT 100', array(
    'tb_pay_scan_review', 'tb_pay',
));

showtableheader('扫码支付审核（微信 pay_ID=12 / 支付宝 pay_ID=11，最近100条）');
showsubtitle(array('订单', '用户/商品', '金额/渠道', '付款信息', '支付凭证', '状态', '提交时间', '审核操作'));
if ($scan_rows) {
    foreach ($scan_rows as $scan_row) {
        $review_id = intval($scan_row['id']);
        $paytype_name = intval($scan_row['paytype']) === 11 ? '支付宝扫码' : '微信扫码';
        $proof_url = $_G['siteurl'] . ltrim($scan_row['proof_path'], '/');
        $proof_html = '<a href="' . dhtmlspecialchars($proof_url) . '" target="_blank"><img src="' . dhtmlspecialchars($proof_url) . '" style="width:90px;max-height:110px;object-fit:cover;border-radius:4px;border:1px solid #ddd" alt="支付凭证"></a>';
        $status = isset($scan_status_text[intval($scan_row['status'])]) ? $scan_status_text[intval($scan_row['status'])] : '未知';
        if (intval($scan_row['status']) === 0) {
            $action = '<div style="min-width:230px"><input id="scan_reason_' . $review_id . '" class="txt" style="width:210px;margin-bottom:6px" placeholder="驳回时填写原因"><br>'
                . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',1)">审核通过并发放</button> '
                . '<button type="button" class="btn" onclick="reviewScan(' . $review_id . ',2)">驳回</button></div>';
        } else {
            $action = intval($scan_row['status']) === 2 ? '等待用户重新提交' : '不可重复审核';
        }
        $table = array(
            dhtmlspecialchars($scan_row['orderid']),
            '<b>' . dhtmlspecialchars($scan_row['username']) . '</b><br>' . dhtmlspecialchars($scan_row['subject']),
            '<b>￥' . dhtmlspecialchars($scan_row['price']) . '</b><br>' . $paytype_name,
            '网名：' . dhtmlspecialchars($scan_row['payer_nickname']) . '<br>姓名末字：' . dhtmlspecialchars($scan_row['realname_last']) . '<br>收款码：' . dhtmlspecialchars($scan_row['qr_key']),
            $proof_html,
            '<b>' . $status . '</b>' . ($scan_row['reject_reason'] ? '<br><span style="color:#c33">' . dhtmlspecialchars($scan_row['reject_reason']) . '</span>' : ''),
            dgmdate($scan_row['updateline'] ? $scan_row['updateline'] : $scan_row['dateline']),
            $action,
        );
        showtablerow('', array(), $table);
    }
} else {
    showtablerow('', array('colspan="8"'), array('暂无扫码支付审核记录'));
}
showtablefooter();

if (submitcheck("forumset")) {
    if(is_array($_GET['delete'])) {
        C::t('#tb_pay#tb_pay')->delete($_GET['delete']);
    }
    cpmsg("操作成功", 'action=plugins&operation=config&identifier=tb_pay&pmod=admin_order1', 'succeed');
}

showtableheader ("搜索");
showformheader ( 'plugins&operation=config&do=' . $pluginid . '&pmod=tb_pay&pmod=admin_order1', 'testhd' );

foreach ($payshstatus as $key=>$value){

    if($_GET['shstatus']>-1){
        if($_GET['shstatus'] == $key){
            $selected2 = "selected='selected'";
        }else{
            $selected2 = "";
        }
    }
    $shstatus_option.= "<option value=$key ".$selected2.">".$value."</option>";
}
foreach ($paystatus as $key=>$value){

    if($_GET['ostatus']>-1){
    if($_GET['ostatus'] == $key){
        $selected3 = "selected='selected'";
    }else{
        $selected3 = "";
    }
    }
    $status_option .="<option value=$key ".$selected3.">".$value."</option>";
}



showtablerow('', array(),
    array(
        "订单号", '<input type="text" name="orderid"  style="width:250px;" class="txt"  value="'.$_GET['orderid'].'" />',
    )
);
showtablerow('', array(),
    array(
        "订单状态", '<select name="ostatus" style="width:100px;"><option value=-1>'.cplang('nolimit').'</option>'.$status_option.'</select>',
    )
);
showtablerow('', array(),
    array(
        "审核状态", '<select name="shstatus" style="width:100px;"><option value=-1>'.cplang('nolimit').'</option>'.$shstatus_option.'</select>',
    )
);
showtablerow('', array(),
    array(
        "订单时间", '<input type="text" name="create_time_start" class="txt" value="'.$_GET['create_time_start'].'" onclick="showcalendar(event, this);" />- &nbsp;<input type="text" name="create_time_end" class="txt" value="'.$_GET['create_time_end'].'" onclick="showcalendar(event, this)" />',
    )
);


showsubmit ('searchsubmit' );
showformfooter ();
showtablefooter ();

$wheres = 'where paytype=10';

if ($_GET['orderid']) {
    $wheres .= " AND orderid like '%" . trim(daddslashes($_GET ['orderid'])) . "%'";
    $pageurl .= "&orderid=".daddslashes($_GET['orderid']);
}
if ($_GET ['ostatus']>-1) {
    $pstatus = intval($_GET['ostatus']);
    $wheres .= " AND ostatus=$pstatus";
    $pageurl .= "&ostatus=".intval($_GET ['ostatus']);
}
if ($_GET ['shstatus']>-1) {
    $shstatus = intval($_GET['shstatus']);
    $wheres .= " AND shstatus=$shstatus";
    $pageurl .= "&shstatus=".intval($_GET ['shstatus']);
}

if ($_GET ['create_time_start'] && $_GET ['create_time_end']) {
    $create_time_start = strtotime($_GET ['create_time_start']);
    $create_time_end = strtotime($_GET ['create_time_end']);
    $wheres .= " AND (dateline>=" .$create_time_start." AND dateline<=".$create_time_end.")";
    $pageurl .= "&create_time_start=".$_GET['create_time_start']."&create_time_end=".$_GET ['create_time_end'];
}


$sprice= C::t ( '#tb_pay#tb_pay' )->fetch_first_field_data("sum(price) as sprice",$wheres);
$sprice = $sprice['sprice']?$sprice['sprice']:0;
showtablerow('', array(),
   "订单金额：<span style='color:red;font-weight: 700'>{$sprice} </span>元"
);

showformheader("plugins&operation=config&do=" . $plugin["pluginid"] . "&identifier=" . $plugin["identifier"] . "&pmod=admin_order1");
showtableheader("支付宝口令红包订单");
showsubtitle(array(
    "ID",
    "支付用户",
    "订单号",
    "支付金额",
    "描述",
    "支付来源",
    "支付方式",
    "支付状态",

    "审核状态",
    "审核意见",
    "支付口令信息",
    "审核时间",
    "创建时间",
    "操作",
));
$pageurl =  ADMINSCRIPT.'?action=plugins&operation=config&identifier=tb_pay&pmod=admin_order1'.$pageurl;
$maxp  = 15;
$page = max(1,intval($_GET['page']));
$startlimit = ($page - 1) * $maxp;
$wheres  .=  " order by id desc";
$allcount = C::t('#tb_pay#tb_pay')->count_all($wheres);




if ($allcount) {
    $query = C::t ( '#tb_pay#tb_pay' )->fetch_page_data( $startlimit, $maxp, $wheres);
    foreach($query as $k=>$v){

        if(!$v['shstatus']){
            $shstr = '<a style="color:#369;" onclick="gosh(' . $v['id'] . ')" href="javascript:void(-1)">立即审核</a>';
        }else{
            $shstr = "N/A";
        }
        $table = array();
        $table[] = '<input type="checkbox" class="checkbox" name="delete[]" value="' . $v['id'] . '" />';
        $table[] = $v['username'];
        $table[] = $v['orderid'];
        $table[] = $v['price'];
        $table[] = $v['subject'];
        $table[] = $v['plugin'];
        $table[] = $zftype_arr[$v['paytype']][0];
        $table[] =  $paystatus[$v['ostatus']];
        $table[] =  $payshstatus[$v['shstatus']];
        $table[] = $v['liyou'];
        $table[] =  "<div><div>红包口令: ".$v['alipaycode']."</div><div>支付宝姓名: ".$v['alipayname']."</div><div>红包金额: ".$v['alipayname']."</div></div>";
        $table[] =  $v['updateline']?dgmdate($v['updateline']):'--';
        $table[] =  $v['dateline']?dgmdate($v['dateline']):'--';
        $table[] =  $shstr;
        showtablerow('',array(), $table);
    }
}
$multipage = '';
$multipage = multi ( $allcount, $maxp, $page, $_G ['siteurl'].$pageurl );
if ($multipage)
    echo '<tr class="hover"><td colspan="9">' . $multipage . '</td></tr>';

showsubmit ( 'forumset', 'submit', 'del' );
showtablefooter();
showformfooter();


echo <<<EOT
	<script src="source/plugin/tb_pay/static/js/jquery-3.3.1.min.js"></script>

	<script src="source/plugin/tb_pay/static/layer/layer.js"></script>
    <link rel="stylesheet" href="source/plugin/tb_pay/static/layui/css/layui.css">
	<script>jQuery.noConflict();</script>
    <script>

    function reviewScan(reviewId, decision){
        var reason = jQuery('#scan_reason_' + reviewId).val() || '';
        if(decision === 2 && !reason){
            alert('驳回时必须填写原因');
            return;
        }
        var confirmText = decision === 1 ? '确认审核通过并立即向用户发放对应服务？' : '确认驳回该支付凭证？';
        if(!window.confirm(confirmText)) return;
        jQuery.ajax({
            type: 'post',
            url: 'plugin.php?id=tb_pay:admin_ajax',
            dataType: 'json',
            data: {ac:'scan_review', review_id:reviewId, decision:decision, reason:reason, formhash:'{FORMHASH}'},
            success: function(data){
                alert(data.msg);
                if(data.code === 200) window.location.reload();
            },
            error: function(){ alert('网络异常，请稍后重试'); }
        });
    }
    
    function gosh(jfid){
            layer.open({
            id:1,
            type: 1,
            title:'请审核',
            style: 'height:auto;',
            content: '<div style="width:300px;padding:10px"><div style="margin:20px 0"><label><input type="radio" name="shval" value="1" checked>&nbsp;通过</label>&nbsp; &nbsp; &nbsp;<label><input type="radio" name="shval" value="2">&nbsp;拒绝</label></div><div><input id="liyoustr" name="liyoustr" placeholder="拒绝理由" class="layui-input"></div></div>',
            btn:['确定','取消'],
            yes:function (index,layero) {
                //获取输入框里面的值
                var liyoustr = jQuery("#liyoustr").val();
                var shval = jQuery('input[name="shval"]:checked').val();
                if(shval == 2){
                    if(liyoustr == ""){
                        layer.msg("请输入拒绝理由");
                        return false;
                    }
                }
                //layer.close(index);
                // 在这里提交数据
                var formdata=new FormData();
                formdata.append('sstatus',shval);
                formdata.append('liyou',liyoustr);
                formdata.append('ac','sh');
                formdata.append('jfid', jfid);
                formdata.append('formhash', '{FORMHASH}');
                jQuery.ajax({
                    type: 'post',
                    url: 'plugin.php?id=tb_pay:admin_ajax',
                    data :  formdata,
                    processData : false,
                    contentType : false,
                    dataType: 'json',
                    success: function (data) {
                        if(data.code==200){
                            layer.msg("操作成功");
                            setTimeout(function(){window.location.reload()},2000)
                        }else{
                            layer.msg(data.msg);
                        }
                    }
                });
            },
            no:function (index,layero) {
                layer.close(index);
            }
        });
        }
    
</script>
EOT;
