<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS pre_tb_pay;
CREATE TABLE IF NOT EXISTS `pre_tb_pay` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
   `uid` bigint(11) unsigned NOT NULL,
  `username`  varchar(20)  NOT NULL,
  `orderid` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `plugin` varchar(255) NOT NULL,
  `paytype` smallint,
   `alipaycode` varchar(20) NOT NULL,
   `alipayname` varchar(20) NOT NULL,
   `alipaynprice` decimal(10,2) NOT NULL,
   `liyou` varchar(255) NOT NULL,
  `ostatus` tinyint(1) NOT NULL DEFAULT '0',
  `shstatus` tinyint(1) NOT NULL DEFAULT '0',
  `payno` varchar(100) NOT NULL,
  `odateline` int(11) NOT NULL,
  `dateline` int(11) NOT NULL,
  `updateline` int(11) NOT NULL,
   PRIMARY KEY (`id`),
   INDEX pre_tb_pay(`orderid`)
) ENGINE=MyISAM;
EOF;
runquery($sql);

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `pre_tb_pay_scan_review` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_id` int(10) unsigned NOT NULL DEFAULT '0',
  `orderid` varchar(50) NOT NULL,
  `uid` bigint(11) unsigned NOT NULL,
  `paytype` smallint(6) NOT NULL,
  `qr_key` varchar(64) NOT NULL,
  `payer_nickname` varchar(60) NOT NULL,
  `realname_last` varchar(8) NOT NULL,
  `proof_path` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `reject_reason` varchar(255) NOT NULL DEFAULT '',
  `submit_count` smallint(5) unsigned NOT NULL DEFAULT '1',
  `reviewer_uid` bigint(11) unsigned NOT NULL DEFAULT '0',
  `dateline` int(11) unsigned NOT NULL DEFAULT '0',
  `updateline` int(11) unsigned NOT NULL DEFAULT '0',
  `reviewtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderid` (`orderid`),
  KEY `uid_status` (`uid`,`status`),
  KEY `status_dateline` (`status`,`dateline`)
) ENGINE=InnoDB;
EOF;
runquery($sql);
$finish = TRUE;




?>
