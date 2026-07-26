<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>签米会员 · VIP尊享中心</title>
    <link href="source/plugin/xigua_hb/static/tgb-r02/vendor/remixicon-3.5.0/remixicon.css?v=20260726-r02" rel="stylesheet">
    <link rel="stylesheet" href="source/plugin/tb_cus_admin/template/layuimini/lib/font-awesome-4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --gold: #f0b90b;
            --gold-dark: #d4a017;
            --orange: #ff6933;
            --coral: #ff4d4d;
            --bg-start: #fff9f0;
            --bg-end: #fef3e2;
            --card-bg: rgba(255, 255, 255, 0.85);
            --text: #333;
            --accent-1: #ff7b00;
            --accent-2: #e63946;
            --blue-accent: #5b8def;
            --teal-accent: #0ea5a0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%);
            background-attachment: fixed;
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            padding-bottom: env(safe-area-inset-bottom, 20px);
            -webkit-overflow-scrolling: touch;
        }
        .bg-float {
            position: fixed;
            top: -15%;
            left: -25%;
            width: 150%;
            height: 150%;
            background:
                radial-gradient(ellipse at 55% 18%, rgba(255, 160, 40, 0.18) 0%, transparent 55%),
                radial-gradient(ellipse at 35% 75%, rgba(255, 90, 40, 0.10) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 55%, rgba(255, 180, 60, 0.08) 0%, transparent 45%),
                radial-gradient(ellipse at 20% 30%, rgba(90, 140, 240, 0.06) 0%, transparent 50%);
            z-index: 0;
            animation: bgFloat 12s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes bgFloat {
            0%,
            100% {
                opacity: 0.75;
                transform: translate(0, 0) scale(1);
            }
            25% {
                opacity: 0.9;
                transform: translate(1.5%, -1%) scale(1.03);
            }
            50% {
                opacity: 0.8;
                transform: translate(-0.5%, 1.2%) scale(1.01);
            }
            75% {
                opacity: 0.95;
                transform: translate(1%, 0.5%) scale(1.04);
            }
        }
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(255, 150, 50, 0.5);
            border-radius: 50%;
            animation: floatUp 8s linear infinite;
            box-shadow: 0 0 6px rgba(255, 150, 40, 0.5);
        }
        .particle:nth-child(odd) {
            background: rgba(255, 180, 80, 0.45);
            animation-duration: 10s;
            animation-delay: -2s;
            width: 4px;
            height: 4px;
        }
        .particle:nth-child(3n) {
            background: rgba(255, 130, 60, 0.4);
            animation-duration: 7s;
            animation-delay: -4s;
            width: 2.5px;
            height: 2.5px;
        }
        .particle:nth-child(4n+1) {
            animation-duration: 9s;
            animation-delay: -1s;
        }
        .particle:nth-child(5n+2) {
            animation-duration: 11s;
            animation-delay: -3s;
        }
        @keyframes floatUp {
            0% {
                transform: translateY(105vh) translateX(0) scale(0.3);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
                transform: translateY(90vh) translateX(15px) scale(0.8);
            }
            40% {
                opacity: 1;
                transform: translateY(50vh) translateX(-10px) scale(1.2);
            }
            70% {
                opacity: 0.6;
                transform: translateY(20vh) translateX(8px) scale(0.7);
            }
            100% {
                transform: translateY(-5vh) translateX(-5px) scale(0.1);
                opacity: 0;
            }
        }
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            padding: 0 4% 50px;
        }
        /* 导航栏 */
        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border-bottom: 1px solid rgba(255, 200, 120, 0.35);
            padding: 45px 5% 12px 5%;
            margin: 0 -5%;
            width: 110%;
            box-shadow: 0 2px 20px rgba(255, 150, 30, 0.06);
        }
        .nav-inner {
            max-width: 500px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .nav-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            color: #b08968;
            font-size: 20px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .nav-back:active {
            transform: scale(0.92);
            background: rgba(255, 240, 220, 0.9);
        }
        .nav-title {
            font-size: 19px;
            font-weight: 800;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1.2px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .nav-title i {
            font-size: 22px;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-right-group {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .nav-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #b08968;
            font-size: 18px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
            transition: all 0.3s;
        }
        .nav-icon-btn:active {
            transform: scale(0.92);
            background: rgba(255, 240, 220, 0.9);
        }
        .dot-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 8px;
            height: 8px;
            background: #e8553d;
            border-radius: 50%;
            border: 1.5px solid #fff;
            z-index: 3;
        }
        /* 欢迎头部 */
        .welcome-hero {
            position: relative;
            margin-top: 16px;
            background: linear-gradient(155deg,
                    #fffef9 0%, #fffdf2 18%, #fff9e2 40%,
                    #fff5d5 65%, #fef0ca 100%);
            border-radius: 28px;
            padding: 20px 18px 20px;
            text-align: center;
            box-shadow:
                0 18px 42px rgba(200, 130, 20, 0.10),
                0 3px 10px rgba(0, 0, 0, 0.025),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1.5px solid rgba(240, 185, 11, 0.35);
            overflow: hidden;
        }
        .welcome-hero::before {
            content: '';
            position: absolute;
            top: -55px;
            right: -45px;
            width: 170px;
            height: 170px;
            background: radial-gradient(circle, rgba(249, 212, 35, 0.16), transparent 68%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .welcome-hero::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -35px;
            width: 140px;
            height: 140px;
            background: radial-gradient(circle, rgba(255, 160, 40, 0.10), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .hero-badge-top {
            position: relative;
            z-index: 1;
            display: inline-block;
            background: rgba(201, 164, 75, 0.14);
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            color: #a07d2e;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .hero-badge-top i {
            margin-right: 4px;
            font-size: 12px;
            color: #c9a44b;
        }
        .hero-crown-icon {
            position: relative;
            z-index: 1;
            display: inline-flex;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(145deg, #fff8d6, #ffe9a8, #ffda70);
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(240, 185, 11, 0.35),
                0 0 0 6px rgba(249, 212, 35, 0.12),
                0 0 0 14px rgba(249, 212, 35, 0.05);
            margin-bottom: 12px;
            animation: crownPulse 2.8s ease-in-out infinite;
        }
        @keyframes crownPulse {
            0%,
            100% {
                transform: translateY(0) scale(1);
                box-shadow: 0 8px 24px rgba(240, 185, 11, 0.35), 0 0 0 6px rgba(249, 212, 35, 0.12), 0 0 0 14px rgba(249, 212, 35, 0.05);
            }
            50% {
                transform: translateY(-5px) scale(1.06);
                box-shadow: 0 14px 32px rgba(240, 185, 11, 0.5), 0 0 0 10px rgba(249, 212, 35, 0.2), 0 0 0 20px rgba(249, 212, 35, 0.08);
            }
        }
        .hero-crown-icon i {
            background: linear-gradient(135deg, #c87010, #9a5a08);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-title {
            position: relative;
            z-index: 1;
            font-size: 21px;
            font-weight: 900;
            background: linear-gradient(135deg, #5c3800, #8b5a10, #5c3800);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1.2px;
            margin-bottom: 5px;
        }
        .hero-subtitle {
            position: relative;
            z-index: 1;
            font-size: 12px;
            color: #9b7a50;
            font-weight: 500;
            letter-spacing: 0.5px;
            line-height: 1.5;
        }
        .hero-tags-row {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .hero-tag-mini {
            background: #f8f9fa;
            padding: 4px 11px;
            border-radius: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            color: #495057;
            font-weight: 600;
            letter-spacing: 0.3px;
            border: 1px solid rgba(200, 170, 120, 0.2);
        }
        .hero-tag-mini i {
            color: #c9a44b;
            font-size: 11px;
        }
        .hero-sparkle-row {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 6px;
        }
        .hero-sparkle {
            font-size: 10px;
            color: #d4a017;
            opacity: 0.7;
            animation: sparkleFloat 2s ease-in-out infinite;
        }
        .hero-sparkle:nth-child(2) {
            animation-delay: 0.5s;
            font-size: 13px;
            opacity: 0.9;
        }
        .hero-sparkle:nth-child(3) {
            animation-delay: 1s;
        }
        @keyframes sparkleFloat {
            0%,
            100% {
                transform: translateY(0);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-7px);
                opacity: 1;
            }
        }
        /* 会员卡选择区 */
        .cards-section {
            margin-top: 16px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 11px;
        }
        .vip-plan-card {
            position: relative;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-radius: 24px;
            padding: 18px 12px 16px;
            text-align: center;
            border: 1.5px solid rgba(220, 180, 100, 0.3);
            box-shadow: 0 12px 28px rgba(180, 120, 30, 0.06),
                0 2px 6px rgba(0, 0, 0, 0.02),
                inset 0 1px 0 rgba(255, 255, 255, 0.75);
            transition: all 0.35s cubic-bezier(0.22, 0.9, 0.18, 1);
            cursor: pointer;
            overflow: hidden;
            z-index: 1;
        }
        .vip-plan-card:active {
            transform: scale(0.95);
            box-shadow: 0 6px 16px rgba(180, 120, 30, 0.12);
        }
        .vip-plan-card.recommended {
            border: 2px solid rgba(240, 185, 11, 0.6);
            box-shadow: 0 16px 36px rgba(220, 150, 20, 0.14),
                0 0 0 5px rgba(249, 212, 35, 0.06),
                0 2px 6px rgba(0, 0, 0, 0.02),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            z-index: 2;
            background: rgba(255, 255, 250, 0.88);
        }
        .vip-plan-card.recommended::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(249, 212, 35, 0.2), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .recommend-badge {
            position: absolute;
            top: -1px;
            right: 12px;
            background: linear-gradient(135deg, #f9d423, #e6a800, #d4840a);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 0 0 12px 12px;
            letter-spacing: 0.8px;
            box-shadow: 0 4px 14px rgba(240, 185, 11, 0.4);
            z-index: 3;
            animation: badgeShine 2.5s ease-in-out infinite;
        }
        .card-badge-yearly {
            position: absolute;
            top: 8px;
            right: 10px;
            background: linear-gradient(135deg, #ff9a56, #ff6b35);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 0 0 10px 10px;
            letter-spacing: 0.5px;
            box-shadow: 0 3px 10px rgba(255, 107, 53, 0.3);
            z-index: 3;
        }
        @keyframes badgeShine {
            0%,
            100% {
                box-shadow: 0 4px 14px rgba(240, 185, 11, 0.4);
            }
            50% {
                box-shadow: 0 6px 22px rgba(240, 185, 11, 0.7), 0 0 0 4px rgba(249, 212, 35, 0.15);
            }
        }
        .plan-icon-wrap {
            position: relative;
            z-index: 1;
            display: inline-flex;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 8px;
        }
        .plan-icon-wrap.yearly {
            background: linear-gradient(135deg, #fff3d0, #ffe8a8);
            box-shadow: 0 5px 16px rgba(210, 160, 40, 0.25);
            color: #b8860b;
        }
        .plan-icon-wrap.forever {
            background: linear-gradient(135deg, #ffe8b0, #ffd670, #f9c830);
            box-shadow: 0 6px 20px rgba(240, 185, 11, 0.4);
            color: #7a4e00;
            animation: iconGlow 2.5s ease-in-out infinite;
        }
        @keyframes iconGlow {
            0%,
            100% {
                box-shadow: 0 6px 20px rgba(240, 185, 11, 0.4);
            }
            50% {
                box-shadow: 0 8px 28px rgba(240, 185, 11, 0.65), 0 0 0 6px rgba(249, 212, 35, 0.18);
            }
        }
        .plan-name {
            position: relative;
            z-index: 1;
            font-size: 15px;
            font-weight: 800;
            color: #4a3020;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .plan-duration {
            position: relative;
            z-index: 1;
            font-size: 10px;
            color: #9b7a50;
            font-weight: 500;
            margin-bottom: 8px;
        }
        .plan-price-area {
            position: relative;
            z-index: 1;
            margin-bottom: 10px;
        }
        .plan-price-original {
            font-size: 12px;
            color: #bfa080;
            text-decoration: line-through;
            font-weight: 500;
            margin-bottom: 1px;
        }
        .plan-price-current {
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(135deg, #c87010, #8b4a08);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
        }
        .plan-price-current .symbol {
            font-size: 16px;
            font-weight: 700;
        }
        .plan-btn {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            width: 100%;
            padding: 11px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.6px;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            color: #fff;
            background: linear-gradient(135deg, #ff8c38, #e65c20, #d14410);
            box-shadow: 0 5px 18px rgba(230, 90, 30, 0.3);
        }
        .plan-btn:active {
            transform: scale(0.93);
            box-shadow: 0 3px 10px rgba(230, 90, 30, 0.2);
        }
        .plan-btn.btn-gold {
            background: linear-gradient(135deg, #f9d423, #e6a800, #d4840a);
            color: #4a2800;
            box-shadow: 0 5px 20px rgba(240, 185, 11, 0.4);
            animation: btnGlow 2.5s ease-in-out infinite;
        }
        @keyframes btnGlow {
            0%,
            100% {
                box-shadow: 0 5px 20px rgba(240, 185, 11, 0.4);
            }
            50% {
                box-shadow: 0 8px 28px rgba(240, 185, 11, 0.65), 0 0 0 5px rgba(249, 212, 35, 0.12);
            }
        }
        .plan-btn.btn-gold:active {
            transform: scale(0.93);
            box-shadow: 0 3px 12px rgba(240, 185, 11, 0.25);
        }
        /* VIP权益区 */
        .benefits-section {
            margin-top: 16px;
            background: rgba(255, 255, 255, 0.76);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: 18px 16px 14px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.035),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(220, 180, 100, 0.28);
        }
        .benefits-title {
            font-size: 15px;
            font-weight: 800;
            color: #4a3020;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }
        .benefits-title i {
            font-size: 19px;
            color: #e6a800;
        }
        .benefits-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 10px;
            background: rgba(255, 250, 240, 0.7);
            border-radius: 14px;
            border: 1px solid rgba(220, 180, 100, 0.18);
            transition: all 0.3s;
            cursor: default;
        }
        .benefit-item:active {
            background: rgba(255, 240, 210, 0.85);
            transform: scale(0.97);
        }
        .benefit-icon-mini {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            background: linear-gradient(135deg, #fff8d6, #ffe9a8);
            color: #b8860b;
            box-shadow: 0 3px 10px rgba(210, 160, 40, 0.2);
        }
        .benefit-text {
            flex: 1;
            min-width: 0;
        }
        .benefit-label {
            font-size: 12px;
            font-weight: 700;
            color: #4a3020;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .benefit-desc {
            font-size: 10px;
            color: #9b7a50;
            letter-spacing: 0.2px;
            line-height: 1.3;
        }
        /* 底部保证区 */
        .trust-strip {
            margin-top: 14px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 11px;
            color: #9b7a50;
            font-weight: 500;
            letter-spacing: 0.4px;
        }
        .trust-strip span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .trust-strip i {
            color: #4caf50;
            font-size: 13px;
        }
        .footer-note-mini {
            text-align: center;
            font-size: 10px;
            color: #9aa0ac;
            margin-top: 10px;
            letter-spacing: 0.3px;
            position: relative;
            z-index: 1;
        }
        /* Toast */
        .toast-container {
            position: fixed;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            pointer-events: none;
        }
        .toast {
            padding: 12px 22px;
            background: rgba(30, 32, 40, 0.92);
            backdrop-filter: blur(12px);
            color: #fff;
            border-radius: 24px;
            font-size: 13px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            animation: toastIn 0.35s cubic-bezier(0.22, 0.9, 0.18, 1) forwards,
                toastOut 0.3s cubic-bezier(0.55, 0, 1, 0.45) 1.6s forwards;
            white-space: nowrap;
            letter-spacing: 0.4px;
        }
        @keyframes toastIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.88);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes toastOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(-10px) scale(0.9);
            }
        }
        /* 弹窗遮罩 */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(20, 16, 10, 0.6);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6%;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.35s cubic-bezier(0.22, 0.9, 0.18, 1);
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        /* 弹窗卡片 */
        .modal-dialog {
            background: rgba(255, 255, 252, 0.94);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border-radius: 28px;
            padding: 24px 18px 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 24px 56px rgba(0, 0, 0, 0.2),
                0 6px 16px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.85);
            border: 1.5px solid rgba(240, 185, 11, 0.3);
            position: relative;
            overflow: hidden;
            transform: translateY(18px) scale(0.94);
            transition: transform 0.4s cubic-bezier(0.22, 0.9, 0.18, 1);
            z-index: 9999;
        }
        .modal-overlay.active .modal-dialog {
            transform: translateY(0) scale(1);
        }
        .modal-dialog::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(249, 212, 35, 0.14), transparent 65%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .modal-dialog::after {
            content: '';
            position: absolute;
            bottom: -35px;
            left: -35px;
            width: 120px;
            height: 120px;
            background: radial-gradient(circle, rgba(255, 160, 40, 0.08), transparent 60%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .modal-close-btn {
            position: absolute;
            top: 12px;
            right: 14px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(240, 235, 225, 0.7);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #8b6f5c;
            z-index: 10;
            transition: all 0.3s;
        }
        .modal-close-btn:active {
            transform: scale(0.88);
            background: rgba(220, 200, 170, 0.8);
        }
        .modal-icon-area {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-bottom: 10px;
        }
        .modal-icon-circle {
            display: inline-flex;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, #fff8d6, #ffe9a8, #ffda70);
            box-shadow: 0 7px 22px rgba(240, 185, 11, 0.35);
        }
        .modal-icon-circle i {
            background: linear-gradient(135deg, #c87010, #8b4a08);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .modal-plan-label {
            position: relative;
            z-index: 1;
            text-align: center;
            font-size: 16px;
            font-weight: 800;
            color: #4a3020;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
        }
        .modal-plan-price {
            position: relative;
            z-index: 1;
            text-align: center;
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #c87010, #8b4a08);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .modal-plan-price .sm {
            font-size: 18px;
            font-weight: 700;
        }
        .modal-policy-box {
            position: relative;
            z-index: 1;
            background: rgba(255, 248, 235, 0.75);
            border-radius: 16px;
            padding: 14px 14px 10px;
            margin: 12px 0 16px;
            border: 1px solid rgba(220, 180, 100, 0.25);
        }
        .modal-policy-title {
            font-size: 12px;
            font-weight: 700;
            color: #6b4d28;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.4px;
        }
        .modal-policy-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .modal-policy-list li {
            font-size: 11px;
            color: #6b5a48;
            display: flex;
            align-items: flex-start;
            gap: 6px;
            line-height: 1.4;
            letter-spacing: 0.3px;
        }
        .modal-policy-list li i {
            flex-shrink: 0;
            margin-top: 1px;
            font-size: 13px;
        }
        .modal-policy-list li i.icon-check {
            color: #4caf50;
        }
        .modal-policy-list li i.icon-warn {
            color: #e6a800;
        }
        .modal-policy-highlight {
            background: #fef7e8;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 11px;
            color: #b56f1a;
            line-height: 1.5;
            letter-spacing: 0.3px;
            text-align: center;
            margin-bottom: 6px;
            border: 1px solid rgba(220, 170, 80, 0.2);
        }
        .modal-policy-highlight i {
            margin-right: 4px;
            font-size: 12px;
        }
        .modal-actions {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 10px;
        }
        .modal-btn {
            flex: 1;
            padding: 13px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.6px;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
        }
        .modal-btn-cancel {
            background: rgba(240, 235, 225, 0.7);
            color: #6b5a48;
            border: 1px solid rgba(200, 180, 150, 0.35);
        }
        .modal-btn-cancel:active {
            transform: scale(0.94);
            background: rgba(220, 210, 195, 0.85);
        }
        .modal-btn-confirm {
            background: linear-gradient(135deg, #f9d423, #e6a800, #d4840a);
            color: #4a2800;
            box-shadow: 0 6px 22px rgba(240, 185, 11, 0.45);
        }
        .modal-btn-confirm:active {
            transform: scale(0.94);
            box-shadow: 0 3px 12px rgba(240, 185, 11, 0.3);
        }
        .modal-bottom-note {
            position: relative;
            z-index: 1;
            text-align: center;
            font-size: 10px;
            color: #9aa0ac;
            margin-top: 10px;
            letter-spacing: 0.3px;
        }
        /* 响应式 */
        @media screen and (max-width: 380px) {
            .cards-section {
                gap: 8px;
            }
            .vip-plan-card {
                padding: 14px 8px 13px;
                border-radius: 20px;
            }
            .plan-price-current {
                font-size: 24px;
            }
            .plan-price-current .symbol {
                font-size: 14px;
            }
            .plan-btn {
                font-size: 11px;
                padding: 10px 6px;
            }
            .plan-name {
                font-size: 13px;
            }
            .plan-icon-wrap {
                width: 36px;
                height: 36px;
                font-size: 17px;
            }
            .benefits-list {
                grid-template-columns: 1fr 1fr;
                gap: 5px;
            }
            .benefit-item {
                padding: 8px 7px;
                gap: 5px;
                border-radius: 10px;
            }
            .benefit-label {
                font-size: 10px;
            }
            .benefit-desc {
                font-size: 9px;
            }
            .benefit-icon-mini {
                width: 24px;
                height: 24px;
                font-size: 11px;
            }
            .hero-title {
                font-size: 18px;
            }
            .hero-crown-icon {
                width: 46px;
                height: 46px;
                font-size: 23px;
            }
            .modal-dialog {
                padding: 18px 14px 16px;
                border-radius: 22px;
            }
            .modal-plan-price {
                font-size: 26px;
            }
            .modal-plan-price .sm {
                font-size: 15px;
            }
            .hero-tags-row {
                gap: 4px;
            }
            .hero-tag-mini {
                font-size: 9px;
                padding: 3px 8px;
            }
            .trust-strip {
                gap: 8px;
                font-size: 10px;
            }
        }
        @media screen and (max-width: 340px) {
            .cards-section {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .vip-plan-card.recommended {
                order: -1;
            }
            .benefits-list {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- 浮动背景光效 -->
    <div class="bg-float"></div>

    <!-- 浮动粒子 -->
    <div class="particles-container" id="particlesContainer"></div>

    <!-- Toast容器 -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- 主容器 -->
    <div class="container">

        <!-- ========== 顶部导航 ========== -->
        <nav class="nav">
            <div class="nav-inner">
                <a href="#" class="nav-back" id="navBackBtn" aria-label="返回">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
                <span class="nav-title">
                    <i class="ri-vip-crown-fill"></i> 签米VIP会员
                </span>
                <div class="nav-right-group">
                    <!-- 客服 -->
                    <div class="nav-icon-btn" id="serviceBtn" title="联系客服">
                        <i class="ri-customer-service-2-fill"></i>
                    </div>
                    <!-- 帮助 -->
                    <div class="nav-icon-btn" id="helpBtn" title="帮助说明">
                        <i class="ri-question-fill"></i>
                        <span class="dot-badge"></span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- ========== 欢迎头部 ========== -->
        <div class="welcome-hero">

            <div class="hero-crown-icon">
                <i class="ri-vip-crown-fill"></i>
            </div>
            <h1 class="hero-title">开通签米VIP会员</h1>
            <p class="hero-subtitle">200%获客效率 · 签到奖励翻倍 · 消费5折</p>
            <div class="hero-tags-row">
                <span class="hero-tag-mini"><i class="fa fa-bolt"></i> 签到翻倍</span>
                <span class="hero-tag-mini"><i class="fa fa-clock-o"></i> 优先审核</span>
                <span class="hero-tag-mini"><i class="fa fa-credit-card"></i> 手续费减免</span>
            </div>
            <div class="hero-sparkle-row">
                <span class="hero-sparkle">✦</span>
                <span class="hero-sparkle">✦</span>
                <span class="hero-sparkle">✦</span>
            </div>
        </div>

        <!-- ========== 会员卡选择区 ========== -->
        <div class="cards-section">
            <!-- 年卡 - 超值年包 -->
            <div class="vip-plan-card" id="cardYearly">
                <div class="card-badge-yearly">🔥 超值年包</div>
                <div class="plan-icon-wrap yearly">
                    <i class="ri-calendar-check-fill"></i>
                </div>
                <div class="plan-name">年度会员</div>
                <div class="plan-duration">有效期 365 天</div>
                <div class="plan-price-area">
                    <div class="plan-price-original">原价¥328</div>
                    <div class="plan-price-current">
                        <span class="symbol">¥</span>88
                    </div>
                </div>
                <button class="plan-btn" onclick="openModal('yearly')">
                    立即开通 <i class="ri-arrow-right-line" style="font-size:14px;"></i>
                </button>
            </div>

            <!-- 永久卡（推荐） -->
            <div class="vip-plan-card recommended" id="cardForever">
                <div class="recommend-badge">💎 终身尊享</div>
                <div class="plan-icon-wrap forever">
                    <i class="ri-vip-diamond-fill"></i>
                </div>
                <div class="plan-name">永久会员</div>
                <div class="plan-duration">一次开通 · 永久有效</div>
                <div class="plan-price-area">
                    <div class="plan-price-original">原价¥568</div>
                    <div class="plan-price-current">
                        <span class="symbol">¥</span>98
                    </div>
                </div>
                <button class="plan-btn btn-gold" onclick="openModal('forever')">
                    立即开通 <i class="ri-arrow-right-line" style="font-size:14px;"></i>
                </button>
            </div>
        </div>

        <!-- ========== VIP权益区 ========== -->
        <div class="benefits-section">
            <div class="benefits-title">
                <i class="ri-shining-fill"></i> VIP专属权益
            </div>
            <div class="benefits-list">
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-money-dollar-circle-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">签到25元/天</div>
                        <div class="benefit-desc">现金滑落加持</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-user-heart-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">直推30%提成</div>
                        <div class="benefit-desc">好友消费返利</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-group-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">间推5%提成</div>
                        <div class="benefit-desc">二级好友消费返利</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-bank-card-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">提现手续费7%</div>
                        <div class="benefit-desc">永久优惠费率</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-discount-percent-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">广告服务5折</div>
                        <div class="benefit-desc" style="white-space:nowrap;">年/永久赠99/200刷新卡</div>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon-mini"><i class="ri-vip-crown-fill"></i></div>
                    <div class="benefit-text">
                        <div class="benefit-label">VIP尊贵标识</div>
                        <div class="benefit-desc">专属身份特权</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== 底部信任条 ========== -->
        <div class="trust-strip">
            <span><i class="ri-clock-fill"></i> 秒级到账</span>
            <span><i class="ri-shield-check-fill"></i> 平台保障</span>
            <span><i class="ri-handshake-fill"></i> 叠加权益</span>
            <span><i class="ri-diamond-fill"></i> 高收益人脉</span>
        </div>
        <p class="footer-note-mini">* 开通后立即生效，权益仅限签米平台使用，支付后不支持退款。</p>
    </div>

    <!-- ========== 确认开通弹窗 ========== -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-dialog" id="modalDialog">
            <!-- 关闭按钮 -->
            <button class="modal-close-btn" onclick="closeModal()" aria-label="关闭">
                <i class="ri-close-line"></i>
            </button>

            <!-- 图标 -->
            <div class="modal-icon-area">
                <div class="modal-icon-circle">
                    <i class="ri-vip-crown-fill"></i>
                </div>
            </div>

            <!-- 套餐名称 -->
            <div class="modal-plan-label" id="modalPlanLabel">永久会员</div>

            <!-- 价格 -->
            <div class="modal-plan-price" id="modalPlanPrice">
                <span class="sm">¥</span>138
            </div>

            <!-- 政策说明 -->
            <div class="modal-policy-box">
                <div class="modal-policy-title">
                    <i class="ri-information-fill"></i> 开通须知
                </div>
                <!-- 高亮警告 -->
                <div class="modal-policy-highlight">
                    <i class="fa fa-exclamation-triangle"></i>
                    开通会员属于<strong>消费</strong>，不是投资！会员主要是获取平台特权（例如消费5折等），请根据自己需求开通。一旦支付后<strong>不受理任何理由的退款申请</strong>，请再次确认会员权益是否是你所需再决定是否开通。
                </div>
                <ul class="modal-policy-list">
                    <li>
                        <i class="ri-check-line icon-check"></i>
                        <span>开通后会员权益<strong>即时生效</strong></span>
                    </li>
                    <li>
                        <i class="ri-check-line icon-check"></i>
                        <span>享受全部VIP专属权益与福利</span>
                    </li>
                    <li>
                        <i class="ri-error-warning-line icon-warn"></i>
                        <span>虚拟商品开通后<strong>不支持退款</strong></span>
                    </li>
                    <li>
                        <i class="ri-information-line icon-warn"></i>
                        <span>如有疑问请联系客服咨询</span>
                    </li>
                </ul>
            </div>

            <!-- 按钮组 -->
            <div class="modal-actions">
                <button class="modal-btn modal-btn-cancel" onclick="closeModal()">
                    取消
                </button>
                <button class="modal-btn modal-btn-confirm" id="modalConfirmBtn" onclick="confirmPurchase()">
                    立即开通
                </button>
            </div>
            <div class="modal-bottom-note">开通后立即生效，特权即刻享用</div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // ============ 生成浮动粒子 ============
    (function() {
        const container = document.getElementById('particlesContainer');
        const count = 35;
        for (let i = 0; i < count; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = -(Math.random() * 10) + 's';
            particle.style.animationDuration = (7 + Math.random() * 8) + 's';
            container.appendChild(particle);
        }
    })();

    // ============ 弹窗逻辑 ============
    let currentPlan = 'forever';

    const planConfig = {
        'yearly': {
            label: '年度会员',
            price: '88',
            original: '¥328',
            duration: '有效期 365 天',
            type: 'yearly'
        },
        'forever': {
            label: '永久会员',
            price: '98',
            original: '¥568',
            duration: '一次开通 · 永久有效',
            type: 'forever'
        }
    };

    function openModal(planType) {
        currentPlan = planType;
        const config = planConfig[planType];
        document.getElementById('modalPlanLabel').textContent = config.label;
        document.getElementById('modalPlanPrice').innerHTML = '<span class="sm">¥</span>' + config.price;
        document.getElementById('modalConfirmBtn').textContent = '立即开通 · ¥' + config.price;
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const overlay = document.getElementById('modalOverlay');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function confirmPurchase() {
        const config = planConfig[currentPlan];
        showToast('正在生成订单...');
        $.ajax({
            type: 'GET',
            url: 'plugin.php?id=tb_toutiao&modac=member&submodac=create&type=' + config.type,
            dataType: 'json',
            success: function(res) {
                if (res.rs == 200) {
                    window.location.href = res.msg;
                } else {
                    showToast(res.msg || '订单生成失败');
                }
            },
            error: function() {
                showToast('网络错误，请稍后重试');
            }
        });
    }

    function showToast(message) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(function() {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 2000);
    }

    // 事件绑定
    document.getElementById('modalOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.getElementById('modalDialog').addEventListener('click', function(e) {
        e.stopPropagation();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('modalOverlay').classList.contains('active')) closeModal();
        }
    });
    document.getElementById('cardYearly').addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        openModal('yearly');
    });
    document.getElementById('cardForever').addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        openModal('forever');
    });
    document.getElementById('navBackBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) window.history.back();
        else showToast('当前在会员中心首页');
    });
    document.getElementById('serviceBtn').addEventListener('click', function() {
        showToast('📞 请在个人中心联系在线客服');
    });
    document.getElementById('helpBtn').addEventListener('click', function() {
        showToast('❓ 会员权益：开通即享所有标注特权，支付后不可退款');
    });
    console.log('✨ 签米VIP会员中心已就绪（动态支付）');
</script>
</body>
</html>