<?php exit(''); ?>
<!--{template xigua_hb:common_header}-->

<link rel="stylesheet" href="source/plugin/tb_cus_base/static/bootstrapfont/1.11/bootstrap-icons.min.css">
<script src="source/plugin/tb_cus_base/static/lib/swiper/swiper-bundle.min.js"></script>
<script src="source/plugin/tb_cus_base/static/layer/layer.js"></script>
<!--{eval
if($_G['cache']['plugin']['xigua_jy'] && $v[catid]==$_G['cache']['plugin']['xigua_jy']['jydt']):
    $config['maincolor'] = $_G['cache']['plugin']['xigua_hb']['maincolor'] = $_G['cache']['plugin']['xigua_jy']['dftcolor'];
endif;
}--><!--{if strpos($v[description],'&lt;')!==false}-->
<!--{eval $v[description] = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $v[description]);}-->
<!--{/if}-->
<!--{if $totalpubs<1}--><!--{eval $totalpubs=1;}--><!--{/if}-->
<!--{eval
if($_GET[ecid]||$_GET['edid']||$_GET[fx]):
  $config[showguide] = 1;
endif;
if(1||$v[realname]=='-'||!$v[realname]):
$v[realname] = $v[user][username];
endif;
if($_G['uid']==$v[uid] || IS_ADMINID):
    $catinfo['telpri']=0;
endif;
$bttxt = lang_hb('duanxin', 0);
$calltel = $v[mobile] ? "tel:$v[mobile]" : '';
$callsms = $v[mobile] ? "sms:$v[mobile]" : '';
if($v[weixin]):
  $calltel = "javascript:lxfs_tip(this, '$v[mobile]', '$v[weixin]', '$v[id]');";
endif;
if($_G['uid']):
  $viewtels = C::t('#xigua_hb#xigua_hb_viewtel')->fetch_by_uid_ids($_G['uid'], array($pubid));
endif;
if($v[wancheng]):
  $callsms = $calltel = "javascript:$.toast('{lang xigua_hb:xinxiwc}','error');";
else:
  $vcatid = $v[catid];
  $vtelp = $catinfo['telpri'];
  if($vtelp>0):
    include DISCUZ_ROOT. 'source/plugin/xigua_hb/include/c_addon.php';
    if(!$viewtels[$v[id]] && !$ishk):
      $callsms = $calltel = "javascript:hb_paytel('$v[id]','$vtelp','$vcatid');";
    endif;
  endif;
endif;
if($catinfo['hidereal']):
$calltel = $callsms = '';
endif;
}-->
<!--{eval
$vavatar = avatar($v['uid'], 'middle', 1);
$subtit = $disvar = '';
$subtit = cutstr(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))), 80);
foreach($v[vars] as $___k => $___v):
  if($___v[autoin]&& $___v[html]):
    $subtit = $___v[html];
    unset($v[vars][$___k]);
  endif;
endforeach;
if($v[vars]):
  $v[vars] = array_values($v[vars]);
endif;
if($catinfo['pid']>0 && function_exists('array_column')):
    $pidindex = array_search($catinfo['pid'], array_column($cat_list, 'id'));
    $pidcat = $cat_list[$pidindex];
endif;
$hasvideo = $v['video'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php');
$icc = count($v[imglist]);
$vimglist = range(0, $icc-1);
if($hasvideo):
    $vimglist = range(0, $icc);
    $icc ++;
endif;
$no_header_fix = 1;
if($_G[uid]):
    $hasfave = C::t('#xigua_hb#xigua_hb_follow')->fetch_follow_by_favid_uid($v['uid'], $_G['uid'], 'favuser');
endif;

$adwhere = array();
$adwhere[] = 'types=\'view\'';
if($v['catid']):
    $adwhere[] = '( FIND_IN_SET('.intval($v['catid']).' , catids) OR FIND_IN_SET(-1, catids) )';
else:
    $adwhere[] = '( FIND_IN_SET(-1, catids) )';
endif;
$index_list =  C::t('#xigua_hb#xigua_hb_index')->list_by_where($adwhere);
$newindex_list = array();
if($index_list):
    foreach ($index_list as $index => $item):
        $newindex_list[$item['style']][] = $item;
    endforeach;
endif;
$cntimg = count($v[imglist]);
if($cntimg>3):
    $cntimg = 3;
endif;
if($_G['cache']['plugin']['xigua_hr']):
    $veris1 = C::t('#xigua_hr#xigua_hr_verify')->fetch_veris(array($v[uid]));
    $veris2 = C::t('#xigua_hr#xigua_hr_verify')->fetch_veris(array($v[uid]), 2);
    $bao = C::t('#xigua_hr#xigua_hr_paybao')->fetchb(array($v[uid]));
endif;
if($_G['cache']['plugin']['xigua_jy']):
    $jyusers = DB::fetch_all('select uid,gender from %t where uid in (%n)', array('xigua_jy_user', array($v[uid])), 'uid');
endif;



$uidfollow =  DB::result_first("select count(id) from %t  WHERE favtype='favuser' and favid=%d", array('xigua_hb_follow', $v['uid']));
$uidfollow1 = DB::result_first("select count(id) from %t  WHERE favtype='favuser' and uid=%d", array('xigua_hb_follow', $v['uid']));
    $uidpub = DB::result_first("select count(id) from %t  WHERE uid=%d", array('xigua_hb_pub', $v['uid']));

$uidvote = DB::result_first("select count(id) from %t  WHERE pubid=%d", array('xigua_hb_votelog', $v['id']));

}-->


<style>
    body {
        background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
        color: #333 !important;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .view {
        margin-top: 70px;
        background: transparent;
        width: 100%;
        height: auto;
        color: #333;
        z-index: 999;
        padding: 0px 0px 0px 0px;
        margin-bottom: 1px;
    }

    .view-header-left {}
    .view-header-right {
        text-align: right;
    }
    .view-header-right span {
        margin-left: 15px;
    }
    .weui-cells-view {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 2rem;
        border: 1px solid rgba(255, 190, 90, 0.35);
        margin-top: 20px;
        box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8) !important;
    }
    .weui-cell-view {
        padding: 0px;
    }
    .weui-cell-view p {
        font-size: 0.6rem;
        margin-bottom: 5px;
    }
    .view-guanzhuan {
        color: #fff;
        margin: 0px;
        padding: 3px 15px;
        background: linear-gradient(135deg, #ff7b00, #e63946) !important;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 500;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(255,50,0,0.25) !important;
    }
    .vipinfo {
        align-items: center;
        padding: 2px 7px;
        margin-left: 6px;
        height: 20px;
        width: 70px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: #4a3000;
        background: rgba(255, 245, 235, 0.7);
        border: 1px solid rgba(255, 200, 100, 0.3);
    }
    .weui-cell__hd {
        margin-right: 0px;
    }

    .view-content {
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 2rem;
        border: 1px solid rgba(255, 190, 90, 0.35);
        margin: 10px;
        margin-top: -30px;
        color: #333;
        box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03) !important;
    }
    .view-content-1-right img {
        width: 30px;
        border-radius: 50%;
        border: 1px solid #ffb380;
        margin-left: -15px;
        margin-top: 3px;
    }
    .view-content-thread {
        color: #333;
        background: transparent;
        margin: 0px;
        border-radius: 10px;
        padding: 5px;
        margin-top: -0px;
        font-size: 16px;
        line-height: 28px;
    }

    .jly_photo {
        margin-top: 20px;
        width: 100%;
        height: auto;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        margin-bottom: 0px;
    }
    .jly_photo .imgloading {
        margin: 5px;
        position: relative;
        width: 30.2%;
        height: 100%;
        border-radius: 0rem;
        overflow: hidden;
    }
    .jly_photo .imgloading span {
        display: block;
        margin-top: 100%;
    }
    .jly_photo .imgloading .imgl {
        width: 100%;
        display: block;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-size: cover;
        background-repeat: no-repeat;
    }

    .bigtxt {
        font-weight: bold;
        font-size: 0.7rem;
    }
    .smalltxt {
        color: #8b6f5c;
        font-size: 0.5rem;
    }
    .view-content-comment-text {
        margin-left: 0px;
        font-size: 0.7rem;
        margin-top: 10px;
        color: #333;
        padding: 5px 0px 0px 10px;
        background: transparent;
        height: 100%;
        padding-bottom: 5px;
    }

    .footer-box {
        position: fixed;
        bottom: 0;
        left: 0;
        z-index: 800;
        display: flex;
        align-items: center;
        padding: 5px 10px;
        width: 100%;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);
        border-top: 1px solid rgba(255, 200, 120, 0.35);
    }
    .footer-box .left-notice-box {
        display: flex;
        align-items: center;
        padding-left: 10px;
        width: 52%;
        height: 40px;
        background: rgba(255, 245, 235, 0.7);
        border-radius: 5px;
        border: 1px solid rgba(255, 200, 120, 0.3);
    }
    .footer-box .right-user-oprate-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-left: 25px;
        width: 35%;
    }

    .swiper-container {
        margin-left: auto;
        margin-right: auto;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    .swiper-wrapper {
        -webkit-transform: translate3d(0,0,0);
        -moz-transform: translate3d(0,0,0);
        -o-transform: translate(0,0);
        -ms-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
    }
    .swiper-wrapper {
        position: relative;
        width: 100%;
        height: 100%;
        z-index: 1;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-transition-property: -webkit-transform;
        -moz-transition-property: -moz-transform;
        -o-transition-property: -o-transform;
        -ms-transition-property: -ms-transform;
        transition-property: transform;
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
    }
    .row {
        display: flex;
        align-items: center;
    }
    img {
        display: inline-block;
    }
    a {
        text-decoration: none;
    }
    .view-content-1-right {
        margin-left: 10px;
    }
    .gzusebtn {
        margin-left: 10px;
    }
    .view-guanzhuan {
        font-size: 11px;
        border-radius: 5px;
    }
    
    .kt {
        background: transparent;
        height: 80px;
        position: fixed;
        bottom: 0;
        left: 0px;
        width: 100%;
        display: flex;
        margin: 0 auto;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .price-desc-btn {
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-iteration-count: infinite;
        animation-iteration-count: infinite;
        -webkit-animation-name: buttonAnimate;
        animation-name: buttonAnimate;
        -webkit-animation-timing-function: ease;
        animation-timing-function: ease;
    }

    @-webkit-keyframes buttonAnimate {
        50% {
            -webkit-transform: scale(1.05);
            transform: scale(1.05)
        }
    }
    @keyframes buttonAnimate {
        50% {
            -webkit-transform: scale(1.05);
            transform: scale(1.05)
        }
    }

    @keyframes sweep {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }
    @-webkit-keyframes sweep {
        0% { -webkit-transform: translateX(-100%) rotate(45deg); }
        100% { -webkit-transform: translateX(100%) rotate(45deg); }
    }  

    .saoguang {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(360deg,transparent 30%,rgba(255,255,255,0.3) 50%,transparent 70%);
        animation: sweep 3.5s infinite;
        pointer-events: none;
        transform: rotate(45deg);
        z-index: 1;
    }
</style>

<!--{eval $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[uid]); $hhcard = C::t('#xigua_hk#xigua_hk_card')->fetch_by_uid($v[uid]); }-->
<!--{eval $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[uid]);}-->

<div class="view tgb-r05-detail">
    <div>
        <div class="weui-flex" style="margin-top: -71px; position: fixed; background: rgba(255,255,255,0.85); backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px); z-index: 9999; width: 100%; height: 90px; border-bottom: 1px solid rgba(255,200,120,0.35); box-shadow: 0 2px 20px rgba(255,150,30,0.06);">
            <a href="javascript:history.go(-1)" class="weui-flex__item view-header-left" style="margin-top: 42px;">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </a>
            <em style="color: #3d2b1a; font-size: 20px; font-weight: 800; margin-top: 50px; margin-left: 20px; text-align: center;">项目详情</em>
            <div class="weui-flex__item view-header-right" style="margin-top: 57px; font-size: 12px; color: #b08968; margin-right: 20px;">
                <a href="plugin.php?id=xigua_hj">
                    <i class="bi bi-flag" aria-hidden="true"></i> 举报
                </a>
            </div>
        </div>
     
        <style>
            /* 整体头部样式 */
            #header {
                z-index: 999;
                background: rgba(255,255,255,0.85);
                backdrop-filter: blur(22px);
                -webkit-backdrop-filter: blur(22px);
                width: 100%;
                height: 90px;
                font-size: 18px;
                position: fixed;
                top: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                box-sizing: border-box;
                border-bottom: 1px solid rgba(255,200,120,0.35);
                box-shadow: 0 2px 20px rgba(255,150,30,0.06);
            }

            #back-button {
                margin-top: 150px;
                display: flex;
                align-items: center;
                text-decoration: none;
                color: #b08968;
                font-weight: 600;
            }

            #back-button span {
                margin-right: 30px;
            }

            #header-title {
                color: #3d2b1a;
                font-weight: 800;
                font-size: 20px;
                margin: 0;
                margin-top: 15px;
            }
            .reward {
                position: fixed;
                bottom: 35%;
                right: 0;
                z-index: 99999;
            }

            #circule {
                color: #4a3000;
                text-align: center;
                font-size: 16px;
                background-color: #fff0;
                margin-left: 0rem;
            }

            #circule span {
                margin-top: 5px;
                display: inline-block;
            }
        </style>
        <!-- 头部栏 -->
        <div id="header" style="margin-top:0px;">
            <a href="javascript:window.history.go(-1);">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </a>
            <h1 id="header-title"></h1>
            <div style="width: 20px;"></div>
        </div>  

        <div class="crypto-project-container crypto-gradient-bg">
            <!-- 置顶标识 -->
            <!--{if $v[dig_on]}-->
            <div class="crypto-pinned-badge crypto-glass" style="margin-top:20px;">
                <div class="crypto-pinned-content">
                    <span class="crypto-pinned-text">
                        <!--{if $v[zdword]}-->
                        $v[zdword]
                        <!--{else}-->
                        本项目已使用置顶服务，具备一定的热度
                        <!--{/if}-->
                    </span>
                </div>
            </div>
            <!--{/if}-->

            <!-- 项目标题 -->
            <div class="crypto-project-header crypto-glass" style="margin-top:20px;">
                <h1 class="crypto-project-title">
                    <a href="plugin.php?id=xigua_hb&ac=view&pubid=$v[id]">$v['title']</a>
                </h1>
            </div>

            <!-- 用户信息区域 -->
            <div class="crypto-user-card crypto-glass">
                <div class="crypto-user-info">
                    <div class="crypto-avatar-section">
                        <img src='uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1' alt="用户头像" class="crypto-avatar">
                        <!--{if $hhme[joininfo][name] == "董事代理"}
                        <span class="crypto-badge-director" aria-label="董事代理"><i class="bi bi-award-fill" aria-hidden="true"></i></span>
                        {/if}-->
                    </div>
                    
                    <div class="crypto-user-details">
                        <div class="crypto-user-name-row">
                            <!--{if $hhme[joininfo][name] == "星创会员"}-->
                            <span class="crypto-user-name crypto-vip-name">{$v[realname]}</span>
                            <span class="crypto-vip-badge crypto-vip-mk">
                                <i class="fa fa-crown">VIP</i>
                            </span>
                            <!--{else}-->
                            <span class="crypto-user-name">{$v[realname]}</span>
                            
                            <!--{if $hhme[joininfo][name] == "商业会员"}-->
                            <span class="crypto-vip-badge crypto-vip-business">
                                <i class="fa fa-briefcase">商</i>
                            </span>
                            <!--{/if}-->
                            <!--{/if}-->

                            <!-- 认证标识 -->
                            <!--{if $_G['cache']['plugin']['xigua_hr']}-->
                            <!--{eval
                                $veris1 = C::t('#xigua_hr#xigua_hr_verify')->fetch_veris(array($v[uid]));
                                $veris2 = C::t('#xigua_hr#xigua_hr_verify')->fetch_veris(array($v[uid]), 2);
                                $veris4 = C::t('#xigua_hr#xigua_hr_verify')->fetch_veris(array($v[uid]), 4);
                                $bao = C::t('#xigua_hr#xigua_hr_paybao')->fetchb(array($v[uid]));
                                $xiaomy_certification = C::t('#xiaomy_certification#xiaomy_certification')->fetch_first_field_data("rescodebdres","where uid=".$v['uid']." order by dateline desc");
                            }-->
                            <span class="crypto-verify-badges">
                                <!--{if $xiaomy_certification['rescodebdres']==1}-->
                                <span class="crypto-verify-badge crypto-verify-realname" title="实名认证">
                                    <i class="fa fa-id-card"></i>
                                </span>
                                <!--{/if}-->
                                <!--{if $veris2[$v[uid]]}-->
                                <span class="crypto-verify-badge crypto-verify-company" title="企业认证">
                                    <i class="fa fa-building"></i>
                                </span>
                                <!--{/if}-->
                                <!--{if $veris4[$v[uid]]}-->
                                <span class="crypto-verify-badge crypto-verify-video" title="视频认证">
                                    <i class="fa fa-video-camera"></i>
                                </span>
                                <!--{/if}-->
                            </span>
                            <!--{else}-->
                            <span class="crypto-user-id">{$user[uid]}</span>
                            <!--{/if}-->
                        </div>
                        
                        <div class="crypto-user-stats">
                            <span class="crypto-stat-item">
                                <i class="fa fa-eye crypto-stat-icon"></i>
                                <span class="crypto-stat-value">{$v[views]}</span>
                                <span class="crypto-stat-label">人看过</span>
                            </span>
                            <span class="crypto-stat-divider">·</span>
                            <span class="crypto-stat-item">
                                <i class="fa fa-folder crypto-stat-icon"></i>
                                <span class="crypto-stat-value">{$uidpub}</span>
                                <span class="crypto-stat-label">个项目</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="crypto-user-actions" style="width:50%;">
                    <a href="$SCRITPTNAME?id=xigua_hb&ac=chat&touid=$v[uid]" class="crypto-message-btn crypto-gradient-primary">
                        <i class="fa fa-comment"></i>
                        私信
                    </a>
                </div>
            </div>

            <!-- 项目介绍 -->
            <div class="crypto-section crypto-glass">
                <div class="crypto-section-header">
                    <h3 class="crypto-section-title">项目介绍</h3>
                </div>
                
                <div class="crypto-section-content crypto-project-description">
                    <!--{if $v[description]}-->
                    <div class="crypto-description-text">
                        {echo hb_nl2br($v[description])}
                    </div>
                    <!--{else}-->
                    <p class="crypto-no-description">{$v[realname]}{lang xigua_hb:tl}</p>
                    <!--{/if}-->
                </div>
            </div>

            <!-- 项目图片 -->
            <!--{if $v[imglist]}-->
            <div class="crypto-section crypto-glass">
                <div class="crypto-section-header">
                    <h3 class="crypto-section-title">项目图片</h3>
                </div>
                
                <div class="crypto-project-images">
                    <!--{loop $v[imglist] $_k $slider}-->
                    <div class="crypto-image-item">
                        <div class="crypto-image-wrapper">
                            <img class="crypto-image crypto-image-loading" src="$slider" alt="项目图片" data-src="$slider" data-index="$_k">
                            <div class="crypto-image-loader">
                                <div class="crypto-loader-spinner crypto-gradient-primary"></div>
                            </div>
                        </div>
                    </div>
                    <!--{/loop}-->
                </div>
            </div>

            <!-- 图片放大模态框 -->
            <div id="image-modal" class="crypto-image-modal">
                <div class="crypto-modal-overlay"></div>
                <div class="crypto-modal-content">
                    <div class="crypto-modal-header">
                        <button class="crypto-modal-close">&times;</button>
                        <span class="crypto-modal-counter">
                            <span id="current-index">1</span> / <span id="total-images">{$v[imglist][count]}</span>
                        </span>
                    </div>
                    <div class="crypto-modal-body">
                        <button class="crypto-modal-nav crypto-modal-prev">‹</button>
                        <img id="modal-image" src="" alt="放大图片" class="crypto-modal-img">
                        <button class="crypto-modal-nav crypto-modal-next">›</button>
                    </div>
                    <div class="crypto-modal-footer">
                        <div class="crypto-image-info"></div>
                    </div>
                </div>
            </div>

            <style>
                /* 图片放大模态框样式 */
                .crypto-image-modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                    align-items: center;
                    justify-content: center;
                }
                .crypto-image-modal.active {
                    display: flex;
                }
                .crypto-modal-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.85);
                    backdrop-filter: blur(5px);
                }
                .crypto-modal-content {
                    position: relative;
                    z-index: 10000;
                    width: 90%;
                    max-width: 1000px;
                    max-height: 90vh;
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(12px);
                    border-radius: 2rem;
                    border: 1px solid rgba(255, 190, 90, 0.35);
                    box-shadow: 0 20px 45px rgba(255,140,30,0.10);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                .crypto-modal-header {
                    padding: 16px 24px;
                    background: rgba(255, 245, 235, 0.7);
                    border-bottom: 1px solid rgba(255, 200, 120, 0.3);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                .crypto-modal-close {
                    background: rgba(255, 200, 100, 0.1);
                    border: 1px solid rgba(255, 190, 50, 0.4);
                    color: #b45309;
                    font-size: 28px;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }
                .crypto-modal-close:hover {
                    background: rgba(255, 220, 180, 0.5);
                    transform: scale(1.1);
                }
                .crypto-modal-counter {
                    color: #4a3000;
                    font-size: 16px;
                    font-weight: 600;
                }
                .crypto-modal-body {
                    flex: 1;
                    padding: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }
                .crypto-modal-img {
                    max-width: 100%;
                    max-height: 70vh;
                    object-fit: contain;
                    border-radius: 16px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    transition: transform 0.3s ease;
                }
                .crypto-modal-img.zoomed {
                    cursor: zoom-out;
                    transform: scale(1.5);
                }
                .crypto-modal-nav {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background: linear-gradient(135deg, #ff7b00, #e63946);
                    border: none;
                    color: white;
                    font-size: 32px;
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                    z-index: 2;
                    box-shadow: 0 4px 15px rgba(255,50,0,0.25);
                }
                .crypto-modal-prev {
                    left: 20px;
                }
                .crypto-modal-next {
                    right: 20px;
                }
                .crypto-modal-nav:hover {
                    background: linear-gradient(135deg, #e63946, #ff7b00);
                    transform: translateY(-50%) scale(1.1);
                }
                .crypto-modal-footer {
                    padding: 16px 24px;
                    background: rgba(255, 245, 235, 0.7);
                    border-top: 1px solid rgba(255, 200, 120, 0.3);
                    color: #8b6f5c;
                    text-align: center;
                }
                .crypto-image-item {
                    cursor: pointer;
                    transition: transform 0.3s ease;
                }
                .crypto-image-item:hover {
                    transform: translateY(-2px);
                }
                .crypto-image-wrapper {
                    transition: box-shadow 0.3s ease;
                }
                .crypto-image-item:hover .crypto-image-wrapper {
                    box-shadow: 0 8px 25px rgba(255, 140, 30, 0.2);
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const images = document.querySelectorAll('.crypto-image');
                    const modal = document.getElementById('image-modal');
                    const modalImage = document.getElementById('modal-image');
                    const closeBtn = document.querySelector('.crypto-modal-close');
                    const overlay = document.querySelector('.crypto-modal-overlay');
                    const currentIndexSpan = document.getElementById('current-index');
                    const totalImagesSpan = document.getElementById('total-images');
                    const prevBtn = document.querySelector('.crypto-modal-prev');
                    const nextBtn = document.querySelector('.crypto-modal-next');
                    
                    let currentIndex = 0;
                    let isZoomed = false;
                    
                    totalImagesSpan.textContent = images.length;
                    
                    images.forEach((img, index) => {
                        img.addEventListener('click', function(e) {
                            e.stopPropagation();
                            currentIndex = index;
                            updateModalImage();
                            modal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        });
                    });
                    
                    function updateModalImage() {
                        const imgSrc = images[currentIndex].getAttribute('data-src') || images[currentIndex].src;
                        modalImage.src = imgSrc;
                        currentIndexSpan.textContent = currentIndex + 1;
                        isZoomed = false;
                        modalImage.classList.remove('zoomed');
                    }
                    
                    function closeModal() {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                    
                    closeBtn.addEventListener('click', closeModal);
                    overlay.addEventListener('click', closeModal);
                    
                    document.addEventListener('keydown', function(e) {
                        if (!modal.classList.contains('active')) return;
                        switch(e.key) {
                            case 'Escape':
                                closeModal();
                                break;
                            case 'ArrowLeft':
                                navigate(-1);
                                break;
                            case 'ArrowRight':
                                navigate(1);
                                break;
                            case ' ':
                                toggleZoom();
                                break;
                        }
                    });
                    
                    function navigate(direction) {
                        currentIndex += direction;
                        if (currentIndex < 0) {
                            currentIndex = images.length - 1;
                        } else if (currentIndex >= images.length) {
                            currentIndex = 0;
                        }
                        updateModalImage();
                    }
                    
                    function toggleZoom() {
                        isZoomed = !isZoomed;
                        modalImage.classList.toggle('zoomed', isZoomed);
                    }
                    
                    modalImage.addEventListener('click', function(e) {
                        e.stopPropagation();
                        toggleZoom();
                    });
                    
                    prevBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        navigate(-1);
                    });
                    
                    nextBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        navigate(1);
                    });
                    
                    modal.querySelector('.crypto-modal-content').addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            </script>
            <!--{/if}-->

            <!-- 联系方式 -->
            <div class="crypto-section crypto-glass">
                <h3 class="crypto-section-title">联系方式</h3>
                <div class="crypto-section-header">
                    <span class="crypto-contact-info">{$v[new_lianxi]}</span>
                </div>
                
                <div class="crypto-contact-actions">
                    <div onclick='copyyqm(this)' data-clipboard-text='{$v[new_lianxi]}' class="crypto-copy-btn crypto-gradient-primary">
                        <div class="crypto-copy-content">
                            <i class="fa fa-copy"></i>
                            <span class="crypto-copy-text">免费复制对方微信或QQ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 防骗提醒 -->
            <div class="crypto-warning-section crypto-glass">
                <div class="crypto-warning-header">
                    <h4 class="crypto-warning-title">重要提醒</h4>
                </div>
                
                <div class="crypto-warning-content">
                    <p class="crypto-warning-greeting">$_G['username']，您好：</p>
                    <div class="crypto-warning-list">
                        <p>1、建立合作之前，请务必签订合同，平台作为信息共享平台，虽然严格审核内容，但仍然无法完全对信息的真实性及准确性做出判断，因此不承担任何财产损失和法律责任；</p>
                        <p>2、若您不同意该提示，请关闭页面且不要在本平台拓展任何商业合作，否则造成的任何损失由您个人承担，您继续使用即代表同意该风险提示和同意<a href="/m/fpsm.html" class="crypto-warning-link">《防骗提醒与免责声明》</a></p>
                    </div>
                </div>
            </div>

            <!-- 防骗提醒浮动按钮 -->
            <a href="/m/fpsm.html" class="crypto-floating-warning crypto-gradient-accent">
                <i class="fa fa-shield-alt"></i>
                <span class="crypto-floating-text">防骗提醒</span>
            </a>
        </div>

        <style>
            /* 容器样式 */
            .crypto-project-container {
                background: linear-gradient(180deg, #fef9f0 0%, #fff5e6 30%, #fef3e2 60%, #fdf0db 100%) !important;
                min-height: 100vh;
                padding: 15px;
                position: relative;
            }
            
            /* 玻璃态效果 */
            .crypto-glass {
                background: rgba(255, 255, 255, 0.82) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border: 1px solid rgba(255, 190, 90, 0.35) !important;
                border-radius: 2rem !important;
                box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8) !important;
                margin-bottom: 15px;
                overflow: hidden;
            }
            
            /* 置顶标识 */
            .crypto-pinned-badge {
                background: rgba(255, 200, 100, 0.08) !important;
                border: 1px solid rgba(240, 185, 11, 0.3) !important;
                padding: 12px 16px;
                margin-bottom: 15px;
            }
            .crypto-pinned-content {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .crypto-pinned-text {
                color: #d4a017 !important;
                font-size: 14px;
                font-weight: 600;
                flex: 1;
            }
            
            /* 项目标题 */
            .crypto-project-header {
                padding: 20px;
            }
            .crypto-project-title {
                color: #3d2b1a !important;
                font-size: 24px;
                font-weight: 800;
                line-height: 1.4;
                margin: 0;
            }
            .crypto-project-title a {
                color: inherit;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .crypto-project-title a:hover {
                color: #ff7b00 !important;
            }
            
            /* 用户卡片 */
            .crypto-user-card {
                padding: 16px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }
            .crypto-user-info {
                display: flex;
                gap: 16px;
                flex: 1;
            }
            .crypto-avatar-section {
                position: relative;
            }
            .crypto-avatar {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                border: 3px solid #ffffff !important;
                box-shadow: 0 8px 20px rgba(255,140,30,0.25) !important;
                background: linear-gradient(135deg, #ffb47b, #ff8a5c);
                padding: 3px;
            }
            .crypto-badge-director {
                position: absolute;
                top: -10px;
                right: -10px;
                width: 30px;
                height: 30px;
                z-index: 2;
            }
            .crypto-user-details {
                flex: 1;
            }
            .crypto-user-name-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
                flex-wrap: wrap;
            }
            .crypto-user-name {
                color: #3d2b1a !important;
                font-size: 18px;
                font-weight: 700;
            }
            .crypto-vip-name {
                color: #d4a017 !important;
            }
            .crypto-vip-badge {
                width: 24px;
                height: 24px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
            }
            .crypto-vip-mk {
                background: linear-gradient(135deg, #ff7b00, #e63946) !important;
                color: white !important;
                box-shadow: 0 4px 12px rgba(255,50,0,0.25);
            }
            .crypto-vip-business {
                background: linear-gradient(135deg, #d4a017, #b8860b) !important;
                color: white !important;
            }
            .crypto-verify-badges {
                display: flex;
                gap: 6px;
            }
            .crypto-verify-badge {
                width: 20px;
                height: 20px;
                border-radius: 6px;
                background: rgba(255, 200, 100, 0.1) !important;
                border: 1px solid rgba(255, 190, 50, 0.4) !important;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                color: #b45309 !important;
            }
            .crypto-user-stats {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }
            .crypto-stat-item {
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .crypto-stat-icon {
                color: #b08968 !important;
                font-size: 12px;
            }
            .crypto-stat-value {
                color: #d35400 !important;
                font-size: 14px;
                font-weight: 700;
            }
            .crypto-stat-label {
                color: #8b6f5c !important;
                font-size: 12px;
            }
            .crypto-stat-divider {
                color: rgba(255, 200, 100, 0.4);
            }
            .crypto-user-actions {
                flex-shrink: 0;
            }
            .crypto-message-btn {
                color: white !important;
                padding: 10px 24px;
                border-radius: 30px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 5px 15px rgba(255,50,0,0.25) !important;
                background: linear-gradient(135deg, #ff7b00, #e63946) !important;
                transition: all 0.3s ease;
            }
            .crypto-message-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255,50,0,0.35) !important;
            }
            
            /* 内容区块 */
            .crypto-section {
                padding: 20px;
            }
            .crypto-section-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }
            .crypto-section-title {
                color: #3d2b1a !important;
                font-size: 18px;
                font-weight: 800;
                margin: 0;
                letter-spacing: 0.5px;
            }
            .crypto-contact-info {
                color: #3d2b1a !important;
                font-size: 16px;
                font-weight: 600;
                margin-left: auto;
            }
            .crypto-section-content {
                color: #4a3020 !important;
                font-size: 15px;
                line-height: 1.6;
            }
            .crypto-project-description {
                font-size: 16px;
                line-height: 1.8;
                color: #333 !important;
            }
            .crypto-description-text {
                word-wrap: break-word;
                word-break: break-word;
                white-space: pre-wrap;
                color: #333 !important;
            }
            .crypto-no-description {
                color: #8b6f5c !important;
                font-style: italic;
            }
            
            /* 图片展示 */
            .crypto-project-images {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 12px;
            }
            .crypto-image-item {
                aspect-ratio: 1;
                overflow: hidden;
                border-radius: 16px;
            }
            .crypto-image-wrapper {
                position: relative;
                width: 100%;
                height: 100%;
            }
            .crypto-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 16px;
                transition: transform 0.3s ease;
            }
            .crypto-image:hover {
                transform: scale(1.05);
            }
            .crypto-image-loader {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 245, 235, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 16px;
            }
            .crypto-loader-spinner {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                background: linear-gradient(135deg, #ff7b00, #e63946) !important;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            /* 联系方式复制按钮 */
            .crypto-contact-actions {
                margin-top: 20px;
            }
            .crypto-copy-btn {
                padding: 16px;
                border-radius: 30px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(255,50,0,0.25) !important;
                background: linear-gradient(135deg, #ff7b00, #e63946) !important;
            }
            .crypto-copy-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(255,50,0,0.35) !important;
            }
            .crypto-copy-content {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                color: white !important;
                font-size: 16px;
                font-weight: 700;
            }
            .crypto-copy-text {
                flex: 1;
                text-align: center;
            }
            
            /* 警告区域 */
            .crypto-warning-section {
                border: 1px solid rgba(255, 190, 90, 0.35) !important;
                background: rgba(255, 245, 235, 0.7) !important;
                padding: 20px;
            }
            .crypto-warning-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 16px;
            }
            .crypto-warning-title {
                color: #d35400 !important;
                font-size: 18px;
                font-weight: 700;
                margin: 0;
            }
            .crypto-warning-content {
                color: #4a3020 !important;
                font-size: 14px;
                line-height: 1.6;
            }
            .crypto-warning-greeting {
                color: #3d2b1a !important;
                font-weight: 700;
                margin-bottom: 12px;
            }
            .crypto-warning-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .crypto-warning-list p {
                margin: 0;
                line-height: 1.6;
                color: #4a3020 !important;
            }
            .crypto-warning-link {
                color: #ff7b00 !important;
                font-weight: 700;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .crypto-warning-link:hover {
                color: #e63946 !important;
            }
            
            /* 浮动防骗提醒按钮 */
            .crypto-floating-warning {
                position: fixed;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                writing-mode: vertical-rl;
                text-orientation: mixed;
                text-decoration: none;
                font-size: 14px;
                font-weight: 700;
                color: white !important;
                padding: 15px 8px;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(255, 50, 0, 0.3);
                z-index: 1000;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: all 0.3s ease;
                animation: crypto-warning-float 3s ease-in-out infinite;
                background: linear-gradient(135deg, #ff7b00, #e63946) !important;
            }
            .crypto-floating-warning:hover {
                transform: translateY(-50%) scale(1.05);
                box-shadow: 0 6px 25px rgba(255, 50, 0, 0.4);
            }
            .crypto-floating-text {
                margin-top: 8px;
            }
            @keyframes crypto-warning-float {
                0%, 100% {
                    transform: translateY(-50%);
                }
                50% {
                    transform: translateY(-55%);
                }
            }
            
            /* 响应式调整 */
            @media (max-width: 768px) {
                .crypto-project-title {
                    font-size: 20px;
                }
                .crypto-user-card {
                    flex-direction: column;
                    gap: 16px;
                }
                .crypto-user-actions {
                    width: 100% !important;
                }
                .crypto-message-btn {
                    
                    justify-content: center;
                }
                .crypto-project-images {
                    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                }
            }
        </style>

        <link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r05/detail-light-grid-r05.css?20260727-r05-v5a">
        <script src="source/plugin/xigua_hh/template/touch/clipboard.min.js?{VERHASH}"></script>
        <script>
            function copyyqm(id) {
                var clipboard = new Clipboard(id);
                clipboard.on('success', function(e) {
                    var successMsg = document.createElement('div');
                    successMsg.className = 'crypto-toast crypto-gradient-primary';
                    successMsg.style.cssText = 'position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); padding:12px 24px; border-radius:8px; color:white; font-weight:600; z-index:9999; box-shadow:0 4px 12px rgba(0,0,0,0.3); background: linear-gradient(135deg, #ff7b00, #e63946);';
                    successMsg.textContent = '联系方式复制成功';
                    document.body.appendChild(successMsg);
                    setTimeout(function() {
                        document.body.removeChild(successMsg);
                    }, 2000);
                    e.clearSelection();
                });
                clipboard.on('error', function(e) {
                    console.error('复制失败:', e.action);
                });
                id.click();
                clipboard.destroy();
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                var images = document.querySelectorAll('.crypto-image-loading');
                images.forEach(function(img) {
                    var src = img.getAttribute('data-src');
                    if (src) {
                        var tempImg = new Image();
                        tempImg.onload = function() {
                            img.src = src;
                            img.classList.remove('crypto-image-loading');
                            var loader = img.parentElement.querySelector('.crypto-image-loader');
                            if (loader) {
                                loader.style.display = 'none';
                            }
                        };
                        tempImg.src = src;
                    }
                });
            });
        </script>

        <script>
            var act = [];
            <!--{if $_G['cache']['hb_ext_config']['tanchuang_jg']&& is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/chuang_ext.php')}-->
            act.push({text:'&#21457;&#24067;&#24377;&#31383;',onClick: function () {tchuang(id);}});
            <!--{/if}-->
        </script>
        <!--{if $_G['cache']['hb_ext_config']['tanchuang_jg'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/chuang_ext.php')}--><!--{template xigua_hb:touch/chuang_ext}--><!--{/if}-->

        <!--{if 1}-->
        <div class="hong_res animated zoomIn" id="hong_res">
            <a class="hong_close"><i class="iconfont icon-guanbijiantou f22"></i></a>
            <div class="hong_res_wrap">
                <div class="hong_res_head">
                    <div class="hong_res_head_in">
                        <img src="{$vavatar}">
                    </div>
                </div>
                <div class="hong_res_cnt">
                    <div class="hong_res_box">
                        <p>{$v[realname]}</p>
                        <p>{lang xigua_hb:maile}</p>
                    </div>
                    <div class="hong_res_list">
                        <div class="send_title"></div>
                        <div class="hong_tip">{lang xigua_hb:gongxihou}</div>
                        <div class="money_bg">
                            <p class="hong_money">
                                <i>&yen;</i>
                                <span id="hong_size">{$v[hb_money]}</span>
                                <em>{lang xigua_hb:yuan}</em>
                            </p>
                        </div>
                        <a <!--{if !(IN_MAGAPP || IN_QIANFAN)&&$config[qbguide]&&$config[qbguidelink]}-->onclick="return jump_download();"<!--{else}--><!--{if IN_QIANFAN && $config['autoinapp']}-->onclick="QFH5.jumpMyPackage();"<!--{elseif IN_MAGAPP&&$config['autoinapp']}-->onclick="mag.newWin('/mag/user/v1/user/wallet');"<!--{else}-->href="$SCRITPTNAME?id=xigua_hb&ac=qianbao"<!--{/if}--><!--{/if}--> class="sub_title">{lang xigua_hb:zidongfang}</a>
                    </div>
                </div>
                <div class="view_oth">
                    <a href="$SCRITPTNAME?id=xigua_hb&ac=hong_list&pubid=$v[id]">{lang xigua_hb:kkdaj}</a>
                </div>
                <div class="sub_bg"></div>
            </div>
        </div>
        <div class="hong_res hong_box" id="hong_box">
            <div class="hong_box_main zoomIn animated ">
                <div class="hong_box_title">
                    <div class="send_title"></div>
                    <div class="hong_star"></div>
                    <div class="hong_box_showname">
                        <p>{lang xigua_hb:zongji}{$v[hb_money]}{lang xigua_hb:yuan}</p>
                    </div>
                    <div class="hong_btn animated" id="hong_btn" onclick="showHongBox(this);">
                        <div class="hong_btn_mask"></div>
                        <a href="javascript:;"> </a>
                    </div>
                </div>
                <div class="hong_from">
                    <p>{$v[realname]}</p>
                    <p>{lang xigua_hb:mai}</p>
                </div>
                <div class="view_oth">
                    <p>{lang xigua_hb:lingqu}{$config['tname']}{lang xigua_hb:qb}</p>
                </div>
                <div class="sub_bg"></div>
            </div>
        </div>
        <!--{/if}-->
        <!--{if $config[voice]}-->
        <div class="none"><audio id="media" preload="preload"><source src="{$config[voice]}" type="audio/mpeg" /></audio></div>
        <!--{/if}-->

        {template tb_cus_adv:myadvshow}
        {template tb_cus_card:tbcuscard}

        <div class="swiper-container global-lightbox animated" id="globalLightbox"><div class="swiper-wrapper" id="globalWrapper" ></div>
            <div class="swiper-pagination lightbox-pagination"></div><a class="iconfont icon-guanbijiantou closeLightbox"> </a>
        </div>
    </div>
</div>
