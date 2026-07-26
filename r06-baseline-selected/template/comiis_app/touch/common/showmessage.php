<?PHP exit('Access Denied');?>
<!--{if $param['login']}-->
	<!--{eval dheader('Location:member.php?mod=logging&action=login');exit;}-->
<!--{/if}-->
<!--{eval $comiis_bg = 1;$comiis_app_switch = $_G['cache']['comiis_app_switch'];$comiis_app_nav = $_G['cache']['comiis_app_nav'];}-->
<!--{template common/header}-->
<!--{if $_G['inajax']}-->
	<!--{if $_GET['ac'] == 'privacy'}-->
		$show_message
	<!--{else}-->
		<style>
			.comiis_tip {
				width: 92%;
				max-width: 400px;
				margin: 80px auto 0;
				background: #fff;
				border-radius: 22px;
				padding: 30px 24px;
				box-shadow: 0 8px 32px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.03);
				border: 1px solid #e8e0d5;
				text-align: center;
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: 18px;
			}
			.comiis_message_text p {
				font-size: 1rem;
				font-weight: 600;
				color: #1a1a2e;
				background: #f7f5f2;
				padding: 12px 24px;
				border-radius: 40px;
				border: 1px solid #e8e0d5;
			}
			.comiis_message_actions {
				display: flex;
				gap: 12px;
				justify-content: center;
				flex-wrap: wrap;
			}
			.btn {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				height: 44px;
				padding: 0 28px;
				border-radius: 24px;
				font-size: 0.95rem;
				font-weight: 700;
				text-decoration: none;
				transition: all 0.15s ease;
				cursor: pointer;
				border: none;
			}
			.btn-primary {
				background: #ff6b35;
				color: #fff;
				box-shadow: 0 4px 16px rgba(255,107,53,0.3);
			}
			.btn-outline {
				background: #fff;
				color: #5a5f6e;
				border: 1px solid #e8e0d5;
			}
			.btn-primary:active { transform: scale(0.96); }
			.btn-outline:active { background: #f5f0eb; }
		</style>
		<div class="comiis_tip">
			<div class="comiis_message_text">
				<p>$show_message</p>
			</div>
			<!--{if $_G['forcemobilemessage']}-->
				<div class="comiis_message_actions">
					<!--{if $comiis_app_switch['comiis_post_btnwz'] == 1}-->
						<a href="javascript:history.back();" class="btn btn-outline">{lang goback}</a>
						<a href="{$_G['setting']['mobile']['pageurl']}" class="btn btn-primary">{lang continue}</a>
					<!--{else}-->
						<a href="{$_G['setting']['mobile']['pageurl']}" class="btn btn-primary">{lang continue}</a>
						<a href="javascript:history.back();" class="btn btn-outline">{lang goback}</a>
					<!--{/if}-->
				</div>
			<!--{/if}-->
			<!--{if $url_forward && !$_GET['loc']}-->
				<script type="text/javascript">
					setTimeout(function() {
						window.location.href = '$url_forward';
					}, '3000');
				</script>
			<!--{elseif $allowreturn}-->
				<div class="comiis_message_actions"><a href="javascript:;" onclick="popup.close();" class="btn btn-outline">{lang close}</a></div>
			<!--{/if}-->
		</div>
	<!--{/if}-->
<!--{else}-->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
	<style>
		:root {
			--pri: #FF6B35;
			--pri-light: #FF8C42;
			--sec: #1A1A2E;
			--card: #FFFFFF;
			--bg: #F5F0EB;
			--muted: #8C8C8C;
			--border: #E8E0D5;
			--danger: #E8553D;
		}

		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			-webkit-tap-highlight-color: transparent;
			-webkit-font-smoothing: antialiased;
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', 'Microsoft YaHei', sans-serif;
		}

		body {
			background: var(--bg) !important;
			color: var(--sec) !important;
			min-height: 100vh;
			max-width: 100vw;
			overflow-x: hidden;
			padding-top: 70px;
			padding-bottom: 40px;
			position: relative;
		}

		body::before {
			content: '';
			position: fixed;
			inset: 0;
			pointer-events: none;
			z-index: 0;
			background:
				radial-gradient(ellipse at 20% 10%, rgba(255,107,53,0.04) 0%, transparent 50%),
				radial-gradient(ellipse at 80% 30%, rgba(255,140,66,0.03) 0%, transparent 50%);
		}

		/* 顶部导航 */
		.top-nav {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 56px;
			background: rgba(255,255,255,0.85);
			backdrop-filter: blur(20px);
			-webkit-backdrop-filter: blur(20px);
			border-bottom: 1px solid rgba(200,200,210,0.25);
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 8px 16px;
			z-index: 100;
			box-shadow: 0 1px 8px rgba(0,0,0,0.04);
		}
		.nav-back {
			display: flex;
			align-items: center;
			justify-content: center;
			width: 44px;
			height: 44px;
			border-radius: 50%;
			background: rgba(0,0,0,0.03);
			color: var(--sec);
			text-decoration: none;
			font-size: 1.3rem;
			cursor: pointer;
			transition: background 0.2s;
		}
		.nav-back:active { background: rgba(0,0,0,0.08); }
		.nav-title {
			font-size: 1.1rem;
			font-weight: 700;
			color: var(--sec);
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
		}
		.nav-placeholder { width: 44px; }

		/* 主内容容器 */
		.main-container {
			width: 92%;
			max-width: 440px;
			margin: 0 auto;
			position: relative;
			z-index: 1;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 24px;
			padding-top: 36px;
		}

		/* 消息卡片 */
		.message-card {
			width: 100%;
			background: var(--card);
			border-radius: 24px;
			padding: 32px 20px 28px;
			box-shadow: 0 8px 32px rgba(0,0,0,0.06), 0 2px 8px rgba(0,0,0,0.03);
			border: 1px solid var(--border);
			text-align: center;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 20px;
		}

		.message-icon {
			width: 72px;
			height: 72px;
			border-radius: 24px;
			background: var(--pri);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 36px;
			color: #fff;
			box-shadow: 0 8px 24px rgba(255,107,53,0.25);
		}

		.message-text {
			font-size: 1rem;
			font-weight: 600;
			color: var(--sec);
			background: #F7F5F2;
			padding: 12px 28px;
			border-radius: 40px;
			border: 1px solid var(--border);
			line-height: 1.5;
		}

		/* 按钮组 */
		.button-group {
			display: flex;
			flex-wrap: wrap;
			gap: 12px;
			justify-content: center;
		}

		.btn {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			height: 46px;
			padding: 0 32px;
			border-radius: 50px;
			font-size: 0.95rem;
			font-weight: 700;
			text-decoration: none;
			transition: all 0.15s ease;
			cursor: pointer;
			border: none;
			letter-spacing: 0.3px;
		}

		.btn-primary {
			background: var(--pri);
			color: #fff;
			box-shadow: 0 6px 20px rgba(255,107,53,0.3);
		}
		.btn-primary:active {
			transform: scale(0.96);
			box-shadow: 0 4px 12px rgba(255,107,53,0.4);
		}

		.btn-outline {
			background: #fff;
			color: var(--sec);
			border: 2px solid #FFD5C0;
		}
		.btn-outline:active {
			background: #FFFBF7;
			border-color: var(--pri);
		}

		/* 响应式微调 */
		@media (max-width: 360px) {
			.message-text { font-size: 0.9rem; padding: 10px 20px; }
		}
	</style>

	<!-- 头部导航 -->
	<div class="top-nav">
		<a href="javascript:window.history.go(-1);" class="nav-back">
			<i class="fa fa-chevron-left"></i>
		</a>
		<div class="nav-title">签米</div>
		<div class="nav-placeholder"></div>
	</div>

	<!-- 主内容 -->
	<div class="main-container">
		<div class="message-card">
			<div class="message-icon">
				<i class="fa fa-info-circle"></i>
			</div>
			<p class="message-text">$show_message</p>
			<div class="button-group">
				<!--{if $_G['forcemobilemessage']}-->
					<a href="{$_G['setting']['mobile']['pageurl']}" class="btn btn-primary">{lang continue}</a>
					<a href="javascript:history.back();" class="btn btn-outline">{lang goback}</a>
				<!--{/if}-->
				<!--{if $url_forward}-->
					<a href="$url_forward" class="btn btn-primary">{lang message_forward_mobile}</a>
					<script>
						setTimeout(function() {
							window.location.href = '$url_forward';
						}, 1500);
					</script>
				<!--{elseif $allowreturn}-->
					<a href="javascript:history.back();" class="btn btn-outline">点击返回</a>
					<script>
						setTimeout(function() {
							history.back();
						}, 3000);
					</script>
				<!--{/if}-->
			</div>
		</div>
	</div>
<!--{/if}-->
<!--{eval $comiis_foot = 'no';}-->
<!--{template common/footer}-->