<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

if (!$_G['uid']) {
    showmessage('not_loggedin', NULL, array(), array('login' => 1));
}
$curymd = dgmdate(time(), 'Y-m-d');
$mymoney = getUsermoney($_G['uid']);

$xigua_user = DB::fetch_first('SELECT curtxsxf,realname,alipay_account,bank_card FROM %t WHERE uid IN (%n)', array('xigua_hb_user', $_G['uid']), 'uid');

$xigua_hb_config = $_G['cache']['plugin']['xigua_hb'];

// 保留原提现档位参数，固定按每个原档位单位10条成功广告换算。
$withdrawLevelUnits = array(
    10 => 5,
    30 => 15,
    100 => 45,
    300 => 120,
    500 => 300,
    1000 => 600,
);
$adsPerUnit = 10;
$requiredAdViews = array();
foreach ($withdrawLevelUnits as $amount => $units) {
    $requiredAdViews[$amount] = $units * $adsPerUnit;
}

DB::query("CREATE TABLE IF NOT EXISTS " . DB::table('view_ad_user_stats') . " (
    `uid` int(11) NOT NULL,
    `completed_ads` int(11) unsigned NOT NULL DEFAULT '0',
    `withdraw_spent_ads` int(11) unsigned NOT NULL DEFAULT '0',
    `created_at` int(11) NOT NULL DEFAULT '0',
    `updated_at` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$adStats = DB::fetch_first('SELECT completed_ads,withdraw_spent_ads FROM %t WHERE uid=%d', array('view_ad_user_stats', $_G['uid']));
if (!$adStats) {
    // 仅首次初始化时汇总明细；以后每次完成广告直接原子累计，不再扫描广告记录表。
    $completedAds = intval(DB::result_first('SELECT COUNT(*) FROM %t WHERE uid=%d AND status=%s', array('view_ad_task_impression', $_G['uid'], 'completed')));
    $legacySpentAds = intval(DB::result_first('SELECT COALESCE(SUM(spent_days),0) FROM %t WHERE uid=%d', array('view_sign_cost', $_G['uid']))) * 10;
    DB::insert('view_ad_user_stats', array(
        'uid' => $_G['uid'],
        'completed_ads' => $completedAds,
        'withdraw_spent_ads' => $legacySpentAds,
        'created_at' => TIMESTAMP,
        'updated_at' => TIMESTAMP,
    ));
    $adStats = array('completed_ads' => $completedAds, 'withdraw_spent_ads' => $legacySpentAds);
}
$totalCompletedAds = intval($adStats['completed_ads']);
$spentWithdrawAds = intval($adStats['withdraw_spent_ads']);
$availableWithdrawAds = max(0, $totalCompletedAds - $spentWithdrawAds);

$txAvailability = array();
$defaultTxid = 0;
foreach ($txprice as $priceId => $priceInfo) {
    $isAffordable = floatval($priceInfo[0]) <= floatval($mymoney);
    $txAvailability[$priceId] = $isAffordable ? 1 : 0;
    if (!$defaultTxid && $isAffordable) $defaultTxid = intval($priceId);
}

$hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($_G['uid']);
if ($hhme['status'] == 1) {
    $showhhmename = $hhme['joininfo']['name'];
} else {
    $oldback = $hhme['oldback'];
    $oldjoin = unserialize($oldback);
    $oldjoin = unserialize($oldjoin['joininfo']);
    $showhhmename = $oldjoin['name'];
}

// 手续费初始化（前端展示）
$info = $xigua_user;
if ($info['curtxsxf']) {
    $curtxfee = $info['curtxsxf'];
} else {
    $curtxfee = $xigua_hb_config['txsxf'];
}
if ($showhhmename == '推广宝会员') {
    $curtxfee = $xigua_hb_config['txsxf'];
}



if ($submodac == "txsubmit") {
    $txid = intval($_GET['txid']);
    if (!$txprice[$txid]) {
        json_echo(-1, "提现项目不存在");
    }

    $processname = 'tb_tx_' . $_G['uid'];
    $txmoney = $txprice[$txid][0];
    $txmoneyInt = intval($txmoney);

    // 实名认证
    $isverify = C::t("#xiaomy_certification#xiaomy_certification")->fetch_first_field_data("id", "where rescodebdres=1 AND uid=" . $_G['uid']);
    if (!$isverify) {
        json_echo(-1, "请先完成实名认证再进行提现");
    }

    // 提现开放时间
    $withdrawStartDate = strtotime('2026-05-18 00:00:00');
    if (TIMESTAMP < $withdrawStartDate) {
        json_echo(-1, "签到奖励将于5月18日早上10点（明日）正式开放");
    }

   
     // 工作日提现时间
    $currentHour = intval(date('H', TIMESTAMP));
    $currentWeekday = intval(date('N', TIMESTAMP));
    if ($currentHour < 1 || $currentHour >= 18) {
        json_echo(-1, "提现仅限工作日周1~周5 早10:00~晚18:00");
    }
    if ($currentWeekday == 6 || $currentWeekday == 7) {
        json_echo(-1, "提现仅限工作日周1~周5");
    } 

    if ($txmoney > $mymoney) {
        json_echo(-1, "金额不足!");
    }

    // 每日提现一次限制（排除失败记录 ostatus=2）
    $cur_date = dgmdate(time(), 'Y-m-d');
    $cur_date_start = strtotime($cur_date . ' 00:00:00');
    $cur_date_end = strtotime($cur_date . ' 23:59:59');
    $cur_tx1 = DB::fetch_first("select id from " . DB::table('tb_cus_xiguahh_user_txlog') . " where uid=" . $_G['uid'] . " AND ostatus != 2 AND (dateline>={$cur_date_start} AND dateline<={$cur_date_end})");
    if ($cur_tx1) {
        json_echo(-1, "你每天总的只能在平台提现一次");
    }
   
    // 注意：原代码中 xigua_hb_tixian 表可能也需要排除失败，如果该表有 ostatus 字段可同样排除，
    // 若没有则不处理（保留原逻辑）

    // txid=32 每人只能提现1次，排除失败记录
    if ($txid == 31) {
        $count32 = DB::result_first("SELECT COUNT(*) FROM " . DB::table('tb_cus_xiguahh_user_txlog') . " WHERE txpid=32 AND uid=" . $_G['uid'] . " AND ostatus != 2");
        if ($count32 >= 1) {
            json_echo(-1, "该金额每人仅限提现1次，30元以上无任何限制");
        }
    }

    // ========== 成功观看广告次数提现限制（10元以上） ==========
    if ($txmoney >= 10) {
        if (!isset($requiredAdViews[$txmoneyInt])) {
            json_echo(-1, "该提现金额暂不可用");
        }
        $needAds = intval($requiredAdViews[$txmoneyInt]);
        $currentStats = DB::fetch_first('SELECT completed_ads,withdraw_spent_ads FROM %t WHERE uid=%d', array('view_ad_user_stats', $_G['uid']));
        $currentAvailableAds = max(0, intval($currentStats['completed_ads']) - intval($currentStats['withdraw_spent_ads']));

        if ($currentAvailableAds < $needAds) {
            $maxAchievable = 0;
            foreach ($requiredAdViews as $amt => $ads) {
                if ($currentAvailableAds >= $ads && $amt > $maxAchievable) {
                    $maxAchievable = $amt;
                }
            }
            if ($maxAchievable > 0) {
                json_echo(-1, "当前可用成功广告数为 {$currentAvailableAds} 条，可提现 {$maxAchievable} 元；提现 {$txmoneyInt} 元需要 {$needAds} 条");
            }
            json_echo(-1, "提现 {$txmoneyInt} 元需要累计成功观看 {$needAds} 条广告，当前可用 {$currentAvailableAds} 条");
        }
    }
       // 会员限制：提现3元无需会员，30元以上必须推广宝会员
        if ($txmoney >= 10 && $showhhmename != '推广宝会员') {
            json_echo(-1, "提现10元以上需开通推广宝会员");
        }

    // 1元体验提现次数限制，排除失败记录
    if ($txid == 31 && $showhhmename != '商合会') {
        $txcount = DB::result_first("select count(*) from " . DB::table('xigua_hb_tixian') . " where txpid=31 AND uid=" . $_G['uid']);
        $txcount_new = DB::result_first("select count(*) from " . DB::table('tb_cus_xiguahh_user_txlog') . " where txpid=31 AND uid=" . $_G['uid'] . " AND ostatus != 2");
        $cur_sum_tx_count = $txcount + $txcount_new;
        if ($cur_sum_tx_count >= $tb_cus_xiguahh['novipcount']) {
            json_echo(-1, "该金额每人仅限提现3次");
        }
    }

    if (!discuz_process::islocked($processname, 5)) {
        // 手续费
        $txfee_rate = ($showhhmename == '推广宝会员') ? 7 : 50;
        $zhmoney = $txmoney - ($txmoney * ($txfee_rate / 100));
        $zhmoney = round($zhmoney, 2);
        if ($zhmoney <= 0) {
            json_echo(-1, "提现金额不足");
        }

        // 记录提现日志
        $txlog = array(
            'uid' => $_G['uid'],
            'username' => $_G['username'],
            'ostatus' => 0,
            'datelinestr' => $curymd,
            'money' => $zhmoney,
            'dateline' => time(),
            'txpid' => $txid,
        );
        C::t('#tb_cus_xiguahh#tb_cus_xiguahh_user_txlog')->insert($txlog);
        $logid = DB::insert_id();

        // 扣减余额
        set_reward_newqianbao_common($_G['uid'], $txmoney, "提现金额", 2, 3);

        // 原子消耗提现所需广告次数，避免并发重复使用同一批观看记录。
        $spentAdsForWithdrawal = 0;
        if ($txmoney >= 10 && isset($requiredAdViews[$txmoneyInt])) {
            $spentAdsForWithdrawal = intval($requiredAdViews[$txmoneyInt]);
            DB::query(
                'UPDATE %t SET withdraw_spent_ads=withdraw_spent_ads+%d,updated_at=%d WHERE uid=%d AND completed_ads-withdraw_spent_ads>=%d',
                array('view_ad_user_stats', $spentAdsForWithdrawal, TIMESTAMP, $_G['uid'], $spentAdsForWithdrawal)
            );
            if (DB::affected_rows() !== 1) {
                set_reward_newqianbao_common($_G['uid'], $txmoney, "提现资格不足退回", 1, 3);
                DB::update('tb_cus_xiguahh_user_txlog', array('ostatus' => 2, 'liyou' => '成功广告次数不足'), "id='$logid'");
                json_echo(-1, "成功观看广告次数不足，请刷新后重试");
            }
        }

        // 秒到账逻辑（3元以内）
        if ($zhmoney <= 3) {
            if (empty($xigua_user['alipay_account'])) {
                // 失败：退回余额、标记日志为失败、删除消耗记录
                set_reward_newqianbao_common($_G['uid'], $txmoney, "自动打款失败-无支付宝账号，退回", 1, 3);
                DB::update('tb_cus_xiguahh_user_txlog', array('ostatus' => 2, 'liyou' => '支付宝账号未填写'), "id='$logid'");
                if ($spentAdsForWithdrawal) {
                    DB::query('UPDATE %t SET withdraw_spent_ads=GREATEST(0,withdraw_spent_ads-%d),updated_at=%d WHERE uid=%d', array('view_ad_user_stats', $spentAdsForWithdrawal, TIMESTAMP, $_G['uid']));
                }
                json_echo(-1, "提现失败：未绑定支付宝账号，无法自动打款");
            }

            $dkres = alipay_dirc_alipay($_G['uid'], $zhmoney);
            if ($dkres == 'success') {
                DB::update('tb_cus_xiguahh_user_txlog', array('ostatus' => 1, 'liyou' => '秒到账'), "id='$logid'");
                json_echo(200, "提现成功，已秒到账");
            } else {
                // 打款失败：退回余额、标记日志为失败、删除消耗记录
                set_reward_newqianbao_common($_G['uid'], $txmoney, "自动打款失败退回：" . $dkres, 1, 3);
                DB::update('tb_cus_xiguahh_user_txlog', array('ostatus' => 2, 'liyou' => '自动打款失败：' . $dkres), "id='$logid'");
                if ($spentAdsForWithdrawal) {
                    DB::query('UPDATE %t SET withdraw_spent_ads=GREATEST(0,withdraw_spent_ads-%d),updated_at=%d WHERE uid=%d', array('view_ad_user_stats', $spentAdsForWithdrawal, TIMESTAMP, $_G['uid']));
                }
                json_echo(-1, "自动打款失败，请检查支付宝账号是否正确，或请联系客服：" . $dkres);
            }
        }

        json_echo(200, "提现申请成功，待审核");
    } else {
        json_echo(-1, "操作频繁，请稍后再试");
    }

} elseif ($submodac == "getdata") {
    // 明细查询（原样保留）
    $wheres = 'where uid=' . $_G['uid'];
    $wheres .= " ORDER BY dateline desc";
    $startlimit = $_GET['start'];
    $ppp = $startlimit + intval($_GET['pagesize']);
    $total = C::t('#tb_cus_xiguahh#tb_cus_xiguahh_user_txlog')->count_all($wheres);
    $listdata = C::t('#tb_cus_xiguahh#tb_cus_xiguahh_user_txlog')->fetch_page_data($startlimit, $ppp, $wheres);
    for ($i = 0; $i < count($listdata); $i++) {
        $listdata[$i]['dateline'] = dgmdate($listdata[$i]['dateline'], 'u');
        if ($listdata[$i]['ostatus'] == 1 || $listdata[$i]['ostatus'] == 3) {
            $listdata[$i]['ostatusstr'] = "恭喜！已到账支付宝";
        } elseif ($listdata[$i]['ostatus'] == 2) {
            $listdata[$i]['ostatusstr'] = "提现失败,原因:" . $listdata[$i]['liyou'];
        } else {
            $listdata[$i]['ostatusstr'] = "审核中，24小时内到账";
        }
        $listdata[$i] = gbktouft8_arr($listdata[$i]);
    }
    $res = array('total' => $total, 'code' => 200, 'list' => $listdata);
    $res = json_encode($res);
    echo $res;
    exit;
} else {
    include template('tb_cus_xiguahh:tx');
}
