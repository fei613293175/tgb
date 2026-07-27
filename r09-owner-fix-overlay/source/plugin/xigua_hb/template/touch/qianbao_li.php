<?php exit('Author: https://addon.dismall.com/?@xigua'); ?>
<!--{loop $list $v}-->
<div class="order-card">
    <a <!--{if $_GET[type]=='in'}-->href="$v[link]" class="weui-cell weui-cell_access wallet-record-link"<!--{else}-->class="weui-cell wallet-record-link"<!--{/if}-->>
        <div class="wallet-record-main">
            <div class="wallet-record-amount"><!--{if $_GET[type]=='out'}-->-<!--{/if}-->$v[size]</div>
            <div class="wallet-record-note">
                <!--{if $_GET[type]=='in'}-->
                $v[note]
                <!--{elseif $v[return_msg] || $v[err_code_des]}-->
                {eval echo $v[return_msg]?$v[return_msg]:$v[err_code_des]}
                <!--{else}-->
                {lang xigua_hb:tixiandao}
                <!--{/if}-->
            </div>
        </div>
        <time class="wallet-record-time">$v[crts]</time>
        <!--{if $_GET[type]=='out' && $v[status]==0}--><div class="wallet-record-progress"></div><!--{/if}-->
    </a>
</div>
<!--{/loop}-->
