<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>推广宝 · 个人中心</title>
    <link href="source/plugin/xigua_hb/static/tgb-r02/vendor/remixicon-3.5.0/remixicon.css?v=20260726-r02" rel="stylesheet">
     <link rel="stylesheet" href="source/plugin/tb_cus_admin/template/layuimini/lib/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r06/account-light-grid-r06.css?20260728-r09-owner-v9">

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
              {if $hhme['joininfo']['name'] == '推广宝会员'}
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
                    {if $hhme['joininfo']['name'] == '推广宝会员'}
                        <div class="vip-level-name">推广宝会员</div>
                    {elseif $hhme['joininfo']['name'] == '商业会员'}
                        <div class="vip-level-name">商业会员</div>
                    {else}
                        <div class="vip-level-name">普通会员</div>
                    {/if}
                    <div class="vip-expire">有效期至 {$hhendts}</div>
                </div>
                {if $hhme['joininfo']['name'] == '推广宝会员'}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn"><i class="ri-star-fill"></i> 你已开通</a>
                {elseif $hhme['joininfo']['name'] == '商业会员'}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn"><i class="ri-diamond-fill"></i> 升级会员</a>
                {else}
                    <a href="plugin.php?id=xigua_hb&ac=vip" class="vip-upgrade-btn"><i class="ri-rocket-fill"></i> 开通会员</a>
                {/if}
            </div>
            <div class="vip-privileges">
                <div class="vip-priv-item"><i class="ri-shield-star-fill"></i><span>极速提现</span></div>
                <div class="vip-priv-item"><i class="ri-double-quotes-r"></i><span>看广加成</span></div>
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
                    <div class="asset-label">邀请收益</div>
                    <div class="asset-amount balance-amount" data-original="¥{$usermoney}">¥{$usermoney}</div>
                    <a href="plugin.php?id=xigua_hb&ac=qianbao" class="asset-withdraw"><i class="ri-bank-line"></i> 提现</a>
                </div>
                <div class="asset-item">
                    <div class="asset-icon sign"><i class="ri-money-dollar-circle-fill"></i></div>
                    <div class="asset-label">奖励收益</div>
                    <div class="asset-amount balance-amount" data-original="¥{$ext2}">¥{$ext2}</div>
                    <a href="plugin.php?id=tb_cus_xiguahh:tx" class="asset-withdraw sign-withdraw"><i class="ri-bank-line"></i> 提现</a>
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
                    <span class="tool-name">我的好友</span>
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
                <a href="https://kkkk.zz-yihao.com/chat/index?noCanClose=1&token=61d4c34590b608c3b43da92e5258edcf&kefu_id=13" class="tool-item" id="onlineServiceLink" style="flex-direction:row;gap:10px;padding:14px 14px;justify-content:flex-start;">
                    <div class="tool-icon-wrap t3" style="width:34px;height:34px;border-radius:12px;font-size:16px;">
                        <i class="ri-notification-3-line"></i>
                    </div>
                    <span class="tool-name" style="font-size:13px;">联系客服</span>
                </a>
                <a href="done/app.html" class="tool-item" style="flex-direction:row;gap:10px;padding:14px 14px;justify-content:flex-start;">
                    <div class="tool-icon-wrap t4" style="width:34px;height:34px;border-radius:12px;font-size:16px;">
                        <i class="ri-shield-check-line"></i>
                    </div>
                    <span class="tool-name" style="font-size:13px;">下载app</span>
                </a>
            </div>
        </div>




            </div>
        </div>
    <!--{template xigua_hb:tab5}-->
    <script src="source/plugin/xigua_hb/static/lib/jquery-2.1.4.js?51{VERHASH}"></script>
    {template tb_cus_adv:myadvshow}

    <script>
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

</body>
</html>
