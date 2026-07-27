<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{loop $list $_k $v}-->
<!--{if strpos($v['comment'], '|/|')!==false}-->
<!--{eval list($_img, $_link, $_title, $_description, $_price) = explode('|/|', trim($v['comment']));}-->
<li class="consult_cell">
    <time class="date-talk" style="margin-bottom:.5rem">{$v['crts_u']}</time>
    <a href="{$_link}">
        <div class="chat___goodsCard">
            <!--{if $_img}-->
            <div class="taro_img"><img src="$_img" onerror="this.error=null;this.src='source/plugin/xigua_hb/static/img/zhanwei.png'"></div>
            <!--{/if}-->
            <div class="chat___goodsInfo___2DueR">
                <div class="chat___name___10ZSG" >$_title</div>
                <!--{if $_price}--><div class="chat___price___2AE1P" >$_price</div><!--{/if}-->
                <div class="chat___hosName___jk80L" >$_description</div>
                <div class="chat___reserveBtn___2Fq9t">{eval echo lang('space', 'click_view')}</div>
            </div>
        </div></a>
</li>
<!--{else}-->
<li class="talk-li type_$v[type]">
    <time class="date-talk {$v['who']}-date">{$v['crts_u']}</time>
    <section class="{$v['who']}-talk">
        <a class="avatar-talk" href="$SCRITPTNAME?id=xigua_hb&ac=member&uid={$v['authorid']}"><img src="uc_server/avatar.php?uid={$v[uid]}&size=middle&ts=1" onerror="this.error=null;this.src='source/plugin/xigua_hb/static/img/zhanwei.png'" alt=""></a>
        <div class="content-talk content-wb">{$v['comment']}</div>
    </section>
</li>
<!--{/if}-->
<!--{/loop}-->