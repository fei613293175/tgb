<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: index.php 34524 2014-05-15 04:42:23Z nemohou $
 */

if(version_compare(PHP_VERSION, '8.0.0', '>=')) {
	exit('This version of Discuz! is not compatible with >= PHP 8.0, Please install or update to higher version.');
}

$tgbUserAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
$tgbMobilePattern = '/android|iphone|ipad|ipod|mobile|phone|webos|blackberry|iemobile|opera mini|opera mobi|ucweb|windows phone|tui guang bao|tuiguangbaoandroid/i';
$tgbForceMobile = isset($_GET['mobile']) && (string)$_GET['mobile'] === '2';
if(!$tgbForceMobile && !preg_match($tgbMobilePattern, $tgbUserAgent)) {
	header('Content-Type: text/html; charset=utf-8');
	header('X-Content-Type-Options: nosniff');
	?>
<!doctype html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#F4F7FB">
	<meta name="color-scheme" content="light">
	<link rel="icon" type="image/svg+xml" href="source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg?v=20260726-r02c">
	<title>推广宝 - 请使用手机访问</title>
	<style>
		:root{color-scheme:light;--bg:#f4f7fb;--surface:#fff;--ink:#0e1b2a;--text:#405166;--muted:#718096;--border:#d8e1ec;--primary:#2764ff;--primary-soft:#e8efff;--mint:#19b8a9}
		*{box-sizing:border-box}
		html,body{min-height:100%;margin:0}
		body{display:grid;place-items:center;padding:32px;background:radial-gradient(circle at 15% 10%,#e8efff 0,transparent 32%),radial-gradient(circle at 86% 86%,#daf7f2 0,transparent 28%),var(--bg);color:var(--text);font-family:"PingFang SC","Microsoft YaHei","Noto Sans CJK SC",system-ui,-apple-system,sans-serif}
		main{width:min(100%,920px);display:grid;grid-template-columns:minmax(0,1.1fr) minmax(280px,.9fr);gap:48px;align-items:center;padding:56px;background:rgba(255,255,255,.94);border:1px solid var(--border);border-radius:28px;box-shadow:0 28px 80px rgba(26,52,84,.12)}
		.brand{display:flex;align-items:center;gap:16px;margin-bottom:42px;color:var(--ink);font-size:22px;font-weight:800;letter-spacing:.08em}
		.brand img{width:52px;height:52px}
		.eyebrow{margin:0 0 14px;color:var(--primary);font-size:14px;font-weight:800;letter-spacing:.16em}
		h1{max-width:520px;margin:0;color:var(--ink);font-size:clamp(38px,5vw,64px);line-height:1.08;letter-spacing:-.04em}
		.lead{max-width:520px;margin:24px 0 30px;font-size:18px;line-height:1.8}
		.actions{display:flex;flex-wrap:wrap;gap:12px;align-items:center}
		.button{display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 22px;color:#fff;background:var(--primary);border-radius:12px;box-shadow:0 10px 24px rgba(39,100,255,.24)}
		.note{color:var(--muted);font-size:14px}
		.phone{position:relative;width:min(100%,320px);aspect-ratio:9/18;margin:auto;padding:12px;background:#152337;border-radius:40px;box-shadow:0 30px 70px rgba(14,27,42,.24)}
		.screen{height:100%;overflow:hidden;background:var(--bg);border-radius:30px}
		.status{height:34px;background:#fff}
		.appbar{display:flex;align-items:center;gap:10px;padding:13px 15px;background:#fff;border-bottom:1px solid var(--border)}
		.appbar img{width:34px;height:34px}
		.appbar strong{color:var(--ink)}
		.search{height:42px;margin:16px;background:#edf3fa;border:1px solid var(--border);border-radius:12px}
		.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;padding:0 16px}
		.card{height:102px;background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 8px 20px rgba(26,52,84,.07)}
		.card::before{content:"";display:block;width:34px;height:34px;margin:14px;background:var(--primary-soft);border-radius:10px}
		.card:nth-child(2)::before,.card:nth-child(3)::before{background:#daf7f2}
		.nav{position:absolute;right:12px;bottom:12px;left:12px;height:62px;background:#fff;border-top:1px solid var(--border);border-radius:0 0 30px 30px}
		@media(max-width:760px){body{padding:18px}main{grid-template-columns:1fr;padding:32px 24px}.phone{display:none}.brand{margin-bottom:28px}h1{font-size:40px}}
		@media(prefers-reduced-motion:reduce){*{scroll-behavior:auto!important}}
	</style>
</head>
<body>
	<main>
		<section>
			<div class="brand"><img src="source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg?v=20260726-r02c" alt="">推广宝</div>
			<p class="eyebrow">MOBILE EXPERIENCE</p>
			<h1>请使用手机打开推广宝</h1>
			<p class="lead">推广宝专为移动端和 Android App 设计。请使用手机浏览器扫码或直接访问，以获得完整的业务功能与交互体验。</p>
			<div class="actions">
				<span class="button" aria-disabled="true">仅支持移动端</span>
				<span class="note">建议屏幕宽度 360–430 px</span>
			</div>
		</section>
		<div class="phone" aria-hidden="true">
			<div class="screen">
				<div class="status"></div>
				<div class="appbar"><img src="source/plugin/xigua_hb/static/tgb-r02/brand-mark-r02.svg?v=20260726-r02c" alt=""><strong>推广宝</strong></div>
				<div class="search"></div>
				<div class="grid"><div class="card"></div><div class="card"></div><div class="card"></div><div class="card"></div></div>
			</div>
			<div class="nav"></div>
		</div>
	</main>
</body>
</html>
	<?php
	exit;
}

if(!empty($_SERVER['QUERY_STRING']) && is_numeric($_SERVER['QUERY_STRING'])) {
	$_ENV['curapp'] = 'home';
	$_GET = array('mod'=>'space', 'uid'=>$_SERVER['QUERY_STRING']);
} else {

	$url = '';
	$domain = $_ENV = array();
	$jump = false;
	@include_once './data/sysdata/cache_domain.php';
	$_ENV['domain'] = $domain;
	if(empty($_ENV['domain'])) {
		$_ENV['curapp'] = 'forum';
	} else {
		$_ENV['defaultapp'] = array('portal.php' => 'portal', 'forum.php' => 'forum', 'group.php' => 'group', 'home.php' => 'home');
		$_ENV['hostarr'] = explode('.', $_SERVER['HTTP_HOST']);
		$_ENV['domainroot'] = substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], '.')+1);
		if(!empty($_ENV['domain']['app']) && is_array($_ENV['domain']['app']) && in_array($_SERVER['HTTP_HOST'], $_ENV['domain']['app'])) {
			$_ENV['curapp'] = array_search($_SERVER['HTTP_HOST'], $_ENV['domain']['app']);
			if($_ENV['curapp'] == 'mobile') {
				$_ENV['curapp'] = 'forum';
				if(!isset($_GET['mobile'])) {
					@$_GET['mobile'] = '2';
				}
			}
			if($_ENV['curapp'] == 'default' || !isset($_ENV['defaultapp'][$_ENV['curapp'].'.php'])) {
				$_ENV['curapp'] = '';
			}
		} elseif(!empty($_ENV['domain']['root']) && is_array($_ENV['domain']['root']) && in_array($_ENV['domainroot'], $_ENV['domain']['root'])) {

			$_G['setting']['holddomain'] = $_ENV['domain']['holddomain'] ? $_ENV['domain']['holddomain'] : array('www');
			$list = $_ENV['domain']['list'];
			if(isset($list[$_SERVER['HTTP_HOST']])) {
				$domain = $list[$_SERVER['HTTP_HOST']];
				switch($domain['idtype']) {
					case 'subarea':
						$_ENV['curapp'] = 'forum';
						$_GET['gid'] = intval($domain['id']);
						break;
					case 'forum':
						$_ENV['curapp'] = 'forum';
						$_GET['mod'] = 'forumdisplay';
						$_GET['fid'] = intval($domain['id']);
						break;
					case 'topic':
						$_ENV['curapp'] = 'portal';
						$_GET['mod'] = 'topic';
						$_GET['topicid'] = intval($domain['id']);
						break;
					case 'channel':
						$_ENV['curapp'] = 'portal';
						$_GET['mod'] = 'list';
						$_GET['catid'] = intval($domain['id']);
						break;
					case 'plugin':
						$_ENV['curapp'] = 'plugin';
						$_GET['id'] = $domain['id'];
						$_GET['fromapp'] = 'index';
						break;
				}
			} elseif(count($_ENV['hostarr']) > 2 && $_ENV['hostarr'][0] != 'www' && !checkholddomain($_ENV['hostarr'][0])) {
				$_ENV['prefixdomain'] = addslashes($_ENV['hostarr'][0]);
				$_ENV['domainroot'] = addslashes($_ENV['domainroot']);
				require_once './source/class/class_core.php';
				C::app()->init_setting = true;
				C::app()->init_user = false;
				C::app()->init_session = false;
				C::app()->init_cron = false;
				C::app()->init_misc = false;
				C::app()->init();
				$jump = true;
				$domain = C::t('common_domain')->fetch_by_domain_domainroot($_ENV['prefixdomain'], $_ENV['domainroot']);
				$apphost = $_ENV['domain']['app'][$domain['idtype']] ? $_ENV['domain']['app'][$domain['idtype']] : $_ENV['domain']['app']['default'];
				$apphost = $apphost ? $_G['scheme'].'://'.$apphost.'/' : '';
				switch($domain['idtype']) {
					case 'home':
						if($_G['setting']['rewritestatus'] && in_array('home_space', $_G['setting']['rewritestatus'])) {
							$url = rewriteoutput('home_space', 1, $apphost, $domain['id']);
						} else {
							$url = $apphost.'home.php?mod=space&uid='.$domain['id'];
						}
						break;
					case 'group':
						if($_G['setting']['rewritestatus'] && in_array('group_group', $_G['setting']['rewritestatus'])) {
							$url = rewriteoutput('group_group', 1, $apphost, $domain['id']);
						} else {
							$url = $apphost.'forum.php?mod=group&fid='.$domain['id'].'&page=1';
						}
						break;
				}
			}
		} else {
			$jump = true;
		}
		if(empty($url) && empty($_ENV['curapp'])) {
			if(!empty($_ENV['domain']['defaultindex']) && !$jump) {
				if($_ENV['defaultapp'][$_ENV['domain']['defaultindex']]) {
					$_ENV['curapp'] = $_ENV['defaultapp'][$_ENV['domain']['defaultindex']];
				} else {
					$url = $_ENV['domain']['defaultindex'];
				}
			} else {
				if($jump) {
					$url = empty($_ENV['domain']['app']['default']) ? (!empty($_ENV['domain']['defaultindex']) ? $_ENV['domain']['defaultindex'] : 'forum.php') : (is_https() ? 'https' : 'http').'://'.$_ENV['domain']['app']['default'];
				} else {
					$_ENV['curapp'] = 'forum';
				}
			}
		}
	}
}
if(!empty($url)) {
	$url = '/plugin.php?id=xigua_hb';
$_ENV['curapp'] = 'chajian';
	$delimiter = strrpos($url, '?') ? '&' : '?';
	if(isset($_GET['fromuid']) && $_GET['fromuid']) {
		$url .= sprintf('%sfromuid=%d', $delimiter, $_GET['fromuid']);
	} elseif(isset($_GET['fromuser']) && $_GET['fromuser']) {
		$url .= sprintf('%sfromuser=%s', $delimiter, rawurlencode($_GET['fromuser']));
	}
	$parse = parse_url($url);
	if(!isset($parse['host']) && file_exists($parse['path'])) {
		if(!empty($parse['query'])) {
			parse_str($parse['query'], $_GET);
		}
		require './'.$parse['path'];
	} else {
		header("location: $url");
	}
} else {
	require './'.$_ENV['curapp'].'.php';
}

function checkholddomain($domain) {
	global $_G;

	$domain = strtolower($domain);
	if(preg_match("/^[^a-z]/i", $domain)) return true;
	$holdmainarr = empty($_G['setting']['holddomain']) ? array('www') : explode('|', $_G['setting']['holddomain']);
	$ishold = false;
	foreach ($holdmainarr as $value) {
		if(strpos($value, '*') === false) {
			if(strtolower($value) == $domain) {
				$ishold = true;
				break;
			}
		} else {
			$value = str_replace('*', '.*?', $value);
			if(@preg_match("/$value/i", $domain)) {
				$ishold = true;
				break;
			}
		}
	}
	return $ishold;
}

function is_https() {
	if(isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off') {
		return true;
	}
	if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') {
		return true;
	}
	if(isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && strtolower($_SERVER['HTTP_X_CLIENT_SCHEME']) == 'https') {
		return true;
	}
	if(isset($_SERVER['HTTP_FROM_HTTPS']) && strtolower($_SERVER['HTTP_FROM_HTTPS']) != 'off') {
		return true;
	}
	if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
		return true;
	}
	return false;
}

?>
