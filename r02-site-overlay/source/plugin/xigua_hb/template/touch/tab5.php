<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">

    <!-- 依赖：remixicon 图标库，若页面已引入可删除此行 -->
    <link href="source/plugin/xigua_hb/static/tgb-r02/vendor/remixicon-3.5.0/remixicon.css?v=20260726-r02" rel="stylesheet">
    <style>
        /* ========== 底部导航 · 独立命名空间 qmn- ========== */
        /* 所有样式均使用 qmn- 前缀，避免与页面其他 CSS 冲突 */

        .qmn-nav {
            /* --- CSS 自定义变量（局部作用域，拓展性强）--- */
            --qmn-h: 65px;
            /* 导航主体高度 */
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

            /* --- 基础定位 --- */
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
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
            /* 确保在移动端全宽 */
        }

        /* --- 顶部光晕线条（灵动装饰）--- */
        .qmn-nav::before {
            content: '';
            position: absolute;
            top: 0;
            left: 6%;
            width: 88%;
            height: 1.2px;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 180, 70, 0.15) 15%,
                    rgba(255, 150, 40, 0.5) 35%,
                    rgba(240, 185, 11, 0.55) 50%,
                    rgba(255, 150, 40, 0.5) 65%,
                    rgba(255, 180, 70, 0.15) 85%,
                    transparent 100%);
            border-radius: 1px;
            pointer-events: none;
            z-index: 2;
            animation: qmn-shimmer 3.5s ease-in-out infinite;
        }

        @keyframes qmn-shimmer {
            0%,
            100% {
                opacity: 0.55;
            }
            40% {
                opacity: 1;
            }
            70% {
                opacity: 0.65;
            }
        }

        /* --- 导航项 --- */
        .qmn-nav .qmn-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            flex: 1;
            min-width: 0;
            height: 100%;
            cursor: pointer;
            text-decoration: none;
            color: var(--qmn-inactive-color);
            transition: color var(--qmn-transition), transform var(--qmn-transition);
            -webkit-tap-highlight-color: transparent;
            outline: none;
            z-index: 3;
            /* 确保可点击区域充足 */;
        }

        /* --- 按压反馈 --- */
        .qmn-nav .qmn-item:active {
            transform: scale(0.9);
            transition: transform 0.12s cubic-bezier(0.25, 0.1, 0.25, 1);
        }

        /* --- 图标容器 --- */
        .qmn-nav .qmn-icon-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 28px;
            transition: transform var(--qmn-transition);
            z-index: 1;
        }
        .qmn-nav .qmn-icon-wrap i {
            font-size: var(--qmn-icon-size);
            line-height: 1;
            transition: all var(--qmn-transition);
            display: inline-block;
        }

        /* --- 文字标签 --- */
        .qmn-nav .qmn-label {
            font-size: var(--qmn-label-size);
            font-weight: 600;
            letter-spacing: 0.4px;
            line-height: 1;
            transition: all var(--qmn-transition);
            white-space: nowrap;
        }

        /* --- 选中态指示光点（图标正下方）--- */
        .qmn-nav .qmn-dot {
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%) translateY(4px) scale(0);
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--qmn-active-color);
            box-shadow: 0 0 9px var(--qmn-active-glow), 0 0 20px var(--qmn-active-glow);
            transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1),
                opacity 0.25s ease;
            opacity: 0;
            pointer-events: none;
            z-index: 0;
        }

        /* --- 选中态样式 --- */
        .qmn-nav .qmn-item.active {
            color: #4a3000;
            /* 深色文字 */;
        }
        .qmn-nav .qmn-item.active .qmn-icon-wrap {
            transform: translateY(-1.5px);
        }
        .qmn-nav .qmn-item.active .qmn-icon-wrap i {
            background: linear-gradient(135deg, #f0b90b, #e6a200);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: drop-shadow(0 2px 5px rgba(240, 185, 11, 0.45));
        }
        .qmn-nav .qmn-item.active .qmn-dot {
            transform: translateX(-50%) translateY(0) scale(1);
            opacity: 1;
        }
        .qmn-nav .qmn-item.active .qmn-label {
            font-weight: 700;
            color: #5c3d1a;
            letter-spacing: 0.6px;
        }

        /* --- 选中项整体微上浮 --- */
        .qmn-nav .qmn-item.active {
            transform: translateY(-2px);
        }
        /* active 时取消 :active 的叠加缩放（避免双击感） */
        .qmn-nav .qmn-item.active:active {
            transform: translateY(-2px) scale(0.94);
        }

        /* --- 角标（拓展预留，默认隐藏）--- */
        .qmn-nav .qmn-badge {
            position: absolute;
            top: -3px;
            right: -10px;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            border-radius: 10px;
            background: #ff4d4d;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            line-height: 16px;
            text-align: center;
            white-space: nowrap;
            box-shadow: 0 2px 7px rgba(255, 50, 50, 0.35);
            z-index: 5;
            display: none;
            /* 默认隐藏，添加 .show 类时显示 */
            letter-spacing: 0.2px;
            animation: qmn-badge-pulse 2s ease-in-out infinite;
        }
        .qmn-nav .qmn-badge.show {
            display: inline-block;
        }
        @keyframes qmn-badge-pulse {
            0%,
            100% {
                box-shadow: 0 2px 7px rgba(255, 50, 50, 0.35);
            }
            50% {
                box-shadow: 0 3px 14px rgba(255, 50, 50, 0.6), 0 0 0 4px rgba(255, 77, 77, 0.15);
            }
        }

    </style>
</head>
<body>

    <!-- ==================== 底部导航（独立组件） ==================== -->
    <nav class="qmn-nav" role="navigation" aria-label="底部导航">
        <!-- 首页（默认选中） -->
        <a href="plugin.php?id=xigua_hb" class="qmn-item" title="首页">
            <span class="qmn-icon-wrap">
                <i class="ri-home-line"></i>
                <span class="qmn-dot"></span>
            </span>
            <span class="qmn-label">首页</span>
        </a>

        <!-- 签到 -->
        <a href="plugin.php?id=view&modac=sign" class="qmn-item" title="签到">
            <span class="qmn-icon-wrap">
                <i class="ri-calendar-check-line"></i>
                <span class="qmn-dot"></span>
            </span>
            <span class="qmn-label">签到</span>
            <!-- 角标示例：添加 class="show" 可显示红点提示 -->
            <span class="qmn-badge">新</span>
        </a>

        <!-- 分红 -->
        <a href="plugin.php?id=tb_cus_pipei" class="qmn-item" title="分红">
            <span class="qmn-icon-wrap">
                <i class="ri-gift-line"></i>
                <span class="qmn-dot"></span>
            </span>
            <span class="qmn-label">分红</span>
        </a>

        <!-- 我的 -->
        <a href="plugin.php?id=xigua_hb&ac=my" class="qmn-item active" title="我的">
            <span class="qmn-icon-wrap">
                <i class="ri-user-line"></i>
                <span class="qmn-dot"></span>
            </span>
            <span class="qmn-label">我的</span>
            <!-- 角标示例：需要时取消注释并添加 class="show"
            <span class="qmn-badge show">3</span>
            -->
        </a>
    </nav>

    <!-- ==================== 导航切换逻辑（可选，用于演示选中态切换） ==================== -->
    <script>
        (function() {
            // 获取所有导航项
            const nav = document.querySelector('.qmn-nav');
            if (!nav) return;
            const items = nav.querySelectorAll('.qmn-item');

            // 为每个导航项绑定点击事件，切换 active 类
            items.forEach(item => {
                item.addEventListener('click', function(e) {
                    // 移除所有导航项的 active 类
                    items.forEach(el => el.classList.remove('active'));
                    // 为当前点击项添加 active 类
                    this.classList.add('active');
                    // 链接正常跳转不受影响（本页面为演示，链接指向插件地址）
                });
            });

            // 可选：根据当前页面 URL 自动匹配激活项
          //  const currentPath = window.location.pathname + window.location.search;
         //   items.forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.includes(href.replace(/\.\//g, '').split('?')[0])) {
                    // 粗略匹配，生产环境建议根据实际路由逻辑调整
                    items.forEach(el => el.classList.remove('active'));
                    item.classList.add('active');
                }
            });
        })();
    </script>
</body>
</html>