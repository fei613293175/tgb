<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958'); ?>
<!--{eval include_once DISCUZ_ROOT.'source/plugin/xigua_hh/include/c_join.php';}-->
<!--{template xigua_hh:header}-->
   <script src="source/plugin/tb_jjd/static/layer/layer.js"></script>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>推广宝 · 我的团队</title>
    <link href="source/plugin/xigua_hb/static/tgb-r02/vendor/remixicon-3.5.0/remixicon.css?v=20260726-r02" rel="stylesheet">
    <style media="not all" data-retired-r08>
        @layer utilities {
            body {
                background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
                color: #3d2b1a !important;
                overflow-x: hidden;
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
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                border-radius: 1.5rem;
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
            }
            
            .crypto-glass {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(22px);
                -webkit-backdrop-filter: blur(22px);
                border: 1px solid rgba(255, 200, 120, 0.35);
            }
            
            .crypto-chip {
                background: rgba(255, 123, 0, 0.08);
                border: 1px solid rgba(255, 123, 0, 0.25);
                color: #d35400;
                border-radius: 9999px;
                padding: 0.25rem 0.75rem;
                font-size: 0.75rem;
            }
            
            .text-gradient {
                background: linear-gradient(90deg, #ff7b00, #e63946);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }
            
            .coin-icon {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            }
            
            .menu-icon {
                width: 56px;
                height: 56px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                margin-bottom: 8px;
            }
            
            .crypto-card {
                border-radius: 1.5rem;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
                overflow: hidden;
            }
            
            .crypto-card-header {
                background: rgba(255, 245, 235, 0.5);
                border-bottom: 1px solid rgba(255, 200, 120, 0.35);
                padding: 16px 20px;
            }
            
            .task-card {
                border-radius: 1rem;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                overflow: hidden;
                transition: all 0.2s;
            }
            
            .task-card-normal {
                border-left: 4px solid #ff7b00;
            }
            
            .crypto-btn {
                border-radius: 40px;
                padding: 12px 24px;
                font-weight: 600;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
            }
            
            .crypto-btn-primary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255, 50, 0, 0.25);
            }
            
            .crypto-btn-secondary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255, 50, 0, 0.25);
            }
            
            .crypto-btn-accent {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255, 50, 0, 0.25);
            }
            
            .token-value {
                font-size: 24px;
                font-weight: 700;
                background: linear-gradient(90deg, #ff7b00, #e63946);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                letter-spacing: 0.5px;
            }
            
            .pulse-glow {
                animation: pulse-glow 2s infinite;
            }
            
            @keyframes pulse-glow {
                0% { box-shadow: 0 0 5px rgba(255, 123, 0, 0.3); }
                50% { box-shadow: 0 0 20px rgba(255, 123, 0, 0.5); }
                100% { box-shadow: 0 0 5px rgba(255, 123, 0, 0.3); }
            }
            
            .modal-crypto {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                border-radius: 1.5rem;
                box-shadow: 0 12px 36px rgba(0,0,0,0.10);
            }
            
            /* 自定义滚动条 */
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
            
            /* 订单卡片样式 */
            .order-card {
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                padding: 16px;
                margin-bottom: 12px;
                transition: all 0.2s;
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
            }
            
            .order-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 30px 45px rgba(255,140,30,0.15), 0 8px 20px rgba(0,0,0,0.05);
            }
            
            .order-user {
                color: #3d2b1a;
                font-size: 18px;
                font-weight: 600;
            }
            
            .order-info {
                color: #8b6f5c;
                line-height: 24px;
                margin-top: 8px;
            }
            
            .price-s {
                color: #e63946 !important;
                font-size: 24px;
                font-weight: 700;
            }
            
            /* 搜索框样式 */
            .crypto-search {
                border-radius: 14px;
                background: rgba(255, 245, 235, 0.7);
                border: 1px solid rgba(255, 200, 120, 0.35);
                padding: 12px 16px;
                color: #3d2b1a;
                width: 100%;
            }
            
            .crypto-search::placeholder {
                color: #b08968;
            }
            
            /* 导航标签样式 */
            .crypto-tab {
                border-radius: 12px;
                padding: 8px 16px;
                font-weight: 500;
                transition: all 0.2s;
                color: #8b6f5c;
            }
            
            .crypto-tab-active {
                background: rgba(255, 123, 0, 0.05);
                color: #d35400;
                border: 1px solid rgba(255, 123, 0, 0.25);
            }
            
            /* 统计卡片样式 */
            .stat-card {
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                padding: 16px;
                text-align: center;
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
            }
            
            .stat-value {
                font-size: 24px;
                font-weight: 700;
                color: #d35400;
            }
            
            .stat-label {
                font-size: 14px;
                color: #8b6f5c;
                margin-bottom: 8px;
            }
            
            /* 好友列表样式 */
            .fans-card {
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 190, 90, 0.35);
                padding: 16px;
                margin-bottom: 12px;
                transition: all 0.2s;
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03);
            }
            
            .fans-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 30px 45px rgba(255,140,30,0.15), 0 8px 20px rgba(0,0,0,0.05);
            }
            
            .fans-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: rgba(255, 245, 235, 0.7);
                border: 2px solid #ff7b00;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }
            
            .fans-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .fans-info {
                flex: 1;
                margin-left: 12px;
            }
            
            .fans-name {
                color: #3d2b1a;
                font-size: 16px;
                font-weight: 600;
            }
            
            .fans-level {
                background: rgba(255, 123, 0, 0.08);
                border: 1px solid rgba(255, 123, 0, 0.25);
                color: #d35400;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 500;
            }
            
            .fans-date {
                color: #8b6f5c;
                font-size: 12px;
                margin-top: 4px;
            }
            
            .fans-action {
                display: flex;
                gap: 8px;
            }
            
            .btn-sm {
                padding: 6px 12px;
                font-size: 12px;
                border-radius: 40px;
            }
            
            /* 头部样式 */
            .crypto-header {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(22px);
                -webkit-backdrop-filter: blur(22px);
                border-bottom: 1px solid rgba(255, 200, 120, 0.35);
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 999;
                height: 85px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 16px;
                box-shadow: 0 2px 20px rgba(255, 150, 30, 0.06);
            }
            
            .header-title {
                color: #3d2b1a;
                font-size: 18px;
                font-weight: 800;
                margin-top: 25px;
                background: none;
                -webkit-background-clip: initial;
                background-clip: initial;
                color: #3d2b1a !important;
            }
            
            .back-button {
                color: #8b6f5c;
                font-size: 16px;
                display: flex;
                align-items: center;
                margin-top: 28px;
                text-decoration: none;
            }
            
            /* 统计数据卡片 - 红橙渐变 */
            .stats-container {
                display: flex;
                gap: 12px;
                margin-bottom: 20px;
            }
            
            .stat-item {
                flex: 1;
                background: linear-gradient(135deg, #ff7b00, #e63946);
                border-radius: 16px;
                padding: 16px;
                text-align: center;
                box-shadow: 0 5px 15px rgba(255, 50, 0, 0.25);
            }
            
            .stat-item-secondary {
                background: linear-gradient(135deg, #ff7b00, #e63946);
            }
            
            .stat-item-accent {
                background: linear-gradient(135deg, #ff7b00, #e63946);
            }
            
            .stat-item-value {
                font-size: 20px;
                font-weight: 700;
                color: white;
            }
            
            .stat-item-label {
                font-size: 12px;
                color: rgba(255, 255, 255, 0.9);
                margin-top: 4px;
            }
            
            /* 导航栏样式 */
            .nav-tabs {
                display: flex;
                background: rgba(255, 245, 235, 0.7);
                border: 1px solid rgba(255, 200, 120, 0.35);
                border-radius: 60px;
                padding: 4px;
                margin-bottom: 20px;
            }
            
            .nav-tab {
                flex: 1;
                text-align: center;
                padding: 8px 12px;
                border-radius: 40px;
                color: #8b6f5c;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.2s;
                text-decoration: none;
            }
            
            .nav-tab.active {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                box-shadow: 0 5px 15px rgba(255, 50, 0, 0.25);
            }
            
            /* 搜索容器 */
            .search-container {
                display: flex;
                gap: 12px;
                margin-bottom: 20px;
            }
            
            .search-input {
                flex: 1;
                background: rgba(255, 245, 235, 0.7);
                border: 1px solid rgba(255, 200, 120, 0.35);
                border-radius: 40px;
                padding: 10px 16px;
                color: #3d2b1a;
                font-size: 14px;
            }
            
            .search-input::placeholder {
                color: #b08968;
            }
            
            .search-button {
                background: linear-gradient(135deg, #ff7b00, #e63946);
                color: white;
                border: none;
                border-radius: 40px;
                padding: 0 20px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.2s;
            }
            
            .search-button:active {
                transform: scale(0.96);
            }
            
            /* 原有weui元素覆盖 */
            .weui-cells {
                background: transparent !important;
            }
            .weui-cell {
                background: transparent !important;
            }
        }
    </style>
    <link href="source/plugin/xigua_hh/static/tgb-r08/growth-light-grid-r08.css?v=20260727-r08a" rel="stylesheet">
</head>
<body class="tgb-growth-page tgb-team-page">
    <!-- 头部栏 -->
    <div class="crypto-header">
        <!-- 返回按钮 -->
        <a href="plugin.php?id=xigua_hh&ac=invite" class="back-button">
            <i class="ri-arrow-left-line"></i>
        </a>
        <!-- 标题 -->
        <div class="header-title"><span>推广宝</span><h1>我的团队</h1></div>
        <div class="header-balance"></div>
    </div>
     
    <div class="page__bd team-content">
        
          <div class="team-notice">统计数据1小时更新1次</div>
       <!-- 统计数据卡片 -->
<div class="stats-container">
    <div class="stat-item">
        <div class="stat-item-value">{$onecount}</div>
        <div class="stat-item-label">已实名直推</div>
    </div>
    <div class="stat-item stat-item-secondary">
        <div class="stat-item-value">{$twocount}</div>
        <div class="stat-item-label">已实名间推</div>
    </div>
</div>


        <!-- 导航标签 -->
        <!--{if $hh_mode2}-->
        <div class="nav-tabs">
            <a href="$SCRITPTNAME?id=xigua_hh&ac=myfans" class="nav-tab <!--{if !$do}-->active<!--{/if}-->">
                直推
            </a>
            <a href="$SCRITPTNAME?id=xigua_hh&ac=myfans&do=sec" class="nav-tab <!--{if $do=='sec'}-->active<!--{/if}-->">
                间推
            </a>
            <!--{if $hh_mode3}-->
           
            <!--{/if}-->
            <!--{if $hh_config[allowsj]}-->
            <a href="$SCRITPTNAME?id=xigua_hh&ac=myfans&do=up" class="nav-tab <!--{if $do=='up'}-->active<!--{/if}-->">
                师父
            </a>
            <!--{/if}-->
        </div>
        <!--{/if}-->
        
        <!-- 搜索容器 -->
        <div class="search-container">
            <input type="text" id="searchuid" value="$_GET['suid']" placeholder="输入ID搜索该分类下好友" class="search-input">
            <button onclick="searchuid()" class="search-button">搜索</button>
        </div>
        
        <div class="team-notice"><i class="ri-chat-1-line"></i>点击好友头像可私信联系对方</div>
       
    <div id="list" class="weui-cells p0 before_none team-list">
   

     
    </div>

           </div>
 
    <!--{template xigua_hb:loading}-->
</div>

<!--{eval $tabbar=1;}-->
<!--{template xigua_hh:footer}-->

<script src="source/plugin/tb_jjd/static/layer/layer.js"></script>
<script>
function searchuid(){
    var searchuid = $("#searchuid").val();
    window.location.href='plugin.php?id=xigua_hh'+'&ac=myfans'+"&do={$_GET['do']}"+"&suid="+searchuid;
}

var join_list={eval echo json_encode($join_list)};

console.log(join_list);

function fuchi(fansuid,that) {
    var hhname=that.data('hh');
    var myhhname='{$joininfo['name']}';
   
    if(hhname==''){
        $.modal({
            title: '扶持',
            text: '签米会员才能扶持',
            buttons: [
                { 
                    text: "确定开通", 
                    className: "default", 
                    onClick: function(){ } 
                }
            ]
        });    
        return false;
    }
    
    content='<select id="selectjoin">';
    $.each(join_list,function(key,val){
        if(myhhname==val['name']){
            return false;
        }
        content+='<option value="'+key+'">'+val['name']+'</option>';
    })
    content+='</select>';
    
    $.modal({
        title: '给TA扶持等级',
        text: content,
        buttons: [
            { 
                text: "取消", 
                className: "default", 
                onClick: function(){ }
            },
            { 
                text: "确定扶持", 
                className: "default", 
                onClick: function(){ 
                    jointype=$("#selectjoin").val();
                    $.ajax({
                        type: 'post',
                        url: 'plugin.php?id=xigua_hh:hh&inajax=1&st=&&do=renew',
                        data:{fansuid:fansuid,formhash:'{FORMHASH}',months:'6',jointype:jointype},
                        dataType: 'xml',
                        success: function (data) {
                            $.hideLoading();
                            if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                            var s = data.lastChild.firstChild.nodeValue;
                            tip_common(s);
                        },
                        error: function () {
                            $.hideLoading();
                        }
                    });   
                } 
            }
        ]
    });    
}

$(document).on('click','.fans_li .opcls', function () {
    var that = $(this);
    hhname=that.data('hh');
    isfuchi=parseInt(that.data('fuchi'));
    
    if(hhname!='' && isfuchi==0){
        $.modal({
            title: '{lang xigua_hh:fansname}',
            text: that.data('username'),
            buttons: [
                { 
                    text: "{lang xigua_hh:cancel}", 
                    className: "default", 
                    onClick: function(){ }
                },
                { 
                    text: "联系", 
                    onClick: function(){ 
                        window.location.href = "plugin.php?id=xigua_hb" + "&ac=chat" + "&touid=" + that.data('fansuid');
                    } 
                }
                <!--{if $_GET['do']!='up' && $_GET['do']!='sec'}-->,
                { 
                    text: "开等级", 
                    onClick: function(){
                        var fansuid = that.data('fansuid');
                        fuchi(fansuid,that);
                    }
                }
                <!--{/if}-->   
            ]
        });
    }else{
        $.modal({
            title: '{lang xigua_hh:fansname}',
            text: that.data('username'),
            buttons: [
                { 
                    text: "{lang xigua_hh:cancel}", 
                    className: "default", 
                    onClick: function(){ } 
                },
                { 
                    text: "{lang xigua_hh:view}", 
                    onClick: function(){ 
                        window.location.href="plugin.php?id=xigua_hb"+"&ac=member"+"&uid="+that.data('fansuid');
                    } 
                }
            ]
        });
    }
});

$(document).on('click','.fans_li2 .opcls', function () {
    var that = $(this);
    $.modal({
        title: '{lang xigua_hh:fansname}',
        text: that.data('username'),
        buttons: [
            { 
                text: "{lang xigua_hh:cancel}", 
                className: "default", 
                onClick: function(){ } 
            },
            { 
                text: "{lang xigua_hb:sixin}", 
                className: "default", 
                onClick: function(){ 
                    window.location.href="$SCRITPTNAME?id=xigua_hb&ac=chat&touid="+that.data('fansuid');  
                } 
            },
            <!--{if $activity}-->
            { 
                text: "助力", 
                onClick: function(){
                    var fansuid = that.data('fansuid');
                    var formdata=new FormData();
                    formdata.append('modac', 'zhuli');
                    formdata.append('fansuid', fansuid);
                    
                    $.ajax({
                        type: 'post',
                        url: 'plugin.php?id=tb_jjd',
                        data : formdata,
                        processData : false,
                        contentType : false,
                        dataType: 'json',
                        success: function (data) {
                            if(data.code==203){
                                layer.msg("正在进行二次验证",{shade:0.01},function(){
                                    window.location.href=data.url;
                                });
                            }else{
                                layer.msg(data.msg,{shade:0.01});
                            }
                        }
                    });
                } 
            },
            <!--{/if}-->
            { 
                text: "{lang xigua_hh:view}", 
                className: "default", 
                onClick: function(){ 
                    window.location.href="plugin.php?id=xigua_hb"+"&ac=member"+"&uid="+that.data('fansuid');
                } 
            }
        ]
    });
});

$(document).on('click','.jihuo', function () {
    var that = $(this);
    var fansuid = that.data('fansuid');
    
    var formdata=new FormData();
    formdata.append('modac', 'jihuo');
    formdata.append('fansuid', fansuid);
    
    $.ajax({
        type: 'post',
        url: 'plugin.php?id=tb_jjd',
        data : formdata,
        processData : false,
        contentType : false,
        dataType: 'json',
        success: function (data) {
            layer.msg(data.msg,{shade:0.01},function(){
                window.location.reload()
            });
        }
    });
});

$(document).on('click','.qxjihuo', function () {
    var that = $(this);
    var fansuid = that.data('fansuid');
    
    var tcid = layer.open({
        id: 1,
        type: 1,
        title: '取消激活',
        content: "<div style='padding:20px;'>确认取消激活吗?</div>",
        area: ['95%', ''],
        btn: ['确定', '取消'],
        yes: function (index, layero) {
            var formdata=new FormData();
            formdata.append('modac', 'jihuo');
            formdata.append('submodac', "qx");
            formdata.append('fansuid', fansuid);
            
            $.ajax({
                type: 'post',
                url: 'plugin.php?id=tb_jjd',
                data : formdata,
                processData : false,
                contentType : false,
                dataType: 'json',
                success: function (data) {
                    layer.close(tcid);
                    layer.msg(data.msg,{shade:0.01},function(){
                        window.location.reload()
                    });
                }
            });
        },
        no: function (index, layero) {
            layer.close(index);
        }
    });
});

var loadingurl = window.location.href+'&ac=fans_li&do=$do&inajax=1&pagesize=20&page=';
</script>

{template tb_cus_adv:myadvshow}
</body>
</html>
