<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/tb_credit/credit.core.php';
require_once DISCUZ_ROOT . './source/plugin/tb_cus_base/common.php';
require_once DISCUZ_ROOT . './source/plugin/view/function.core.php';
require_once DISCUZ_ROOT . './source/plugin/view/config.php';

$uid = $_G['uid'];
$username = addslashes($_G['username']);
if (!$uid) showmessage('未登录', '', array(), array('login' => 1));

// ---------- 辅助函数 ----------
function _sign_ensure_account($uid, $username) {
    $user = DB::fetch_first("SELECT uid, money FROM %t WHERE uid=%d", array('tb_cus_xiguahh_user', $uid));
    if (!$user) {
        DB::insert('tb_cus_xiguahh_user', array('uid' => $uid, 'username' => $username, 'money' => 0));
        return array('uid' => $uid, 'username' => $username, 'money' => 0);
    }
    return $user;
}

function _sign_is_vip($uid) {
    if (!$uid) return false;
    $member = DB::fetch_first("SELECT * FROM " . DB::table('xigua_hh_member') . " WHERE uid = " . intval($uid));
    if (!$member) return false;
    $is_qm = false;
    if ($member['status'] == 1) {
        $joininfo = @unserialize($member['joininfo']);
        if (is_array($joininfo) && $joininfo['name'] == '签米会员') $is_qm = true;
    } else {
        $oldback = @unserialize($member['oldback']);
        if (is_array($oldback) && $oldback['name'] == '签米会员') $is_qm = true;
    }
    if (!$is_qm) return false;
    $selfPaid = DB::fetch_first("SELECT orderid FROM " . DB::table('tb_member_order') . " WHERE uid = " . intval($uid) . " AND paystatus = 1 LIMIT 1");
    return !empty($selfPaid);
}

function _sign_today_viewed($uid) {
    $today = dgmdate(TIMESTAMP, 'Y-m-d');
    $count = DB::result_first("SELECT COUNT(DISTINCT pubid) FROM %t WHERE uid=%d AND datelinestr=%s",
        array('view_user_view_log', $uid, $today));
    return $count >= 6;
}
function _sign_today_signed($uid) {
    return DB::result_first("SELECT id FROM %t WHERE uid=%d AND sign_date=%s",
        array('view_sign_log', $uid, date('Y-m-d')));
}
function _sign_total_days($uid) {
    return DB::result_first("SELECT COUNT(*) FROM %t WHERE uid=%d", array('view_sign_log', $uid));
}
function _sign_add_money($uid, $amount) {
    $amount = floatval($amount);
    if ($amount <= 0) return false;
    return DB::query("UPDATE " . DB::table('tb_cus_xiguahh_user') . " SET money = money + {$amount} WHERE uid = " . intval($uid));
}

// 发放推广奖励到 xigua_hb_user（自动创建账户）
function _sign_add_xigua_money($uid, $amount, $note) {
    $amount = floatval($amount);
    if ($amount <= 0) return false;
    $table = DB::table('xigua_hb_user');
    $exists = DB::result_first("SELECT uid FROM {$table} WHERE uid = " . intval($uid));
    if (!$exists) {
        DB::insert('xigua_hb_user', array('uid' => $uid, 'money' => 0));
    }
    DB::query("UPDATE {$table} SET money = money + {$amount} WHERE uid = " . intval($uid));
    DB::insert('xigua_hb_moneylog', array(
        'uid' => $uid,
        'crts' => TIMESTAMP,
        'size' => $amount,
        'link' => 'plugin.php?id=view&modac=sign',
        'note' => $note,
    ));
    return true;
}

// 推广等级计算与缓存 (每小时更新)
function _sign_get_promo_level($uid) {
    $cache = DB::fetch_first("SELECT * FROM %t WHERE uid=%d", array('view_promo_level', $uid));
    $now = time();
    if ($cache && ($now - $cache['last_calc_time'] < 3600)) {
        return $cache;
    }

    $levels = array(
        1 => array('direct_req' => 0,   'indirect_req' => 0,   'direct_pct' => 5,   'indirect_pct' => 0.8),
        2 => array('direct_req' => 10,  'indirect_req' => 20,  'direct_pct' => 8,   'indirect_pct' => 1),
        3 => array('direct_req' => 30,  'indirect_req' => 50,  'direct_pct' => 11,  'indirect_pct' => 1.2),
        4 => array('direct_req' => 50,  'indirect_req' => 120, 'direct_pct' => 14,  'indirect_pct' => 1.4),
        5 => array('direct_req' => 100, 'indirect_req' => 240, 'direct_pct' => 17,  'indirect_pct' => 1.6),
        6 => array('direct_req' => 200, 'indirect_req' => 500, 'direct_pct' => 20,  'indirect_pct' => 1.8),
        7 => array('direct_req' => 300, 'indirect_req' => 999, 'direct_pct' => 23,  'indirect_pct' => 2),
    );

    // 修改为12小时内签到视为有效
    $validTime = time() - 24 * 3600;

    $direct = DB::result_first("SELECT COUNT(DISTINCT i.fansuid) FROM %t i
        INNER JOIN %t c ON i.fansuid = c.uid AND c.rescodebdres = 1
        WHERE i.uid = %d AND EXISTS (
            SELECT 1 FROM %t s WHERE s.uid = i.fansuid AND s.dateline >= %d
        )", array('xigua_hh_invite', 'xiaomy_certification', $uid, 'view_sign_log', $validTime));

    $indirect = DB::result_first("SELECT COUNT(DISTINCT i2.fansuid) FROM %t i1
        INNER JOIN %t i2 ON i1.fansuid = i2.uid
        INNER JOIN %t c ON i2.fansuid = c.uid AND c.rescodebdres = 1
        WHERE i1.uid = %d AND EXISTS (
            SELECT 1 FROM %t s WHERE s.uid = i2.fansuid AND s.dateline >= %d
        )", array('xigua_hh_invite', 'xigua_hh_invite', 'xiaomy_certification', $uid, 'view_sign_log', $validTime));

    $current = 1;
    $direct_pct = 5;
    $indirect_pct = 0.8;
    for ($l = 7; $l >= 1; $l--) {
        if ($direct >= $levels[$l]['direct_req'] && $indirect >= $levels[$l]['indirect_req']) {
            $current = $l;
            $direct_pct = $levels[$l]['direct_pct'];
            $indirect_pct = $levels[$l]['indirect_pct'];
            break;
        }
    }

    $data = array(
        'uid' => $uid,
        'level' => $current,
        'direct_count' => intval($direct),
        'indirect_count' => intval($indirect),
        'last_calc_time' => $now,
        'direct_reward_percent' => $direct_pct,
        'indirect_reward_percent' => $indirect_pct,
    );

    if ($cache) {
        DB::update('view_promo_level', $data, "uid='{$uid}'");
    } else {
        DB::insert('view_promo_level', $data);
    }

    return $data;
}

function _sign_promo_give($fromUid, $fromUsername, $reward) {
    $fromReal = DB::result_first("SELECT rescodebdres FROM %t WHERE uid=%d", array('xiaomy_certification', $fromUid));
    if ($fromReal != 1) return;

    $isVipFrom = _sign_is_vip($fromUid);
    
    $invite = DB::fetch_first("SELECT uid FROM " . DB::table('xigua_hh_invite') . " WHERE fansuid = " . intval($fromUid));
    if (!$invite) return;
    
    $firstUid = $invite['uid'];
    $firstLevel = _sign_get_promo_level($firstUid);
    $money = round($reward * $firstLevel['direct_reward_percent'] / 100, 2);

    // 直推奖励发放逻辑（非会员限制3次）
    $shouldGiveDirect = true;
    if ($money > 0) {
        if (!$isVipFrom) {
            $giveCount = DB::result_first("SELECT COUNT(*) FROM %t WHERE from_uid=%d AND uid=%d",
                array('view_sign_promo_log', $fromUid, $firstUid));
            if ($giveCount >= 2) {
                $shouldGiveDirect = false;
            }
        }
        if ($shouldGiveDirect) {
            $vipTag = $isVipFrom ? 'VIP' : '';
            _sign_add_xigua_money($firstUid, $money, "直推好友签到{$vipTag}奖励");
            DB::insert('view_sign_promo_log', array(
                'uid' => $firstUid, 'from_uid' => $fromUid, 'level' => 1,
                'reward_money' => $money, 'sign_date' => date('Y-m-d'), 'dateline' => TIMESTAMP,
                'note' => $isVipFrom ? '直推VIP加成' : '直推奖励'
            ));
        }
    }

    $secInvite = DB::fetch_first("SELECT uid FROM " . DB::table('xigua_hh_invite') . " WHERE fansuid = " . intval($firstUid));
    if ($secInvite) {
        $secUid = $secInvite['uid'];
        $secLevel = _sign_get_promo_level($secUid);
        $money = round($reward * $secLevel['indirect_reward_percent'] / 100, 2);
        $shouldGiveIndirect = true;
        if ($money > 0) {
            if (!$isVipFrom) {
                $giveCount = DB::result_first("SELECT COUNT(*) FROM %t WHERE from_uid=%d AND uid=%d",
                    array('view_sign_promo_log', $fromUid, $secUid));
                if ($giveCount >= 2) {
                    $shouldGiveIndirect = false;
                }
            }
            if ($shouldGiveIndirect) {
                $vipTag = $isVipFrom ? 'VIP' : '';
                _sign_add_xigua_money($secUid, $money, "间推好友签到{$vipTag}奖励");
                DB::insert('view_sign_promo_log', array(
                    'uid' => $secUid, 'from_uid' => $fromUid, 'level' => 2,
                    'reward_money' => $money, 'sign_date' => date('Y-m-d'), 'dateline' => TIMESTAMP,
                    'note' => $isVipFrom ? '间推VIP加成' : '间推奖励'
                ));
            }
        }
    }
}

// ========== 活动有效拉新统计（3小时缓存） ==========
function _get_activity_valid_direct_count($uid) {
    $cache = DB::fetch_first("SELECT valid_count, last_calc_time FROM %t WHERE uid=%d", array('view_invite_activity_cache', $uid));
    $now = time();
    if ($cache && ($now - $cache['last_calc_time'] < 10800)) {
        return intval($cache['valid_count']);
    }

    $start = '2026-05-17';
    $end   = '2026-06-17';
    $invites = DB::fetch_all("SELECT fansuid FROM %t WHERE uid=%d", array('xigua_hh_invite', $uid));
    $valid = 0;
    foreach ($invites as $inv) {
        $fansuid = $inv['fansuid'];
        $real = DB::result_first("SELECT rescodebdres FROM %t WHERE uid=%d", array('xiaomy_certification', $fansuid));
        if ($real != 1) continue;
        $days = DB::fetch_all("SELECT sign_date FROM %t WHERE uid=%d AND sign_date >= %s AND sign_date <= %s ORDER BY sign_date ASC",
            array('view_sign_log', $fansuid, $start, $end));
        if (count($days) < 3) continue;
        $has = false;
        $len = count($days);
        for ($i = 0; $i <= $len - 3; $i++) {
            $d1 = $days[$i]['sign_date'];
            $d2 = $days[$i+1]['sign_date'];
            $d3 = $days[$i+2]['sign_date'];
            $ts1 = strtotime($d1);
            $ts2 = strtotime($d2);
            $ts3 = strtotime($d3);
            if (($ts2 - $ts1) == 86400 && ($ts3 - $ts2) == 86400) {
                $has = true;
                break;
            }
        }
        if ($has) $valid++;
    }

    if ($cache) {
        DB::update('view_invite_activity_cache', array('valid_count' => $valid, 'last_calc_time' => $now), "uid='{$uid}'");
    } else {
        DB::insert('view_invite_activity_cache', array('uid' => $uid, 'valid_count' => $valid, 'last_calc_time' => $now));
    }

    return $valid;
}

// ========== 奖励统计缓存（2小时） ==========
function _get_reward_stats($uid) {
    $cache = DB::fetch_first("SELECT * FROM %t WHERE uid=%d", array('view_reward_stats_cache', $uid));
    $now = time();
    if ($cache && ($now - $cache['last_calc_time'] < 7200)) {
        return $cache;
    }

    $totalSign = DB::result_first("SELECT COALESCE(SUM(reward_money), 0) FROM %t WHERE uid=%d", array('view_sign_reward_detail', $uid));
    $totalPromo = DB::result_first("SELECT COALESCE(SUM(reward_money), 0) FROM %t WHERE uid=%d", array('view_sign_promo_log', $uid));

    $data = array(
        'uid' => $uid,
        'total_sign' => $totalSign,
        'total_promo' => $totalPromo,
        'last_calc_time' => $now
    );

    if ($cache) {
        DB::update('view_reward_stats_cache', $data, "uid='{$uid}'");
    } else {
        DB::insert('view_reward_stats_cache', $data);
    }

    return $data;
}

// 建表（增加奖励统计缓存表）
$tables = array('view_sign_log', 'view_sign_reward_detail', 'view_sign_promo_log', 'view_promo_level', 'view_invite_activity_cache', 'view_invite_activity_reward', 'view_reward_stats_cache');
foreach ($tables as $t) {
    $tableName = DB::table($t);
    if (!DB::fetch_first("SHOW TABLES LIKE %s", array($tableName))) {
        $sqls = array(
            'view_sign_log' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `sign_date` date NOT NULL, `is_vip` tinyint(1) NOT NULL DEFAULT '0', `reward_money` decimal(10,2) NOT NULL DEFAULT '0.00', `dateline` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `uid_sign_date` (`uid`,`sign_date`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_sign_reward_detail' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `sign_log_id` int(11) NOT NULL, `reward_money` decimal(10,2) NOT NULL, `dateline` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `uid` (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_sign_promo_log' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `from_uid` int(11) NOT NULL, `level` tinyint(1) NOT NULL, `reward_money` decimal(10,2) NOT NULL, `sign_date` date NOT NULL, `dateline` int(11) NOT NULL, `note` varchar(255) NOT NULL DEFAULT '', PRIMARY KEY (`id`), KEY `uid` (`uid`), KEY `from_uid` (`from_uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_promo_level' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`uid` int(11) NOT NULL, `level` tinyint(2) NOT NULL DEFAULT '1', `direct_count` int(11) NOT NULL DEFAULT '0', `indirect_count` int(11) NOT NULL DEFAULT '0', `last_calc_time` int(11) NOT NULL DEFAULT '0', `direct_reward_percent` decimal(5,1) NOT NULL DEFAULT '5.0', `indirect_reward_percent` decimal(5,1) NOT NULL DEFAULT '0.8', PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_invite_activity_cache' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`uid` int(11) NOT NULL, `valid_count` int(11) NOT NULL DEFAULT '0', `last_calc_time` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_invite_activity_reward' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, `uid` int(11) NOT NULL, `reward_count` int(11) NOT NULL COMMENT '直推人数阶梯', `reward_money` decimal(10,2) NOT NULL, `dateline` int(11) NOT NULL, PRIMARY KEY (`id`), KEY `uid` (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            'view_reward_stats_cache' => "CREATE TABLE IF NOT EXISTS `{$tableName}` (`uid` int(11) NOT NULL, `total_sign` decimal(10,2) NOT NULL DEFAULT '0.00', `total_promo` decimal(10,2) NOT NULL DEFAULT '0.00', `last_calc_time` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        );
        if (isset($sqls[$t])) DB::query($sqls[$t]);
    }
}

$promoTable = DB::table('view_sign_promo_log');
if (DB::fetch_first("SHOW TABLES LIKE %s", array($promoTable))) {
    $columns = DB::fetch_all("SHOW COLUMNS FROM `{$promoTable}` LIKE 'note'");
    if (empty($columns)) {
        DB::query("ALTER TABLE `{$promoTable}` ADD COLUMN `note` varchar(255) NOT NULL DEFAULT ''");
    }
}

$userAccount = _sign_ensure_account($uid, $username);

// AJAX 接口
$submodac = isset($_GET['submodac']) ? daddslashes($_GET['submodac']) : '';
if ($submodac) {
    header('Content-Type: application/json; charset=utf-8');
    if ($submodac == 'status') {
        echo json_encode(array(
            'signed' => _sign_today_signed($uid) ? true : false,
            'total_days' => (int)_sign_total_days($uid),
            'tomorrow_reward' => _sign_is_vip($uid) ? 25.00 : 12.00,
            'is_vip' => _sign_is_vip($uid),
            'has_views' => _sign_today_viewed($uid),
            'balance' => number_format($userAccount['money'], 2),
        ));
        exit;
    }
    if ($submodac == 'sign') {
        $process = "sign_{$uid}_" . date('Ymd');
        if (discuz_process::islocked($process, 3)) {
            echo json_encode(array('code' => -1, 'msg' => '操作太频繁，请稍后再试')); exit;
        }
        if (_sign_today_signed($uid)) {
            echo json_encode(array('code' => -1, 'msg' => '今日已完成签到')); exit;
        }
        if (!_sign_today_viewed($uid)) {
            echo json_encode(array('code' => -2, 'msg' => '浏览不足6个项目', 'redirect' => 'plugin.php?id=xigua_hb')); exit;
        }
        $isVip = _sign_is_vip($uid);
        $reward = $isVip ? 25.00 : 12.00;
        $update = _sign_add_money($uid, $reward);
        if (!$update) {
            discuz_process::unlock($process);
            echo json_encode(array('code' => -1, 'msg' => '余额更新失败')); exit;
        }
        $signLogId = DB::insert('view_sign_log', array(
            'uid' => $uid, 'sign_date' => date('Y-m-d'), 'is_vip' => $isVip ? 1 : 0,
            'reward_money' => $reward, 'dateline' => TIMESTAMP,
        ), true);
        DB::insert('view_sign_reward_detail', array(
            'uid' => $uid, 'sign_log_id' => $signLogId, 'reward_money' => $reward, 'dateline' => TIMESTAMP,
        ));
        _sign_promo_give($uid, $username, $reward);
        discuz_process::unlock($process);
        $newBalance = number_format(DB::result_first("SELECT money FROM %t WHERE uid=%d", array('tb_cus_xiguahh_user', $uid)), 2);
        echo json_encode(array('code' => 0, 'msg' => "签到成功，获得{$reward}元", 'reward' => $reward, 'new_balance' => $newBalance));
        exit;
    }
    if ($submodac == 'records') {
        $type = $_GET['type'] == 'promo' ? 'promo' : 'sign';
        $page = max(1, intval($_GET['page']));
        $perpage = 10;
        $start = ($page - 1) * $perpage;
        if ($type == 'sign') {
            $list = DB::fetch_all("SELECT reward_money, dateline FROM %t WHERE uid=%d ORDER BY id DESC LIMIT %d,%d",
                array('view_sign_reward_detail', $uid, $start, $perpage));
            $records = array_map(function($r) { return array('money' => $r['reward_money'], 'time' => dgmdate($r['dateline'], 'Y-m-d H:i')); }, $list);
        } else {
            $list = DB::fetch_all("SELECT level, reward_money, from_uid, dateline, note FROM %t WHERE uid=%d ORDER BY id DESC LIMIT %d,%d",
                array('view_sign_promo_log', $uid, $start, $perpage));
            $records = array();
            foreach ($list as $r) {
                $fromUser = DB::result_first("SELECT username FROM " . DB::table('common_member') . " WHERE uid = " . intval($r['from_uid']));
                $records[] = array(
                    'level' => $r['level'] == 1 ? '直推' : '间推',
                    'from' => $fromUser,
                    'money' => $r['reward_money'],
                    'time' => dgmdate($r['dateline'], 'Y-m-d H:i'),
                    'note' => $r['note']
                );
            }
        }
        echo json_encode(array('code' => 0, 'data' => $records));
        exit;
    }
    // 新增：奖励统计接口
    if ($submodac == 'get_reward_stats') {
        $stats = _get_reward_stats($uid);
        echo json_encode(array(
            'code' => 0,
            'data' => array(
                'total_sign' => number_format($stats['total_sign'], 2),
                'total_promo' => number_format($stats['total_promo'], 2),
                'update_time' => date('m-d H:i', $stats['last_calc_time'])
            )
        ));
        exit;
    }
    if ($submodac == 'promo_info') {
        $levelData = _sign_get_promo_level($uid);
        $levels = array(
        1 => array('direct_req' => 0,   'indirect_req' => 0,   'direct_pct' => 5,   'indirect_pct' => 0.8),
        2 => array('direct_req' => 10,  'indirect_req' => 20,  'direct_pct' => 8,   'indirect_pct' => 1),
        3 => array('direct_req' => 30,  'indirect_req' => 50,  'direct_pct' => 11,  'indirect_pct' => 1.2),
        4 => array('direct_req' => 50,  'indirect_req' => 120, 'direct_pct' => 14,  'indirect_pct' => 1.4),
        5 => array('direct_req' => 100, 'indirect_req' => 240, 'direct_pct' => 17,  'indirect_pct' => 1.6),
        6 => array('direct_req' => 200, 'indirect_req' => 500, 'direct_pct' => 20,  'indirect_pct' => 1.8),
        7 => array('direct_req' => 300, 'indirect_req' => 999, 'direct_pct' => 23,  'indirect_pct' => 2),
        );
        $current = $levelData['level'];
        $next = $current < 7 ? $current + 1 : null;
        $needDirect = $next ? max(0, $levels[$next]['direct_req'] - $levelData['direct_count']) : 0;
        $needIndirect = $next ? max(0, $levels[$next]['indirect_req'] - $levelData['indirect_count']) : 0;

        echo json_encode(array(
            'code' => 0,
            'data' => array(
                'level' => $current,
                'direct_count' => $levelData['direct_count'],
                'indirect_count' => $levelData['indirect_count'],
                'direct_pct' => $levelData['direct_reward_percent'],
                'indirect_pct' => $levelData['indirect_reward_percent'],
                'next_level' => $next,
                'need_direct' => $needDirect,
                'need_indirect' => $needIndirect,
                'levels' => $levels
            )
        ));
        exit;
    }
    // ========== 活动接口 ==========
    if ($submodac == 'invite_activity_info') {
        $activityStart = strtotime('2026-05-17');
        $activityEnd = strtotime('2026-06-17 23:59:59');
        $now = TIMESTAMP;
        $remaining = $activityEnd - $now;
        $remainingDays = $remaining > 0 ? ceil($remaining / 86400) : 0;

        $validCount = _get_activity_valid_direct_count($uid);
        $rewards = [
            ['count' => 10, 'money' => 10],
            ['count' => 30, 'money' => 28],
            ['count' => 50, 'money' => 38],
            ['count' => 100, 'money' => 58],
            ['count' => 150, 'money' => 88],
            ['count' => 200, 'money' => 138],
            ['count' => 300, 'money' => 198],
            ['count' => 500, 'money' => 330],
        ];

        $receivedList = DB::fetch_all("SELECT reward_count FROM %t WHERE uid=%d", array('view_invite_activity_reward', $uid));
        $receivedMap = [];
        foreach ($receivedList as $row) {
            $receivedMap[$row['reward_count']] = true;
        }

        $rewardList = [];
        foreach ($rewards as $r) {
            $canReceive = ($validCount >= $r['count'] && !isset($receivedMap[$r['count']]));
            $received = isset($receivedMap[$r['count']]);
            $rewardList[] = [
                'count' => $r['count'],
                'money' => $r['money'],
                'can_receive' => $canReceive,
                'received' => $received
            ];
        }

        echo json_encode([
            'code' => 0,
            'data' => [
                'valid_count' => $validCount,
                'remaining_days' => $remainingDays,
                'rewards' => $rewardList,
                'activity_start' => date('Y-m-d', $activityStart),
                'activity_end' => date('Y-m-d', $activityEnd)
            ]
        ]);
        exit;
    }
    if ($submodac == 'invite_activity_receive') {
        $count = intval($_GET['count']);
        $rewards = [
            10 => 10, 30 => 28, 50 => 38, 100 => 58,
            150 => 88, 200 => 138, 300 => 198, 500 => 330
        ];
        if (!isset($rewards[$count])) {
            echo json_encode(['code' => -1, 'msg' => '奖励不存在']); exit;
        }
        if (TIMESTAMP < strtotime('2026-05-17') || TIMESTAMP > strtotime('2026-06-17 23:59:59')) {
            echo json_encode(['code' => -1, 'msg' => '不在活动时间内']); exit;
        }
        $exists = DB::result_first("SELECT id FROM %t WHERE uid=%d AND reward_count=%d", array('view_invite_activity_reward', $uid, $count));
        if ($exists) {
            echo json_encode(['code' => -1, 'msg' => '该奖励已领取']); exit;
        }
        $validCount = _get_activity_valid_direct_count($uid);
        if ($validCount < $count) {
            echo json_encode(['code' => -1, 'msg' => '有效拉新人数不足']); exit;
        }

        $money = $rewards[$count];
        _sign_add_xigua_money($uid, $money, "拉新活动奖励：直推{$count}人奖励{$money}元");
        DB::insert('view_invite_activity_reward', [
            'uid' => $uid,
            'reward_count' => $count,
            'reward_money' => $money,
            'dateline' => TIMESTAMP
        ]);

        echo json_encode(['code' => 0, 'msg' => "恭喜！成功领取{$money}元奖励", 'money' => $money]);
        exit;
    }
    exit;
}

// 页面数据
$signed = _sign_today_signed($uid);
$isVip = _sign_is_vip($uid);
$totalDays = _sign_total_days($uid);
$tomorrowReward = number_format($isVip ? 25.00 : 12.00, 2);
$balance = number_format($userAccount['money'], 2);
$todayReward = $isVip ? '25.00' : '12.00';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>签米 · 签到中心</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --gold: #f0b90b;
            --gold-dark: #d4a017;
            --orange: #ff6933;
            --coral: #ff4d4d;
            --bg-start: #fff9f0;
            --bg-end: #fef3e2;
            --card-bg: rgba(255,255,255,0.85);
            --text: #333;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            background-attachment: fixed;
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        .bg-float {
            position: fixed; top: -10%; left: -20%;
            width: 140%; height: 140%;
            background: radial-gradient(circle at 60% 20%, rgba(255, 180, 50, 0.2), transparent 60%),
                        radial-gradient(circle at 40% 80%, rgba(255, 100, 50, 0.1), transparent 50%);
            z-index: 0;
            animation: bgPulse 10s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes bgPulse { 0%,100% { opacity: 0.8; transform: scale(1); } 50% { opacity: 1; transform: scale(1.05); } }
        .container { position: relative; z-index: 1; margin-top: 15px; padding: 0 18px 40px; }
        .nav {
            position: sticky; top: 0; z-index: 50;
            background: rgba(255,255,255,0.8); backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.7);
            padding: 45px 20px 10px 20px; text-align: center;
        }
        .nav-title {
            font-size: 22px; font-weight: 800;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            letter-spacing: 2px;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .nav-title i { font-size: 26px; background: linear-gradient(135deg, #ff8c00, #ff2d55); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .balance-card {
            background: var(--card-bg); backdrop-filter: blur(25px);
            border-radius: 36px; padding: 24px;
            box-shadow: 0 30px 50px rgba(255,150,30,0.15), inset 0 2px 0 rgba(255,255,255,0.9);
            border: 1px solid rgba(255,200,100,0.4);
            margin-bottom: 28px;
            display: flex; justify-content: space-between; align-items: center;
            position: relative; overflow: hidden;
        }
        .balance-card .card-ornament {
            position: absolute; top: -30px; right: -30px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(255,180,0,0.3), transparent);
            border-radius: 50%; pointer-events: none;
        }
        .balance-label { font-size: 15px; color: #b08968; display: flex; align-items: center; gap: 6px; }
        .balance-value {
            font-size: 36px; font-weight: 900;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            line-height: 1.2; margin-top: 4px;
        }
        .detail-btn {
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            border: none; color: #c2410c;
            padding: 12px 22px; border-radius: 60px;
            font-weight: 700; display: flex; align-items: center; gap: 6px;
            cursor: pointer; box-shadow: 0 4px 12px rgba(255,120,40,0.25);
            transition: all 0.3s; z-index: 1; font-size: 14px;
        }
        .detail-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(255,120,40,0.4); }
        .sign-zone { display: flex; flex-direction: column; align-items: center; margin: 30px 0 25px; }
        .sign-btn {
            width: 200px; height: 200px; border-radius: 50%;
            border: none;
            background: linear-gradient(145deg, #ff9a56, #ff6b35);
            box-shadow: 0 25px 50px rgba(255,100,40,0.45), 0 0 0 10px rgba(255,150,80,0.2);
            color: #fff; font-size: 20px; font-weight: 800;
            position: relative; cursor: pointer;
            transition: all 0.2s; outline: none;
            -webkit-tap-highlight-color: transparent;
            overflow: hidden; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .sign-btn::after {
            content: ''; position: absolute; top: -30%; left: -30%;
            width: 160%; height: 160%;
            background: radial-gradient(circle, rgba(255,255,255,0.6) 0%, transparent 60%);
            opacity: 0.4; animation: shimmer 3s infinite;
        }
        @keyframes shimmer { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .sign-btn .spark {
            position: absolute; width: 8px; height: 8px;
            background: #fff; border-radius: 50%;
            animation: sparkle 2s linear infinite;
            box-shadow: 0 0 8px #fff;
        }
        .spark:nth-child(1) { top: 15%; left: 20%; animation-delay: 0s; }
        .spark:nth-child(2) { top: 70%; left: 80%; animation-delay: 0.6s; }
        .spark:nth-child(3) { top: 40%; left: 70%; animation-delay: 1.2s; }
        @keyframes sparkle { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.2); opacity: 1; } 100% { transform: scale(0); opacity: 0; } }
        .sign-btn.vip {
            background: linear-gradient(145deg, #fbe48b, #f0b90b);
            box-shadow: 0 25px 50px rgba(240,185,11,0.5), 0 0 0 10px rgba(255,215,0,0.25);
            color: #4a3000; border: 2px solid #fff3c0;
        }
        .sign-btn:active { transform: scale(0.94); }
        .sign-btn.signed {
            background: linear-gradient(145deg, #8fd19e, #4caf50);
            box-shadow: 0 15px 30px rgba(76,175,80,0.5), inset 0 0 20px rgba(255,255,255,0.6);
            color: #fff; cursor: default;
        }
        .sign-btn.signed::after, .sign-btn.signed .spark { display: none; }
        .sign-btn .btn-icon { font-size: 40px; margin-bottom: 4px; position: relative; z-index: 2; }
        .sign-btn .btn-text { position: relative; z-index: 2; }
        .sign-btn .btn-sub { font-size: 13px; opacity: 0.9; position: relative; z-index: 2; }
        .sign-status {
            text-align: center; font-size: 15px; color: #8b5e3c;
            background: rgba(255,255,255,0.7); padding: 10px 20px;
            border-radius: 30px; backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .rule-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 25px 0; }
        .rule-card {
            background: var(--card-bg); backdrop-filter: blur(15px);
            border-radius: 28px; padding: 24px 16px; text-align: center;
            box-shadow: 0 20px 35px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.9);
            border: 1px solid rgba(255,200,120,0.4);
            cursor: pointer; transition: all 0.3s;
            position: relative;
        }
        .rule-card:active { transform: scale(0.96); }
        .rule-card i {
            font-size: 38px;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
        }
        .rule-card .label { font-weight: 800; font-size: 16px; color: #4a3000; }
        .rule-card .desc { font-size: 13px; color: #b08968; margin-top: 4px; }

        /* ========== 推广卡片特效 ========== */
        .rule-card.promo-highlight {
            border: 2px solid rgba(255,80,0,0.6);
            box-shadow: 0 0 25px rgba(255,100,30,0.4), inset 0 2px 0 #fff;
            animation: promoGlow 2s infinite alternate;
        }
        @keyframes promoGlow {
            0% { box-shadow: 0 0 20px rgba(255,100,30,0.4), inset 0 2px 0 #fff; }
            100% { box-shadow: 0 0 35px rgba(255,60,0,0.7), inset 0 2px 0 #fff; }
        }
        .promo-highlight::after {
            content: "超高收益";
            position: absolute;
            top: -8px;
            right: -10px;
            background: linear-gradient(135deg, #ff2d55, #ff7b00);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(255,50,0,0.5);
            animation: badgePulse 1.5s infinite;
            z-index: 2;
            white-space: nowrap;
        }
        @keyframes badgePulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .promo-highlight .label {
            color: #ff4d2d;
            font-size: 18px;
        }
        .promo-highlight .desc {
            color: #d35400;
            font-weight: 600;
        }

        /* ========== 活动卡片样式 ========== */
        .activity-card {
            background: linear-gradient(135deg, #ff6b35, #c81d25);
            border-radius: 24px;
            padding: 20px;
            margin: 20px 0;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(200, 30, 40, 0.4);
            position: relative;
            overflow: hidden;
            animation: activityGlow 2s ease-in-out infinite alternate;
        }
        @keyframes activityGlow {
            0% { box-shadow: 0 10px 30px rgba(200,30,40,0.4); }
            100% { box-shadow: 0 10px 40px rgba(255,100,40,0.7); }
        }
        .activity-card::after {
            content: "限时";
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffd700;
            color: #c81d25;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 12px;
            box-shadow: 0 0 10px rgba(255,215,0,0.6);
        }
        .activity-card .act-title {
            font-size: 17px;
            white-space: nowrap;
            font-weight: 800;
            margin-bottom: 6px;
        }
        .activity-card .act-desc {
            font-size: 13px;
            opacity: 0.9;
        }
        .activity-card .act-progress {
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
        }

        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4); z-index: 500;
            display: flex; align-items: center; justify-content: center;
            visibility: hidden; opacity: 0;
            transition: opacity 0.3s; backdrop-filter: blur(6px);
        }
        .modal-overlay.active { visibility: visible; opacity: 1; }
        .modal-box {
            background: #fffdf7; backdrop-filter: blur(25px);
            border-radius: 40px; width: 85%; max-height: 70vh;
            padding: 28px 22px;
            position: relative;
            box-shadow: 0 35px 70px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,180,0,0.3);
            transform: translateY(30px); transition: transform 0.3s;
            overflow-y: auto; display: flex; flex-direction: column;
        }
        .modal-overlay.active .modal-box { transform: translateY(0); }
        .modal-close {
            position: absolute; top: 16px; right: 16px;
            font-size: 26px; color: #cc9a6c; cursor: pointer; z-index: 2;
            background: rgba(0,0,0,0.03); border-radius: 50%;
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-title {
            font-size: 20px; font-weight: 800; text-align: center; margin-bottom: 20px;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .record-tabs { display: flex; margin-bottom: 20px; background: rgba(0,0,0,0.03); border-radius: 30px; padding: 4px; }
        .record-tab {
            flex: 1; text-align: center; padding: 10px 0; font-weight: 700;
            color: #b08968; border-radius: 30px; cursor: pointer; transition: 0.3s;
            font-size: 14px;
        }
        .record-tab.active {
            background: linear-gradient(135deg, #ff7b00, #e63946);
            color: #fff; box-shadow: 0 4px 12px rgba(255,100,40,0.3);
        }
        .record-item {
            padding: 14px 10px; border-bottom: 1px solid rgba(0,0,0,0.04);
            font-size: 14px;
        }
        .record-item .row { display: flex; justify-content: space-between; align-items: center; }
        .record-item .time { margin-top: 6px; font-size: 12px; color: #999; text-align: right; }
        .record-item .amount { font-weight: 800; color: #ff4d2d; }
        .no-more { text-align: center; padding: 15px; color: #b08968; font-size: 13px; }
        .rule-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 10px; }
        .rule-modal-card {
            background: #fff9f0; border-radius: 24px; padding: 20px 10px;
            text-align: center; box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border: 1px solid #ffe3b0; color: #4a3000;
        }
        .rule-modal-card i {
            font-size: 32px;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
        }
        .confirm-text { text-align: center; font-size: 16px; margin: 20px 0; color: #4a3000; line-height: 1.6; }
        .btn-row { display: flex; justify-content: center; gap: 16px; margin-top: 20px; }
        .btn { padding: 12px 30px; border-radius: 50px; font-weight: 700; border: none; cursor: pointer; transition: 0.3s; font-size: 15px; }
        .btn.primary { background: linear-gradient(135deg, #ff7b00, #e63946); color: #fff; box-shadow: 0 10px 25px rgba(255,50,0,0.3); }
        .btn.gold { background: linear-gradient(135deg, #f0b90b, #d4a017); color: #4a3000; box-shadow: 0 10px 25px rgba(240,185,11,0.4); }
        .promo-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .promo-table th { background: #fff3e0; padding: 8px; text-align: center; }
        .promo-table td { padding: 8px; text-align: center; border-bottom: 1px solid #eee; }
        .promo-level-current { background: #ffe0b2; font-weight: bold; }
        .promo-summary {
            background: linear-gradient(135deg, #fff9f0, #ffe8cc);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(255,150,30,0.1);
            border: 1px solid rgba(255,180,0,0.3);
        }
        .promo-summary .level-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            color: #fff;
            font-weight: 900;
            font-size: 18px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 12px;
        }
        .promo-summary .stats { display: flex; justify-content: space-around; margin: 16px 0; gap: 10px; }
        .promo-summary .stat-item {
            text-align: center; background: rgba(255,255,255,0.7);
            border-radius: 16px; padding: 10px 15px; flex: 1;
        }
        .promo-summary .stat-num { font-size: 24px; font-weight: 800; color: #ff4d2d; }
        .promo-summary .stat-label { font-size: 12px; color: #b08968; margin-top: 4px; }
        .promo-progress { margin-top: 12px; font-size: 13px; color: #777; }
        .promo-invite-btn {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 14px 0;
            text-align: center;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            color: #fff;
            border-radius: 60px;
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 20px;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(255,50,0,0.3);
            transition: all 0.3s;
        }
        .promo-invite-btn:active { transform: scale(0.97); }
        .promo-intro {
            background: #fff8e7;
            border-radius: 20px;
            padding: 18px;
            margin: 15px 0;
            font-size: 14px;
            line-height: 1.7;
            color: #5d4e37;
            border: 1px solid #fad7a1;
        }
        .promo-intro b { color: #ff4d2d; }
        .coin-rain { position: fixed; top: -50px; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1000; }
        .coin { position: absolute; font-size: 24px; animation: fall linear infinite; opacity: 0.9; }
        @keyframes fall { 0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; } 100% { transform: translateY(110vh) rotate(720deg); opacity: 0; } }

        button, .rule-card, .detail-btn, .record-tab, .modal-close, .btn { touch-action: manipulation; }

        .vip-card {
            background: linear-gradient(135deg, #fff5e6, #ffe0b2);
            border-radius: 24px; padding: 20px; margin-top: 20px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 15px 30px rgba(255,150,0,0.15);
            border: 1px solid rgba(255,180,0,0.5);
            cursor: pointer; transition: all 0.3s;
        }
        .vip-card:active { transform: scale(0.97); }
        .vip-card .vip-icon { font-size: 36px; color: #f0b90b; }
        .vip-card .vip-info { flex: 1; margin-left: 15px; }
        .vip-card .vip-title { font-weight: 800; font-size: 17px; color: #4a3000; }
        .vip-card .vip-desc { font-size: 13px; color: #b08968; margin-top: 2px; }
        .vip-card .vip-arrow { font-size: 22px; color: #f0b90b; }

        .group-card {
            background: linear-gradient(135deg, #f0f8ff, #e6f3ff);
            border-radius: 24px; padding: 20px; margin-top: 16px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 10px 20px rgba(100,149,237,0.1);
            border: 1px solid rgba(100,149,237,0.3);
            cursor: pointer; transition: all 0.3s;
        }
        .group-card:active { transform: scale(0.97); }
        .group-card .group-icon { font-size: 36px; color: #6495ed; }
        .group-card .group-info { flex: 1; margin-left: 15px; }
        .group-card .group-title { font-weight: 800; font-size: 17px; color: #1e3c72; }
        .group-card .group-desc { font-size: 13px; color: #5a7fa0; margin-top: 2px; }
        .group-card .group-arrow { font-size: 22px; color: #6495ed; }
        
        /* 底部导航样式 */
        .qmn-nav {
            --qmn-h: 65px;
            --qmn-bg: rgba(255, 255, 255, 0.8);
            --qmn-blur: 22px;
            --qmn-shadow: 0 -6px 28px rgba(255, 140, 30, 0.08), 0 -2px 8px rgba(0, 0, 0, 0.04);
            --qmn-border-color: rgba(255, 190, 80, 0.38);
            --qmn-active-color: #f0b90b;
            --qmn-active-glow: rgba(240, 185, 11, 0.45);
            --qmn-icon-size: 22px;
            --qmn-label-size: 10px;
            --qmn-inactive-color: #b8a08a;
            --qmn-transition: 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
            --qmn-safe-bottom: env(safe-area-inset-bottom, 8px);
            position: fixed;
            bottom: 0; left: 0; right: 0; z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-around;
            height: var(--qmn-h);
            padding-bottom: var(--qmn-safe-bottom);
            background: var(--qmn-bg);
            backdrop-filter: blur(var(--qmn-blur));
            -webkit-backdrop-filter: blur(var(--qmn-blur));
            box-shadow: var(--qmn-shadow);
            border-top: 1px solid var(--qmn-border-color);
            border-radius: 22px 22px 0 0;
            user-select: none;
            -webkit-user-select: none;
            -webkit-tap-highlight-color: transparent;
            transition: height 0.25s ease;
            max-width: 100%;
        }
        .qmn-nav::before {
            content: '';
            position: absolute;
            top: 0; left: 6%; width: 88%; height: 1.2px;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 180, 70, 0.15) 15%, rgba(255, 150, 40, 0.5) 35%, rgba(240, 185, 11, 0.55) 50%, rgba(255, 150, 40, 0.5) 65%, rgba(255, 180, 70, 0.15) 85%, transparent 100%);
            border-radius: 1px;
            pointer-events: none;
            z-index: 2;
            animation: qmn-shimmer 3.5s ease-in-out infinite;
        }
        @keyframes qmn-shimmer { 0%,100% { opacity: 0.55; } 40% { opacity: 1; } 70% { opacity: 0.65; } }
        .qmn-nav .qmn-item { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; flex: 1; min-width: 0; height: 100%; cursor: pointer; text-decoration: none; color: var(--qmn-inactive-color); transition: color var(--qmn-transition), transform var(--qmn-transition); -webkit-tap-highlight-color: transparent; outline: none; z-index: 3; }
        .qmn-nav .qmn-item:active { transform: scale(0.9); transition: transform 0.12s cubic-bezier(0.25, 0.1, 0.25, 1); }
        .qmn-nav .qmn-icon-wrap { position: relative; display: flex; align-items: center; justify-content: center; width: 36px; height: 28px; transition: transform var(--qmn-transition); z-index: 1; }
        .qmn-nav .qmn-icon-wrap i { font-size: var(--qmn-icon-size); line-height: 1; transition: all var(--qmn-transition); display: inline-block; }
        .qmn-nav .qmn-label { font-size: var(--qmn-label-size); font-weight: 600; letter-spacing: 0.4px; line-height: 1; transition: all var(--qmn-transition); white-space: nowrap; }
        .qmn-nav .qmn-dot { position: absolute; bottom: -2px; left: 50%; transform: translateX(-50%) translateY(4px) scale(0); width: 5px; height: 5px; border-radius: 50%; background: var(--qmn-active-color); box-shadow: 0 0 9px var(--qmn-active-glow), 0 0 20px var(--qmn-active-glow); transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease; opacity: 0; pointer-events: none; z-index: 0; }
        .qmn-nav .qmn-item.active { color: #4a3000; }
        .qmn-nav .qmn-item.active .qmn-icon-wrap { transform: translateY(-1.5px); }
        .qmn-nav .qmn-item.active .qmn-icon-wrap i { background: linear-gradient(135deg, #f0b90b, #e6a200); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 5px rgba(240, 185, 11, 0.45)); }
        .qmn-nav .qmn-item.active .qmn-dot { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
        .qmn-nav .qmn-item.active .qmn-label { font-weight: 700; color: #5c3d1a; letter-spacing: 0.6px; }
        .qmn-nav .qmn-item.active { transform: translateY(-2px); }
        .qmn-nav .qmn-item.active:active { transform: translateY(-2px) scale(0.94); }
        .qmn-nav .qmn-badge { position: absolute; top: -3px; right: -10px; min-width: 16px; height: 16px; padding: 0 5px; border-radius: 10px; background: #ff4d4d; color: #fff; font-size: 9px; font-weight: 700; line-height: 16px; text-align: center; white-space: nowrap; box-shadow: 0 2px 7px rgba(255, 50, 50, 0.35); z-index: 5; display: none; letter-spacing: 0.2px; animation: qmn-badge-pulse 2s ease-in-out infinite; }
        .qmn-nav .qmn-badge.show { display: inline-block; }
        @keyframes qmn-badge-pulse { 0%,100% { box-shadow: 0 2px 7px rgba(255, 50, 50, 0.35); } 50% { box-shadow: 0 3px 14px rgba(255, 50, 50, 0.6), 0 0 0 4px rgba(255, 77, 77, 0.15); } }

        /* ========== 公告弹窗样式 ========== */
        #noticeModal .modal-box {
            height: 70%;
            max-height: 70%;
            width: 85%;
            display: flex;
            flex-direction: column;
        }
        #noticeContent {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
            font-size: 15px;
            color: #4a3000;
            line-height: 1.6;
        }
        #noticeContent img {
            max-width: 100%;
            border-radius: 15px;
            margin: 10px 0;
        }
        .notice-header {
            text-align: center;
            margin-bottom: 15px;
        }
        .notice-header .notice-date {
            font-size: 13px;
            color: #b08968;
        }
        .notice-nav {
            display: flex;
            justify-content: center;
            margin-top: 15px;
            gap: 10px;
        }
        .notice-nav .btn {
            padding: 8px 20px;
            font-size: 13px;
        }

        /* ========== 统计卡片 ========== */
        .stats-card {
            background: linear-gradient(135deg, #fff5e6, #ffe8cc);
            border-radius: 20px;
            padding: 15px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            box-shadow: 0 4px 15px rgba(255,150,30,0.15);
            border: 1px solid #ffe0b2;
        }
        .stats-item {
            text-align: center;
            flex: 1;
        }
        .stats-item .stats-num {
            font-size: 22px;
            font-weight: 800;
            color: #ff4d2d;
        }
        .stats-item .stats-label {
            font-size: 12px;
            color: #b08968;
            margin-top: 2px;
        }
        .stats-update {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: -5px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="bg-float"></div>
<div class="nav"><span class="nav-title">签到中心</span></div>
<div class="container">
    <div class="balance-card" style="margin-top:20px;">
        <div class="card-ornament"></div>
        <div>
            <div class="balance-label"><i class="ri-wallet-3-line"></i> 账户余额 (元)</div>
            <div class="balance-value" id="balanceAmount"><?php echo $balance; ?></div>
        </div>
        <button class="detail-btn" id="detailBtn"><i class="ri-file-list-3-line"></i> 明细</button>
    </div>
    <div class="sign-zone" style="margin-bottom:20px;">
        <button class="sign-btn <?php echo $isVip ? 'vip' : ''; ?> <?php echo $signed ? 'signed' : ''; ?>" id="signMainBtn" <?php echo $signed ? 'disabled' : ''; ?>>
            <?php if (!$signed): ?>
                <div class="spark"></div><div class="spark"></div><div class="spark"></div>
            <?php endif; ?>
            <i class="btn-icon ri-<?php echo $signed ? 'checkbox-circle-fill' : 'gift-2-fill'; ?>"></i>
            <span class="btn-text"><?php echo $signed ? '今日已签到' : '立即签到领钱'; ?></span>
            <?php if ($signed): ?><span class="btn-sub">累计<?php echo $totalDays; ?>天</span><?php endif; ?>
        </button>
        <div class="sign-status">
            <?php if ($signed): ?>
                累计签到 <b><?php echo $totalDays; ?></b> 天 · 明日签到领 <b><?php echo $tomorrowReward; ?></b> 元
                 <div class="stats-update" style="margin-top:5px;margin-bottom:0px;">签到奖励100%可提现！</div>
            <?php else: ?>
                <?php echo $isVip ? '<span style="color:#d4af37;font-weight:800;">签米会员尊享</span>' : '普通用户'; ?>
                 · 今日签到领 <b style="font-size:18px;"><?php echo $todayReward; ?></b> 元
                  <div class="stats-update" style="margin-top:5px;margin-bottom:0px;">签到奖励100%可提现！</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isVip): ?>
    <div class="vip-card" onclick="location.href='plugin.php?id=xigua_hb&ac=vip'">
        <i class="ri-vip-crown-fill vip-icon"></i>
        <div class="vip-info">
            <div class="vip-title">升级签米会员 · 赚更多</div>
            <div class="vip-desc">每日签到领 25 元，收益翻倍！</div>
        </div>
        <i class="ri-arrow-right-s-line vip-arrow"></i>
    </div>
    <?php endif; ?>

    <div class="rule-grid">
        <div class="rule-card" id="rulePlayBtn">
            <i class="ri-gift-2-line"></i>
            <div class="label">玩法规则</div>
            <div class="desc">签到规则 & 金额</div>
        </div>
        <div class="rule-card promo-highlight" id="rulePromoBtn">
            <i class="ri-user-shared-2-line"></i>
            <div class="label">推广日赚千元</div>
            <div class="desc">好友签到你得钱</div>
        </div>
    </div>

    <!-- ========== 限时拉新活动卡片 ========== -->
    <div class="activity-card" id="inviteActivityCard">
        <div class="act-title"><i class="ri-fire-fill"></i> 官方大力扶持，来领888元</div>
        <div class="act-desc">活动期间邀请好友实名+连续签到3天，最高领888元！</div>
        <div class="act-progress">
            <span>已有 <span id="actValidCount">-</span> 好友达成</span>
            <span>活动剩余 <span id="actRemainDays">-</span> 天</span>
        </div>
    </div>

    <div class="group-card" id="groupCard">
        <i class="ri-qq-fill group-icon"></i>
        <div class="group-info">
            <div class="group-title">加入官方QQ群</div>
            <div class="group-desc">最新活动、互助交流、专属福利</div>
        </div>
        <i class="ri-arrow-right-s-line group-arrow"></i>
    </div>
</div>

<div id="confirmModal" class="modal-overlay">
    <div class="modal-box" style="text-align:center;">
        <i class="ri-close-line modal-close" id="closeConfirmModal"></i>
        <div class="modal-title" id="confirmTitle"></div>
        <div class="confirm-text" id="confirmMsg"></div>
        <div class="btn-row" id="confirmBtns"></div>
    </div>
</div>

<div id="detailModal" class="modal-overlay">
    <div class="modal-box" id="detailScrollBox">
        <i class="ri-close-line modal-close" id="closeDetailModal"></i>
        <div class="modal-title">奖励明细</div>
        <div id="rewardStatsArea"></div>
        <div class="record-tabs">
            <div class="record-tab active" data-type="sign" id="tabSign">签到奖励</div>
            <div class="record-tab" data-type="promo" id="tabPromo">推广奖励</div>
        </div>
        <div id="recordList" style="flex:1;"></div>
    </div>
</div>

<div id="ruleModal" class="modal-overlay">
    <div class="modal-box" id="ruleScrollBox">
        <i class="ri-close-line modal-close" id="closeRuleModal"></i>
        <div class="modal-title" id="ruleTitle"></div>
        <div id="ruleContent" style="flex:1;"></div>
    </div>
</div>

<div id="groupModal" class="modal-overlay">
    <div class="modal-box" style="text-align:center;">
        <i class="ri-close-line modal-close" id="closeGroupModal"></i>
        <div class="modal-title">加入官方QQ群</div>
        <div style="margin: 20px 0; color: #4a3000; font-size: 15px;">
            <p>群号：<b style="font-size: 22px; color: #ff4d2d;">2164070898</b></p>
            <p style="margin-top: 8px; color: #888; font-size: 13px;">点击下方按钮一键加群或复制群号</p>
        </div>
        <div class="btn-row" style="flex-wrap: wrap; gap: 10px;">
            <button class="btn primary" id="copyGroupBtn"><i class="ri-file-copy-line"></i> 复制群号</button>
            <a href="https://qm.qq.com/q/GVj7FxJuIo" target="_blank" class="btn gold" style="text-decoration: none; display: inline-block;">一键加群</a>
        </div>
    </div>
</div>

<!-- 活动弹窗 -->
<div id="inviteActivityModal" class="modal-overlay">
    <div class="modal-box" id="inviteActivityScrollBox">
        <i class="ri-close-line modal-close" id="closeInviteActivityModal"></i>
        <div class="modal-title">🎁 限时拉新奖励活动</div>
        <div id="inviteActivityContent"></div>
    </div>
</div>

<!-- ========== 公告弹窗 ========== -->
<div id="noticeModal" class="modal-overlay">
    <div class="modal-box">
        <i class="ri-close-line modal-close" id="closeNoticeModal"></i>
        <div class="modal-title" id="noticeTitle">公告</div>
        <div class="notice-header">
            <span class="notice-date" id="noticeDate"></span>
        </div>
        <div id="noticeContent"></div>
        <div class="notice-nav">
            <button class="btn gold" id="prevNoticeBtn">上一条</button>
            <button class="btn gold" id="nextNoticeBtn">下一条</button>
        </div>
    </div>
</div>

<div class="coin-rain" id="coinRain" style="display:none;"></div>
<div style="margin-bottom:100px;"> </div>

<!-- 底部导航 -->
<nav class="qmn-nav" role="navigation" aria-label="底部导航">
    <a href="plugin.php?id=xigua_hb" class="qmn-item" title="首页">
        <span class="qmn-icon-wrap"><i class="ri-home-line"></i><span class="qmn-dot"></span></span>
        <span class="qmn-label">首页</span>
    </a>
    <a href="plugin.php?id=view&modac=sign" class="qmn-item active" title="签到">
        <span class="qmn-icon-wrap"><i class="ri-calendar-check-line"></i><span class="qmn-dot"></span></span>
        <span class="qmn-label">签到</span>
        <span class="qmn-badge">新</span>
    </a>
    <a href="plugin.php?id=tb_cus_pipei" class="qmn-item" title="分红">
        <span class="qmn-icon-wrap"><i class="ri-gift-line"></i><span class="qmn-dot"></span></span>
        <span class="qmn-label">分红</span>
    </a>
    <a href="plugin.php?id=xigua_hb&ac=my" class="qmn-item" title="我的">
        <span class="qmn-icon-wrap"><i class="ri-user-line"></i><span class="qmn-dot"></span></span>
        <span class="qmn-label">我的</span>
    </a>
</nav>

<script>
    (function() {
        const nav = document.querySelector('.qmn-nav');
        if (!nav) return;
        const items = nav.querySelectorAll('.qmn-item');
        items.forEach(item => {
            item.addEventListener('click', function(e) {
                items.forEach(el => el.classList.remove('active'));
                this.classList.add('active');
            });
        });
    })();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function $(id) { return document.getElementById(id); }
    function json(url, cb) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function() {
            if (xhr.status === 200) { cb(JSON.parse(xhr.responseText)); }
            else { cb({ code: -1, msg: '网络异常' }); }
        };
        xhr.onerror = function() { cb({ code: -1, msg: '网络异常' }); };
        xhr.send();
    }

    // ========== 公告配置 ==========
    var noticeList = [
        {
            id: 1,
            title: '访问平台慢、打不开？可以试试加速器！',
            date: '2026-05-18',
            content: '<p><b style="font-size:18px;">这是免费的，绿茶VPN：</b><br>如果你的下级、好友打不开平台也可以让好友下载尝试连接，长按复制下载链接：<br>下载链接1：https://3.lvcha.one?id=508331497<br>下载链接2：https://www.lvcha.org?id=508331497 <br><b>你也可以在“我的”——“APP/加速器”内随时获取加速器下载链接；</b><br><img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260517/17789865526a092e382ecc4.jpg"></p>',
            interval: 7200
        },
        
        {
            id: 2,
            title: '签米刚刚上线，截止29日凌晨已突破8万人注册，团队长速度布局！',
            date: '2026-05-29',
            content: '<p><b style="font-size:18px;">想要一份睡后收入的团队长速度裂变起来！</b><br>好友每天签到你都有现金奖励，超强睡后收入，推广人数越多等级越高，奖励加成越高！具体加成规则点击“推广日赚千元”查看！收益提现无任何限制！<br><br><img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260517/17789964176a0954c19c644.png"><br><img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260517/17789965266a09552e52ee2.jpg"></p>',
            interval: 3600
        }
    ];

    var currentNoticeIndex = 0;
    var currentNoticeId = 0;
    var noticeModal = $('noticeModal');
    var noticeTitle = $('noticeTitle');
    var noticeDate = $('noticeDate');
    var noticeContent = $('noticeContent');

    function showNotice(index) {
        if (!noticeList.length) return;
        if (index < 0) index = noticeList.length - 1;
        if (index >= noticeList.length) index = 0;
        currentNoticeIndex = index;
        var notice = noticeList[index];
        currentNoticeId = notice.id;
        noticeTitle.textContent = notice.title;
        noticeDate.textContent = notice.date;
        noticeContent.innerHTML = notice.content;
        noticeModal.classList.add('active');
    }

    function closeNoticeModal() {
        noticeModal.classList.remove('active');
        if (currentNoticeId) {
            var key = 'notice_last_' + currentNoticeId;
            localStorage.setItem(key, Math.floor(Date.now() / 1000));
            currentNoticeId = 0;
        }
    }

    function checkAndShowNotice() {
        var now = Math.floor(Date.now() / 1000);
        var dueNotices = [];
        noticeList.forEach(function(notice, idx) {
            var key = 'notice_last_' + notice.id;
            var lastShow = parseInt(localStorage.getItem(key)) || 0;
            if (now - lastShow >= notice.interval || lastShow === 0) {
                dueNotices.push({notice: notice, index: idx});
            }
        });
        if (dueNotices.length > 0) {
            showNotice(dueNotices[0].index);
        }
    }

    $('nextNoticeBtn').onclick = function() {
        showNotice(currentNoticeIndex + 1);
    };
    $('prevNoticeBtn').onclick = function() {
        showNotice(currentNoticeIndex - 1);
    };
    $('closeNoticeModal').onclick = closeNoticeModal;
    noticeModal.addEventListener('click', function(e) {
        if (e.target === noticeModal) closeNoticeModal();
    });

    setTimeout(function() {
        checkAndShowNotice();
    }, 500);

    // 以下为原有功能
    var currentRecordType = 'sign';
    var recordPage = 1;
    var isLoadingMore = false;
    var noMoreData = false;

    function closeModal(id) { $(id).classList.remove('active'); }
    function showConfirm(title, msg, btns) {
        $('confirmTitle').innerHTML = title;
        $('confirmMsg').innerHTML = msg;
        var html = '';
        if (btns.length === 0) {
            html = '<button class="btn primary" id="confirmOk">确定</button>';
        } else {
            btns.forEach(function(b, idx) {
                html += '<button class="btn '+b.cls+'" id="confirmBtn_'+idx+'">'+b.text+'</button>';
            });
        }
        $('confirmBtns').innerHTML = html;
        if (btns.length === 0) {
            $('confirmOk').onclick = function() { closeModal('confirmModal'); };
        } else {
            btns.forEach(function(b, idx) {
                var btn = $('confirmBtn_'+idx);
                if (b.action) btn.onclick = b.action;
            });
        }
        $('confirmModal').classList.add('active');
    }

    function startCoinRain() {
        var rain = $('coinRain');
        rain.style.display = 'block';
        rain.innerHTML = '';
        for (var i = 0; i < 50; i++) {
            var coin = document.createElement('div');
            coin.className = 'coin';
            coin.textContent = '💰';
            coin.style.left = Math.random() * 100 + '%';
            coin.style.animationDuration = (Math.random() * 2 + 2) + 's';
            coin.style.animationDelay = Math.random() + 's';
            coin.style.fontSize = (Math.random() * 20 + 22) + 'px';
            rain.appendChild(coin);
        }
        setTimeout(function() { rain.style.display = 'none'; rain.innerHTML = ''; }, 4000);
    }

    // 加载统计并显示
    function loadRewardStats() {
        json('plugin.php?id=view&modac=sign&submodac=get_reward_stats', function(res) {
            var html = '';
            if (res.code === 0) {
                var d = res.data;
                html += '<div class="stats-card">';
                html += '<div class="stats-item"><div class="stats-num">' + d.total_sign + '</div><div class="stats-label">累计签到收益(元)</div></div>';
                html += '<div class="stats-item"><div class="stats-num">' + d.total_promo + '</div><div class="stats-label">累计推广收益(元)</div></div>';
                html += '</div>';
                html += '<div class="stats-update">数据每2小时更新，上次更新：' + d.update_time + '</div>';
            } else {
                html = '<div style="text-align:center;padding:10px;color:#b08968;">统计数据加载失败</div>';
            }
            $('rewardStatsArea').innerHTML = html;
        });
    }

    function loadRecords() {
        isLoadingMore = true;
        json('plugin.php?id=view&modac=sign&submodac=records&type=' + currentRecordType + '&page=' + recordPage, function(res) {
            var html = '';
            if (res.data && res.data.length > 0) {
                res.data.forEach(function(r) {
                    if (currentRecordType === 'sign') {
                        html += '<div class="record-item"><div class="row"><span>签到奖励</span><span class="amount">+'+r.money+'元</span></div><div class="time">'+r.time+'</div></div>';
                    } else {
                        html += '<div class="record-item"><div class="row"><span>'+r.level+'好友完成签到 </span><span class="amount">+'+r.money+'元</span></div>';
                        if (r.note) html += '<div style="color:#d4af37; font-size:12px; margin-top:2px;">'+r.note+'</div>';
                        html += '<div class="time">'+r.time+'</div></div>';
                    }
                });
                noMoreData = res.data.length < 10;
                if (noMoreData) html += '<div class="no-more">— 没有更多了 —</div>';
            } else {
                html = '<div style="text-align:center;padding:40px;color:#b08968;">暂无记录</div>';
                noMoreData = true;
            }
            $('recordList').innerHTML = html;
            isLoadingMore = false;
            $('detailScrollBox').scrollTop = 0;
        });
    }

    function loadMoreRecords() {
        if (isLoadingMore || noMoreData) return;
        isLoadingMore = true;
        recordPage++;
        json('plugin.php?id=view&modac=sign&submodac=records&type=' + currentRecordType + '&page=' + recordPage, function(res) {
            var html = '';
            if (res.data && res.data.length > 0) {
                res.data.forEach(function(r) {
                    if (currentRecordType === 'sign') {
                        html += '<div class="record-item"><div class="row"><span>签到奖励</span><span class="amount">+'+r.money+'元</span></div><div class="time">'+r.time+'</div></div>';
                    } else {
                        html += '<div class="record-item"><div class="row"><span>'+r.level+'奖励 ('+r.from+')</span><span class="amount">+'+r.money+'元</span></div>';
                        if (r.note) html += '<div style="color:#d4af37; font-size:12px; margin-top:2px;">'+r.note+'</div>';
                        html += '<div class="time">'+r.time+'</div></div>';
                    }
                });
                var noMore = $('recordList').querySelector('.no-more');
                if (noMore) $('recordList').removeChild(noMore);
                $('recordList').insertAdjacentHTML('beforeend', html);
                noMoreData = res.data.length < 10;
                if (noMoreData) $('recordList').insertAdjacentHTML('beforeend', '<div class="no-more">— 没有更多了 —</div>');
            } else {
                noMoreData = true;
                $('recordList').insertAdjacentHTML('beforeend', '<div class="no-more">— 没有更多了 —</div>');
            }
            isLoadingMore = false;
        });
    }

    function openDetailModal() {
        currentRecordType = 'sign'; recordPage = 1; noMoreData = false;
        $('tabSign').classList.add('active');
        $('tabPromo').classList.remove('active');
        loadRewardStats();
        loadRecords();
        $('detailModal').classList.add('active');
    }

    function switchRecordTab(type) {
        if (isLoadingMore) return;
        currentRecordType = type; recordPage = 1; noMoreData = false;
        if (type === 'sign') {
            $('tabSign').classList.add('active');
            $('tabPromo').classList.remove('active');
        } else {
            $('tabPromo').classList.add('active');
            $('tabSign').classList.remove('active');
        }
        loadRecords();
    }

    function openRuleModal(type) {
        if (type === 'play') {
            $('ruleTitle').innerHTML = '玩法奖励规则';
            $('ruleContent').innerHTML = `
            <div class="rule-modal-grid" style="margin-top:0;">
                <div class="rule-modal-card"><i class="ri-user-line"></i><div>普通用户签到<br><b>+12.00元</b></div></div>
                <div class="rule-modal-card"><i class="ri-vip-crown-line" style="background: linear-gradient(135deg, #f0b90b, #ffd700); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i><div>签米会员签到<br><b>+25.00元</b></div></div>
                <div class="rule-modal-card"><i class="ri-eye-line"></i><div>完成6次浏览<br>方可签到</div></div>
                <div class="rule-modal-card"><i class="ri-calendar-check-line"></i><div>每日限领一次</div></div>
            </div>`;
            $('ruleModal').classList.add('active');
        } else {
            $('ruleTitle').innerHTML = '推广奖励规则';
            $('ruleContent').innerHTML = '<div style="text-align:center; padding:20px;">加载中...</div>';
            json('plugin.php?id=view&modac=sign&submodac=promo_info', function(res) {
                if (res.code === 0) {
                    var d = res.data;
                    var cardHtml = `<div class="promo-summary">
                        <b>当前等级：</b><div class="level-badge">V${d.level}</div>
                      <div class="stats-update">推广等级/有效人数每1小时更新1次</div>
                        <div class="stats" style="margin-top:5px;margin-bottom:-5px;">
                            <div class="stat-item"><div class="stat-num">${d.direct_count}</div><div class="stat-label">有效直推</div></div>
                            <div class="stat-item"><div class="stat-num">${d.indirect_count}</div><div class="stat-label">有效间推</div></div>
                        </div>
                        
                        
                         <br>当前推广奖励比例：</br>直推签到提成：<b style="color:#ff4d2d;">${d.direct_pct}% </b><br>间推签到提成：<b style="color:#ff4d2d;">${d.indirect_pct}%</b><br>
                         
                      直推开通会员奖励：<b style="color:#ff4d2d;">16.8元</b>，间推开通会员奖励<b style="color:#ff4d2d;">3.8元</b><br>
                         <div class="promo-progress">好友签到奖励、开通会员提成奖励、消费等所得提成奖励均到“提成账户”，可无限制提现；</div>
                        `;
                    if (d.next_level) {
                        cardHtml += `<div class="promo-progress">距 V${d.next_level} 还需：直推 ${d.need_direct} 人，间推 ${d.need_indirect} 人</div>`;
                    } else {
                        cardHtml += `<div class="promo-progress">🎉 已达最高等级</div>`;
                    }
                    cardHtml += `</div>`;

                    var introHtml = `
                    <div class="promo-intro">
                   
                        <b>如何获得奖励？</b><br>
                        好友每天签到即可为您带来签到现金奖励提成，直推好友签到，您可获得其签到金额的百分比奖励；间推好友签到，您可获得百分比奖励(邀请的好友必须完成实名认证，否则签到无奖励，但不用担心，好友只要在往后任何时候完成认证，你就有奖励了，但之前未获得的不会补发。)
                        <br><b>签米会员特权：</b>若好友是签米会员，每次签到您都可获得提成奖励；若好友不是签米会员，每位好友最多为您提供2次签到奖励。
                        <br>
                        <b>如何提高奖励？</b><br>
                        提升推广等级！直推和间推的有效人数越多，等级越高，奖励百分比越大，达到下一等级即可享受更高提成，最高可达直推23%、间推2%。立即邀请好友，加速升级！
                         <br> <b>等级有效统计规则：</b><br>
                        好友在最近2天内有签到记录才计为有效，否则不计入等级统计，等级会动态升降。
                        <img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260517/17789964176a0954c19c644.png">
                        <img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260517/17789965266a09552e52ee2.jpg">
                    </div>`;

                    var tableHtml = `<table class="promo-table"><tr><th>等级</th><th>直推数</th><th>间推数</th><th>直推奖</th><th>间推奖</th></tr>`;
                    for (var l = 1; l <= 7; l++) {
                        var levelData = d.levels[l];
                        var cls = l === d.level ? 'promo-level-current' : '';
                        tableHtml += `<tr class="${cls}"><td>V${l}</td><td>≥${levelData.direct_req}</td><td>≥${levelData.indirect_req}</td><td>${levelData.direct_pct}%</td><td>${levelData.indirect_pct}%</td></tr>`;
                    }
                    tableHtml += `</table>`;

                    var inviteBtn = '<a href="plugin.php?id=xigua_hh&ac=invite" class="promo-invite-btn"><i class="ri-user-add-line"></i> 立即邀请好友</a>';

                    $('ruleContent').innerHTML = cardHtml + inviteBtn + tableHtml + introHtml;
                } else {
                    $('ruleContent').innerHTML = '<div style="text-align:center;padding:20px;">获取等级失败</div>';
                }
                $('ruleScrollBox').scrollTop = 0;
            });
            $('ruleModal').classList.add('active');
        }
    }

    function openGroupModal() { $('groupModal').classList.add('active'); }
    function copyGroupNumber() {
        var text = "1015789277";
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                alert('群号已复制！');
            }, function() {
                var input = document.createElement('input');
                input.value = text; document.body.appendChild(input); input.select();
                document.execCommand('copy'); document.body.removeChild(input);
                alert('群号已复制！');
            });
        } else {
            var input = document.createElement('input');
            input.value = text; document.body.appendChild(input); input.select();
            document.execCommand('copy'); document.body.removeChild(input);
            alert('群号已复制！');
        }
    }

    // ========== 活动相关 ==========
    function loadInviteActivityInfo() {
        json('plugin.php?id=view&modac=sign&submodac=invite_activity_info', function(res) {
            if (res.code === 0) {
                var d = res.data;
                $('actValidCount').textContent = d.valid_count;
                $('actRemainDays').textContent = d.remaining_days;
                var html = '<div class="promo-intro"><b>活动时间：</b>' + d.activity_start + ' ~ ' + d.activity_end + '<br>';
                html += '<b>规则：</b>邀请新用户完成实名认证且连续3天签到，即可累计有效拉新人数。<br>达到对应阶梯可领取现金奖励，每人每阶梯限领一次。</div>';
                html += '<div class="promo-summary"><b>当前有效拉新：</b><span style="font-size:24px;color:#ff4d2d;">' + d.valid_count + '</span> 人</div><div class="stats-update">奖励数据每3小时更新1次</div>';
                html += '<table class="promo-table"><tr><th>直推人数</th><th>奖励(元)</th><th>状态</th></tr>';
                d.rewards.forEach(function(r) {
                    var status = '';
                    if (r.received) status = '<span style="color:#4caf50;">已领取</span>';
                    else if (r.can_receive) status = '<button class="btn gold" style="padding:6px 12px;font-size:12px;" onclick="receiveActivityReward(' + r.count + ')">领取</button>';
                    else status = '<span style="color:#999;">未达成</span>';
                    html += '<tr><td>≥' + r.count + '</td><td>' + r.money + '</td><td>' + status + '</td></tr>';
                });
                html += '</table>';
                $('inviteActivityContent').innerHTML = html;
            } else {
                alert(res.msg);
            }
        });
    }

    window.receiveActivityReward = function(count) {
        if (confirm('确认领取该阶梯奖励吗？')) {
            json('plugin.php?id=view&modac=sign&submodac=invite_activity_receive&count=' + count, function(res) {
                if (res.code === 0) {
                    alert(res.msg);
                    loadInviteActivityInfo();
                    json('plugin.php?id=view&modac=sign&submodac=status', function(statusRes) {
                        if (statusRes.code === 0) {
                            $('balanceAmount').textContent = statusRes.balance;
                        }
                    });
                } else {
                    alert(res.msg);
                }
            });
        }
    };

    function startSign() {
        var signBtn = $('signMainBtn');
        if (signBtn.classList.contains('loading') || signBtn.classList.contains('signed')) return;
        signBtn.classList.add('loading');
        json('plugin.php?id=view&modac=sign&submodac=status', function(res) {
            signBtn.classList.remove('loading');
            if (res.signed) {
                showConfirm('已完成签到', '今日已领过奖励，明天再来吧！', []);
                return;
            }
            if (!res.has_views) {
                showConfirm('浏览广告不足', '您今日浏览项目不足6个，去完成浏览任务才能签到哦！', [
                    { text: '取消', cls: '', action: function(){ closeModal('confirmModal'); } },
                    { text: '去浏览', cls: 'primary', action: function(){ location.href = 'plugin.php?id=xigua_hb'; } }
                ]);
                return;
            }
            if (!res.is_vip) {
                showConfirm('升级会员奖励加倍', '开通签米会员后，每日签到可领<b style="font-size:20px;color:#ff7b00;"> 25 元</b>！<br>早升级早赚钱！', [
                    { text: '先签到', cls: '', action: executeSign },
                    { text: '立即开通', cls: 'gold', action: function(){ location.href = 'plugin.php?id=xigua_hb&ac=vip'; } }
                ]);
            } else {
                executeSign();
            }
        });
    }

    function executeSign() {
        var signBtn = $('signMainBtn');
        signBtn.classList.add('loading');
        json('plugin.php?id=view&modac=sign&submodac=sign', function(res) {
            signBtn.classList.remove('loading');
            if (res.code === 0) {
                startCoinRain();
                showConfirm('🎉 签到成功', '恭喜获得 <b style="color:#ff4d2d;">' + res.reward + '</b> 元', [
                    { text: '太棒了', cls: 'primary', action: function(){ location.reload(); } }
                ]);
                $('balanceAmount').textContent = res.new_balance;
            } else if (res.code === -2) {
                showConfirm('提示', res.msg, [
                    { text: '取消', cls: '', action: function(){ closeModal('confirmModal'); } },
                    { text: '去浏览', cls: 'primary', action: function(){ location.href = res.redirect; } }
                ]);
            } else {
                showConfirm('操作失败', res.msg, []);
            }
        });
    }

    $('detailBtn').onclick = openDetailModal;
    $('closeDetailModal').onclick = function() { closeModal('detailModal'); };
    $('tabSign').onclick = function() { switchRecordTab('sign'); };
    $('tabPromo').onclick = function() { switchRecordTab('promo'); };
    $('detailScrollBox').onscroll = function() {
        if (this.scrollTop + this.clientHeight >= this.scrollHeight - 20 && !isLoadingMore && !noMoreData) {
            loadMoreRecords();
        }
    };
    $('rulePlayBtn').onclick = function() { openRuleModal('play'); };
    $('rulePromoBtn').onclick = function() { openRuleModal('promo'); };
    $('closeRuleModal').onclick = function() { closeModal('ruleModal'); };
    $('closeConfirmModal').onclick = function() { closeModal('confirmModal'); };
    $('groupCard').onclick = openGroupModal;
    $('closeGroupModal').onclick = function() { closeModal('groupModal'); };
    $('copyGroupBtn').onclick = copyGroupNumber;

    $('inviteActivityCard').onclick = function() {
        if (!$('inviteActivityContent').innerHTML.trim()) {
            loadInviteActivityInfo();
        }
        $('inviteActivityModal').classList.add('active');
    };
    $('closeInviteActivityModal').onclick = function() { closeModal('inviteActivityModal'); };

    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });

    var signBtn = $('signMainBtn');
    signBtn.addEventListener('touchend', function(e) { e.preventDefault(); startSign(); });
    signBtn.addEventListener('click', function(e) { e.preventDefault(); startSign(); });

    loadInviteActivityInfo();
});
</script>

</body>
</html>