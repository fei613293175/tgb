<?php exit('Author: https://addon.dismall.com/?@xigua �������� �ͷ�QQ 1628585958 ΢�� wxiguabbs'); ?>
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
        <img src="{avatar($kuid, 'big', true)}" style="width:44px;height:44px;margin-right:8px;display:block;border:2px solid #edf3fa;border-radius:50%" />
    </div>
    <div class="weui-cell__bd">
        <p>{$users[$kuid]['username']}</p>
    </div>
    <div class="weui-cell__ft">
        $v[crts_u]
    </div>
</a>
<!--{/loop}-->
