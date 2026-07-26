<?php exit('new'); ?>

<!--{template xigua_hb:common_header}-->

<link rel="stylesheet" href="source/plugin/tb_cus_base/static/bootstrapfont/1.11/bootstrap-icons.min.css">
<script src="source/plugin/tb_cus_base/static/lib/swiper/swiper-bundle.min.js"></script>
<script src="source/plugin/tb_cus_base/static/layer/layer.js"></script>

<style>
    /* ========== 趣赚汇·轻奢金白风格（仅替换样式，不改变任何PHP/模板逻辑） ========== */


    .listdata {
        background-color: transparent;
        background-repeat: no-repeat;
        width:100% !important;
        margin-top: 137px;
    }

    .listdata-card {
        background-color: rgba(255, 255, 255, 0.82) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 190, 90, 0.35) !important;
        border-radius: 2rem !important;
        margin-bottom: 5px;
        height: 230px;
        margin-left: 15px;
        margin-right: 15px;
        box-shadow: 0 20px 45px rgba(255, 140, 30, 0.10), 0 4px 12px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }
    
    .listdata-card1 {
        background-color: rgba(255, 255, 255, 0.82) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 190, 90, 0.35) !important;
        border-radius: 2rem !important;
        margin-bottom: 5px;
        height: 230px;
        margin-left: 15px;
        margin-right: 15px;
        box-shadow: 0 20px 45px rgba(255, 140, 30, 0.10), 0 4px 12px rgba(0, 0, 0, 0.03) !important;
    }

    .listdata-card-top, .listdata-card-bottom {
        padding: 25px 15px;
        font-size: 0.75rem;
        color: #d4a017; /* 金色 */
        font-weight: bold;
    }

    .listdata-card-top img {
        vertical-align: middle;
        width: 20px;
    }

    .listdata-card-item-content {
        font-weight: normal;
        font-size: 0.65rem;
    }

    .listdata {
        margin-top: -20px;
    }
    
    /* 自定义滚动条 - 金色 */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #fef9f0;
    }
    ::-webkit-scrollbar-thumb {
        background: #f0b90b;
        border-radius: 10px;
    }

    /* 重置a标签默认样式（保持原有功能） */
    a {
        text-decoration: none;
        color: inherit;
        display: inline-block;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Helvetica Neue', 'Microsoft YaHei', sans-serif;
    }
    
    /* 头部 - 玻璃质感浅色 */
    .tech-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);
        border-bottom: 1px solid rgba(255, 200, 120, 0.35);
        box-shadow: 0 2px 20px rgba(255, 150, 30, 0.06);
        z-index: 9999;
        padding: 12px 0;
    }
    
    .header-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    /* 搜索区 - 浅金边框 */
    .search-section {
        width: 100%;
    }
    
    .search-form {
        width: 100%;
        display: flex;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 30px;
        overflow: hidden;
        border: 1px solid rgba(255, 200, 120, 0.3);
        transition: all 0.2s ease;
        align-items: center;
    }
    
    .search-form:focus-within {
        border-color: #f0b90b;
        box-shadow: 0 0 0 3px rgba(240, 185, 11, 0.2);
    }
    
    .search-input {
        flex: 1;
        padding: 12px 16px;
        border: none;
        background: transparent;
        font-size: 15px;
        color: #3d2b1a;
        outline: none;
    }
    
    .search-input::placeholder {
        color: #b08968;
        font-size: 14px;
    }
    
    /* 搜索按钮 - 金色渐变 */
    .search-btn {
        background: linear-gradient(135deg, #ff7b00, #e63946);
        color: #fff;
        border: none;
        padding: 8px 20px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        border-radius: 30px;
        margin: 5px 8px 5px 0;
        box-shadow: 0 4px 12px rgba(240, 185, 11, 0.35);
        transition: all 0.2s;
    }
    .search-btn:active {
        transform: scale(0.96);
        opacity: 0.9;
    }
    
    /* 导航区 - 浅色卡片风格 */
    .nav-section {
        width: 100%;
        position: relative;
    }
    
    .nav-tabs {
        display: flex;
        list-style: none;
        width: 100%;
        gap: 8px;
    }
    
    .nav-tabs li {
        flex: 1;
        text-align: center;
        padding: 8px 0;
        font-weight: 600;
        font-size: 15px;
        color: #8b6f5c;
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 40px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(255, 200, 120, 0.3);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .tab-percent {
        font-size: 10px;
        color: #8b6f5c;
        margin-top: 2px;
    }
    
    .nav-tabs li:hover {
        color: #d4a017;
        background: rgba(255, 220, 180, 0.5);
        border-color: rgba(240, 185, 11, 0.5);
    }
    
    .nav-tabs li.weui_bar__item_on {
        color: #d35400;
        background: rgba(255, 220, 180, 0.8);
        border-color: #ffb380;
        font-weight: 700;
    }
    
    .nav-tabs li.weui_bar__item_on .tab-percent {
        color: #d4a017;
    }
    
    /* 隐藏原指示器（不使用） */
    .active-indicator {
        display: none;
    }
    
    /* 内容区文字颜色 */
    .content-demo h1,
    .content-demo p,
    .feature-card p,
    .feature-card h3 {
        color: #3d2b1a;
    }
    .feature-card {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 190, 90, 0.35);
    }
    .feature-card h3::before {
        background: #f0b90b;
    }
    
    /* 全局发布按钮 - 金色 */
    .publish-btn {
        background: linear-gradient(135deg, #f0b90b 0%, #d4a017 100%) !important;
        box-shadow: 0 6px 18px rgba(240, 185, 11, 0.4) !important;
    }
    .publish-btn:active {
        transform: scale(0.94);
    }
    
    /* 卡片内头像边框金色 */
    .ad-user-section a div div:first-child div {
        background: linear-gradient(135deg, #ffb47b 0%, #ff8a5c 100%) !important;
    }
    
    /* 头条标签、分类标签、浏览数等统一金色调 */
    .font1 span,
    .category-tag span,
    .view-count div {
        background: rgba(255, 240, 210, 0.8) !important;
        color: #b45309 !important;
        border-color: rgba(255, 190, 50, 0.4) !important;
    }
    svg,
    .iconfont {
        fill: #d4a017 !important;
        color: #d4a017 !important;
    }
    
    /* 修复之前深色模式遗留的文字颜色 */
    .ad-title,
    .ad-title a {
        color: #3d2b1a !important;
    }
    .ad-user-section div[style*="color:#eef5ff"],
    .ad-user-section .font1 span {
        color: #3d2b1a !important;
    }
    .ad-user-section div[style*="font-size:11px; color:#8e9aaf"] {
        color: #8b6f5c !important;
    }
</style>

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
} else {
    include_once $cache_file_left;
    $showonlinecount=$contents[0];
}


if($hhme['status'] == 1){
    $showhhmename = $hhme[joininfo][name];
} else {
    $oldback  = $hhme['oldback'];
    $oldjoin = unserialize($oldback);
    $oldjoin = unserialize($oldjoin['joininfo']);
    $showhhmename = $oldjoin['name'];
}

if($hhme['endts']){
    $hhendts = date("Y-m-d",$hhme['endts']);
}

{/eval}

{eval}
$hhendts = $hhme['endts_u'];
{/eval}




<header class="tech-header">
    <div class="header-container">
        <div class="search-section" style="margin-top:35px;">
            <form action="plugin.php" method="get" id="searchForm" target="_blank" class="search-form">
                <input type="text" id="keyword" name="keyword" placeholder="搜索想找的项目内容" class="search-input">
                <button type="submit" class="search-btn">搜索</button>
                <input type="hidden" name="id" value="xigua_hb">
                <input type="hidden" name="ac" value="cat">
                <input type="hidden" name="st" value="">
                <input type="hidden" name="idu" value="">
            </form>
        </div>
        
      
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tabs li');
        const indicator = document.querySelector('.active-indicator');
        
        function updateIndicator() {
            const activeTab = document.querySelector('.nav-tabs li.weui_bar__item_on');
            if (!tabs.length || !indicator || !activeTab) {
                return;
            }
            const tabIndex = Array.from(tabs).indexOf(activeTab);
            const tabWidth = (100 / tabs.length) - (8 / tabs.length * 3);
            const leftPosition = tabIndex * (100 / tabs.length) + 4;
            indicator.style.left = leftPosition + '%';
            indicator.style.width = tabWidth + '%';
            indicator.style.animation = 'pulse-glow 2s infinite';
        }
        
        updateIndicator();
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('weui_bar__item_on'));
                this.classList.add('weui_bar__item_on');
                updateIndicator();
                
            });
        });
        
        const searchForm = document.querySelector('.search-form');
        const searchInput = document.querySelector('.search-input');
        
        searchInput.addEventListener('focus', function() {
            searchForm.style.transform = 'scale(1.02)';
            searchForm.style.boxShadow = '0 6px 20px rgba(255, 140, 30, 0.4), 0 0 0 2px rgba(240, 185, 11, 0.3)';
        });
        
        searchInput.addEventListener('blur', function() {
            searchForm.style.transform = 'scale(1)';
            searchForm.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.08)';
        });
    });
</script>
{eval
    include DISCUZ_ROOT.'./source/plugin/tb_toutiao/super_show.inc.php';
}
<!--{eval
$_ctaid = array_filter(explode(':', $config['fid_c']));
if($_ctaid):
 $_publist = DB::fetch_all("SELECT p.* FROM %t tt LEFT JOIN %t p ON tt.pubid = p.id WHERE p.display=1 AND tt.endtime>".time()." AND p.endts>".TIMESTAMP." order by RAND()",array('tb_toutiao','xigua_hb_pub'));
$_ctaid = intval($_ctaid[0]);
endif;
}-->

<!-- 全新样式代码块：现代杂志风格 · 暖白珊瑚色系 -->
<style>
/* ========== 全新卡片风格：暖白 · 珊瑚橙 · 杂志感 ========== */
/* 全局重置与背景 */
body, 
div[style*="margin-top:80px;background: #f6f8f5;"] {
    background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
    background-attachment: fixed !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif !important;
}

/* 卡片主容器 — 新拟态柔和质感 */
.ad-card-premium {
   background: rgba(255, 255, 255, 0.82) !important;
   backdrop-filter: blur(20px) !important;
   -webkit-backdrop-filter: blur(20px) !important;
   border: 1px solid rgba(255, 190, 90, 0.35) !important;
   border-radius: 32px !important;
   box-shadow: 0 20px 45px rgba(255, 140, 30, 0.10), 0 4px 12px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
   margin-bottom: 24px !important;
   transition: transform 0.25s ease, box-shadow 0.3s ease !important;
}

.ad-card-premium:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 45px rgba(255, 140, 30, 0.15), 0 8px 20px rgba(0, 0, 0, 0.05) !important;
}

/* 隐藏旧版角标装饰 */
.ad-card-premium > div[style*="position: absolute; top: 0; right: 0;"] {
    display: none !important;
}

/* 用户区域 — 更舒展的布局 */
.ad-user-section {
    padding: 20px 24px 12px 24px !important;
}

/* 头像外层 — 磨砂暖光 */
.ad-user-section a > div > div:first-child > div:first-child {
    background: linear-gradient(135deg, #ffb47b, #ff8a5c) !important;
    width: 56px !important;
    height: 56px !important;
    padding: 3px !important;
    box-shadow: 0 10px 20px -5px rgba(255, 110, 64, 0.2) !important;
    border-radius: 50% !important;
    margin-top: 0 !important;
}

.ad-user-section img {
    border: 3px solid #ffffff !important;
    border-radius: 50% !important;
    object-fit: cover;
}

/* 用户名 & 认证信息 */
.ad-user-section div[style*="font-size:18px;color:#eef5ff"] {
    color: #3d2b1a !important;
    font-size: 1.35rem !important;
    font-weight: 700 !important;
    letter-spacing: -0.3px !important;
    margin-bottom: 0 !important;
}

/* 替换原有「头条」标签为全新样式 */
.nb .font1 div, 
.nb > div[style*="display: flex; align-items: center; background: linear-gradient"] {
    background: rgba(240, 185, 11, 0.08) !important;
    border: 1px solid rgba(240, 185, 11, 0.3) !important;
    border-radius: 40px !important;
    padding: 6px 14px !important;
    box-shadow: none !important;
}
.nb span, .nb svg {
    color: #d4a017 !important;
    fill: #d4a017 !important;
}

/* 时间与状态标签 — 清爽浅灰 */
.ad-user-section span[style*="display: flex; align-items: center; padding: 1px 8px; border-radius: 12px;background: rgba(59, 130, 246, 0.1)"] {
    background: rgba(255, 220, 180, 0.25) !important;
    color: #b45309 !important;
    border-color: rgba(255, 190, 50, 0.25) !important;
}
.ad-user-section svg[fill="#3b82f6"] {
    fill: #f0b90b !important;
}

/* 内容标题 — 精致深灰，增加行高 */
.ad-title {
    color: #3d2b1a !important;
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    line-height: 1.5 !important;
    margin-bottom: 8px !important;
}
.ad-title a {
    color: #3d2b1a !important;
}

/* 底部标签全新设计 — 珊瑚色圆润胶囊 */
.category-tag span,
div[style*="display: inline-flex; align-items: center; background: linear-gradient(135deg, rgba(59, 130, 246, 0.2)"] {
    background: rgba(255, 220, 180, 0.7) !important;
    border: 1px solid rgba(255, 190, 50, 0.4) !important;
    color: #b45309 !important;
    border-radius: 60px !important;
    padding: 6px 18px !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    box-shadow: none !important;
}
.category-tag svg,
.ad-footer-section svg[fill="#3b82f6"] {
    fill: #f0b90b !important;
}

/* 阅读量样式更新 */
.view-count > div[style*="display: flex; align-items: center; background: rgba(59, 130, 246, 0.1)"] {
    background: rgba(255, 220, 180, 0.25) !important;
    border-color: rgba(255, 190, 50, 0.25) !important;
    border-radius: 40px !important;
    padding: 6px 18px !important;
}
.view-count font {
    color: #b45309 !important;
    font-weight: 600 !important;
}

/* 调整整体间距与响应式 */
.ad-footer-section {
    padding: 0 24px 24px 24px !important;
}
.ad-content-section > div {
    padding: 4px 24px 8px 24px !important;
}
@media (max-width: 480px) {
    .ad-user-section {
        padding: 16px 18px 8px 18px !important;
    }
    .ad-footer-section {
        padding: 0 18px 20px 18px !important;
    }
    .ad-content-section > div {
        padding: 0 18px !important;
    }
    .ad-title {
        font-size: 1rem !important;
    }
}

/* 额外优化分隔与微动效 */
.ad-footer-content {
    font-size: 0.7rem !important;
}
.ad-card-premium {
    animation: fadeInUp 0.45s ease-out;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(16px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<div style="margin-top: 125px; background: transparent;">
    <div class="feed-list" style="max-width: 600px; margin: 0 auto; padding: 0 16px;">
        {eval shuffle($_publist);$i = 0;}
        <!--{loop $_publist $_k $_v}-->
        <!--{eval
            $imglist = unserialize($_v['imglist']);
            $views = $_v['views'];
            foreach($_v[vars] as $___k => $___v):
                if($___v[autoin]&& $___v[html]):
                    $subtit = $___v[html];
                endif;
            endforeach;
            $_k=$_k+1;
        }-->
        <!--{eval $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($_v[uid]);}-->
        <!--{eval $xiaomy_certification = C::t('#xiaomy_certification#xiaomy_certification')->fetch_first_field_data("rescodebdres","where uid=".$_v['uid']." order by dateline desc"); }-->

        <!-- 全新卡片设计：极简留白 · 鼠尾草主题 -->
        <article class="post-card" style="background: rgba(255, 255, 255, 0.82); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-radius: 28px; margin-bottom: 20px; box-shadow: 0 20px 45px rgba(255, 140, 30, 0.10), 0 4px 12px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8); border: 1px solid rgba(255, 190, 90, 0.35); transition: all 0.2s ease; overflow: hidden;">
            <!-- 作者信息区：左图右文，清爽分隔 -->
            <div class="post-author" style="display: flex; align-items: center; padding: 20px 20px 12px 20px;">
                <a href="#" style="display: flex; align-items: center; text-decoration: none; flex: 1; gap: 12px;">
                    <div class="avatar" style="flex-shrink: 0;">
                        <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #ffb47b, #ff8a5c); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(255, 140, 30, 0.25);">
                            <img style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 3px solid white; background: #fff;" src="uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1" alt="avatar">
                        </div>
                    </div>
                    <div class="author-info" style="flex: 1;">
                        <div style="display: flex; align-items: baseline; flex-wrap: wrap; gap: 6px;">
                            <strong style="font-size: 16px; font-weight: 800; color: #3d2b1a;">$_v['realname']</strong>
                            <span style="background: rgba(240, 185, 11, 0.08); border: 1px solid rgba(240, 185, 11, 0.3); padding: 2px 8px; border-radius: 20px; font-size: 10px; color: #d4a017; letter-spacing: 0.3px;">头条项目</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px;">
                            <span style="font-size: 11px; color: #b08968; display: flex; align-items: center; gap: 4px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="#d4a017"/>
                                </svg>
                                {$_v[up_time]}
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 文章标题内容：大字号、清晰可读 -->
            <div class="post-content" style="padding: 0 20px 12px 20px;">
                <h2 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; line-height: 1.4; color: #3d2b1a;">
                    <a href="plugin.php?id=xigua_hb&ac=view&pubid={$_v[id]}" style="color: inherit; text-decoration: none; display: block;">
                        {eval echo cutstr(strip_tags($_v['description']), 80)}
                    </a>
                </h2>
                <!-- 可选的摘要（如果有描述） -->
                <p style="margin: 6px 0 0 0; font-size: 14px; color: #8b6f5c; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {$subtit}
                </p>
            </div>

            <!-- 底部互动栏：分类、阅读量 全新胶囊设计 -->
            <div class="post-meta" style="display: flex; justify-content: space-between; align-items: center; padding: 6px 20px 20px 20px; border-top: 1px solid rgba(255, 200, 120, 0.25); margin-top: 6px;">
                <div class="category">
                    <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 220, 180, 0.7); padding: 5px 14px; border-radius: 40px; font-size: 12px; color: #b45309; border: 1px solid rgba(255, 190, 50, 0.4);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#f0b90b"/>
                        </svg>
                        <!--{eval $catinfo = C::t('#xigua_hb#xigua_hb_cat')->fetch_by_catid($_v[catid]);}-->
                        {$catinfo['name']}
                    </span>
                </div>
                <div class="stats" style="display: flex; align-items: center; gap: 16px;">
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #b08968;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#d4a017"/>
                        </svg>
                        {$_v[views]} 阅读
                    </span>
                </div>
            </div>
        </article>
        <!--{/loop}-->
    </div>
</div>

<style>
/* 全新样式定义 - 极简暖金杂志风格，不使用任何原蓝紫/金色系 */
.feed-list .post-card {
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
}
.feed-list .post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 30px 45px rgba(255, 140, 30, 0.15), 0 8px 20px rgba(0,0,0,0.05);
}
@media (max-width: 540px) {
    .post-content h2 {
        font-size: 18px;
    }
    .post-author {
        padding: 16px 16px 8px 16px;
    }
    .post-content {
        padding: 0 16px 8px 16px;
    }
    .post-meta {
        padding: 6px 16px 16px 16px;
    }
}
</style>

<div style="margin-bottom:15px;"> </div>

<!-- 全局发布按钮 -->
<style>
    .publish-btn {
        position: fixed;
        bottom: 15px;
        right: 15px;
        width: 80px;
        height: 80px;
       background: linear-gradient(135deg, #ff7b00, #e63946)!important;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(240, 185, 11, 0.4);
        z-index: 10000;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        font-size: 18px;
        font-weight: 700;
        color: #fff!important;
        border: none;
        font-family: inherit;
    }
    .publish-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 25px rgba(240, 185, 11, 0.6);
    }
    /* 移动端适配 */
    @media (max-width: 768px) {
        .publish-btn {
            width: 70px;
            height: 70px;
            bottom: 120px;
            right: 15px;
            font-size: 16px;
        }
    }
</style>
<a href="plugin.php?id=xigua_hb&ac=pub&step=3&catid=31" class="publish-btn" style="color:#fff!important;" target="_self">
    发布
</a>



<script>
    var act = [];
    <!--{if $_G['cache']['hb_ext_config']['tanchuang_jg']&& is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/chuang_ext.php')}-->
    act.push({text:'&#21457;&#24067;&#24377;&#31383;',onClick: function () {tchuang(id);}});
    <!--{/if}-->
</script>
<!--{if $_G['cache']['hb_ext_config']['tanchuang_jg'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/chuang_ext.php')}--><!--{template xigua_hb:touch/chuang_ext}--><!--{/if}-->

<!--{template xigua_hb:list_by_cat1}-->
<!--{template xigua_hb:tab1}-->

<script>
    function showrightbottom() {
        var dialog_h = $("#pop-up-content-hongbao").height() + 60;
        layer.open({
            type: 1,
            anim: 2,
            shade: 0.65,
            shadeClose: true,
            skin: "right-bottom-menu",
            offset: "b",
            area: ["100%", "50%"],
            content: $("#right-bottom-menu"),
        });
    }
</script>

<script>
    $("body").delegate("#cateparent li","click", function(){
        let url=$(this).data('url');
        window.location.href=url;
    });
</script>

<!--{eval
$c2 = $city;
if($c2==lang_hb('quanbu', 0)):
$c2 = '';
endif;
if(!$config['indexshowdist']):
$c2 =  $_GET[dist] = $_GET[province] = '';
endif;
$pgsize = 6;
if($_GET[tpl]):
$pgsize =20;
endif;
}-->

<script>var loadingurl =(typeof indexloadingurl!='undefined') ? indexloadingurl : _APPNAME+'?id=xigua_hb&ac=list_item&inajax=1&from=index&pagesize=$pgsize&tpl={$_GET[tpl]}&province={$_GET[province]}&city=$c2&dist={eval echo $_GET[dist]?$dist:'';}&page=';
    <!--{if $allowdp}-->scrollto=0;<!--{else}-->scrollto=1;<!--{/if}-->
    var TIMELINE_TITLE = '{$desc}';
    var TOUTIAOS = [];
    <!--{if $toutiao}-->
    <!--{loop $toutiao $v}-->
    <!--{eval $v[cat_name] = $v[cat_name] ? $v[cat_name] : $tmpcats[$v[catid]][name];}-->
    TOUTIAOS.push("<a href=\"$SCRITPTNAME?id=xigua_hb&ac=view&pubid=$v[id]\"><img src=\"{avatar($v[user][uid], 'middle', true)}\" class=\"avt\">{echo trim($v[user][username])}{$v[cr_time]}{lang xigua_hb:fabule}{$v[cat_name]}{lang xigua_hb:xinxi}</a>");
    <!--{/loop}-->
    <!--{/if}-->
    <!--{if $shwidthauto&& $shtime&&$_G['cache']['plugin']['xigua_hs']['sh_list']&& $_G['cache']['plugin']['xigua_hs'][autosh]}-->
    var SH_SLIDER = $('.sh_slider');
    SH_SLIDER.animate({"scrollLeft":$shwidthauto}, $shtime, 'linear');
    SH_SLIDER.on('scroll', function(){
        if($(this).scrollLeft()>={echo $shwidthauto-20;}){
            $(this).animate({"scrollLeft":0}, 1, 'linear');
            $(this).animate({"scrollLeft":$shwidthauto}, $shtime, 'linear');
        }
    });
    SH_SLIDER.on('touchstart', function () {
        SH_SLIDER.stop().unbind();
    });
    <!--{/if}-->
</script>

<!--{eval $tabbar=1;}-->

<!--{if $config['indexshowdist']}-->
<script>$('.x_logo').removeAttr('href').addClass('dist_nav').attr('data-id',1).html('<span style="padding:0 .5rem;display:block;margin:0 .5rem">{$fontop} <i class="iconfont icon-xiangxia f13"></i></span>')</script>
<div class="dist_show"><div id="dist_show_1" class="nav_expand_panel " <!--{if $config[intopindex]}-->style="top:0"<!--{else}-->style="top:2.1rem"<!--{/if}-->>
    <div class="weui-flex">
        <div class="weui-flex__item">
            <ul>
                <li class="first_check border_bfull <!--{if !$_GET[province]}-->checked main_color<!--{/if}-->"><a href="$SCRITPTNAME?id=xigua_hb&cat_id=$cat_id&province=&city=&orderby=$orderby&keyword=$keyword&lat=$lat&lng=$lng{$urlext}">{lang xigua_hb:quanbu}</a></li>
                <!--{loop $dist0 $v}-->
                <li class="first_check border_bfull <!--{if $_GET[province]==$v[name]}-->checked main_color<!--{eval $city_id=$v['id'];}--><!--{/if}-->" data-id="$v[id]" data-link="{$v[link]}"><a>$v[name]</a></li>
                <!--{/loop}-->
            </ul>
        </div>
        <div class="weui-flex__item checked">
            <!--{loop $dist0 $k $v}-->
            <ul class="sub_cheker <!--{if $_GET[province]!=$v['name']}-->none<!--{else}-->checked<!--{/if}-->" id="sub_cheker_$v[id]">
                <li class="sub_check border_bfull"><a data-href="$SCRITPTNAME?id=xigua_hb&cat_id=$cat_id&province={$v[name]}&city=&orderby=$orderby&keyword=$keyword&lat=$lat&lng=$lng{$urlext}" class="choose color-red">{lang xigua_hb:quan}{$v[name]} <i class="iconfont icon-coordinates_fill f14 "></i></a></li>
                <!--{loop $v[child] $vv}-->
                <li class="sub_check border_bfull <!--{if $city==$vv[name]&&$_GET[city]}-->checked main_color autotrigger<!--{/if}-->"><a data-href="$SCRITPTNAME?id=xigua_hb&cat_id=$cat_id&province=$v[name]&city=$vv[name]&orderby=$orderby&keyword=$keyword&lat=$lat&lng=$lng{$urlext}" id="sub_check{$vv[id]}" data-id="$vv[id]" onclick="hs_getnext($vv[id], '{$vv[name]}','$SCRITPTNAME?id=xigua_hb&cat_id=$cat_id&orderby=$orderby&keyword=$keyword&lat=$lat&lng=$lng{$urlext}', '{$vv[link]}')">$vv[name]</a></li>
                <!--{/loop}-->
            </ul>
            <!--{/loop}-->
        </div>
        <div class="weui-flex__item checked" id="ajaxbox"> <ul class="ajaxbox_cheker"></ul> </div>
    </div>
</div></div>

<script>
    function hs_getnext(id, name, datahref, datalink){
        if(datalink){
            hb_jump(datalink);
            return false
        }
        $('.sub_check a').removeClass('checked').removeClass('main_color');
        $('.sub_check a').parent().removeClass('checked').removeClass('main_color');
        $('#sub_check'+id).addClass('checked').addClass('main_color');
        $.ajax({
            type: 'get',
            url: _APPNAME + '?id=xigua_hb&province='+$('.first_check+.checked').find('a').text()+'&name='+name+'&ctid='+id+'&datahref='+encodeURIComponent(datahref)+'&inajax=1',
            dataType: 'xml',
            success: function (data) {
                if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                var s = data.lastChild.firstChild.nodeValue;
                $('.ajaxbox_cheker').html(s);
            }
        });
    }
    $(document).on('click','.choose', function () {
        if($(this).data('link')){
            hb_jump($(this).data('link'));
            return false
        }
        var that = $(this), c_jmpurl = '';
        if(that.data('href')){ c_jmpurl = that.data('href'); }
        if(that.data('ctid')){ c_jmpurl = $('#sub_check'+that.data('ctid')).data('href'); }
        window.location.href= c_jmpurl;
    });
    $(document).on('click','.dist_check', function () {$('.dist_check').removeClass('checked').removeClass('main_color'); $(this).addClass('checked').addClass('main_color');});
    $(document).on('click','.dist_nav', function () {if($('.autotrigger').length>0){$('.autotrigger').find('a').trigger('click');}});
    $(document).on('click','.first_check', function () {
        if($(this).data('link')){
            hb_jump($(this).data('link'));
            return false
        }
        $('.ajaxbox_cheker').html('');
    });
</script>
<!--{/if}-->

<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css?v=20260726-r04-2">
<!--{template xigua_hb:common_footer}-->
<script src="source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js?v=20260726-r04-2"></script>

<!--{if $_G['cache']['plugin']['xigua_hs']}-->
<!--{if $_G['cache']['plugin']['xigua_st']['dingwei'] && getcookie('nowst') && $_REQUEST[st]==0}-->
<!--{eval
dheader("Location: $SCRITPTNAME?id=xigua_hb&mobile=2&st=".getcookie('nowst'));
}-->
<!--{/if}-->
<!--{if $_G['cache']['plugin']['xigua_st']['dingwei'] && !getcookie('setcitygeo')}-->
<script>var HB_INWECHAT = '{HB_INWECHAT}',mkey = "{$_G['cache']['plugin']['xigua_hs'][mkey]}",HS_MULTIUPLOAD = "{$_G['cache']['plugin']['xigua_hb'][multiupload]}";</script>
<script type="text/javascript" src="https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js?{VERHASH}"></script>
<script src="source/plugin/xigua_hs/static/hs.js?{VERHASH}"></script>
<script>
    function fzdw(){
        hs_getlocation(function (position) {
            var citylat = (position.latitude||position.lat);
            var citylng = (position.longitude||position.lng);
            $.ajax({
                type: 'GET',
                url: _APPNAME + '?id=xigua_hs&ac=getloc&checkst=1&lat='+citylat+'&lng='+citylng+'&inajax=1',
                dataType: 'xml',
                success: function (data) {
                    if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                    var s = data.lastChild.firstChild.nodeValue;
                    console.log(s);
                    var m = s.split('|');
                    if('success' == m[0]){
                        var _t = m[1].split(',');
                        console.log(_t);
                        if(_t[0]>0 && _t[0]!='{$_GET[st]}'){
                            $.confirm("{lang xigua_hb:dqdws}"+_t[1]+'{lang xigua_hb:setcitygeo2}', function() {
                                hb_setcookie('setcitygeo', 1, $_G['cache']['plugin']['xigua_st']['dingagain']);
                                hb_setcookie('nowst', _t[0], 864000);
                                window.location.href = _APPNAME+"?id=xigua_hb&st="+_t[0]+'{echo $_GET[app]?"&app=1":"";}';
                            }, function() {
                                hb_setcookie('setcitygeo', 1, $_G['cache']['plugin']['xigua_st']['dingagain']);
                            });
                        }else{
                            hb_setcookie('setcitygeo', 1, $_G['cache']['plugin']['xigua_st']['dingagain']);
                        }
                    }else{
                    }
                }
            });
        });
    }
    if(typeof wx!='undefined'){wx.ready(function () {  fzdw(); });}else{setTimeout(function(){ fzdw(); }, 350);}
</script>
<!--{elseif trim($config['getbygeo']) && !getcookie('setcitygeo')}-->
<script>var HB_INWECHAT = '{HB_INWECHAT}',mkey = "{$_G['cache']['plugin']['xigua_hs'][mkey]}",HS_MULTIUPLOAD = "{$_G['cache']['plugin']['xigua_hb'][multiupload]}";</script>
<script type="text/javascript" src="https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js?{VERHASH}"></script>
<script src="source/plugin/xigua_hs/static/hs.js?{VERHASH}"></script>
<script>
    function autocitydw(){
        hs_getlocation(function (position) {
            var citylat = (position.latitude||position.lat);
            var citylng = (position.longitude||position.lng);
            $.ajax({
                type: 'GET',
                url: _APPNAME + '?id=xigua_hs&ac=getloc&geoauto=1&lat='+citylat+'&lng='+citylng+'&inajax=1',
                dataType: 'xml',
                success: function (data) {
                    if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                    var s = data.lastChild.firstChild.nodeValue;
                    console.log(s);
                    var m = s.split('|');
                    if('success' == m[0]){
                        $.confirm("{lang xigua_hb:setcitygeo1}"+m[1].split(':')[0]+'{lang xigua_hb:setcitygeo2}', function() {
                            hb_setcookie('setcitygeo', 1, 3600);
                            window.location.href = _APPNAME+"?id=xigua_hb&"+m[1].split(':')[1]+_URLEXT+'{echo $_GET[app]?"&app=1":"";}';
                        }, function() {
                            hb_setcookie('setcitygeo', 1, 3600);
                        });
                    }else{
                        hb_setcookie('setcitygeo', 1, 3600);
                    }
                }
            });
        });
    }
    if(typeof wx!='undefined'){wx.ready(function () { autocitydw(); });}else{setTimeout(function(){ autocitydw(); }, 350);}
</script>
<!--{/if}-->
<!--{/if}-->


{if $_GET['opid']}
<script>
    showdialog($_GET['opid']);
</script>
{/if}
