<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>推广宝 · 个人中心</title>
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
            0%, 100% { opacity: 0.75; transform: translate(0, 0) scale(1); }
            25% { opacity: 0.9; transform: translate(1.5%, -1%) scale(1.03); }
            50% { opacity: 0.8; transform: translate(-0.5%, 1.2%) scale(1.01); }
            75% { opacity: 0.95; transform: translate(1%, 0.5%) scale(1.04); }
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
            width: 3px; height: 3px;
            background: rgba(255, 150, 50, 0.5);
            border-radius: 50%;
            animation: floatUp 8s linear infinite;
            box-shadow: 0 0 6px rgba(255, 150, 40, 0.5);
        }
        .particle:nth-child(odd) { background: rgba(255, 180, 80, 0.45); animation-duration: 10s; animation-delay: -2s; width: 4px; height: 4px; }
        .particle:nth-child(3n) { background: rgba(255, 130, 60, 0.4); animation-duration: 7s; animation-delay: -4s; width: 2.5px; height: 2.5px; }
        .particle:nth-child(4n+1) { animation-duration: 9s; animation-delay: -1s; }
        .particle:nth-child(5n+2) { animation-duration: 11s; animation-delay: -3s; }
        @keyframes floatUp {
            0% { transform: translateY(105vh) translateX(0) scale(0.3); opacity: 0; }
            10% { opacity: 0.8; transform: translateY(90vh) translateX(15px) scale(0.8); }
            40% { opacity: 1; transform: translateY(50vh) translateX(-10px) scale(1.2); }
            70% { opacity: 0.6; transform: translateY(20vh) translateX(8px) scale(0.7); }
            100% { transform: translateY(-5vh) translateX(-5px) scale(0.1); opacity: 0; }
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            flex-shrink: 0;
            transition: all 0.3s;
        }
        .nav-back:active {
            transform: scale(0.92);
            background: rgba(255, 240, 220, 0.9);
        }
        .nav-title {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, #ff8c00, #ff2d55);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1.5px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        .nav-title i {
            font-size: 24px;
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
        /* 用户资料卡 */
        .profile-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 22px 18px;
            margin-top: 20px;
            box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8);
            border: 1px solid rgba(255,190,90,0.35);
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            overflow: visible;
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: -40px; right: -40px;
            width: 140px; height: 140px;
            background: radial-gradient(circle, rgba(255,160,40,0.14), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .avatar-wrap { position: relative; flex-shrink: 0; z-index: 1; }
        .avatar {
            width: 62px; height: 62px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffe0b8, #ffbc6c);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            box-shadow: 0 8px 20px rgba(255,140,30,0.25);
            border: 3px solid #fff;
            overflow: hidden;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-vip-badge {
            position: absolute; bottom: -2px; right: -4px;
            width: 22px; height: 22px;
            background: linear-gradient(135deg, #f0b90b, #d4a017);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: #fff;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(240,185,11,0.4);
            z-index: 2;
        }
        .profile-info { flex: 1; min-width: 0; z-index: 1; }
        .profile-name { font-size: 18px; font-weight: 800; color: #3d2b1a; margin-bottom: 2px; letter-spacing: 0.3px; }
        .profile-meta { display: flex; flex-direction: column; gap: 5px; margin-top: 4px; }
        .meta-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #8b6f5c;
            background: rgba(255,245,235,0.7);
            padding: 6px 10px; border-radius: 20px;
            cursor: pointer; transition: all 0.25s;
            white-space: nowrap; max-width: fit-content;
            border: 1px solid rgba(255,200,130,0.3);
            user-select: none;
        }
        .meta-row:active { background: rgba(255,220,180,0.8); transform: scale(0.96); }
        .meta-row .meta-label { font-weight: 600; color: #a07a5c; font-size: 11px; flex-shrink: 0; }
        .meta-row .meta-value { font-weight: 700; color: #4a3020; font-size: 12px; letter-spacing: 0.4px; overflow: hidden; text-overflow: ellipsis; }
        .meta-row .copy-icon { font-size: 13px; color: #c09060; flex-shrink: 0; transition: all 0.2s; }
        .meta-row.copied .copy-icon { color: #4caf50; }
        .profile-edit {
            flex-shrink: 0; z-index: 1;
            width: 36px; height: 36px;
            border-radius: 50%;
            background: rgba(255,240,220,0.8);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; text-decoration: none;
            color: #b08968; font-size: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid rgba(255,200,130,0.3);
            transition: all 0.3s;
        }
        .profile-edit:active { transform: scale(0.9); background: rgba(255,220,180,0.9); }
        /* VIP卡片 */
        .vip-card {
            background: linear-gradient(145deg, #fffdf5 0%, #fff8e6 30%, #fff3d5 70%, #ffecd0 100%);
            border-radius: 28px; padding: 20px 16px;
            margin-top: 16px;
            box-shadow: 0 18px 40px rgba(255,150,20,0.12), 0 3px 10px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.9);
            border: 1.5px solid rgba(240,185,11,0.45);
            position: relative; overflow: hidden; transition: all 0.3s;
        }
        .vip-card:active { transform: scale(0.97); }
        .vip-card::after {
            content: ''; position: absolute; top: -35px; right: -35px;
            width: 130px; height: 130px;
            background: radial-gradient(circle, rgba(240,185,11,0.18), transparent 70%);
            border-radius: 50%; pointer-events: none;
        }
        .vip-card-header { display: flex; align-items: center; gap: 10px; position: relative; z-index: 1; }
        .vip-level-icon {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #f9d423, #f0b90b);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: #fff;
            box-shadow: 0 6px 18px rgba(240,185,11,0.4); flex-shrink: 0;
        }
        .vip-info-text { flex: 1; }
        .vip-level-name { font-weight: 800; font-size: 16px; color: #4a3000; letter-spacing: 0.5px; }
        .vip-expire { font-size: 11px; color: #a08050; margin-top: 2px; }
        .vip-upgrade-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; border-radius: 60px;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            color: #fff; font-weight: 700; font-size: 13px;
            text-decoration: none; box-shadow: 0 4px 15px rgba(240,185,11,0.4);
            transition: all 0.3s; white-space: nowrap; flex-shrink: 0; position: relative; z-index: 2;
        }
        .vip-upgrade-btn:active { transform: scale(0.94); }
        .vip-privileges { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 14px; position: relative; z-index: 1; }
        .vip-priv-item {
            text-align: center; background: rgba(255,255,255,0.65);
            border-radius: 16px; padding: 10px 4px;
            border: 1px solid rgba(255,200,100,0.3); transition: all 0.3s; cursor: pointer;
        }
        .vip-priv-item:active { background: rgba(255,240,210,0.9); transform: scale(0.94); }
        .vip-priv-item i {
            font-size: 20px; background: linear-gradient(135deg, #ff7b00, #e63946);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; display: block; margin-bottom: 3px;
        }
        .vip-priv-item span { font-size: 10px; font-weight: 600; color: #6b4d28; letter-spacing: 0.3px; }
        /* 资产卡片 */
        .assets-card {
            background: rgba(255,255,255,0.82); backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px; padding: 18px 16px; margin-top: 16px;
            box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8);
            border: 1px solid rgba(255,190,90,0.35); position: relative; overflow: hidden;
        }
        .assets-card::before {
            content: ''; position: absolute; bottom: -50px; left: -50px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(255,150,50,0.08), transparent 65%);
            border-radius: 50%; pointer-events: none;
        }
        .assets-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; position: relative; z-index: 1; }
        .assets-title { font-size: 15px; font-weight: 800; color: #4a3020; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px; }
        .assets-title i { font-size: 20px; color: #ff8c00; }
        .assets-eye {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,245,235,0.7); display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 18px; color: #b08968; transition: all 0.3s; z-index: 2;
        }
        .assets-eye:active { background: rgba(255,220,180,0.9); transform: scale(0.9); }
        .assets-grid { display: flex; gap: 12px; position: relative; z-index: 1; }
        .asset-item {
            flex: 1; background: linear-gradient(160deg, #fffdf7 0%, #fff9ee 100%);
            border-radius: 22px; padding: 16px 12px; text-align: center;
            border: 1px solid rgba(255,200,110,0.35);
            box-shadow: 0 8px 22px rgba(255,150,30,0.06), inset 0 1px 0 rgba(255,255,255,0.7);
            transition: all 0.3s; position: relative; overflow: hidden;
        }
        .asset-item::after {
            content: ''; position: absolute; top: -20px; right: -20px;
            width: 70px; height: 70px;
            background: radial-gradient(circle, rgba(255,160,40,0.1), transparent 65%);
            border-radius: 50%; pointer-events: none;
        }
        .asset-item:active { transform: scale(0.96); }
        .asset-icon { display: inline-flex; width: 38px; height: 38px; border-radius: 50%; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 6px; position: relative; z-index: 1; }
        .asset-icon.commission { background: linear-gradient(135deg, #ffe0cc, #ffb380); color: #d35400; box-shadow: 0 4px 12px rgba(255,100,30,0.25); }
        .asset-icon.sign { background: linear-gradient(135deg, #fff3c8, #ffe08a); color: #b8860b; box-shadow: 0 4px 12px rgba(240,185,11,0.3); }
        .asset-label { font-size: 11px; color: #8b6f5c; font-weight: 600; margin-bottom: 3px; position: relative; z-index: 1; letter-spacing: 0.3px; }
        .asset-amount {
            font-size: 22px; font-weight: 900;
            background: linear-gradient(135deg, #ff7b00, #e63946);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; position: relative; z-index: 1; letter-spacing: 0.5px; margin-bottom: 8px;
        }
        .asset-withdraw {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 7px 16px; border-radius: 30px; font-size: 11px; font-weight: 700;
            text-decoration: none; position: relative; z-index: 1; transition: all 0.3s; letter-spacing: 0.4px;
            border: none; cursor: pointer;
            background: linear-gradient(135deg, #ff7b00, #e63946); color: #fff;
            box-shadow: 0 5px 15px rgba(255,50,0,0.25);
        }
        .asset-withdraw:active { transform: scale(0.93); }
        .asset-withdraw.sign-withdraw { background: linear-gradient(135deg, #f0b90b, #d4a017); color: #4a3000; box-shadow: 0 5px 15px rgba(240,185,11,0.35); }
        .project-row { text-align: center; margin-top: 16px; position: relative; z-index: 1; }
        .project-link { font-size: 13px; color: #d4a017; text-decoration: none; font-weight: 600; background: rgba(255,245,235,0.7); padding: 8px 18px; border-radius: 20px; display: inline-block; transition: all 0.3s; }
        .project-link:active { background: rgba(255,220,180,0.9); }
        /* 工具区 */
        .tools-section {
            margin-top: 16px; background: rgba(255,255,255,0.78);
            backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
            border-radius: 24px; padding: 16px 3% 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.04), inset 0 1px 0 rgba(255,255,255,0.7);
            border: 1px solid rgba(255,200,120,0.3);
        }
        .tools-title { font-size: 15px; font-weight: 800; color: #4a3020; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; letter-spacing: 0.5px; padding-left: 2px; }
        .tools-title i { font-size: 19px; color: #ff8c00; }
        .tools-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
        .tool-item {
            background: rgba(255,255,255,0.7); border-radius: 20px; padding: 16px 6px;
            text-align: center; text-decoration: none; color: #4a3020;
            border: 1px solid rgba(255,200,120,0.25);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s;
            display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer;
        }
        .tool-item:active { transform: scale(0.94); background: rgba(255,245,235,0.9); }
        .tool-icon-wrap { width: 42px; height: 42px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .tool-name { font-size: 12px; font-weight: 700; }
        .tool-badge {
            position: absolute; top: 6px; right: 6px; font-size: 9px; font-weight: 700;
            background: #ff4d4d; color: #fff; padding: 2px 7px; border-radius: 12px;
            z-index: 3; box-shadow: 0 2px 6px rgba(255,50,50,0.3);
        }
        .tool-badge1 {
            top: 6px; right: 0px; font-size: 9px; font-weight: 700;white-space: nowrap;
            background: #ff4d4d; color: #fff; padding: 2px 7px; border-radius: 12px;
            z-index: 3; box-shadow: 0 2px 6px rgba(255,50,50,0.3);
        }
        /* Toast */
        .toast-container { position: fixed; top: 50%; left: 50%; transform: translateX(-50%); z-index: 9999; pointer-events: none; }
        .toast {
            padding: 12px 22px; background: rgba(30,32,40,0.92); backdrop-filter: blur(12px);
            color: #fff; border-radius: 24px; font-size: 13px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            animation: toastIn 0.35s cubic-bezier(0.22,0.9,0.18,1) forwards,
                       toastOut 0.3s cubic-bezier(0.55,0,1,0.45) 1.6s forwards;
            white-space: nowrap;
        }
        @keyframes toastIn { from { opacity: 0; transform: translateY(12px) scale(0.88); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes toastOut { from { opacity: 1; transform: translateY(0) scale(1); } to { opacity: 0; transform: translateY(-10px) scale(0.9); } }
        /* 底部快捷 */
        .bottom-tools { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
        .bottom-tool-item {
            background: rgba(255,255,255,0.75); backdrop-filter: blur(12px);
            border-radius: 20px; padding: 14px; display: flex; align-items: center; gap: 10px;
            text-decoration: none; color: #4a3020; border: 1px solid rgba(255,200,120,0.25);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03); transition: all 0.3s;
        }
        .bottom-tool-item:active { transform: scale(0.96); background: rgba(255,245,235,0.9); }
        .bottom-tool-icon { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .bottom-tool-label { font-weight: 700; font-size: 13px; }
        @media screen and (max-width: 360px) {
            .vip-privileges { gap: 5px; }
            .vip-priv-item { padding: 8px 2px; border-radius: 12px; }
            .vip-priv-item i { font-size: 16px; }
            .vip-priv-item span { font-size: 9px; }
            .asset-amount { font-size: 18px; }
            .asset-withdraw { padding: 6px 12px; font-size: 10px; }
            .tools-grid { gap: 5px; }
            .tool-item { padding: 12px 4px; border-radius: 16px; }
            .tool-icon-wrap { width: 34px; height: 34px; border-radius: 12px; font-size: 17px; }
            .tool-name { font-size: 10px; }
            .avatar { width: 50px; height: 50px; }
            .profile-name { font-size: 15px; }
        }
    </style>
</head>
{eval}
$tb_cus_base_config  =  $_G['cache']['plugin']['tb_cus_base'];
$onlinecount = explode("~",$tb_cus_base_config['onlinecount']);
$showonlinecount = rand($onlinecount[0],$onlinecount[1]);
$showonlinecount = 0;
$cache_file_left = DISCUZ_ROOT.'./data/sysdata/cache_tb_cus_base.php';
if(($_G['timestamp'] - @filemtime($cache_file_left)) > $tb_cus_base_config['cachetime']*60) {
    $showonlinecount = rand($onlinecount[0],$onlinecount[1]);
    $contents[]=$showonlinecount;
    $cacheArray .= "\$contents=".arrayeval($contents).";\n";
    writetocache('tb_cus_base', $cacheArray);
}else{
    include_once $cache_file_left;
    $showonlinecount=$contents[0];
}

if($_G['uid']){
    $uid = $_G['uid'];
    $usermoney = DB::fetch_first('SELECT money FROM %t WHERE uid=%d', array("xigua_hb_user", $uid));
    $usermoney = $usermoney['money'];
    $usermfsxnum = DB::fetch_first('SELECT mfsxnum FROM %t WHERE uid=%d', array("xigua_hb_user", $uid));
    $usermfsxnum = $usermfsxnum['mfsxnum'];
    $software = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d', array("tb_cus_taojing_software", $uid));
    $software = $software['sc']?$software['sc']:0;
    $card = DB::fetch_first('SELECT count(id) as sc FROM %t WHERE uid=%d', array("tb_cus_card", $uid));
    $card = $card['sc']?$card['sc']:0;
    $totalmypub = C::t('#xigua_hb#xigua_hb_pub')->count_by_uid($_G['uid']);
    $xiguahh_user = C::t("#tb_cus_xiguahh#tb_cus_xiguahh_user")->fetch_first_field_data("*","where uid={$uid}");
    $ext2 = $xiguahh_user['money']?$xiguahh_user['money']:0.00;
    $myextcredits = getuserprofile('extcredits' . $config['credit_type']);
}

if($hhme['status'] == 1){
    $showhhmename = $hhme[joininfo][name];
}else{
    $oldback  = $hhme['oldback'];
    $oldjoin = unserialize($oldback);
    $oldjoin = unserialize($oldjoin['joininfo']);
    $showhhmename = $oldjoin['name'];
}
if($hhme['endts']){
    $hhendts = date("Y-m-d",$hhme['endts']);
}
require DISCUZ_ROOT . './source/plugin/tb_cus_mobilereg/common.php';
$yqmcode = getUserInviteCode($_G['uid']);
$commentnew = DB::result_first("select cid from %t  WHERE new=1 AND touid=%d", array('xigua_hb_comment', $_G['uid']));
{/eval}
{eval}
$hhendts = $hhme['endts_u'];
{/eval}
<body class="tgb-r06-account-page">
    <div class="bg-float"></div>
    <div class="particles-container" id="particlesContainer"></div>
    <div class="toast-container" id="toastContainer"></div>

    <!-- 导航栏 (修复右侧按钮布局) -->
    <div class="nav">
        <div class="nav-inner">
            <a href="#" class="nav-back" style="margin-left:15px;" aria-label="返回">
                <i class="ri-arrow-left-line"></i>
            </a>
            <div class="nav-title">
              个人中心
            </div>
            <div class="nav-right-group" style="margin-right:15px;">
                <a href="plugin.php?id=xigua_lt" class="nav-icon-btn" aria-label="消息">
                    {if $commentnew}<span class="dot-badge"></span>{/if}
                    <i class="ri-notification-3-line"></i>
                </a>
                <a href="plugin.php?id=xigua_hb&ac=shezhi" class="nav-icon-btn" aria-label="设置">
                    <i class="ri-settings-3-line"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- 用户资料卡 -->
        <div class="profile-card">
            <div class="avatar-wrap">
                <div class="avatar">
                    <img src="uc_server/avatar.php?uid={$_G['uid']}&size=middle&ts=1" alt="头像">
                </div>
              {if $hhme['joininfo']['name'] == '签米会员'}
                <div class="avatar-vip-badge">
                    <i class="ri-vip-crown-fill" style="font-size:10px;"></i>
                </div>{/if}
            </div>
            <div class="profile-info">
                <div class="profile-name">$_G['username']</div>
                <div class="profile-meta">
                    <div class="meta-row" data-copy="{if $userlianghao}{$userlianghao}{else}$_G['uid']{/if}" onclick="copyToClipboard(this.getAttribute('data-copy'), this)" title="点击复制UID">
                        <span class="meta-label">UID</span>
                        <span class="meta-value">{if $userlianghao}{$userlianghao}{else}$_G['uid']{/if}</span>
                        <i class="ri-file-copy-line copy-icon"></i>
                    </div>
                    <div class="meta-row" data-copy="{$yqmcode}" onclick="copyToClipboard(this.getAttribute('data-copy'), this)" title="点击复制邀请码">
                        <span class="meta-label">邀请码</span>
                        <span class="meta-value">{$yqmcode}</span>
                        <i class="ri-file-copy-line copy-icon"></i>
                    </div>
                </div>
            </div>
            <a href="#" class="profile-edit" aria-label="编辑资料" title="编辑资料">
                <i class="ri-edit-2-line"></i>
            </a>
        </div>

        <!-- VIP会员卡片 -->
        <div class="vip-card">
            <div class="vip-card-header">
                <div class="vip-level-icon"><i class="ri-vip-crown-fill"></i></div>
                <div class="vip-info-text">
                    {if $hhme['joininfo']['name'] == '签米会员'}
                        <div class="vip-level-name">推广宝会员</div>
                    {elseif $hhme['joininfo']['name'] == '商业会员'}
                        <div class="vip-level-name">商业会员</div>
                    {else}
                        <div class="vip-level-name">普通会员</div>
                    {/if}
                    <div class="vip-expire">有效期至 {$hhendts}</div>
                </div>
                {if $hhme['joininfo']['name'] == '签米会员'}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn"><i class="ri-star-fill"></i> 你已开通</a>
                {elseif $hhme['joininfo']['name'] == '商业会员'}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn" style="background: linear-gradient(135deg, #ff7b00, #e63946);"><i class="ri-diamond-fill"></i> 升级会员</a>
                {else}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn"><i class="ri-rocket-fill" style="background: linear-gradient(135deg, #ff7b00, #e63946);"></i> 开通会员</a>
                {/if}
            </div>
            <div class="vip-privileges">
                <div class="vip-priv-item"><i class="ri-shield-star-fill"></i><span>利润分红</span></div>
                <div class="vip-priv-item"><i class="ri-double-quotes-r"></i><span>签到加成</span></div>
                <div class="vip-priv-item"><i class="ri-flashlight-fill"></i><span>提现低费率</span></div>
                <div class="vip-priv-item"><i class="ri-customer-service-2-fill"></i><span>消费5折</span></div>
            </div>
        </div>

        <!-- 资产余额卡片 -->
        <div class="assets-card">
            <div class="assets-header">
                <div class="assets-title"><i class="ri-wallet-3-line"></i> 我的钱包</div>
                <div class="assets-eye" id="eyeToggle"><i class="ri-eye-line"></i></div>
            </div>
            <div class="assets-grid">
                <div class="asset-item">
                    <div class="asset-icon commission"><i class="ri-money-dollar-circle-fill"></i></div>
                    <div class="asset-label">提成账户(元)</div>
                    <div class="asset-amount balance-amount" data-original="¥{$usermoney}">¥{$usermoney}</div>
                    <a href="plugin.php?id=xigua_hb&ac=qianbao" class="asset-withdraw"><i class="ri-bank-line"></i> 立即提现</a>
                </div>
                <div class="asset-item">
                    <div class="asset-icon sign"><i class="ri-money-dollar-circle-fill"></i></div>
                    <div class="asset-label">签到账户(元)</div>
                    <div class="asset-amount balance-amount" data-original="¥{$ext2}">¥{$ext2}</div>
                    <a href="plugin.php?id=tb_cus_xiguahh:tx" class="asset-withdraw sign-withdraw"><i class="ri-bank-line"></i> 立即提现</a>
                </div>
            </div>

        </div>

    <!-- ========== 常用工具区 ========== -->
        <div class="tools-section">
            <div class="tools-title">
                <i class="ri-apps-2-line"></i> 常用工具
            </div>
            <div class="tools-grid">

                <!-- 邀请好友 -->
                <a href="plugin.php?id=xigua_hh&ac=invite" class="tool-item" style="position:relative;">
                    <span class="tool-badge">热</span>
                    <div class="tool-icon-wrap t1">
                       <i class="fa fa-paper-plane"></i>
                    </div>
                    <span class="tool-name">邀请好友</span>
                </a>
                <!-- 签到日历 -->
                <a href="plugin.php?id=xigua_hh&ac=myfans" class="tool-item">
                    <div class="tool-icon-wrap t2">
                       <i class="fa fa-users"></i>
                    </div>
                    <span class="tool-name">我的团队</span>
                </a>
                <!-- 收益明细 -->
                <a href="plugin.php?id=xiaomy_certification" class="tool-item" style="position:relative;">
                    <span class="tool-badge">认证</span>
                    <div class="tool-icon-wrap t3">

                        <i class="fa fa-id-card"></i>
                    </div>
                    <span class="tool-name">实名认证</span>
                </a>

                                 <!-- 我的团队 -->
                <a href="plugin.php?id=xigua_hb&ac=refresh&do=sxtc" class="tool-item">
                    <div class="tool-icon-wrap t4">
                        <i class="fa fa-refresh"></i>
                    </div>
                    <span class="tool-name">刷新卡({$usermfsxnum})</span>
                </a>
                <!-- 在线客服 -->
                <a href="plugin.php?id=tb_toutiao" class="tool-item">
                    <div class="tool-icon-wrap t5">
                       <i class="fa fa-newspaper-o"></i>
                    </div>
                    <span class="tool-name">头条广告</span>
                </a>
                <!-- 帮助中心 -->
                <a href="plugin.php?id=tb_toutiao&modac=super_main" class="tool-item">
                    <div class="tool-icon-wrap t6">
                       <i class="fa fa-picture-o"></i>
                    </div>
                    <span class="tool-name">超级头条</span>
                </a>
                 <!-- 在线客服 -->
                <a href="plugin.php?id=xigua_hb&ac=mypub" class="tool-item">
                    <div class="tool-icon-wrap t5">
                        <i class="ri-customer-service-line"></i>
                    </div>
                    <span class="tool-name">我的项目({$totalmypub})</span>
                </a>
                <!-- 我的团队 -->
                <a href="plugin.php?id=xigua_hb&ac=manage&stat=display&display=0" class="tool-item">
                    <div class="tool-icon-wrap t4">
                        <i class="ri-team-line"></i>
                    </div>
                    <span class="tool-name">项目审核</span>
                </a>

            </div>
        </div>

        <!-- ========== 底部额外入口 ========== -->
        <div class="tools-section" style="margin-top:12px;">
            <div class="tools-grid" style="grid-template-columns: repeat(2,1fr);">
                <a href="https://kkkk.zz-yihao.com/chat/index?noCanClose=1&token=61d4c34590b608c3b43da92e5258edcf&kefu_id=12" class="tool-item" id="onlineServiceLink" style="flex-direction:row;gap:10px;padding:14px 14px;justify-content:flex-start;">
                    <div class="tool-icon-wrap t3" style="width:34px;height:34px;border-radius:12px;font-size:16px;">
                        <i class="ri-notification-3-line"></i>
                    </div>
                    <span class="tool-name" style="font-size:13px;">在线客服</span>
                </a>
                <a href="done/app.html" class="tool-item" style="flex-direction:row;gap:10px;padding:14px 14px;justify-content:flex-start;">
                    <div class="tool-icon-wrap t4" style="width:34px;height:34px;border-radius:12px;font-size:16px;">
                        <i class="ri-shield-check-line"></i>
                    </div>
                    <span class="tool-name" style="font-size:13px;">APP/加速器</span>
                </a>
            </div>
        </div>



    <!-- ========== 常用工具区 ========== -->
        <div class="tools-section" style="margin-bottom:100px;">
            <div class="tools-title">
                <i class="ri-apps-2-line"></i> 更多好玩
            </div>
            <div class="tools-grid">

                <!-- 邀请好友 -->
                <a href="https://qm.suewammes.com/plugin.php?id=xigua_hb&ac=view&pubid=16952" class="tool-item" style="position:relative;">
                    <span class="tool-badge">推荐</span>
                    <div class="tool-icon-wrap t1">
                      <img style="width:100%;padding:0px;border-radius:15px;" src="https://img.imehui.com/20260519/17791639576a0be3354af63.png">
                    </div>
                    <span class="tool-name">趣赚汇</span>
                </a>

           </div>
            </div>
        </div>
    <!--{template xigua_hb:tab5}-->
    <script src="source/plugin/xigua_hb/static/lib/jquery-2.1.4.js?51{VERHASH}"></script>
    {template tb_cus_adv:myadvshow}

    <script>
        (function initParticles() {
            const container = document.getElementById('particlesContainer');
            for (let i = 0; i < 18; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                p.style.left = Math.random() * 90 + '%';
                p.style.animationDelay = -(Math.random() * 10) + 's';
                p.style.animationDuration = (6 + Math.random() * 10) + 's';
                container.appendChild(p);
            }
        })();

        function showToast(msg) {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = '✓ ' + msg;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 1800);
        }

        function copyToClipboard(text, element) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('复制成功');
                    if (element) {
                        element.classList.add('copied');
                        setTimeout(() => element.classList.remove('copied'), 1500);
                    }
                }).catch(() => fallbackCopy(text, element));
            } else {
                fallbackCopy(text, element);
            }
        }

        function fallbackCopy(text, element) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '-9999px';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                if (document.execCommand('copy')) {
                    showToast('复制成功');
                    if (element) {
                        element.classList.add('copied');
                        setTimeout(() => element.classList.remove('copied'), 1500);
                    }
                } else {
                    showToast('复制失败，请手动复制');
                }
            } catch (err) {
                showToast('复制失败，请手动复制');
            }
            document.body.removeChild(textarea);
        }

        let hidden = false;
        const eyeBtn = document.getElementById('eyeToggle');
        const balanceAmounts = document.querySelectorAll('.balance-amount');
        eyeBtn.addEventListener('click', () => {
            hidden = !hidden;
            balanceAmounts.forEach(el => {
                el.textContent = hidden ? '******' : el.dataset.original;
            });
            eyeBtn.innerHTML = hidden ? '<i class="ri-eye-off-line"></i>' : '<i class="ri-eye-line"></i>';
        });

        (function() {
            const onlineLink = document.getElementById('onlineServiceLink');
            if (onlineLink) {
                onlineLink.addEventListener('click', function(e) {
                    const hours = new Date().getHours();
                    if (hours < 9 || hours >= 21) {
                        e.preventDefault();
                        showToast('当前客服已下班，请在早上9点30~晚上21点联系客服');
                    }
                });
            }
        })();
    </script>
    <link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css?20260727-r09-owner-v3">
</body>
</html>
