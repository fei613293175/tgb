<?php exit('Author: https://addon.dismall.com/?@xigua '); ?>




<a id="cmt_li_{$comment['cid']}" <!--{if $_GET['pagetype']=='page'}-->href="$SCRITPTNAME?id=xigua_hb&ac=chat&touid={echo $comment[authorid]!=$_G[uid]?$comment[authorid]:$comment[touid]}" data-type="1"<!--{/if}--> data-pubid="<!--{if $_GET[type]!='sx'}-->$comment[pubid]<!--{else}-->0<!--{/if}-->" data-cmtid="$comment[cid]" data-authorid="$comment[authorid]" data-author="$comment[author]" <!--{if $_G['cache']['hb_ext_config']['zjpl']>0}-->data-cai="1"<!--{/if}-->>

<div class="weui-cells  <!--{if $comment[og_pubid]}-->ogcmt<!--{/if}-->">
    <div class="weui-cell" style="padding:10px 12px;align-items:center;">
        <div class="weui-cell__hd" style="margin-right:10px;">
            <img src="{avatar($comment['authorid'], 'big', true)}" alt=""
                 style="width:40px;height:40px;display:block;border:2px solid #edf3fa;border-radius:50%;">
        </div>
        <div class="weui-cell__bd">
            <p class="bigtxt" style="color:#0e1b2a;font-weight:500;height:auto;line-height:22px;margin:0;overflow-wrap:anywhere;">$comment[author]</p>
            <p class="smalltxt" style="color:#718096;line-height:18px;overflow-wrap:anywhere;">$comment[crts] ·  IP属地: {$comment[ipaddr]}</p>
        </div>

        
     <!--   <div class="weui-cell__ft smalltxt" style="align-items: center;">
            <i class="far fa-heart"></i>
 <!--{if !$comment[og_pubid]}-->     <!--<p style="font-size: 0.55rem;color:#fff;border-radius: 15px;padding:2px 8px;background-color: #00A1EB;" onclick="showcomment($comment['authorid'],$comment['cid'])">{lang xigua_hb:huifu}</p>
            <!--{/if}-->
     <!--   </div>   !-->
    </div>
    <div class="view-content-comment-text" style="margin:0 12px 12px;padding:10px 12px;background:#edf3fa;border-radius:6px;color:#405166;line-height:22px;overflow-wrap:anywhere;">
        {$comment[comment]}
    </div>
</div>
</a>

