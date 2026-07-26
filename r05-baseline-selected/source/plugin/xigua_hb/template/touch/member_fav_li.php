<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{loop $list $v}-->
<!--{if $_GET['fav']=='fans'}-->
<!--{eval $kuid = $v[uid];}-->
<!--{else}-->
<!--{eval $kuid = $v[favid];}-->
<!--{/if}-->
<!--{if !$users[$kuid]['username']}-->
<!--{eval continue;}-->
<!--{/if}-->
<!--{if !$_GET['uid']}-->
<a href="javascript:;" class="weui-cell favuser" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$kuid" data-uid="{$kuid}" data-username="{$users[$kuid]['username']}">
<!--{else}-->
<a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$kuid" class="weui-cell">
<!--{/if}-->
    <div class="weui-cell__hd" style="position: relative;margin-right:.5rem">
        <img src="{avatar($kuid, 'big', true)}" style="width:1.5rem;height:1.5rem;margin-right:.25rem;display:block;border-radius:50%" />
    </div>
    <div class="weui-cell__bd">
        <p>{$users[$kuid]['username']}</p>
    </div>
    <div class="weui-cell__ft">
        $v[crts_u]
    </div>
</a>
<!--{/loop}-->