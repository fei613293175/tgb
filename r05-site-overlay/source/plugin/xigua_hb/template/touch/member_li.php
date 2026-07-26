<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{loop $list $v}-->
<a href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[visiter]" class="weui-cell">
    <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
        <img src="{avatar($v[visiter], 'big', true)}" style="width:30px;height:30px;margin-right:5px;display:block;border-radius:50%" />
    </div>
    <div class="weui-cell__bd">
        <p>$v[visiter_name]</p>
    </div>
    <div class="weui-cell__ft">
        $v[crts]
    </div>
</a>
<!--{/loop}-->
