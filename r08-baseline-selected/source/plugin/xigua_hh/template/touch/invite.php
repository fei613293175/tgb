<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>签米</title>
    <script src="https://cdn.tailwindcss.com/3.4.17">
    </script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js">
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pri': '#FF6B35',
                        'pri2': '#FF8C42',
                        'sec': '#1A1A2E',
                        'card': '#FFFFFF',
                        'bg': '#F5F0EB',
                        'muted': '#8C8C8C',
                        'border': '#E8E0D5',
                    },
                    fontFamily: {
                        sans: ['"SF Pro Display"', '-apple-system', 'BlinkMacSystemFont', '"PingFang SC"', '"Helvetica Neue"', 'sans-serif'],
                    },
                    boxShadow: {
                        'card': '0 4px 20px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04)',
                        'card-hover': '0 12px 36px rgba(0,0,0,0.10), 0 4px 12px rgba(0,0,0,0.06)',
                        'btn': '0 8px 24px rgba(255,107,53,0.30)',
                        'btn-lg': '0 14px 36px rgba(255,107,53,0.40)',
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            body {
                background: #F5F0EB !important;
                color: #1A1A2E !important;
                overflow-x: hidden;
                -webkit-font-smoothing: antialiased;
                -webkit-tap-highlight-color: transparent;
            }

            /* 隐藏滚动条但可滚动 */
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            /* 统计卡片 */
            .stat-card {
                background: #FFFFFF;
                border-radius: 20px;
                padding: 16px 12px;
                text-align: center;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
                border: 1px solid #EDE8E2;
                transition: all 0.3s;
            }
            .stat-card:active {
                transform: scale(0.96);
                background: #FFFBF7;
            }
            .stat-num {
                font-size: 28px;
                font-weight: 800;
                color: #FF6B35;
                letter-spacing: -1px;
                line-height: 1;
            }
            .stat-label {
                font-size: 11px;
                color: #8C8C8C;
                margin-top: 6px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
            }

            /* 横向滑动卡片 */
            .reward-card {
                min-width: 260px;
                background: #FFFFFF;
                border-radius: 24px;
                padding: 20px;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
                border: 1px solid #EDE8E2;
                flex-shrink: 0;
                transition: all 0.3s;
                position: relative;
                overflow: hidden;
            }
            .reward-card:active {
                transform: scale(0.97);
            }
            .reward-card .card-badge {
                position: absolute;
                top: 16px;
                right: 16px;
                background: #FFF0E8;
                color: #FF6B35;
                font-size: 11px;
                font-weight: 700;
                padding: 4px 12px;
                border-radius: 20px;
                letter-spacing: 0.5px;
            }
            .reward-card .card-icon {
                width: 50px;
                height: 50px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                margin-bottom: 14px;
            }

            /* 折叠面板 */
            .accordion-item {
                background: #FFFFFF;
                border-radius: 16px;
                margin-bottom: 10px;
                border: 1px solid #EDE8E2;
                overflow: hidden;
                transition: all 0.3s;
            }
            .accordion-header {
                padding: 16px 18px;
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
                user-select: none;
                font-weight: 600;
                font-size: 15px;
                color: #1A1A2E;
                transition: background 0.2s;
            }
            .accordion-header:active {
                background: #FDFAF6;
            }
            .accordion-body {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.4s;
                padding: 0 18px;
                font-size: 14px;
                color: #6B6B7B;
                line-height: 1.7;
            }
            .accordion-item.open .accordion-body {
                max-height: 200px;
                padding: 0 18px 16px;
            }
            .accordion-arrow {
                margin-left: auto;
                transition: transform 0.35s;
                font-size: 14px;
                color: #8C8C8C;
            }
            .accordion-item.open .accordion-arrow {
                transform: rotate(180deg);
                color: #FF6B35;
            }
            .accordion-step {
                width: 32px;
                height: 32px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 13px;
                flex-shrink: 0;
                background: #FFF0E8;
                color: #FF6B35;
            }

            /* Tab切换 */
            .tab-btn {
                padding: 12px 28px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s;
                letter-spacing: 0.3px;
                white-space: nowrap;
            }
            .tab-btn.active {
                background: #FF6B35;
                color: #FFFFFF;
                box-shadow: 0 6px 20px rgba(255, 107, 53, 0.30);
            }
            .tab-btn:not(.active) {
                background: #FFFFFF;
                color: #8C8C8C;
                border: 1px solid #E8E0D5;
            }

            /* 主按钮 */
            .btn-primary {
                background: #FF6B35;
                color: #FFFFFF;
                border-radius: 50px;
                padding: 16px 28px;
                font-weight: 700;
                font-size: 15px;
                letter-spacing: 0.5px;
                box-shadow: 0 8px 24px rgba(255, 107, 53, 0.30);
                transition: all 0.3s;
                border: none;
                cursor: pointer;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-primary:active {
                transform: scale(0.96);
                box-shadow: 0 4px 14px rgba(255, 107, 53, 0.40);
            }
            .btn-outline {
                background: #FFFFFF;
                color: #FF6B35;
                border-radius: 50px;
                padding: 10px 15px;
                font-weight: 700;
                font-size: 13px;
                letter-spacing: 0.5px;
                border: 2px solid #FFD5C0;
                transition: all 0.3s;
                cursor: pointer;
                text-align: center;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            .btn-outline:active {
                background: #FFFBF7;
                border-color: #FF6B35;font-size: 11px!important;
            }

            /* Toast */
            .toast-wrap {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) scale(0.85);
                background: #FFFFFF;
                color: #1A1A2E;
                padding: 14px 28px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 14px;
                z-index: 999;
                opacity: 0;
                pointer-events: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
                display: flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }
            .toast-wrap.show {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            /* 弹窗 */
            .modal-overlay {
                background: rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            }
            .modal-panel {
                background: #FFFFFF;
                border-radius: 28px;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
                overflow: hidden;
            }

            /* Layer覆盖 */
            .layer-crypto {
                background: #FFFFFF !important;
                border-radius: 22px !important;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15) !important;
                overflow: hidden !important;
                border: none !important;
            }
            .layer-crypto .layui-layer-title {
                background: #FAF8F5 !important;
                color: #1A1A2E !important;
                border-bottom: 1px solid #EDE8E2 !important;
                font-weight: 700 !important;
                padding: 16px 20px !important;
            }
            .layer-crypto .layui-layer-btn .layui-layer-btn0 {
                background: #FF6B35 !important;
                border: none !important;
                color: #fff !important;
                border-radius: 50px !important;
                font-weight: 600 !important;
                box-shadow: 0 4px 14px rgba(255, 107, 53, 0.25) !important;
            }
            .layer-crypto .layui-layer-btn .layui-layer-btn1 {
                background: #F5F0EB !important;
                border: none !important;
                color: #6B6B7B !important;
                border-radius: 50px !important;
            }
            .layer-msg-crypto {
                background: #FFFFFF !important;
                border-radius: 50px !important;
                box-shadow: 0 10px 36px rgba(0, 0, 0, 0.12) !important;
                color: #1A1A2E !important;
            }
        }
    </style>
</head>
<!--{eval}-->
require DISCUZ_ROOT . './source/plugin/tb_cus_mobilereg/common.php';
$yqmcode = getUserInviteCode($_G['uid']);
$hh_config = $_G['cache']['plugin']['xigua_hh'];
<!--{/eval}-->
{eval}
$tb = DB::table('xigua_hh_invite');
$uid = $_G['uid'];
$curday = dgmdate(time(),'Y-m-d');
$starttime = strtotime($curday." 00:00:00");
$endtime = strtotime($curday." 23:59:59");
$result = DB::fetch_first("SELECT count(id) as scount FROM $tb WHERE uid=$uid AND crts>$starttime AND crts<$endtime");
$todaycount =$result['scount'];
$result = DB::fetch_first("SELECT count(id) as scount FROM $tb WHERE uid=$uid");
$onecount =$result['scount'];
$result = DB::fetch_first("SELECT count(a.uid) as scount FROM `$tb` as a inner join `$tb` as b on a.fansuid=b.uid WHERE a.uid=$uid" );
$twocount =$result['scount'];
$result = DB::fetch_first("SELECT count(a.uid) as scount FROM (`$tb` as a inner join `$tb` as b on a.fansuid=b.uid) inner join `$tb` as c on b.fansuid=c.uid WHERE a.uid=$uid ");
$threecount =$result['scount'];
{/eval}

<body class="font-sans bg-bg text-sec min-h-screen">
    <!-- Toast -->
    <div id="toast" class="toast-wrap">
        <i class="fa fa-check-circle text-pri text-lg"></i>
        <span id="toastMessage">操作成功</span>
    </div>

    <!-- ==================== 主容器 ==================== -->
    <div class="max-w-lg mx-auto relative">

        <!-- 顶部：头像 + 欢迎 + 数据统计 -->
        <header class="px-5 pt-8 pb-4">
            <div class="flex items-center justify-between mb-6">
                <a href="plugin.php?id=xigua_hb&ac=my" class="w-10 h-10 rounded-full bg-white border border-border flex items-center justify-center shadow-sm active:scale-95 transition-transform">
                    <i class="fa fa-arrow-left text-sec text-base"></i>
                </a>
                <h1 class="text-xl font-extrabold tracking-tight text-sec">邀请好友</h1>
                <div class="w-10 h-10 rounded-full  border border-border flex items-center justify-center shadow-sm">
                   
                </div>
            </div>

            <!-- 三列数据统计 -->
            <div class="grid grid-cols-3 gap-3">
                <div class="stat-card">
                    <div class="stat-num">{$todaycount}</div>
                    <div class="stat-label">今日邀请</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num">{$onecount}</div>
                    <div class="stat-label">总直推数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-num">{$twocount}</div>
                    <div class="stat-label">总间推数</div>
                </div>
            </div>
        </header>

        <!-- Tab切换栏 -->
        <nav class="px-5 mb-5 flex gap-3 overflow-x-auto hide-scrollbar">
            <button class="tab-btn active" id="tabReward" onclick="switchTab('reward')">
                <i class="fa fa-gift mr-1.5"></i>奖励机制
            </button>
            <button class="tab-btn" id="tabRule" onclick="switchTab('rule')">
                <i class="fa fa-book mr-1.5"></i>推广规则
            </button>
        </nav>

        <!-- Tab内容区 -->
        <div class="px-5 pb-36">
            <!-- 奖励机制面板 -->
            <div id="panelReward" class="tab-panel">
                <!-- 横向滑动卡片 -->
                <div class="flex gap-4 overflow-x-auto hide-scrollbar pb-2 -mx-2 px-2">
                    <!-- 卡片1：消费分成 -->
                    <div class="reward-card">
                        <span class="card-badge">高回报</span>
                        <div class="card-icon bg-orange-50 text-pri">
                            <i class="fa fa-bullhorn"></i>
                        </div>
                        <h3 class="font-bold text-sec text-base mb-1">好友消费分成</h3>
                        <p class="text-muted text-sm mb-3 leading-relaxed">好友投放广告消费金额分成</p>
                        <div class="flex gap-2 flex-wrap">
                            <span class="bg-orange-50 text-pri text-xs font-bold px-3 py-1.5 rounded-full">直推 30%</span>
                            <span class="bg-amber-50 text-amber-600 text-xs font-bold px-3 py-1.5 rounded-full">间推 10%</span>
                        </div>
                    </div>

                    <!-- 卡片2：任务奖励 -->
                    <div class="reward-card">
                        <span class="card-badge bg-blue-50 text-blue-600">任务</span>
                        <div class="card-icon bg-blue-50 text-blue-600">
                            <i class="fa fa-user"></i>
                        </div>
                        <h3 class="font-bold text-sec text-base mb-1">好友签到奖励</h3>
                        <p class="text-muted text-sm mb-3 leading-relaxed">根据推广等级决定现金奖励</p>
                        <span class="text-blue-600 text-xs font-semibold">👉 签到中心了解详情</span>
                    </div>

                    <!-- 卡片3：VIP奖励 -->
                    <div class="reward-card" style="background: linear-gradient(135deg, #FFFBF7 0%, #FFF5ED 100%); border-color: #FFD5C0;">
                        <span class="card-badge bg-pink-50 text-pink-600">VIP</span>
                        <div class="card-icon bg-pink-50 text-pink-600">
                            <i class="fa fa-star"></i>
                        </div>
                        <h3 class="font-bold text-sec text-base mb-1">会员现金奖励</h3>
                        <p class="text-muted text-sm mb-3 leading-relaxed">好友开通会员即享现金奖励</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="font-bold text-pink-600">直推16.8元 间推3.8元</span></div>
                          
                        </div>
                    </div>
                </div>
            </div>

            <!-- 推广规则面板 -->
            <div id="panelRule" class="tab-panel hidden">
                <p class="text-muted text-sm mb-4 leading-relaxed">请仔细阅读以下推广规则，确保合规操作以获得稳定收益。</p>

                <!-- 折叠面板组 -->
                <div class="accordion-item open">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span class="accordion-step">1</span>
                        <span>邀请方式</span>
                        <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        通过推广链接或邀请海报邀请好友注册，成功注册后即成为您的直属好友。
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span class="accordion-step">2</span>
                        <span>层级定义</span>
                        <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        您直接邀请的好友为<strong>直推好友</strong>；您的直推好友邀请的好友为<strong>间推好友</strong>。
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span class="accordion-step">3</span>
                        <span>奖励结算</span>
                        <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        所有分成奖励将在好友产生相应行为后<strong>自动结算</strong>至您的账户，无需手动领取。
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span class="accordion-step">4</span>
                        <span>提现规则</span>
                        <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        收益提现到账时间为<strong>1-3个工作日</strong>，请耐心等待。
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header" onclick="toggleAccordion(this)">
                        <span class="accordion-step">5</span>
                        <span>违规处理</span>
                        <i class="fa fa-chevron-down accordion-arrow"></i>
                    </div>
                    <div class="accordion-body">
                        严禁通过不正当手段邀请好友，例如任务悬赏平台放单、花钱买人头、机刷等违规方式。<strong>一经发现将取消回收所有奖励</strong>，并可能根据情况封禁账号。
                    </div>
                </div>
            </div>
        </div>

        <!-- 底部操作栏 - 悬浮式 -->
        <footer class="fixed bottom-0 left-0 right-0 z-40 px-5 py-4 max-w-lg mx-auto" style="background: linear-gradient(to top, #F5F0EB 70%, rgba(245,240,235,0)); padding-bottom: max(20px, env(safe-area-inset-bottom));">
            <div class="flex gap-3 mb-3">
                <button onclick="bindcode()" class="btn-primary flex-1" style="white-space:nowrap;">
                    <i class="fa fa-qrcode"></i> 绑定邀请码
                </button>
                <button action="copy-link" onclick="copylink(this)" data-clipboard-text='{$yqmcode}' class="btn-outline flex-1" style="white-space:nowrap;">
                    <i class="fa fa-link"></i> 复制邀请码
                </button>
            </div>
            <div class="flex gap-3">
                <button onclick="showpic('{$invitetpl[0]}')" class="btn-primary flex-1" style="white-space:nowrap;">
                    <i class="fa fa-qrcode"></i> 获取邀请海报
                </button>
                <button action="copy-link" onclick="copylink(this)" data-clipboard-text='$hh_config['yqtext'] {echo hb_currenturl()."&idu=".$_G['uid']}' class="btn-outline flex-1" style="white-space:nowrap;">
                    <i class="fa fa-link"></i> 复制邀请链接
                </button>
            </div>
        </footer>
    </div>

    <!-- ==================== 弹窗：海报 ==================== -->
    <div id="posterModal" class="fixed inset-0 modal-overlay z-50 hidden items-center justify-center p-5" style="display:none;">
        <div class="modal-panel w-full max-w-sm overflow-hidden">
            <div class="p-4 border-b border-border flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-sec">我的邀请海报</h3>
                <button id="closePosterBtn" class="text-muted hover:text-sec w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fa fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-5">
                <div class="bg-gray-50 rounded-2xl p-5 flex flex-col items-center">
                    <div class="w-48 h-48 bg-white p-3 rounded-2xl shadow-sm border border-border mb-4">
                        <img src=" " alt="签米邀请二维码" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <h4 class="font-bold text-center text-sec text-lg">签米 - 邀请好友</h4>
                    <p class="text-muted text-sm text-center mt-1">扫描二维码，立即注册</p>
                </div>
                <button id="savePosterBtn" class="btn-primary w-full mt-4">
                    <i class="fa fa-download"></i> 保存海报
                </button>
            </div>
        </div>
    </div>

    <!-- 绑定邀请码弹窗模板 -->
    <div id="bindcode_tmpl" style="display:none;z-index:9999999;">
        <div style="padding: 14px;">
            <p style="color:#8C8C8C;font-size:13px;margin-bottom:14px;line-height:1.6;">如对方没有绑定上级，可以复制你的邀请码给对方绑定</p>
            <input placeholder="请输入上级给你的邀请码" type="text" id="ymcode" class="w-full bg-gray-50 border border-border rounded-2xl px-4 py-3.5 text-sec text-sm focus:outline-none focus:border-pri focus:ring-4 focus:ring-orange-50 transition-all" style="margin-top:4px;">
        </div>
    </div>

    <!-- 海报图片容器 -->
    <div id="showqrpic_tmpl" style="display: none">
        <div><img style="width: 100%;height: 100%;border-radius:16px;" id="showqrpic"></div>
    </div>

    <!-- ==================== 脚本 ==================== -->
    <script src="source/plugin/tb_cus_base/static/js/jquery-3.3.1.min.js"></script>
    <script src="source/plugin/tb_cus_base/static/layer/layer.js"></script>
    <script src="source/plugin/xigua_hh/template/touch/clipboard.min.js?{VERHASH}"></script>
    <script>
        // Tab切换
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            if (tab === 'reward') {
                document.getElementById('tabReward').classList.add('active');
                document.getElementById('panelReward').classList.remove('hidden');
            } else {
                document.getElementById('tabRule').classList.add('active');
                document.getElementById('panelRule').classList.remove('hidden');
            }
        }

        // 折叠面板
        function toggleAccordion(header) {
            const item = header.parentElement;
            item.classList.toggle('open');
        }

        // Toast
        function showToast(message) {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toastMessage');
            msg.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const posterModal = document.getElementById('posterModal');
            document.getElementById('closePosterBtn')?.addEventListener('click', () => {
                posterModal.style.display = 'none';
            });
            document.getElementById('savePosterBtn')?.addEventListener('click', () => {
                showToast('海报已保存到相册');
                posterModal.style.display = 'none';
            });
            posterModal?.addEventListener('click', (e) => {
                if (e.target === posterModal) posterModal.style.display = 'none';
            });

            // 触摸优化
            const prizeBtn = document.querySelector('.prize-button');
            if (prizeBtn) {
                prizeBtn.addEventListener('touchstart', function(e) { e.preventDefault();
                    this.style.transform = 'scale(0.94)'; }, { passive: false });
                prizeBtn.addEventListener('touchend', function(e) { e.preventDefault();
                    this.style.transform = '';
                    this.click(); }, { passive: false });
            }
        });

        function copylink(id) {
            showToast('复制成功');
            const clipboard = new Clipboard(id);
            clipboard.on('success', () => console.log('复制成功'));
            clipboard.on('error', () => showToast('复制失败，请手动复制'));
            id.click();
            clipboard.destroy();
        }

        function showpic(picurl) {
            $("#showqrpic").attr('src', picurl);
            const mask1 = document.getElementById('mask1');
            if (mask1) mask1.style.display = 'block';
            layer.open({
                type: 1,
                anim: 2,
                offset: 'b',
                shadeClose: true,
                skin: "layer-crypto",
                offset: ['10%'],
                title: "长按或截图保存海报",
                content: $("#showqrpic_tmpl"),
                area: ['75%', '65%'],
                end: function() { if (document.getElementById('mask1')) document.getElementById('mask1').style
                        .display = 'none'; }
            });
        }

        function bindcode() {
            $("#ymcode").val("");
            layer.open({
                type: 1,
                offset: '30%',
                title: "绑定邀请码",
                content: $("#bindcode_tmpl"),
                area: ['90%'],
                btn: ['立即绑定', '取消'],
                skin: 'layer-crypto',
                yes: function(index, layero) {
                    const ymcode = $("#ymcode").val();
                    if (!ymcode) { layer.msg('请输入上级给你的邀请码'); return false; }
                    $.ajax({
                        type: 'post',
                        url: 'plugin.php?id=xigua_hh:bindcode',
                        data: { formhash: '{FORMHASH}', yqcode: ymcode },
                        dataType: 'json',
                        success: function(data) { layer.close(index);
                            layer.msg(data.msg, { offset: '50%' }); },
                        error: function() { layer.close(index); }
                    });
                },
                no: function(index) { layer.close(index); },
                end: function() { $("#bindcode_tmpl").hide(); }
            });
        }

        function get_money(type) {
            if ("{$reward_arr['firstlogin']}" == 0 && type == 1) { layer.msg("暂无可领红包，快去邀请好友吧", { shade: 0.001 }); return false; }
            if ("{$reward_arr['daycount']}" == 0 && type == 2) { layer.msg("暂无可领红包，快去邀请好友吧", { shade: 0.001 }); return false; }
            const formdata = new FormData();
            formdata.append('formhash', '{FORMHASH}');
            formdata.append('type', type);
            $.ajax({
                type: 'post',
                url: 'plugin.php?id=xigua_hh:famoney',
                data: formdata,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    if (data.code != 200) { layer.msg(data.msg); } else { layer.msg(data.msg, { shade: 0.01 },
                            function() { window.location.reload(); }); }
                }
            });
        }
    </script>
</body>
</html>