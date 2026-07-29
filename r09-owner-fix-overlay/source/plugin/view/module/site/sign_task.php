<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/tb_credit/credit.core.php';
require_once DISCUZ_ROOT . './source/plugin/tb_cus_base/common.php';
require_once DISCUZ_ROOT . './source/plugin/view/function.core.php';

$taskConfig = require DISCUZ_ROOT . './source/plugin/view/sign_task_config.php';
$uid = intval($_G['uid']);
$username = addslashes($_G['username']);
if (!$uid) {
    showmessage('未登录', '', array(), array('login' => 1));
}

function _tgb_task_json($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function _tgb_task_require_post() {
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
        _tgb_task_json(array('code' => -1, 'msg' => '请求方式错误'));
    }
    $expected = formhash();
    $actual = isset($_POST['formhash']) ? (string)$_POST['formhash'] : '';
    if (!$actual || !hash_equals($expected, $actual)) {
        _tgb_task_json(array('code' => -1, 'msg' => '页面已过期，请刷新后重试'));
    }
}

function _tgb_task_is_vip($uid) {
    $member = DB::fetch_first(
        'SELECT status,endts,hyname,joininfo FROM %t WHERE uid=%d LIMIT 1',
        array('xigua_hh_member', $uid)
    );
    if (!$member || intval($member['status']) !== 1 || intval($member['endts']) <= TIMESTAMP) return false;
    if (isset($member['hyname']) && $member['hyname'] === '推广宝会员') return true;
    $info = @unserialize($member['joininfo']);
    return is_array($info) && isset($info['name']) && $info['name'] === '推广宝会员';
}

function _tgb_task_ensure_wallet($uid, $username) {
    $wallet = DB::fetch_first('SELECT uid, money FROM %t WHERE uid=%d', array('tb_cus_xiguahh_user', $uid));
    if (!$wallet) {
        DB::insert('tb_cus_xiguahh_user', array('uid' => $uid, 'username' => $username, 'money' => 0));
        $wallet = array('uid' => $uid, 'money' => 0);
    }
    return $wallet;
}

function _tgb_task_add_sign_money($uid, $amount) {
    $amount = round(floatval($amount), 2);
    if ($amount <= 0) return false;
    DB::query('UPDATE %t SET money=money+%f WHERE uid=%d', array('tb_cus_xiguahh_user', $amount, $uid));
    return DB::affected_rows() > 0;
}

function _tgb_task_add_promo_money($uid, $amount, $note) {
    $amount = round(floatval($amount), 2);
    if (!$uid || $amount <= 0) return false;
    if (!DB::result_first('SELECT uid FROM %t WHERE uid=%d', array('xigua_hb_user', $uid))) {
        DB::insert('xigua_hb_user', array('uid' => $uid, 'money' => 0));
    }
    DB::query('UPDATE %t SET money=money+%f WHERE uid=%d', array('xigua_hb_user', $amount, $uid));
    if (DB::affected_rows() !== 1) return false;
    $logId = DB::insert('xigua_hb_moneylog', array(
        'uid' => $uid,
        'crts' => TIMESTAMP,
        'size' => $amount,
        'link' => 'plugin.php?id=view&modac=sign',
        'note' => $note,
    ), true);
    return intval($logId) > 0;
}

function _tgb_task_invite_campaign($date, $config) {
    $campaign = isset($config['invite_campaign']) && is_array($config['invite_campaign'])
        ? $config['invite_campaign']
        : array();
    $start = isset($campaign['start_date']) ? (string)$campaign['start_date'] : '';
    $end = isset($campaign['end_date']) ? (string)$campaign['end_date'] : '';
    $active = $start && $end && $date >= $start && $date <= $end;
    return array(
        'active' => $active,
        'start_date' => $start,
        'end_date' => $end,
        'direct_regular_reward' => $active && isset($campaign['direct_regular_reward'])
            ? round(floatval($campaign['direct_regular_reward']), 2)
            : round(floatval($config['direct_regular_reward']), 2),
        'indirect_regular_reward' => $active && isset($campaign['indirect_regular_reward'])
            ? round(floatval($campaign['indirect_regular_reward']), 2)
            : round(floatval($config['indirect_regular_reward']), 2),
        'direct_vip_reward' => round(floatval($config['direct_vip_reward']), 2),
        'indirect_vip_reward' => round(floatval($config['indirect_vip_reward']), 2),
    );
}

function _tgb_task_campaign_period($campaign) {
    $start = isset($campaign['start_date']) ? strtotime($campaign['start_date']) : 0;
    $end = isset($campaign['end_date']) ? strtotime($campaign['end_date']) : 0;
    return $start && $end ? date('n.j', $start) . '-' . date('n.j', $end) : '';
}

function _tgb_task_give_promo($fromUid, $isVip, $date, $config) {
    $certified = DB::result_first('SELECT rescodebdres FROM %t WHERE uid=%d', array('xiaomy_certification', $fromUid));
    if (intval($certified) !== 1) return;

    $first = DB::fetch_first('SELECT uid FROM %t WHERE fansuid=%d LIMIT 1', array('xigua_hh_invite', $fromUid));
    if (!$first || !$first['uid']) return;

    $campaign = _tgb_task_invite_campaign($date, $config);
    $rewards = array(
        1 => $isVip ? $campaign['direct_vip_reward'] : $campaign['direct_regular_reward'],
        2 => $isVip ? $campaign['indirect_vip_reward'] : $campaign['indirect_regular_reward'],
    );
    $uplines = array(1 => intval($first['uid']));
    $second = DB::fetch_first('SELECT uid FROM %t WHERE fansuid=%d LIMIT 1', array('xigua_hh_invite', $uplines[1]));
    if ($second && $second['uid']) $uplines[2] = intval($second['uid']);

    foreach ($uplines as $level => $upUid) {
        $money = round(floatval($rewards[$level]), 2);
        $levelName = $level == 1 ? '一级' : '二级';
        $memberTag = $isVip ? '推广宝会员' : '普通会员';
        $condition = 'uid=' . $upUid . ' AND from_uid=' . intval($fromUid) . ' AND level=' . intval($level) . " AND reward_date='" . addslashes($date) . "'";
        try {
            DB::query(
                'INSERT IGNORE INTO %t (uid,from_uid,level,reward_date,is_vip,reward_money,status,created_at,updated_at) VALUES (%d,%d,%d,%s,%d,%f,%s,%d,%d)',
                array('view_ad_promo_reward', $upUid, $fromUid, $level, $date, $isVip ? 1 : 0, $money, 'processing', TIMESTAMP, TIMESTAMP)
            );
            if (DB::affected_rows() !== 1) continue;
            $campaignTag = $campaign['active'] && !$isVip ? '限时加码' : '';
            if (!_tgb_task_add_promo_money($upUid, $money, "{$campaignTag}{$levelName}好友完成广告任务奖励")) throw new Exception('推广奖励账户更新失败');
            $promoLogId = DB::insert('view_sign_promo_log', array(
                'uid' => $upUid,
                'from_uid' => $fromUid,
                'level' => $level,
                'reward_money' => $money,
                'sign_date' => $date,
                'dateline' => TIMESTAMP,
                'note' => "{$memberTag}任务奖励",
            ), true);
            if (!$promoLogId) throw new Exception('推广奖励明细写入失败');
            DB::update('view_ad_promo_reward', array('status' => 'paid', 'updated_at' => TIMESTAMP), $condition . " AND status='processing'");
            if (DB::affected_rows() !== 1) throw new Exception('推广奖励状态更新失败');
        } catch (Exception $e) {
            DB::update('view_ad_promo_reward', array('status' => 'review', 'updated_at' => TIMESTAMP), $condition);
        }
    }
}

function _tgb_support_valid_count($uid) {
    $cache = DB::fetch_first('SELECT valid_count,last_calc_time FROM %t WHERE uid=%d', array('view_invite_activity_cache', $uid));
    if ($cache && TIMESTAMP - intval($cache['last_calc_time']) < 10800) {
        return intval($cache['valid_count']);
    }
    $invites = DB::fetch_all('SELECT fansuid FROM %t WHERE uid=%d', array('xigua_hh_invite', $uid));
    $valid = 0;
    foreach ($invites as $invite) {
        $fansUid = intval($invite['fansuid']);
        if (intval(DB::result_first('SELECT rescodebdres FROM %t WHERE uid=%d', array('xiaomy_certification', $fansUid))) !== 1) continue;
        $days = DB::fetch_all('SELECT DISTINCT sign_date FROM %t WHERE uid=%d ORDER BY sign_date ASC', array('view_sign_log', $fansUid));
        $streak = 1;
        $previous = 0;
        foreach ($days as $day) {
            $current = strtotime($day['sign_date']);
            if (!$current) continue;
            $streak = $previous && $current - $previous === 86400 ? $streak + 1 : 1;
            $previous = $current;
            if ($streak >= 3) {
                $valid++;
                break;
            }
        }
    }
    if ($cache) {
        DB::update('view_invite_activity_cache', array('valid_count' => $valid, 'last_calc_time' => TIMESTAMP), 'uid=' . intval($uid));
    } else {
        DB::insert('view_invite_activity_cache', array('uid' => $uid, 'valid_count' => $valid, 'last_calc_time' => TIMESTAMP));
    }
    return $valid;
}

function _tgb_support_invalidate_upline_cache($fromUid) {
    $upUid = intval(DB::result_first('SELECT uid FROM %t WHERE fansuid=%d LIMIT 1', array('xigua_hh_invite', $fromUid)));
    if ($upUid) DB::delete('view_invite_activity_cache', 'uid=' . $upUid);
}

function _tgb_task_create_tables() {
    $progress = DB::table('view_ad_task_progress');
    $impression = DB::table('view_ad_task_impression');
    $promoReward = DB::table('view_ad_promo_reward');
    $supportCache = DB::table('view_invite_activity_cache');
    $supportReward = DB::table('view_invite_activity_reward');
    $supportClaim = DB::table('view_ad_support_claim');
    $userStats = DB::table('view_ad_user_stats');
    DB::query("CREATE TABLE IF NOT EXISTS `{$progress}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL,
        `task_date` date NOT NULL,
        `is_vip` tinyint(1) NOT NULL DEFAULT '0',
        `target_count` smallint(5) NOT NULL DEFAULT '0',
        `viewed_count` smallint(5) NOT NULL DEFAULT '0',
        `unit_reward` decimal(10,2) NOT NULL DEFAULT '0.00',
        `reward_money` decimal(10,2) NOT NULL DEFAULT '0.00',
        `claimed` tinyint(1) NOT NULL DEFAULT '0',
        `claimed_at` int(11) NOT NULL DEFAULT '0',
        `created_at` int(11) NOT NULL,
        `updated_at` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uid_task_date` (`uid`,`task_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$impression}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL,
        `task_date` date NOT NULL,
        `pubid` int(11) NOT NULL,
        `token` char(32) NOT NULL,
        `started_at` int(11) NOT NULL,
        `eligible_at` int(11) NOT NULL,
        `completed_at` int(11) NOT NULL DEFAULT '0',
        `status` varchar(16) NOT NULL DEFAULT 'pending',
        PRIMARY KEY (`id`),
        UNIQUE KEY `token` (`token`),
        KEY `uid_date_status` (`uid`,`task_date`,`status`),
        UNIQUE KEY `uid_date_pubid` (`uid`,`task_date`,`pubid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$promoReward}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL,
        `from_uid` int(11) NOT NULL,
        `level` tinyint(1) NOT NULL,
        `reward_date` date NOT NULL,
        `is_vip` tinyint(1) NOT NULL DEFAULT '0',
        `reward_money` decimal(10,2) NOT NULL DEFAULT '0.00',
        `status` varchar(16) NOT NULL DEFAULT 'processing',
        `created_at` int(11) NOT NULL,
        `updated_at` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `daily_reward` (`uid`,`from_uid`,`level`,`reward_date`),
        KEY `uid_status` (`uid`,`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$supportCache}` (
        `uid` int(11) NOT NULL,
        `valid_count` int(11) NOT NULL DEFAULT '0',
        `last_calc_time` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$supportReward}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL,
        `reward_count` int(11) NOT NULL,
        `reward_money` decimal(10,2) NOT NULL,
        `dateline` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `uid` (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$supportClaim}` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `uid` int(11) NOT NULL,
        `reward_count` int(11) NOT NULL,
        `reward_money` decimal(10,2) NOT NULL,
        `status` varchar(16) NOT NULL DEFAULT 'processing',
        `created_at` int(11) NOT NULL,
        `updated_at` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uid_reward_count` (`uid`,`reward_count`),
        KEY `uid_status` (`uid`,`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    DB::query("CREATE TABLE IF NOT EXISTS `{$userStats}` (
        `uid` int(11) NOT NULL,
        `completed_ads` int(11) unsigned NOT NULL DEFAULT '0',
        `withdraw_spent_ads` int(11) unsigned NOT NULL DEFAULT '0',
        `created_at` int(11) NOT NULL DEFAULT '0',
        `updated_at` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`uid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function _tgb_task_get_progress($uid, $username, $config) {
    $date = dgmdate(TIMESTAMP, 'Y-m-d');
    $row = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND task_date=%s', array('view_ad_task_progress', $uid, $date));
    if (!$row) {
        $isVip = _tgb_task_is_vip($uid);
        $target = $isVip ? intval($config['vip_ad_count']) : intval($config['regular_ad_count']);
        $unit = round(floatval($config['unit_reward']), 2);
        DB::insert('view_ad_task_progress', array(
            'uid' => $uid,
            'task_date' => $date,
            'is_vip' => $isVip ? 1 : 0,
            'target_count' => $target,
            'viewed_count' => 0,
            'unit_reward' => $unit,
            'reward_money' => round($target * $unit, 2),
            'claimed' => 0,
            'claimed_at' => 0,
            'created_at' => TIMESTAMP,
            'updated_at' => TIMESTAMP,
        ));
        $row = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND task_date=%s', array('view_ad_task_progress', $uid, $date));
    }
    if ($row && intval($row['claimed']) === 0) {
        $isVip = _tgb_task_is_vip($uid);
        $target = $isVip ? intval($config['vip_ad_count']) : intval($config['regular_ad_count']);
        $unit = round(floatval($config['unit_reward']), 2);
        $reward = round($target * $unit, 2);
        if (intval($row['is_vip']) !== ($isVip ? 1 : 0)
            || intval($row['target_count']) !== $target
            || round(floatval($row['unit_reward']), 2) !== $unit
            || round(floatval($row['reward_money']), 2) !== $reward) {
            DB::update('view_ad_task_progress', array(
                'is_vip' => $isVip ? 1 : 0,
                'target_count' => $target,
                'unit_reward' => $unit,
                'reward_money' => $reward,
                'updated_at' => TIMESTAMP,
            ), "uid={$uid} AND task_date='" . addslashes($date) . "' AND claimed=0");
            $row = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND task_date=%s', array('view_ad_task_progress', $uid, $date));
        }
    }
    _tgb_task_ensure_wallet($uid, $username);
    return $row;
}

function _tgb_task_status_payload($uid, $username, $config) {
    $task = _tgb_task_get_progress($uid, $username, $config);
    $inviteCampaign = _tgb_task_invite_campaign(dgmdate(TIMESTAMP, 'Y-m-d'), $config);
    $balance = DB::result_first('SELECT money FROM %t WHERE uid=%d', array('tb_cus_xiguahh_user', $uid));
    $viewed = intval($task['viewed_count']);
    $target = intval($task['target_count']);
    return array(
        'code' => 0,
        'data' => array(
            'is_vip' => intval($task['is_vip']) === 1,
            'viewed_count' => $viewed,
            'target_count' => $target,
            'unit_reward' => number_format($task['unit_reward'], 2, '.', ''),
            'reward_money' => number_format($task['reward_money'], 2, '.', ''),
            'claimed' => intval($task['claimed']) === 1,
            'payout_pending' => intval($task['claimed']) === 2,
            'can_claim' => intval($task['claimed']) === 0 && $viewed >= $target,
            'balance' => number_format($balance, 2, '.', ''),
            'countdown_seconds' => intval($config['countdown_seconds']),
            'config' => array(
                'regular_ad_count' => intval($config['regular_ad_count']),
                'vip_ad_count' => intval($config['vip_ad_count']),
                'unit_reward' => number_format($config['unit_reward'], 2, '.', ''),
                'regular_reward' => number_format($config['regular_ad_count'] * $config['unit_reward'], 2, '.', ''),
                'vip_reward' => number_format($config['vip_ad_count'] * $config['unit_reward'], 2, '.', ''),
                'upgrade_extra_reward' => number_format(($config['vip_ad_count'] - $config['regular_ad_count']) * $config['unit_reward'], 2, '.', ''),
                'direct_regular_reward' => number_format($inviteCampaign['direct_regular_reward'], 2, '.', ''),
                'indirect_regular_reward' => number_format($inviteCampaign['indirect_regular_reward'], 2, '.', ''),
                'direct_vip_reward' => number_format($inviteCampaign['direct_vip_reward'], 2, '.', ''),
                'indirect_vip_reward' => number_format($inviteCampaign['indirect_vip_reward'], 2, '.', ''),
                'invite_campaign_active' => $inviteCampaign['active'],
                'invite_campaign_period' => _tgb_task_campaign_period($inviteCampaign),
            ),
        ),
    );
}

function _tgb_task_project_payload($row) {
    $rawImages = dunserialize($row['imglist']);
    $images = array();
    if (is_array($rawImages)) {
        foreach ($rawImages as $candidate) {
            if (!is_string($candidate)) continue;
            $candidate = trim($candidate);
            if (!preg_match('#^(https://|/|data/attachment/)#i', $candidate)) continue;
            if (!in_array($candidate, $images, true)) $images[] = $candidate;
        }
    }
    return array(
        'id' => intval($row['id']),
        'title' => trim(strip_tags($row['title'] ? $row['title'] : $row['description'])),
        'description' => trim(strip_tags($row['description'])),
        'image' => isset($images[0]) ? $images[0] : '',
        'images' => $images,
        'url' => 'plugin.php?id=xigua_hb&ac=view&pubid=' . intval($row['id']),
        'priority_label' => $row['priority'] >= 4 ? '超级头条' : ($row['priority'] >= 3 ? '头条推荐' : ($row['priority'] >= 2 ? '置顶推荐' : '精选项目')),
    );
}

function _tgb_task_pick_project($uid, $date) {
    $now = TIMESTAMP;
    $rows = DB::fetch_all("SELECT p.id,p.title,p.description,p.imglist,
        CASE
            WHEN st.pubid IS NOT NULL AND tt.pubid IS NOT NULL THEN 4
            WHEN tt.pubid IS NOT NULL THEN 3
            WHEN p.dig_endts>%d THEN 2
            ELSE 1
        END AS priority
        FROM %t p
        LEFT JOIN (SELECT pubid,MAX(endtime) endtime FROM %t WHERE endtime>%d GROUP BY pubid) st ON st.pubid=p.id
        LEFT JOIN (SELECT pubid,MAX(endtime) endtime FROM %t WHERE endtime>%d GROUP BY pubid) tt ON tt.pubid=p.id
        WHERE p.display=1 AND p.recycle=0 AND p.endts>%d
        ORDER BY priority DESC, CRC32(CONCAT(p.id,%s)) ASC
        LIMIT 120", array($now, 'xigua_hb_pub', 'tb_super_toutiao', $now, 'tb_toutiao', $now, $now, $uid . $date));
    if (!$rows) return null;
    $seenRows = DB::fetch_all('SELECT pubid FROM %t WHERE uid=%d AND task_date=%s AND status=%s', array('view_ad_task_impression', $uid, $date, 'completed'));
    $seen = array();
    foreach ($seenRows as $item) $seen[intval($item['pubid'])] = true;
    foreach ($rows as $row) {
        if (!isset($seen[intval($row['id'])])) return $row;
    }
    return null;
}

function _tgb_task_get_impression_project($impression) {
    $now = TIMESTAMP;
    return DB::fetch_first("SELECT p.id,p.title,p.description,p.imglist,
        CASE WHEN st.pubid IS NOT NULL AND tt.pubid IS NOT NULL THEN 4
             WHEN tt.pubid IS NOT NULL THEN 3
             WHEN p.dig_endts>%d THEN 2 ELSE 1 END AS priority
        FROM %t p
        LEFT JOIN (SELECT pubid,MAX(endtime) endtime FROM %t WHERE endtime>%d GROUP BY pubid) st ON st.pubid=p.id
        LEFT JOIN (SELECT pubid,MAX(endtime) endtime FROM %t WHERE endtime>%d GROUP BY pubid) tt ON tt.pubid=p.id
        WHERE p.id=%d AND p.display=1 AND p.recycle=0 AND p.endts>%d LIMIT 1", array($now, 'xigua_hb_pub', 'tb_super_toutiao', $now, 'tb_toutiao', $now, intval($impression['pubid']), $now));
}

_tgb_task_create_tables();
$submodac = isset($_GET['submodac']) ? trim($_GET['submodac']) : '';

if ($submodac === 'status') {
    _tgb_task_json(_tgb_task_status_payload($uid, $username, $taskConfig));
}

if ($submodac === 'next_ad') {
    _tgb_task_require_post();
    $task = _tgb_task_get_progress($uid, $username, $taskConfig);
    if ($task['claimed'] || intval($task['viewed_count']) >= intval($task['target_count'])) {
        _tgb_task_json(array('code' => -2, 'msg' => '今日广告任务已完成'));
    }
    $date = $task['task_date'];
    $process = 'tgb_ad_next_' . $uid . '_' . str_replace('-', '', $date);
    if (discuz_process::islocked($process, 5)) _tgb_task_json(array('code' => -1, 'msg' => '广告正在加载，请稍候'));
    DB::query('DELETE FROM %t WHERE uid=%d AND task_date=%s AND status<>%s', array('view_ad_task_impression', $uid, $date, 'completed'));
    $project = _tgb_task_pick_project($uid, $date);
    if (!$project) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -3, 'msg' => '可展示的广告已看完，更多广告接入中，请稍后再来试试！'));
    }
    try {
        $token = bin2hex(random_bytes(16));
    } catch (Exception $e) {
        $token = md5(uniqid($uid, true));
    }
    $impressionId = DB::insert('view_ad_task_impression', array(
        'uid' => $uid,
        'task_date' => $date,
        'pubid' => intval($project['id']),
        'token' => $token,
        'started_at' => TIMESTAMP,
        'eligible_at' => TIMESTAMP + intval($taskConfig['countdown_seconds']),
        'completed_at' => 0,
        'status' => 'pending',
    ), true);
    $impression = DB::fetch_first('SELECT * FROM %t WHERE id=%d', array('view_ad_task_impression', $impressionId));
    discuz_process::unlock($process);
    _tgb_task_json(array(
        'code' => 0,
        'data' => array(
            'token' => $impression['token'],
            'server_time' => TIMESTAMP,
            'eligible_at' => intval($impression['eligible_at']),
            'countdown_seconds' => intval($taskConfig['countdown_seconds']),
            'project' => _tgb_task_project_payload($project),
        ),
    ));
}

if ($submodac === 'abandon_ad') {
    _tgb_task_require_post();
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    if (preg_match('/^[a-f0-9]{32}$/', $token)) {
        DB::query('DELETE FROM %t WHERE token=%s AND uid=%d AND status=%s', array('view_ad_task_impression', $token, $uid, 'pending'));
    }
    _tgb_task_json(array('code' => 0));
}

if ($submodac === 'complete_ad') {
    _tgb_task_require_post();
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) _tgb_task_json(array('code' => -1, 'msg' => '广告凭证无效'));
    $process = 'tgb_ad_complete_' . $uid . '_' . $token;
    if (discuz_process::islocked($process, 5)) _tgb_task_json(array('code' => -1, 'msg' => '正在确认，请稍候'));
    $impression = DB::fetch_first('SELECT * FROM %t WHERE token=%s AND uid=%d LIMIT 1', array('view_ad_task_impression', $token, $uid));
    if (!$impression || $impression['task_date'] !== dgmdate(TIMESTAMP, 'Y-m-d')) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '广告凭证已失效'));
    }
    if ($impression['status'] === 'completed') {
        discuz_process::unlock($process);
        _tgb_task_json(_tgb_task_status_payload($uid, $username, $taskConfig));
    }
    if (!_tgb_task_get_impression_project($impression)) {
        DB::delete('view_ad_task_impression', 'id=' . intval($impression['id']) . " AND status='pending'");
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '该项目已下架，请重新选择广告'));
    }
    if (TIMESTAMP < intval($impression['eligible_at'])) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -4, 'msg' => '观看时间不足', 'remaining' => intval($impression['eligible_at']) - TIMESTAMP));
    }
    $didComplete = false;
    DB::query('START TRANSACTION');
    DB::query('UPDATE %t SET status=%s,completed_at=%d WHERE id=%d AND status=%s', array('view_ad_task_impression', 'completed', TIMESTAMP, intval($impression['id']), 'pending'));
    if (DB::affected_rows() > 0) {
        $didComplete = true;
        DB::query('UPDATE %t SET viewed_count=LEAST(target_count,viewed_count+1),updated_at=%d WHERE uid=%d AND task_date=%s', array('view_ad_task_progress', TIMESTAMP, $uid, $impression['task_date']));
        DB::query(
            'INSERT INTO %t (uid,completed_ads,withdraw_spent_ads,created_at,updated_at) VALUES (%d,1,0,%d,%d) ON DUPLICATE KEY UPDATE completed_ads=completed_ads+1,updated_at=VALUES(updated_at)',
            array('view_ad_user_stats', $uid, TIMESTAMP, TIMESTAMP)
        );
    }
    DB::query('COMMIT');
    if ($didComplete) {
        // xigua_hb_pub is MyISAM. Keep this update outside the InnoDB
        // transaction to satisfy GTID consistency and never block task credit.
        $viewIncrement = mt_rand(2, 5);
        DB::query(
            'UPDATE %t SET views=COALESCE(views,0)+%d WHERE id=%d',
            array('xigua_hb_pub', $viewIncrement, intval($impression['pubid'])),
            true
        );
    }
    discuz_process::unlock($process);
    _tgb_task_json(_tgb_task_status_payload($uid, $username, $taskConfig));
}

if ($submodac === 'claim') {
    _tgb_task_require_post();
    $date = dgmdate(TIMESTAMP, 'Y-m-d');
    $process = 'tgb_ad_claim_' . $uid . '_' . dgmdate(TIMESTAMP, 'Ymd');
    if (discuz_process::islocked($process, 8)) _tgb_task_json(array('code' => -1, 'msg' => '奖励正在发放，请稍候'));
    $task = DB::fetch_first('SELECT * FROM %t WHERE uid=%d AND task_date=%s', array('view_ad_task_progress', $uid, $date));
    if (!$task || intval($task['viewed_count']) < intval($task['target_count'])) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -2, 'msg' => '请先完成今日全部广告'));
    }
    if (intval($task['claimed']) === 1) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '今日奖励已经领取'));
    }
    if (intval($task['claimed']) === 2) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励到账处理中，请勿重复领取'));
    }
    _tgb_task_ensure_wallet($uid, $username);
    DB::query('UPDATE %t SET claimed=2,claimed_at=%d,updated_at=%d WHERE id=%d AND claimed=0', array('view_ad_task_progress', TIMESTAMP, TIMESTAMP, intval($task['id'])));
    if (DB::affected_rows() !== 1) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励状态已更新，请刷新查看'));
    }
    if (!_tgb_task_add_sign_money($uid, $task['reward_money'])) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励发放异常，已进入人工核对，请勿重复领取'));
    }
    $signLogId = DB::insert('view_sign_log', array(
        'uid' => $uid,
        'sign_date' => $date,
        'is_vip' => intval($task['is_vip']),
        'reward_money' => $task['reward_money'],
        'dateline' => TIMESTAMP,
    ), true);
    if (!$signLogId) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励已进入人工核对，请勿重复领取'));
    }
    $rewardDetailId = DB::insert('view_sign_reward_detail', array(
        'uid' => $uid,
        'sign_log_id' => $signLogId,
        'reward_money' => $task['reward_money'],
        'dateline' => TIMESTAMP,
    ), true);
    if (!$rewardDetailId) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励明细已进入人工核对，请勿重复领取'));
    }
    _tgb_support_invalidate_upline_cache($uid);
    _tgb_task_give_promo($uid, intval($task['is_vip']) === 1, $date, $taskConfig);
    DB::query('UPDATE %t SET claimed=1,updated_at=%d WHERE id=%d AND claimed=2', array('view_ad_task_progress', TIMESTAMP, intval($task['id'])));
    discuz_process::unlock($process);
    $payload = _tgb_task_status_payload($uid, $username, $taskConfig);
    $payload['msg'] = '奖励已到账';
    _tgb_task_json($payload);
}

if ($submodac === 'records') {
    $type = isset($_GET['type']) && $_GET['type'] === 'promo' ? 'promo' : 'task';
    $page = max(1, intval($_GET['page']));
    $start = ($page - 1) * 10;
    $records = array();
    if ($type === 'promo') {
        $rows = DB::fetch_all('SELECT level,reward_money,from_uid,dateline,note FROM %t WHERE uid=%d ORDER BY id DESC LIMIT %d,11', array('view_sign_promo_log', $uid, $start));
        $hasMore = count($rows) > 10;
        if ($hasMore) $rows = array_slice($rows, 0, 10);
        foreach ($rows as $row) {
            $records[] = array(
                'title' => ($row['level'] == 1 ? '一级' : '二级') . '好友任务奖励',
                'money' => number_format($row['reward_money'], 2, '.', ''),
                'note' => $row['note'],
                'time' => dgmdate($row['dateline'], 'Y-m-d H:i'),
            );
        }
    } else {
        $rows = DB::fetch_all('SELECT reward_money,dateline FROM %t WHERE uid=%d ORDER BY id DESC LIMIT %d,11', array('view_sign_reward_detail', $uid, $start));
        $hasMore = count($rows) > 10;
        if ($hasMore) $rows = array_slice($rows, 0, 10);
        foreach ($rows as $row) {
            $records[] = array(
                'title' => '每日广告任务奖励',
                'money' => number_format($row['reward_money'], 2, '.', ''),
                'note' => '已发放到钱包',
                'time' => dgmdate($row['dateline'], 'Y-m-d H:i'),
            );
        }
    }
    _tgb_task_json(array('code' => 0, 'data' => $records, 'has_more' => $hasMore, 'next_page' => $hasMore ? $page + 1 : 0));
}

if ($submodac === 'support_info') {
    $validCount = _tgb_support_valid_count($uid);
    $receivedRows = DB::fetch_all('SELECT reward_count FROM %t WHERE uid=%d', array('view_invite_activity_reward', $uid));
    $claimRows = DB::fetch_all('SELECT reward_count,status FROM %t WHERE uid=%d', array('view_ad_support_claim', $uid));
    $received = array();
    $processing = array();
    foreach ($receivedRows as $row) $received[intval($row['reward_count'])] = true;
    foreach ($claimRows as $row) {
        $count = intval($row['reward_count']);
        if ($row['status'] === 'paid') $received[$count] = true;
        if ($row['status'] === 'processing' || $row['status'] === 'review') $processing[$count] = true;
    }
    $rewards = array();
    foreach ($taskConfig['support_rewards'] as $count => $money) {
        $count = intval($count);
        $rewards[] = array(
            'count' => $count,
            'money' => number_format($money, 2, '.', ''),
            'received' => isset($received[$count]),
            'processing' => isset($processing[$count]),
            'can_claim' => $validCount >= $count && !isset($received[$count]) && !isset($processing[$count]),
        );
    }
    _tgb_task_json(array('code' => 0, 'data' => array('valid_count' => $validCount, 'rewards' => $rewards)));
}

if ($submodac === 'support_claim') {
    _tgb_task_require_post();
    $count = isset($_POST['count']) ? intval($_POST['count']) : 0;
    if (!isset($taskConfig['support_rewards'][$count])) _tgb_task_json(array('code' => -1, 'msg' => '该奖励档位不存在'));
    $process = 'tgb_support_claim_' . $uid . '_' . $count;
    if (discuz_process::islocked($process, 8)) _tgb_task_json(array('code' => -1, 'msg' => '奖励正在核对，请稍候'));
    if (DB::result_first('SELECT id FROM %t WHERE uid=%d AND reward_count=%d LIMIT 1', array('view_invite_activity_reward', $uid, $count))) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '该档奖励已经领取'));
    }
    if (_tgb_support_valid_count($uid) < $count) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '有效邀请人数还未达到该档位'));
    }
    $money = round(floatval($taskConfig['support_rewards'][$count]), 2);
    DB::query(
        'INSERT IGNORE INTO %t (uid,reward_count,reward_money,status,created_at,updated_at) VALUES (%d,%d,%f,%s,%d,%d)',
        array('view_ad_support_claim', $uid, $count, $money, 'processing', TIMESTAMP, TIMESTAMP)
    );
    if (DB::affected_rows() !== 1) {
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '该档奖励已提交，请勿重复领取'));
    }
    if (!_tgb_task_add_promo_money($uid, $money, "官方扶持：有效直推{$count}人奖励")) {
        DB::update('view_ad_support_claim', array('status' => 'review', 'updated_at' => TIMESTAMP), 'uid=' . $uid . ' AND reward_count=' . $count);
        discuz_process::unlock($process);
        _tgb_task_json(array('code' => -1, 'msg' => '奖励进入人工核对，请勿重复领取'));
    }
    DB::insert('view_invite_activity_reward', array(
        'uid' => $uid,
        'reward_count' => $count,
        'reward_money' => $money,
        'dateline' => TIMESTAMP,
    ));
    DB::update('view_ad_support_claim', array('status' => 'paid', 'updated_at' => TIMESTAMP), 'uid=' . $uid . ' AND reward_count=' . $count);
    discuz_process::unlock($process);
    _tgb_task_json(array('code' => 0, 'msg' => '官方扶持奖励已到账', 'money' => number_format($money, 2, '.', '')));
}

$task = _tgb_task_get_progress($uid, $username, $taskConfig);
$wallet = _tgb_task_ensure_wallet($uid, $username);
$formhash = formhash();
$isVip = intval($task['is_vip']) === 1;
$inviteCampaign = _tgb_task_invite_campaign(dgmdate(TIMESTAMP, 'Y-m-d'), $taskConfig);
$inviteCampaignPeriod = _tgb_task_campaign_period($inviteCampaign);
$tgbAndroidApp = strpos(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', 'TuiGuangBaoAndroid/') !== false;
?>
<!doctype html>
<html class="<?php echo $tgbAndroidApp ? 'tgb-android-app' : ''; ?>" lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <meta name="theme-color" content="#f4f7fb">
    <meta name="color-scheme" content="light">
    <title>推广宝 · 每日广告任务</title>
    <link href="source/plugin/xigua_hb/static/tgb-r02/vendor/remixicon-3.5.0/remixicon.css?v=20260726-r02" rel="stylesheet">
    <link href="source/plugin/view/static/tgb-ad-task-v1.css?v=20260729-5" rel="stylesheet">
</head>
<body>
<header class="task-header">
    <span class="task-header-spacer"></span>
    <h1>每日广告任务</h1>
    <button class="icon-button" id="recordsButton" aria-label="奖励明细"><i class="ri-file-list-3-line"></i></button>
</header>

<main class="task-page">
    <section class="earnings-hero <?php echo $isVip ? 'vip' : 'regular'; ?>">
        <div class="hero-topline"><span><?php echo $isVip ? '推广宝会员专属任务' : '普通会员任务'; ?></span><span class="hero-badge"><i class="<?php echo $isVip ? 'ri-vip-crown-2-fill' : 'ri-flashlight-fill'; ?>"></i> <?php echo $isVip ? '会员已生效' : '每日可领'; ?></span></div>
        <div class="hero-earning-label" id="heroEarningLabel">今日完成<?php echo intval($task['target_count']); ?>个广告，可赚</div>
        <div class="hero-money"><small>¥</small><span id="taskReward"><?php echo number_format($task['reward_money'], 2); ?></span></div>
        <div class="hero-meta"><span id="heroUnitReward">每条 ¥<?php echo number_format($task['unit_reward'], 2); ?></span><span>奖励发放至钱包</span></div>
        <?php if ($isVip): ?>
        <div class="hero-member-state"><i class="ri-checkbox-circle-fill"></i><span><strong>推广宝会员权益已解锁</strong><small id="vipBenefitText">每天可看<?php echo intval($taskConfig['vip_ad_count']); ?>条，完成最高领<?php echo number_format($taskConfig['vip_ad_count'] * $taskConfig['unit_reward'], 2); ?>元</small></span></div>
        <?php else: ?>
        <a class="hero-upgrade" href="plugin.php?id=xigua_hb&ac=vip"><span><strong id="upgradeExtraText">开通会员，每天多赚 ¥<?php echo number_format(($taskConfig['vip_ad_count'] - $taskConfig['regular_ad_count']) * $taskConfig['unit_reward'], 2); ?></strong><small id="vipTargetText">每日广告任务提升至<?php echo intval($taskConfig['vip_ad_count']); ?>条</small></span><b>立即开通 <i class="ri-arrow-right-s-line"></i></b></a>
        <?php endif; ?>
    </section>
    <button class="support-band" id="supportButton" type="button">
        <span><i class="ri-shield-star-line"></i></span>
        <span><strong>官方888元现金扶持奖励</strong><small>实名直推连续完成3天，达标可手动领取现金</small></span>
        <i class="ri-arrow-right-s-line"></i>
    </button>
    <section class="task-panel" aria-labelledby="todayTaskTitle">
        <div class="section-heading">
            <div><span class="section-kicker">TODAY</span><h2 id="todayTaskTitle">今日观看任务</h2></div>
            <div class="progress-count"><strong id="viewedCount"><?php echo intval($task['viewed_count']); ?></strong>/<span id="targetCount"><?php echo intval($task['target_count']); ?></span></div>
        </div>
        <div class="progress-track"><span id="progressBar"></span></div>
        <div class="task-facts">
            <div><i class="ri-time-line"></i><span>每条广告</span><strong id="taskCountdownText"><?php echo intval($taskConfig['countdown_seconds']); ?>秒</strong></div>
            <div><i class="ri-coins-line"></i><span>单条价值</span><strong id="taskUnitReward">¥<?php echo number_format($task['unit_reward'], 2); ?></strong></div>
        </div>
        <button class="task-primary" id="taskMainButton" type="button"><span>加载任务状态</span><i class="ri-arrow-right-line"></i></button>
        <p class="task-hint" id="taskHint">完整观看倒计时结束后，才计入一条有效广告</p>
        <button class="task-text-button" id="rulesButton" type="button"><i class="ri-question-line"></i> 查看任务与推广规则</button>
    </section>

    <section class="invite-section <?php echo $inviteCampaign['active'] ? 'campaign-active' : ''; ?>">
        <div class="invite-campaign-strip">
            <span><i class="ri-fire-fill"></i> <?php echo $inviteCampaign['active'] ? '7天推广冲刺计划' : '邀请收益计划'; ?></span>
            <b><?php echo $inviteCampaign['active'] ? $inviteCampaignPeriod . ' 限时' : '每天持续收益'; ?></b>
        </div>
        <div class="invite-impact">
            <div class="invite-copy">
                <span class="section-kicker">INVITE & EARN</span>
                <h2>好友每天看广告<br>你每天都能拿收益</h2>
                <p><b>不是一次性奖励：</b>好友完成实名并领取当天广告奖励，你的推广收益自动到账。</p>
            </div>
            <span class="invite-impact-icon"><i class="ri-user-add-line"></i></span>
        </div>
        <div class="invite-reward-grid">
            <div class="regular featured"><span>每位一级好友完成任务</span><strong id="directRegularReward">+¥<?php echo number_format($inviteCampaign['direct_regular_reward'], 2); ?><em>/天</em></strong><?php if ($inviteCampaign['active']): ?><small>活动前 ¥<?php echo number_format($taskConfig['direct_regular_reward'], 2); ?>，现在每天多得 ¥<?php echo number_format($inviteCampaign['direct_regular_reward'] - $taskConfig['direct_regular_reward'], 2); ?></small><?php endif; ?></div>
            <div class="regular"><span>二级好友 <em>每日</em></span><strong id="indirectRegularReward">+¥<?php echo number_format($inviteCampaign['indirect_regular_reward'], 2); ?></strong><?php if ($inviteCampaign['active']): ?><small>原 ¥<?php echo number_format($taskConfig['indirect_regular_reward'], 2); ?></small><?php endif; ?></div>
            <div class="vip"><span>一级好友是会员 <em>每日</em></span><strong id="directVipReward">+¥<?php echo number_format($inviteCampaign['direct_vip_reward'], 2); ?></strong><small>会员好友高收益</small></div>
            <div class="vip"><span>二级好友是会员 <em>每日</em></span><strong id="indirectVipReward">+¥<?php echo number_format($inviteCampaign['indirect_vip_reward'], 2); ?></strong><small>会员好友高收益</small></div>
        </div>
        <div class="invite-income-example"><span><i class="ri-line-chart-fill"></i> 例如邀请10位一级普通好友</span><strong>每天最高可得 ¥<?php echo number_format($inviteCampaign['direct_regular_reward'] * 10, 2); ?></strong><small>需10位好友当天均完成实名广告任务</small></div>
        <div class="invite-support-tip"><i class="ri-award-line"></i><span><strong>邀请收益还能叠加官方扶持</strong><small>实名直推连续完成3天任务计为有效用户，八档累计最高888元</small></span></div>
        <a class="invite-button" href="plugin.php?id=xigua_hh&ac=invite"><i class="ri-user-add-line"></i> 趁加码期，立即邀请好友</a>
    </section>

    <a class="qq-group-card" href="https://qm.qq.com/q/CQCxbFkGME">
        <span class="qq-group-icon"><i class="ri-qq-fill"></i></span>
        <span class="qq-group-copy"><strong>加入官方QQ群</strong><small>群号 873512744 · 活动通知 · 互助交流</small></span>
        <i class="ri-arrow-right-s-line"></i>
    </a>

</main>

<nav class="task-bottom-nav" aria-label="底部导航">
    <a href="plugin.php?id=xigua_hb"><i class="ri-home-line"></i><span>首页</span></a>
    <a href="plugin.php?id=view&modac=sign" class="active"><i class="ri-calendar-check-line"></i><span>发现</span></a>
    <a href="plugin.php?id=xigua_hb&ac=my"><i class="ri-user-line"></i><span>我的</span></a>
</nav>

<div class="task-modal" id="confirmModal" aria-hidden="true">
    <div class="dialog-card" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
        <button class="modal-close" data-close="confirmModal" aria-label="关闭"><i class="ri-close-line"></i></button>
        <span class="dialog-icon"><i class="ri-play-circle-line"></i></span>
        <h3 id="confirmTitle">开始观看广告？</h3>
        <p id="confirmText">完整观看<?php echo intval($taskConfig['countdown_seconds']); ?>秒即可完成1条，每条价值<?php echo number_format($taskConfig['unit_reward'], 2); ?>元。</p>
        <div class="dialog-actions"><button class="secondary" data-close="confirmModal">暂不观看</button><button class="primary" id="confirmWatchButton">确认观看</button></div>
    </div>
</div>

<div class="task-modal ad-layer" id="adModal" aria-hidden="true">
    <article class="reward-ad" role="dialog" aria-modal="true" aria-labelledby="adTitle">
        <div class="ad-toolbar">
            <span class="ad-label" id="adPriority">精选项目</span>
            <span class="ad-countdown"><i class="ri-time-line"></i><strong id="adSeconds"><?php echo intval($taskConfig['countdown_seconds']); ?></strong>秒</span>
            <button id="adCloseButton" aria-label="关闭广告"><i class="ri-close-line"></i></button>
        </div>
        <div class="ad-scroll-body" id="adScrollBody">
            <div class="ad-media" id="adMedia"><div class="ad-placeholder"><i class="ri-image-line"></i></div></div>
            <div class="ad-content">
                <span class="ad-sponsored">推广宝项目推荐</span>
                <h3 id="adTitle">正在加载...</h3>
                <p id="adDescription"></p>
            </div>
        </div>
        <div class="ad-footer">
            <div><span>本条奖励</span><strong id="adUnitReward">+¥<?php echo number_format($task['unit_reward'], 2); ?></strong></div>
            <button id="adContinueButton" disabled>观看中，请保持页面可见</button>
        </div>
    </article>
</div>

<div class="task-modal priority-modal" id="earlyCloseModal" aria-hidden="true">
    <div class="dialog-card warning-dialog" role="dialog" aria-modal="true" aria-labelledby="earlyCloseTitle">
        <span class="dialog-icon warning"><i class="ri-time-line"></i></span>
        <h3 id="earlyCloseTitle">本条广告还未完成</h3>
        <p>现在退出不会计入观看次数；下次开始任务时，这条广告仍可能再次展示。</p>
        <div class="dialog-actions"><button class="secondary" id="keepWatchingButton">继续观看</button><button class="danger" id="confirmEarlyCloseButton">确认退出</button></div>
    </div>
</div>

<div class="task-modal image-preview-layer" id="imagePreviewModal" aria-hidden="true">
    <div class="image-preview-card" role="dialog" aria-modal="true" aria-label="项目图片预览">
        <div class="image-preview-head"><span id="previewImageCount">1 / 1</span><button type="button" id="previewCloseButton" aria-label="关闭图片预览"><i class="ri-close-line"></i></button></div>
        <div class="image-preview-stage" id="previewImageStage"><div class="image-preview-track" id="previewImageTrack"></div><span class="image-preview-tip"><i class="ri-arrow-left-right-line"></i> 左右滑动 · 点击图片或空白处关闭</span></div>
    </div>
</div>

<div class="task-modal invite-campaign-modal" id="inviteCampaignModal" aria-hidden="true">
    <div class="invite-campaign-dialog" role="dialog" aria-modal="true" aria-labelledby="inviteCampaignTitle">
        <button class="modal-close invite-campaign-close" data-close="inviteCampaignModal" aria-label="关闭邀请活动"><i class="ri-close-line"></i></button>
        <div class="invite-campaign-scroll">
        <div class="campaign-visual">
            <span class="campaign-date"><i class="ri-fire-fill"></i> <?php echo $inviteCampaign['active'] ? $inviteCampaignPeriod . ' 限时冲刺' : '邀请好友奖励计划'; ?></span>
            <span class="campaign-icon"><i class="ri-user-add-line"></i></span>
            <p>推广收益限时加码</p>
            <h3 id="inviteCampaignTitle">现在邀请好友<br>每天持续拿收益</h3>
            <div class="campaign-hero-reward"><span>每位一级好友看广告</span><strong>¥<?php echo number_format($inviteCampaign['direct_regular_reward'], 2); ?><small>/天</small></strong></div>
            <small>好友完成实名并领取当天广告奖励，推广收益自动进入你的钱包。</small>
        </div>
        <div class="campaign-body">
            <div class="campaign-income-example"><span>邀请10位一级普通好友<br><small>当天全部完成任务</small></span><strong>¥<?php echo number_format($inviteCampaign['direct_regular_reward'] * 10, 2); ?><small>/天</small></strong></div>
            <?php if ($inviteCampaign['active']): ?><div class="campaign-boost"><i class="ri-arrow-up-circle-fill"></i><span>活动期间普通好友奖励已提升，<b>一级加码60%</b>、<b>二级加码约33%</b></span></div><?php endif; ?>
            <div class="campaign-rewards">
                <div><span>一级普通好友</span><strong>¥<?php echo number_format($inviteCampaign['direct_regular_reward'], 2); ?><small>/天</small></strong><?php if ($inviteCampaign['active']): ?><del>原 ¥<?php echo number_format($taskConfig['direct_regular_reward'], 2); ?></del><?php endif; ?></div>
                <div><span>二级普通好友</span><strong>¥<?php echo number_format($inviteCampaign['indirect_regular_reward'], 2); ?><small>/天</small></strong><?php if ($inviteCampaign['active']): ?><del>原 ¥<?php echo number_format($taskConfig['indirect_regular_reward'], 2); ?></del><?php endif; ?></div>
            </div>
            <div class="campaign-vip-line"><i class="ri-vip-crown-2-fill"></i><span>好友是推广宝会员，一级每天<b>¥<?php echo number_format($inviteCampaign['direct_vip_reward'], 2); ?></b>、二级每天<b>¥<?php echo number_format($inviteCampaign['indirect_vip_reward'], 2); ?></b></span></div>
            <div class="campaign-steps">
                <span><b>1</b>邀请好友</span><i class="ri-arrow-right-s-line"></i><span><b>2</b>好友实名做任务</span><i class="ri-arrow-right-s-line"></i><span><b>3</b>奖励自动到账</span>
            </div>
            <div class="campaign-support"><i class="ri-award-fill"></i><span><strong>再叠加最高888元官方扶持</strong><small>直推好友连续完成3天任务计入有效人数，达到档位即可领取</small></span></div>
            <a class="campaign-primary" href="plugin.php?id=xigua_hh&ac=invite"><i class="ri-user-add-line"></i> 立即邀请，抢限时加码</a>
            <button class="campaign-later" type="button" data-close="inviteCampaignModal">稍后再说</button>
        </div>
        </div>
    </div>
</div>

<div class="task-modal priority-modal" id="resultModal" aria-hidden="true">
    <div class="dialog-card result-dialog" role="dialog" aria-modal="true" aria-labelledby="resultTitle">
        <span class="dialog-icon success" id="resultIcon"><i class="ri-checkbox-circle-fill"></i></span>
        <h3 id="resultTitle">奖励已到账</h3>
        <p id="resultMessage">奖励已发放到对应钱包。</p>
        <button class="result-confirm" data-close="resultModal" type="button">我知道了</button>
    </div>
</div>

<div class="task-modal" id="recordsModal" aria-hidden="true">
    <div class="sheet-card records-sheet" role="dialog" aria-modal="true" aria-labelledby="recordsTitle">
        <div class="sheet-head"><div><span class="section-kicker">RECORDS</span><h3 id="recordsTitle">奖励明细</h3></div><button class="modal-close" data-close="recordsModal"><i class="ri-close-line"></i></button></div>
        <div class="record-tabs"><button class="active" data-record-type="task">任务奖励</button><button data-record-type="promo">推广奖励</button></div>
        <div class="record-list" id="recordList"></div>
    </div>
</div>

<div class="task-modal" id="rulesModal" aria-hidden="true">
    <div class="sheet-card rules-sheet" role="dialog" aria-modal="true" aria-labelledby="rulesTitle">
        <div class="sheet-head"><div><span class="section-kicker">RULES</span><h3 id="rulesTitle">广告任务与推广规则</h3></div><button class="modal-close" data-close="rulesModal"><i class="ri-close-line"></i></button></div>
        <div class="rules-list">
            <div><span>01</span><p id="ruleTaskText">普通会员每日观看<?php echo intval($taskConfig['regular_ad_count']); ?>条广告，完成后领取<?php echo number_format($taskConfig['regular_ad_count'] * $taskConfig['unit_reward'], 2); ?>元；推广宝会员每日观看<?php echo intval($taskConfig['vip_ad_count']); ?>条，完成后领取<?php echo number_format($taskConfig['vip_ad_count'] * $taskConfig['unit_reward'], 2); ?>元。</p></div>
            <div><span>02</span><p id="ruleWatchText">每条广告需保持页面可见并完整观看<?php echo intval($taskConfig['countdown_seconds']); ?>秒，提前关闭不计次数；每日奖励只能领取一次。</p></div>
            <div><span>03</span><p id="rulePromoText">好友必须完成实名认证。普通好友完成任务奖励一级<?php echo number_format($inviteCampaign['direct_regular_reward'], 2); ?>元、二级<?php echo number_format($inviteCampaign['indirect_regular_reward'], 2); ?>元；会员好友完成任务奖励一级<?php echo number_format($inviteCampaign['direct_vip_reward'], 2); ?>元、二级<?php echo number_format($inviteCampaign['indirect_vip_reward'], 2); ?>元。<?php echo $inviteCampaign['active'] ? '当前为' . $inviteCampaignPeriod . '限时加码标准。' : ''; ?></p></div>
            <div><span>04</span><p>推广奖励在好友领取每日任务奖励时自动结算到提成账户，无需手动领取。</p></div>
        </div>
        <a class="invite-button" href="plugin.php?id=xigua_hh&ac=invite"><i class="ri-user-add-line"></i> 参与官方扶持计划</a>
    </div>
</div>

<div class="task-modal" id="supportModal" aria-hidden="true">
    <div class="sheet-card support-sheet" role="dialog" aria-modal="true" aria-labelledby="supportTitle">
        <div class="sheet-head"><div><span class="section-kicker">OFFICIAL SUPPORT</span><h3 id="supportTitle">官方扶持奖励</h3></div><button class="modal-close" data-close="supportModal" aria-label="关闭"><i class="ri-close-line"></i></button></div>
        <div class="support-summary"><span><i class="ri-team-line"></i></span><div><small>当前有效直推</small><strong><b id="supportValidCount">--</b> 人</strong></div><a href="plugin.php?id=xigua_hh&ac=invite">继续邀请</a></div>
        <p class="support-rule">直推好友完成实名认证，并连续3天完成每日广告任务，即计为1个有效用户。达到档位后可手动领取，每个档位仅限1次。</p>
        <div class="support-tier-list" id="supportTierList"><div class="record-empty"><i class="ri-loader-4-line spin"></i> 正在核对奖励进度</div></div>
    </div>
</div>

<div class="task-toast" id="taskToast" role="status"></div>

<script>
(function () {
    'use strict';
    var endpoint = 'plugin.php?id=view&modac=sign';
    var formhash = <?php echo json_encode($formhash); ?>;
    var state = null;
    var adToken = '';
    var timer = null;
    var deadline = 0;
    var hiddenAt = 0;
    var adCompleted = false;
    var completingAd = false;
    var currentImages = [];
    var currentImageIndex = 0;
    var previewPointerStartX = 0;
    var previewDidSwipe = false;
    var recordState = { type: 'task', page: 0, hasMore: true, loading: false, requestId: 0 };
    var configuredCountdown = <?php echo intval($taskConfig['countdown_seconds']); ?>;
    var inviteNoticeInterval = 30 * 60 * 1000;
    var inviteNoticeStorageKey = 'tgb_invite_campaign_notice_v3';
    var inviteNoticeShown = false;
    var $ = function (id) { return document.getElementById(id); };

    function escapeHtml(value) {
        var div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }
    function toast(message) {
        var el = $('taskToast');
        el.textContent = message;
        el.classList.add('show');
        window.clearTimeout(el._timer);
        el._timer = window.setTimeout(function () { el.classList.remove('show'); }, 2400);
    }
    function openModal(id) {
        var el = $(id);
        el.classList.add('open');
        el.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }
    function closeModal(id) {
        var el = $(id);
        el.classList.remove('open');
        el.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.task-modal.open')) document.body.classList.remove('modal-open');
    }
    function maybeShowInviteNotice() {
        if (inviteNoticeShown) return;
        if (document.querySelector('.task-modal.open')) {
            window.setTimeout(maybeShowInviteNotice, 3000);
            return;
        }
        var now = Date.now();
        try {
            var lastShown = parseInt(window.localStorage.getItem(inviteNoticeStorageKey), 10) || 0;
            if (now - lastShown < inviteNoticeInterval) return;
            window.localStorage.setItem(inviteNoticeStorageKey, String(now));
        } catch (error) {
            // Local storage may be disabled in privacy mode; still avoid repeating within this page.
        }
        inviteNoticeShown = true;
        openModal('inviteCampaignModal');
    }
    function showResult(title, message, type) {
        $('resultTitle').textContent = title;
        $('resultMessage').textContent = message;
        $('resultIcon').classList.toggle('warning', type === 'warning');
        $('resultIcon').classList.toggle('success', type !== 'warning');
        $('resultIcon').innerHTML = type === 'warning'
            ? '<i class="ri-error-warning-line"></i>'
            : '<i class="ri-checkbox-circle-fill"></i>';
        openModal('resultModal');
    }
    function request(action, data, method) {
        method = method || 'GET';
        var options = { method: method, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        var url = endpoint + '&submodac=' + encodeURIComponent(action);
        if (method === 'POST') {
            var body = new URLSearchParams();
            body.append('formhash', formhash);
            Object.keys(data || {}).forEach(function (key) { body.append(key, data[key]); });
            options.body = body;
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        } else if (data) {
            Object.keys(data).forEach(function (key) { url += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(data[key]); });
        }
        return fetch(url, options).then(function (response) {
            return response.text().then(function (text) {
                if (!response.ok) throw new Error('网络请求失败，请稍后重试');
                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error('服务器确认异常，请点击重新确认');
                }
            });
        });
    }
    function setText(id, value) {
        var element = $(id);
        if (element) element.textContent = value;
    }
    function renderConfig(payload) {
        var config = payload.config || {};
        configuredCountdown = parseInt(payload.countdown_seconds, 10) || configuredCountdown;
        setText('heroUnitReward', '每条 ¥' + payload.unit_reward);
        setText('taskCountdownText', configuredCountdown + '秒');
        setText('taskUnitReward', '¥' + payload.unit_reward);
        setText('adUnitReward', '+¥' + payload.unit_reward);
        if (!adToken) setText('adSeconds', configuredCountdown);
        setText('confirmText', '完整观看' + configuredCountdown + '秒即可完成1条，每条价值' + payload.unit_reward + '元。');
        if (config.vip_ad_count != null) {
            setText('vipBenefitText', '每天可看' + config.vip_ad_count + '条，完成最高领' + config.vip_reward + '元');
            setText('upgradeExtraText', '开通会员，每天多赚 ¥' + config.upgrade_extra_reward);
            setText('vipTargetText', '每日广告任务提升至' + config.vip_ad_count + '条');
            setText('directRegularReward', '+¥' + config.direct_regular_reward);
            setText('indirectRegularReward', '+¥' + config.indirect_regular_reward);
            setText('directVipReward', '+¥' + config.direct_vip_reward);
            setText('indirectVipReward', '+¥' + config.indirect_vip_reward);
            setText('ruleTaskText', '普通会员每日观看' + config.regular_ad_count + '条广告，完成后领取' + config.regular_reward + '元；推广宝会员每日观看' + config.vip_ad_count + '条，完成后领取' + config.vip_reward + '元。');
            setText('ruleWatchText', '每条广告需保持页面可见并完整观看' + configuredCountdown + '秒，提前关闭不计次数；每日奖励只能领取一次。');
            var campaignSuffix = config.invite_campaign_active && config.invite_campaign_period
                ? '当前为' + config.invite_campaign_period + '限时加码标准。'
                : '';
            setText('rulePromoText', '好友必须完成实名认证。普通好友完成任务奖励一级' + config.direct_regular_reward + '元、二级' + config.indirect_regular_reward + '元；会员好友完成任务奖励一级' + config.direct_vip_reward + '元、二级' + config.indirect_vip_reward + '元。' + campaignSuffix);
        }
    }
    function renderStatus(payload) {
        state = payload;
        renderConfig(payload);
        $('viewedCount').textContent = payload.viewed_count;
        $('targetCount').textContent = payload.target_count;
        $('taskReward').textContent = payload.reward_money;
        $('heroEarningLabel').textContent = '今日完成' + payload.target_count + '个广告，可赚';
        var percent = payload.target_count ? Math.min(100, Math.round(payload.viewed_count * 100 / payload.target_count)) : 0;
        $('progressBar').style.width = percent + '%';
        var button = $('taskMainButton');
        button.classList.remove('claim', 'done');
        button.disabled = false;
        if (payload.payout_pending) {
            button.classList.add('done');
            button.disabled = true;
            button.innerHTML = '<span>奖励到账处理中</span><i class="ri-loader-4-line spin"></i>';
            $('taskHint').textContent = '系统正在核对本次奖励，请勿重复领取';
        } else if (payload.claimed) {
            button.classList.add('done');
            button.disabled = true;
            button.innerHTML = '<span>今日奖励已领取</span><i class="ri-checkbox-circle-fill"></i>';
            $('taskHint').textContent = '奖励已发放到钱包，明天再来完成任务';
        } else if (payload.can_claim) {
            button.classList.add('claim');
            button.innerHTML = '<span>立即领取 ¥' + payload.reward_money + '</span><i class="ri-coins-fill"></i>';
            $('taskHint').textContent = '今日广告已全部完成，点击领取现金奖励';
        } else {
            button.innerHTML = '<span>观看广告赚现金</span><i class="ri-play-fill"></i>';
            $('taskHint').textContent = '还需观看 ' + (payload.target_count - payload.viewed_count) + ' 条，完整观看' + configuredCountdown + '秒计1条';
        }
    }
    function loadStatus() {
        return request('status').then(function (res) {
            if (res.code !== 0) throw new Error(res.msg || '状态加载失败');
            renderStatus(res.data);
            return res.data;
        }).catch(function (error) { toast(error.message); });
    }
    function showWatchConfirm() {
        $('confirmTitle').textContent = '开始观看第 ' + (state.viewed_count + 1) + ' 条广告？';
        $('confirmText').textContent = '完整观看' + configuredCountdown + '秒即可完成1条，本条价值' + state.unit_reward + '元。';
        openModal('confirmModal');
    }
    function renderProject(project) {
        $('adPriority').textContent = project.priority_label;
        $('adTitle').textContent = project.title || '推广宝精选项目';
        $('adDescription').textContent = project.description || '发现更多真实项目机会';
        currentImages = Array.isArray(project.images) ? project.images.filter(Boolean) : [];
        if (!currentImages.length && project.image) currentImages = [project.image];
        currentImageIndex = 0;
        if (!currentImages.length) {
            $('adMedia').innerHTML = '<div class="ad-placeholder"><i class="ri-image-line"></i><span>项目展示</span></div>';
        } else {
            $('adMedia').innerHTML = '<div class="ad-gallery-track" id="adGalleryTrack">' + currentImages.map(function (image, index) {
                return '<button class="ad-gallery-slide" type="button" data-image-index="' + index + '" aria-label="查看第' + (index + 1) + '张项目图片"><img src="' + escapeHtml(image) + '" alt="项目图片 ' + (index + 1) + '" draggable="false"></button>';
            }).join('') + '</div><span class="ad-image-count" id="adImageCount">1 / ' + currentImages.length + '</span>' + (currentImages.length > 1 ? '<span class="ad-swipe-hint"><i class="ri-arrow-left-right-line"></i> 左右滑动-点击放大-长按保存</span>' : '');
            var track = $('adGalleryTrack');
            track.addEventListener('scroll', function () {
                var width = track.clientWidth || 1;
                currentImageIndex = Math.max(0, Math.min(currentImages.length - 1, Math.round(track.scrollLeft / width)));
                $('adImageCount').textContent = (currentImageIndex + 1) + ' / ' + currentImages.length;
            }, { passive: true });
        }
        $('adScrollBody').scrollTop = 0;
    }
    function openImagePreview(index) {
        if (!currentImages.length) return;
        currentImageIndex = Math.max(0, Math.min(currentImages.length - 1, index));
        var track = $('previewImageTrack');
        track.innerHTML = currentImages.map(function (image, imageIndex) {
            return '<div class="image-preview-slide"><img src="' + escapeHtml(image) + '" alt="项目图片 ' + (imageIndex + 1) + '" draggable="false"></div>';
        }).join('');
        $('previewImageCount').textContent = (currentImageIndex + 1) + ' / ' + currentImages.length + (currentImages.length > 1 ? ' · 左右滑动' : '');
        openModal('imagePreviewModal');
        window.requestAnimationFrame(function () {
            track.scrollLeft = currentImageIndex * (track.clientWidth || 1);
        });
    }
    function closeImagePreview() {
        closeModal('imagePreviewModal');
        $('previewImageTrack').innerHTML = '';
    }
    function stopTimer() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }
    function startTimer(seconds) {
        stopTimer();
        adCompleted = false;
        deadline = Date.now() + seconds * 1000;
        $('adSeconds').textContent = seconds;
        $('adContinueButton').disabled = true;
        $('adContinueButton').textContent = '观看中，请保持页面可见';
        timer = window.setInterval(function () {
            if (document.hidden) return;
            var remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
            $('adSeconds').textContent = remaining;
            if (remaining <= 0) completeAd();
        }, 250);
    }
    function beginAd() {
        closeModal('confirmModal');
        $('adTitle').textContent = '正在加载...';
        $('adDescription').textContent = '';
        openModal('adModal');
        request('next_ad', {}, 'POST').then(function (res) {
            if (res.code !== 0) throw new Error(res.msg || '广告加载失败');
            adToken = res.data.token;
            renderProject(res.data.project);
            startTimer(res.data.countdown_seconds);
        }).catch(function (error) {
            closeModal('adModal');
            toast(error.message);
            loadStatus();
        });
    }
    function completeAd() {
        if (!adToken || adCompleted || completingAd) return;
        completingAd = true;
        stopTimer();
        $('adContinueButton').disabled = true;
        $('adContinueButton').textContent = '正在确认，请稍候';
        request('complete_ad', { token: adToken }, 'POST').then(function (res) {
            if (res.code === -4) {
                completingAd = false;
                startTimer(Math.max(1, res.remaining || 1));
                return;
            }
            if (res.code !== 0) throw new Error(res.msg || '观看确认失败');
            completingAd = false;
            adCompleted = true;
            renderStatus(res.data);
            $('adSeconds').textContent = '0';
            $('adContinueButton').disabled = false;
            $('adContinueButton').textContent = res.data.can_claim ? '任务完成，去领取奖励' : '本条已完成，继续任务';
            toast('本条广告已完成');
        }).catch(function (error) {
            completingAd = false;
            $('adContinueButton').disabled = false;
            $('adContinueButton').textContent = '重新确认';
            toast(error.message);
        });
    }
    function claimReward() {
        var button = $('taskMainButton');
        button.disabled = true;
        button.innerHTML = '<span>正在发放奖励...</span><i class="ri-loader-4-line spin"></i>';
        request('claim', {}, 'POST').then(function (res) {
            if (res.code !== 0) throw new Error(res.msg || '领取失败');
            renderStatus(res.data);
            showResult('奖励已到账', '¥' + res.data.reward_money + ' 已发放至钱包，可在奖励明细中查看。');
        }).catch(function (error) {
            button.disabled = false;
            toast(error.message);
            loadStatus();
        });
    }
    function recordRowsHtml(items) {
        return items.map(function (item) {
            return '<div class="record-row"><span class="record-icon"><i class="ri-coins-line"></i></span><span><strong>' + escapeHtml(item.title) + '</strong><small>' + escapeHtml(item.note) + ' · ' + escapeHtml(item.time) + '</small></span><b>+' + escapeHtml(item.money) + '</b></div>';
        }).join('');
    }
    function setRecordFooter(html) {
        var oldFooter = $('recordLoadState');
        if (oldFooter) oldFooter.remove();
        if (html) $('recordList').insertAdjacentHTML('beforeend', '<div class="record-load-state" id="recordLoadState">' + html + '</div>');
    }
    function loadRecords(type, reset) {
        reset = reset !== false;
        if (reset) {
            recordState = { type: type, page: 0, hasMore: true, loading: false, requestId: recordState.requestId + 1 };
            $('recordList').scrollTop = 0;
            $('recordList').innerHTML = '<div class="record-empty"><i class="ri-loader-4-line spin"></i> 正在加载奖励记录</div>';
        }
        if (recordState.loading || !recordState.hasMore) return;
        var requestId = recordState.requestId;
        var nextPage = recordState.page + 1;
        recordState.loading = true;
        if (!reset) setRecordFooter('<i class="ri-loader-4-line spin"></i><span>正在加载更多</span>');
        request('records', { type: recordState.type, page: nextPage }).then(function (res) {
            if (requestId !== recordState.requestId) return;
            if (res.code !== 0) throw new Error(res.msg || '加载失败');
            var items = Array.isArray(res.data) ? res.data : [];
            if (reset) $('recordList').innerHTML = '';
            setRecordFooter('');
            if (items.length) $('recordList').insertAdjacentHTML('beforeend', recordRowsHtml(items));
            recordState.page = nextPage;
            recordState.hasMore = !!res.has_more;
            if (!recordState.page || (!items.length && reset)) {
                $('recordList').innerHTML = '<div class="record-empty"><i class="ri-inbox-2-line"></i><span>暂无奖励记录</span></div>';
            } else if (recordState.hasMore) {
                setRecordFooter('<i class="ri-arrow-down-line"></i><span>继续下滑加载更多</span>');
            } else {
                setRecordFooter('<i class="ri-checkbox-circle-line"></i><span>已加载全部记录</span>');
            }
        }).catch(function (error) {
            if (requestId !== recordState.requestId) return;
            if (reset) {
                $('recordList').innerHTML = '<div class="record-empty"><i class="ri-error-warning-line"></i><span>' + escapeHtml(error.message) + '</span><button type="button" data-record-retry>重新加载</button></div>';
            } else {
                setRecordFooter('<button type="button" data-record-retry><i class="ri-refresh-line"></i> 加载失败，点击重试</button>');
            }
        }).finally(function () {
            if (requestId === recordState.requestId) recordState.loading = false;
        });
    }
    function renderSupport(payload) {
        $('supportValidCount').textContent = payload.valid_count;
        $('supportTierList').innerHTML = payload.rewards.map(function (item) {
            var stateClass = item.received ? 'received' : (item.processing ? 'processing' : (item.can_claim ? 'available' : 'locked'));
            var buttonText = item.received ? '已领取' : (item.processing ? '处理中' : (item.can_claim ? '立即领取' : '未达标'));
            var disabled = item.can_claim ? '' : ' disabled';
            return '<div class="support-tier ' + stateClass + '">' +
                '<span class="support-tier-icon"><i class="' + (item.received ? 'ri-checkbox-circle-fill' : 'ri-gift-2-line') + '"></i></span>' +
                '<span class="support-tier-copy"><small>有效直推达到</small><strong>' + escapeHtml(item.count) + ' 人</strong><em>现金扶持 ¥' + escapeHtml(item.money) + '</em></span>' +
                '<button type="button" data-support-count="' + escapeHtml(item.count) + '"' + disabled + '>' + buttonText + '</button>' +
                '</div>';
        }).join('');
    }
    function loadSupport() {
        $('supportTierList').innerHTML = '<div class="record-empty"><i class="ri-loader-4-line spin"></i> 正在核对奖励进度</div>';
        return request('support_info').then(function (res) {
            if (res.code !== 0) throw new Error(res.msg || '扶持进度加载失败');
            renderSupport(res.data);
        }).catch(function (error) {
            $('supportTierList').innerHTML = '<div class="record-empty"><i class="ri-error-warning-line"></i><span>' + escapeHtml(error.message) + '</span></div>';
        });
    }
    function claimSupport(button) {
        var count = button.getAttribute('data-support-count');
        button.disabled = true;
        button.innerHTML = '<i class="ri-loader-4-line spin"></i> 领取中';
        request('support_claim', { count: count }, 'POST').then(function (res) {
            if (res.code !== 0) throw new Error(res.msg || '领取失败');
            closeModal('supportModal');
            showResult('扶持奖励已到账', '¥' + res.money + ' 已发放至推广钱包，每个奖励档位仅可领取一次。');
            loadSupport();
        }).catch(function (error) {
            showResult('暂时无法领取', error.message, 'warning');
            loadSupport();
        });
    }
    function abandonCurrentAd() {
        stopTimer();
        var token = adToken;
        adToken = '';
        closeModal('earlyCloseModal');
        closeModal('adModal');
        if (!token) return;
        request('abandon_ad', { token: token }, 'POST').catch(function () {
            toast('退出状态同步失败，请稍后刷新');
        });
    }

    $('taskMainButton').addEventListener('click', function () {
        if (!state || state.claimed || state.payout_pending) return;
        if (state.can_claim) claimReward(); else showWatchConfirm();
    });
    $('confirmWatchButton').addEventListener('click', beginAd);
    $('adMedia').addEventListener('click', function (event) {
        var slide = event.target.closest('[data-image-index]');
        if (slide) openImagePreview(parseInt(slide.getAttribute('data-image-index'), 10) || 0);
    });
    $('previewCloseButton').addEventListener('click', closeImagePreview);
    $('previewImageTrack').addEventListener('scroll', function () {
        var track = $('previewImageTrack');
        window.clearTimeout(track.previewScrollTimer);
        track.previewScrollTimer = window.setTimeout(function () {
            var width = track.clientWidth || 1;
            currentImageIndex = Math.max(0, Math.min(currentImages.length - 1, Math.round(track.scrollLeft / width)));
            $('previewImageCount').textContent = (currentImageIndex + 1) + ' / ' + currentImages.length + (currentImages.length > 1 ? ' · 左右滑动' : '');
        }, 60);
    }, { passive: true });
    $('previewImageStage').addEventListener('pointerdown', function (event) {
        previewPointerStartX = event.clientX;
        previewDidSwipe = false;
    }, { passive: true });
    $('previewImageStage').addEventListener('pointermove', function (event) {
        if (Math.abs(event.clientX - previewPointerStartX) > 10) previewDidSwipe = true;
    }, { passive: true });
    $('previewImageStage').addEventListener('click', function () {
        if (previewDidSwipe) {
            previewDidSwipe = false;
            return;
        }
        closeImagePreview();
    });
    $('imagePreviewModal').addEventListener('click', function (event) {
        if (event.target === $('imagePreviewModal')) closeImagePreview();
    });
    $('adContinueButton').addEventListener('click', function () {
        if (!adCompleted) { completeAd(); return; }
        closeModal('adModal');
        if (state && state.can_claim) toast('今日任务完成，点击领取奖励');
    });
    $('adCloseButton').addEventListener('click', function () {
        if (!adCompleted) {
            openModal('earlyCloseModal');
            return;
        }
        closeModal('adModal');
    });
    $('keepWatchingButton').addEventListener('click', function () { closeModal('earlyCloseModal'); });
    $('confirmEarlyCloseButton').addEventListener('click', abandonCurrentAd);
    $('recordsButton').addEventListener('click', function () { openModal('recordsModal'); loadRecords('task', true); });
    $('rulesButton').addEventListener('click', function () { openModal('rulesModal'); });
    $('supportButton').addEventListener('click', function () { openModal('supportModal'); loadSupport(); });
    $('supportTierList').addEventListener('click', function (event) {
        var button = event.target.closest('[data-support-count]');
        if (button && !button.disabled) claimSupport(button);
    });
    $('recordList').addEventListener('scroll', function () {
        var list = $('recordList');
        if (list.scrollTop + list.clientHeight >= list.scrollHeight - 80) loadRecords(recordState.type, false);
    }, { passive: true });
    $('recordList').addEventListener('click', function (event) {
        if (event.target.closest('[data-record-retry]')) loadRecords(recordState.type, false);
    });
    document.querySelectorAll('[data-close]').forEach(function (button) {
        button.addEventListener('click', function () { closeModal(button.getAttribute('data-close')); });
    });
    document.querySelectorAll('[data-record-type]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.querySelectorAll('[data-record-type]').forEach(function (item) { item.classList.remove('active'); });
            button.classList.add('active');
            loadRecords(button.getAttribute('data-record-type'), true);
        });
    });
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) hiddenAt = Date.now();
        else if (hiddenAt && timer) { deadline += Date.now() - hiddenAt; hiddenAt = 0; }
    });
    loadStatus().then(function () {
        window.setTimeout(maybeShowInviteNotice, 700);
    });
})();
</script>
</body>
</html>
