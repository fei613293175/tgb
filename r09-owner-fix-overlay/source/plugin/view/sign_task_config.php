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

    // 官方扶持：实名直推连续完成3天后计为1个有效用户
    'support_rewards' => array(
        10 => 10.00,
        30 => 28.00,
        50 => 38.00,
        100 => 58.00,
        150 => 88.00,
        200 => 138.00,
        300 => 198.00,
        500 => 330.00,
    ),
);
