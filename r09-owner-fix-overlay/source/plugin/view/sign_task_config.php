<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

return array(
    // 每日广告任务
    'countdown_seconds' => 20,
    'regular_ad_count' => 5,
    'vip_ad_count' => 10,
    'unit_reward' => 0.50,

    // 好友完成每日广告任务后的两级奖励
    'direct_regular_reward' => 0.50,
    'indirect_regular_reward' => 0.30,
    'direct_vip_reward' => 1.00,
    'indirect_vip_reward' => 0.50,
);
