<?php exit('Author: '); ?>
<!--{if ($_GET[cid]||$comment_simple) && !$_GET[multi]}-->
<!--{loop $comments $comment}-->
<p class="cmt_p" data-pubid="$comment[pubid]" data-cmtid="$comment[cid]" data-authorid="$comment[authorid]" data-author="$comment[author]"><!--{if $comment[touser]}--> <span>$comment[author] </span> {lang xigua_hb:huifu} <span>$comment[touser] :</span> <!--{else}--> <span>$comment[author]: </span> <!--{/if}--> {$comment[comment]}</p>
<!--{/loop}-->
<!--{else}-->

<!--{if $comments}-->
<!--{loop $comments $comment}-->
    <!--{subtemplate xigua_hb:comment_li_01_sub}-->
    <!--{eval $subss = $comment[subs];}-->
    <!--{loop $subss $comment}-->
        <!--{subtemplate xigua_hb:comment_li_01_sub}-->
    <!--{/loop}-->
<!--{/loop}-->
<!--{if $_GET['needmore']&& count($comments)>=$_GET[pagesize]}-->
<a href="javascript:;" id="comment_ul_more" class="weui-media-box weui-media-box_appmsg">
    <div class="weui-media-box__bd">
        <p class="weui-media-box__desc tc mt0 c9">{lang xigua_hb:morehuifu}</p>
    </div>
</a>
<!--{/if}-->
<!--{else}-->
<!--{if !$_GET[hidezw]}-->



<div class="weui-cell">
    <div class="weui-cell__bd">
        <!--{if $_GET['isly']}-->
        <div class="lybox" style="text-align: center;margin-top: .5rem;">
            <p class="c9 f14">&#24819;&#30693;&#36947;&#23453;&#36125;&#26356;&#22810;&#32454;&#33410;</p>
            <a style="box-sizing:border-box;background:#2764ff;color:#fff;border-radius:8px;font-size:14px;display:block;width:160px;margin:10px auto;min-height:44px;line-height:44px;box-shadow:none;" href="javascript:;" onclick="$('#comment_{$_GET[pubid]}').trigger('click');">&#28857;&#20987;&#30041;&#35328;</a>
        </div>
        <!--{else}-->
        <p class="c9 f14">{lang xigua_hb:zanwuhuifu}</p>
        <!--{/if}-->
    </div>
</div>
<!--{/if}-->
<!--{/if}-->

<!--{/if}-->
