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
	<title>提示信息 · 推广宝</title>
	<script>document.title = '提示信息 · 推广宝';</script>
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
	<style>
	body.pg_tb_credit {
		--tgb-r06-primary: #146ef5;
		--tgb-r06-ink: #172033;
		--tgb-r06-text: #526176;
		--tgb-r06-border: #dbe3ef;
		--tgb-r06-bg: #f4f7fb;
		--tgb-r06-surface: #ffffff;
		min-height: 100vh;
		padding: calc(68px + env(safe-area-inset-top, 0px)) 16px calc(24px + env(safe-area-inset-bottom, 0px)) !important;
		overflow-x: hidden;
		background: var(--tgb-r06-bg) !important;
		color: var(--tgb-r06-ink) !important;
		letter-spacing: 0;
	}
	body.pg_tb_credit::before,
	body.pg_tb_credit .comiis_loadings,
	body.pg_tb_credit #comiis_head,
	body.pg_tb_credit .comiis_footer_scroll,
	body.pg_tb_credit #comiis_foot_box { display: none !important; }
	body.pg_tb_credit .comiis_body,
	body.pg_tb_credit .comiis_bodybox {
		width: 100%;
		max-width: 100%;
		margin: 0 !important;
		padding: 0 !important;
		background: transparent !important;
	}
	body.pg_tb_credit .top-nav {
		top: env(safe-area-inset-top, 0px);
		height: 56px;
		padding: 6px 12px;
		border-bottom: 1px solid var(--tgb-r06-border);
		background: rgba(255, 255, 255, 0.98);
		box-shadow: none;
		backdrop-filter: none;
	}
	body.pg_tb_credit .nav-back {
		width: 44px;
		height: 44px;
		border: 1px solid var(--tgb-r06-border);
		border-radius: 8px;
		background: var(--tgb-r06-surface);
		color: var(--tgb-r06-primary);
		font-size: 0;
	}
	body.pg_tb_credit .nav-back i::before {
		content: "<";
		font-family: Arial, sans-serif;
		font-size: 22px;
		font-weight: 700;
	}
	body.pg_tb_credit .nav-title {
		color: transparent;
		font-size: 0;
		letter-spacing: 0;
	}
	body.pg_tb_credit .nav-title::after {
		content: "推广宝";
		color: var(--tgb-r06-ink);
		font-size: 17px;
		font-weight: 700;
	}
	body.pg_tb_credit .main-container {
		width: 100%;
		max-width: 520px;
		margin: 0 auto;
		padding: 16px 0 0;
		gap: 12px;
	}
	body.pg_tb_credit .message-card {
		width: 100%;
		gap: 16px;
		padding: 24px 18px;
		border: 1px solid var(--tgb-r06-border);
		border-radius: 8px;
		background: var(--tgb-r06-surface);
		box-shadow: 0 4px 14px rgba(12, 27, 51, 0.06);
	}
	body.pg_tb_credit .message-icon {
		width: 56px;
		height: 56px;
		border-radius: 8px;
		background: #e8f1ff;
		color: var(--tgb-r06-primary);
		font-size: 0;
		box-shadow: none;
	}
	body.pg_tb_credit .message-icon i::before {
		content: "i";
		font-family: Georgia, serif;
		font-size: 28px;
		font-weight: 700;
	}
	body.pg_tb_credit .message-text {
		width: 100%;
		padding: 14px;
		border: 1px solid var(--tgb-r06-border);
		border-radius: 8px;
		background: #f8fbff;
		color: var(--tgb-r06-text);
		font-size: 15px;
		font-weight: 500;
		line-height: 1.7;
	}
	body.pg_tb_credit .message-text a {
		color: var(--tgb-r06-primary);
		font-weight: 700;
	}
	body.pg_tb_credit .button-group {
		width: 100%;
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 10px;
	}
	body.pg_tb_credit .btn {
		min-width: 0;
		min-height: 46px;
		padding: 0 14px;
		border-radius: 8px;
		font-size: 15px;
		letter-spacing: 0;
		box-shadow: none;
	}
	body.pg_tb_credit .btn-primary {
		background: var(--tgb-r06-primary);
		color: #ffffff;
	}
	body.pg_tb_credit .btn-outline {
		border: 1px solid var(--tgb-r06-border);
		background: var(--tgb-r06-surface);
		color: var(--tgb-r06-text);
	}
	@media (max-width: 360px) {
		body.pg_tb_credit { padding-right: 12px !important; padding-left: 12px !important; }
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
