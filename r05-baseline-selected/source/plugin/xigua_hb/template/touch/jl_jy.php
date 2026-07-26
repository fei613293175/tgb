<?php exit('232323'); ?>
<style>
/* ========== 现代杂志风格 · 暖白珊瑚色系（与头条列表完全一致） ========== */
/* 全局背景 */
body {
    background-color: #fff9f5 !important;
    font-family: 'Inter', -apple-system, 'Segoe UI', system-ui, sans-serif !important;
}

/* 卡片容器 */
.post-card {
    background: #ffffff;
    border-radius: 15px;
    margin-bottom: 15px;
    box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 6px 12px -6px rgba(0, 0, 0, 0.02);
    transition: transform 0.25s ease, box-shadow 0.3s ease;
    overflow: hidden;
    border: 1px solid rgba(255, 215, 190, 0.6);
}
.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 45px -12px rgba(0, 0, 0, 0.15);
}

/* 作者区域 */
.post-author {
    padding: 20px 24px 12px 24px;
    display: flex;
    align-items: center;
}
.post-author a {
    display: flex;
    align-items: center;
    text-decoration: none;
    flex: 1;
    gap: 12px;
}
.avatar-outer {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffb47b, #ff8a5c);
    padding: 3px;
    box-shadow: 0 10px 20px -5px rgba(255, 110, 64, 0.2);
    margin-right: 14px;
    flex-shrink: 0;
}
.avatar-outer img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid #ffffff;
    object-fit: cover;
    background: #fff;
}
.author-info {
    flex: 1;
}
.author-name {
    font-size: 16px;
    font-weight: 650;
    color: #2c2a2a;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
}
.author-time {
    display: flex;
    align-items: center;
    gap: 8px;
}
.time-badge {
    display: inline-flex;
    align-items: center;
    background: #f0eee9;
    border-radius: 30px;
    padding: 4px 12px;
    font-size: 11px;
    color: #7c6e64;
    border: 1px solid #e6dfd8;
}
.time-badge svg {
    width: 12px;
    height: 12px;
    margin-right: 4px;
    fill: #ff8a5c;
}
.pinned-tag {
    margin-left: auto;
    background: #ffefe5;
    color: #d9562c;
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 500;
    border: 1px solid #ffceb0;
}

/* 内容区域 */
.post-content {
    padding: 4px 24px 12px 24px;
}
.post-title {
    font-size: 16px;
    font-weight: 500;
    line-height: 1.5;
    margin-bottom: 8px;
    color: #1f1c1a;
}
.post-title a {
    color: inherit;
    text-decoration: none;
    display: block;
}
.post-summary {
    font-size: 16px;
    color: #6a7b66;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* 底部栏 */
.post-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 24px 24px 24px;
    border-top: 1px solid #f0f2ee;
    margin-top: 6px;
}
.category-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffefe5;
    border: 1px solid #ffe0d0;
    border-radius: 60px;
    padding: 6px 18px;
    font-size: 13px;
    font-weight: 300;
    color: #d9562c;
}
.category-tag svg {
    width: 14px;
    height: 14px;
    fill: #ff8a5c;
}
.read-stats {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f4f2ef;
    border-radius: 40px;
    padding: 6px 18px;
    font-size: 13px;
    font-weight: 300;
    color: #a56844;
}
.read-stats svg {
    width: 14px;
    height: 14px;
    fill: #ff8a5c;
}


</style>

<!--{if $list}-->
<!--{/if}-->

<!--{if $_GET['is_my'] || $_GET['is_admin']}-->
<!--{template xigua_hb:mypub_item}-->
<!--{else}--><!--{eval
if($_G['cache']['plugin']['xigua_jy'] && $users && $list):
    $jyusers = DB::fetch_all('select uid,gender from %t where uid in (%n)', array('xigua_jy_user', array_keys($users)), 'uid');
endif;
}-->

<div style="background: #fff9f5; padding: 20px 0 40px 0;">
    <div class="feed-list" style="max-width: 600px; margin: 0 auto; padding: 0 16px;">
        <!--{loop $list $kkkk $v}-->
        <!--{eval
        if ($config['showcomment']):
            $v['commentlist'] = C::t('#xigua_hb#xigua_hb_comment')->fetch_comment_by_pubid($v['id'], 0, 3);
        endif;
        $subtit = '';
        $subtit = cutstr(str_replace(array("\n\n","\r\r", "\n\r\n\r"), '', trim(strip_tags($v[description]))), 61);
        foreach($v[vars] as $___k => $___v):
          if($___v[autoin] && $___v[html] && !$subtit):
            $subtit = $___v[html];
            break;
          endif;
        endforeach;
        $showideo = $v['video_cover'] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/api_qr.inc.php');
        $cntimg = count($v[img_preview]);
        if(!$showideo):
        if($cntimg<3 && strpos($v[description], '<img')!==false):
            preg_match_all('/img\s+src\=\"([^\"]*?)\"/is', $v[description], $tmpimg);
            $v[img_count] = count($tmpimg[1]);
            if($tmpimg[1]):
                $v[img_preview] = array_slice(array_merge($v[img_preview], $tmpimg[1]), 0, 3);
            endif;
        endif;
        else:
            if(!$cntimg):
                $v[img_preview] = array( $v['video_cover']);
            else:
                $v[img_preview] = array_merge(array( $v['video_cover']) ,$v[img_preview]);
            endif;
        endif;
        $v[img_preview] = array_slice($v[img_preview], 0, 3);
        $cntimg = count($v[img_preview]);
        }-->

        <!--{eval //$hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[uid]); //$hhcard = C::t('#xigua_hk#xigua_hk_card')->fetch_by_uid($v[uid]); }-->
        <!--{eval //$hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[uid]);}-->
        <!--{eval  //$xiaomy_certification = C::t('#xiaomy_certification#xiaomy_certification')->fetch_first_field_data("rescodebdres","where uid=".$v['uid']." order by dateline desc"); }-->

        <!-- 卡片：暖白珊瑚色系杂志风格 -->
        <article class="post-card">
            <!-- 作者信息区 -->
            <div class="post-author">
                <a href="#">
                    <div class="avatar-outer">
                        <img src="uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1" alt="avatar">
                    </div>
                    <div class="author-info">
                        <div class="author-name">{$v[realname]}</div>
                        <div class="author-time">
                            <span class="time-badge">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                                {$v[time_u]}刷新
                            </span>
                        </div>
                    </div>
                    <!-- 置顶标签（如果有） -->
                    <!--{if $v[dig_on]}-->
                    <div class="pinned-tag">
                        <!--{if $v[zdword]}-->$v[zdword]<!--{else}-->置顶<!--{/if}-->
                    </div>
                    <!--{/if}-->
                </a>
            </div>

            <!-- 标题与摘要 -->
            <div class="post-content">
                <h2 class="post-title" style="font-size:16px!important;">
                    <a href="plugin.php?id=xigua_hb&ac=view&pubid=$v[id]">$subtit</a>
                </h2>
               
            </div>

            <!-- 底部：分类 + 阅读量 -->
            <div class="post-meta">
                <div class="category-tag">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                    </svg>
                    $cats[$v[catid]][name]
                </div>
                <div class="read-stats">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                    </svg>
                    {$v[views]} 阅读
                </div>
            </div>
        </article>
        <!--{template xigua_hb:ad}-->
        <!--{/loop}-->
    </div>
</div>
<!--{/if}-->