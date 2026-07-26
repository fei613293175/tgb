<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>设置中心 · 推广宝</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'crypto-primary': '#ff7b00',
                        'crypto-secondary': '#e63946',
                        'crypto-accent': '#ff7b00',
                        'crypto-dark': '#fff9f5',
                        'crypto-card': 'rgba(255,255,255,0.85)',
                        'crypto-border': 'rgba(255,200,120,0.35)',
                        'crypto-text': '#3d2b1a',
                        'crypto-text-secondary': '#8b6f5c',
                    },
                    fontFamily: {
                        sans: ['Inter', 'SF Pro Display', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'crypto': '0 5px 15px rgba(255,50,0,0.25)',
                        'crypto-card': '0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03)',
                        'crypto-glow': '0 0 20px rgba(255,123,0,0.3)',
                    }
                },
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer utilities {
            body {
                background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
                color: #3d2b1a !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', sans-serif;
            }
            
            .crypto-gradient-bg {
                background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%);
            }
            
            .crypto-gradient-primary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
            }
            
            .crypto-gradient-secondary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
            }
            
            .crypto-gradient-accent {
                background: linear-gradient(135deg, #ff7b00, #e63946);
            }
            
            .crypto-gradient-card {
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255,190,90,0.35);
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
            }
            
            .crypto-glass {
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(22px);
                -webkit-backdrop-filter: blur(22px);
                border-bottom: 1px solid rgba(255,200,120,0.35);
            }
            
            .crypto-chip {
                background: rgba(255,123,0,0.08);
                border: 1px solid rgba(255,123,0,0.25);
                color: #d35400;
            }
            
            .text-gradient {
                background: linear-gradient(90deg, #ff7b00, #e63946);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .setting-icon {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255,190,90,0.35);
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                margin-right: 16px;
            }
            
            .setting-card {
                border-radius: 24px;
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255,190,90,0.35);
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
                overflow: hidden;
            }
            
            .setting-item {
                padding: 16px;
                border-bottom: 1px solid rgba(255,200,120,0.25);
                transition: all 0.2s;
                cursor: pointer;
            }
            
            .setting-item:hover {
                background: rgba(255,123,0,0.03);
            }
            
            .setting-item:last-child {
                border-bottom: none;
            }
            
            .setting-title {
                font-size: 16px;
                font-weight: 600;
                color: #3d2b1a;
                margin-bottom: 4px;
            }
            
            .setting-description {
                font-size: 13px;
                color: #8b6f5c;
            }
            
            .modal-crypto {
                background: rgba(255,255,255,0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255,190,90,0.35);
                border-radius: 24px;
                box-shadow: 0 12px 36px rgba(0,0,0,0.10);
            }
            
            .crypto-input {
                border-radius: 16px;
                background: rgba(255,245,235,0.7);
                border: 1px solid rgba(255,200,120,0.35);
                padding: 12px 16px;
                color: #3d2b1a;
                width: 100%;
                transition: all 0.2s;
            }
            
            .crypto-input:focus {
                outline: none;
                border-color: #ff7b00;
                box-shadow: 0 0 0 2px rgba(255,123,0,0.15);
            }
            
            .crypto-input::placeholder {
                color: #b08968;
            }
            
            .crypto-btn {
                border-radius: 60px;
                padding: 12px 24px;
                font-weight: 700;
                transition: all 0.2s;
                border: none;
            }
            
            .crypto-btn-primary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255,50,0,0.25);
            }
            
            .crypto-btn-secondary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255,50,0,0.25);
            }
            
            .crypto-btn-accent {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255,50,0,0.25);
            }
            
            .crypto-btn-outline {
                background: transparent;
                border: 1px solid rgba(255,200,120,0.35);
                color: #8b6f5c;
            }
            
            .crypto-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255,50,0,0.35);
            }
            
            .animate-fade-in {
                animation: fadeIn 0.5s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            ::-webkit-scrollbar {
                width: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: #fef9f0;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #ff7b00;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body class="min-h-screen tgb-r06-settings-page">
    <!-- 顶部导航栏 -->
    <header class="crypto-glass fixed top-0 left-0 right-0 z-30" style="padding:15px 0 0 0; box-shadow: 0 2px 20px rgba(255,150,30,0.06);">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <button id="backBtn" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                <i class="fa fa-arrow-left text-crypto-primary"></i>
            </button>
            <h1 class="text-xl font-bold text-crypto-text">设置中心</h1>
            <div class="w-10 h-10"></div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="container mx-auto px-4 pt-20 pb-16">
        <!-- 个人资料设置 -->
        <section class="mt-6 animate-fade-in">
            <div class="setting-card">
                <div class="p-5 border-b border-crypto-border">
                    <h2 class="text-crypto-text-secondary font-medium">账户设置</h2>
                </div>
                <a href="$SCRITPTNAME?id=xigua_member:profile" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon crypto-gradient-primary" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-user text-white"></i>
                                </div>
                                <div>
                                    <p class="setting-title">资料设置</p>
                                    <p class="setting-description">头像、用户名等</p>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>

                <a href="$SCRITPTNAME?id=xigua_hb&ac=myaddr&mobile=2{$urlext}" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon crypto-gradient-secondary" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-map-marker text-white"></i>
                                </div>
                                <div>
                                    <p class="setting-title">收货地址</p>
                                    <p class="setting-description">后续商城需要收货地址</p>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
                
                
             
                
                
                <a href="plugin.php?id=tb_credit&modac=userext" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-phone text-white"></i>
                                </div>
                                <div>
                                    <p class="setting-title">联系方式</p>
                                    <p class="setting-description">手机号、微信、QQ等</p>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- 协议相关 -->
        <section class="mt-6 animate-fade-in">
            <div class="setting-card">
                <div class="p-5 border-b border-crypto-border">
                    <h2 class="text-crypto-text-secondary font-medium">协议与条款</h2>
                </div>
                <a href="m/yhxy.html" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-file-text-o text-white"></i>
                                </div>
                                <p class="setting-title">服务协议</p>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
                
                <a href="m/yszc.html" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-user-secret text-white"></i>
                                </div>
                                <p class="setting-title">隐私政策</p>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
                
                <a href="m/xfxy.html" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-shopping-cart text-white"></i>
                                </div>
                                <p class="setting-title">消费协议</p>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- 其他设置 -->
        <section class="mt-6 animate-fade-in">
            <div class="setting-card">
                <div class="p-5 border-b border-crypto-border">
                    <h2 class="text-crypto-text-secondary font-medium">其他</h2>
                </div>
                
              
                
                <a href="plugin.php?id=deluser" class="block">
                    <div class="setting-item">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="setting-icon" style="background: linear-gradient(135deg, #ff7b00, #e63946);">
                                    <i class="fa fa-user-circle text-white"></i>
                                </div>
                                <p class="setting-title">注销账号</p>
                            </div>
                            <i class="fa fa-chevron-right text-crypto-text-secondary transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- 退出登录 -->
            <a href="member.php?mod=logging&action=logout&formhash={FORMHASH}" 
               class="w-full crypto-btn crypto-btn-accent mt-6 py-4 rounded-xl font-bold shadow-crypto hover:opacity-90 block text-center transition-all duration-300">
                退出登录
            </a>
        </section>
    </main>

  
    <script>
    
        // DOM元素
        const modals = {
            profile: document.getElementById('profileModal'),
            loginPassword: document.getElementById('loginPasswordModal'),
            transactionPassword: document.getElementById('transactionPasswordModal'),
            contact: document.getElementById('contactModal'),
            agreement: document.getElementById('agreementModal')
        };
        
        const saveButtons = {
            profile: document.getElementById('saveProfileBtn'),
            loginPassword: document.getElementById('saveLoginPasswordBtn'),
            transactionPassword: document.getElementById('saveTransactionPasswordBtn'),
            contact: document.getElementById('saveContactBtn')
        };
        
        const closeButtons = document.querySelectorAll('.closeModal');
        const backButton = document.getElementById('backBtn');
        const avatarUpload = document.getElementById('avatarUpload');
        const changeAvatarBtn = document.getElementById('changeAvatarBtn');
        const avatarPreview = document.getElementById('avatarPreview');
        const passwordTogglers = document.querySelectorAll('.togglePassword');
        
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastMessage = document.getElementById('toastMessage');
        
        const agreementTitle = document.getElementById('agreementTitle');
        const agreementContent = document.getElementById('agreementContent');

     

        // 返回按钮
        backButton.addEventListener('click', () => {
            window.history.back();
        });

        // 为所有可点击项添加微动画效果
        document.querySelectorAll('.setting-item').forEach(item => {
            item.addEventListener('click', function() {
                this.classList.add('scale-95');
                setTimeout(() => {
                    this.classList.remove('scale-95');
                }, 200);
            });
        });
    </script>
    <link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css?20260727-r06-a1">
</body>
</html>
