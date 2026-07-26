<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{if !$_GET[inview]}-->
<!--{loop $list $v}-->
<div class="hong_item">
    <div class="hong_portrait">
        <img src="{avatar($v['uid'], 'big', true)}">
    </div>
    <div class="hong_iteminfo">
        <p>{$v[user][username]}</p>
        <p class="hong_itemdate">{$v[crts]}</p>
    </div>
    <div class="hong_itemamount">
        <p>{$v[size]}{lang xigua_hb:yuan}</p>
    </div>
</div>
<!--{/loop}-->
<!--{else}-->
<!--{if $list}-->
<!--{loop $list $v}-->
<div class="weui-cell cell_hong_list">
    <div class="weui-cell__hd"><img src="{avatar($v['uid'], 'big', true)}" style=""></div>
    <div class="weui-cell__bd">
        <p>{$v[user][username]}</p>
    </div>
    <div class="weui-cell__ft color-red">{$v[size]}{lang xigua_hb:yuan}</div>
</div>
<!--{/loop}-->
<!--{else}-->

<div class="weui-cell">
    <div class="weui-cell__bd">
        <p class="c9 f14">{lang xigua_hb:zanwu}</p>
    </div>
</div>
<!--{/if}-->

<!--{/if}-->