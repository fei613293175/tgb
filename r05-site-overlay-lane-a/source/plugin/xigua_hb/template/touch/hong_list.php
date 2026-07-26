<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}-->
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r05/lane-a-light-grid-r05.css?20260727-r05-a1">
<script>document.documentElement.classList.add('tgb-r05-redpack-page');</script>
<!--{eval $no_header_fix=1;$hide_nav=0;}--><!--{if IN_MAGAPP || IN_QIANFAN || IN_APPBYME||IN_PROG}--><style>.x_header a:first-child{display:none}</style><!--{/if}-->
<!--{eval $hidenav=1;}-->
<style>
.hong_res{position: relative}
.footer_fix{display:none}
</style>
<div class="page__bd hong tgb-r05-redpack">
<!--{eval
if(0&& !(IN_MAGAPP || IN_QIANFAN)&&$config['qbguide']&&$config['qbguidelink']):
    $url = "javascript:onclick='return jump_download();'";
else:
    if(0 && IN_QIANFAN && $config['autoinapp']):
        $url = "javascript:onclick='QFH5.jumpMyPackage();'";
    elseif(IN_MAGAPP&&$config['autoinapp']):
        $url = "javascript:onclick='mag.newWin('/mag/user/v1/user/wallet');'";
    else:
        $url = "$SCRITPTNAME?id=xigua_hb&ac=qianbao".$urlext;
    endif;
endif;
$custom_side = array($url,lang_hb('qb', 0));
}--><!--{template xigua_hb:common_nav}-->
    <div class="hong_res animated zoomIn" style="display:block">
        <div class="hong_res_wrap">
            <div class="hong_res_head">
                <div class="hong_res_head_in">
                    <img src="{avatar($v['uid'], 'big', true)}">
                </div>
            </div>
            <div class="hong_res_cnt">
                <div class="hong_res_box">
                    <p>$v[user][username]</p>
                    <p>{lang xigua_hb:mai}</p>
                </div>

                <div class="hong_list_outer" style="display: block">
                    <div class="hong_list_h weui-flex">
                        <span></span>
                        <p class="weui-flex__item tit js-cnt">{lang xigua_hb:gong}<span id="total">$v[hb_num]</span>{lang xigua_hb:mai1}<span id="sendnum">$v[hb_sendnum]</span>{lang xigua_hb:mai2}<span id="over">{eval echo $v[hb_num]-$v[hb_sendnum];}</span>{lang xigua_hb:fen}</p>
                        <span></span>
                    </div>
                    <div class="hong_list" id="hong_list">

                    </div>
                </div>



            </div>
            <div class="sub_bg"></div>
        </div>
    </div>

</div>

<!--{template xigua_hb:common_footer}-->
<script>

    var loadingurl = window.location.href+'&ac=hong_li&inajax=1&page=';
    $(document.body).infinite().on("infinite", function() {
        if(loading) return;
        loading = true;
        load_morehong();
    });
    load_morehong();
    var dolock = 1;
    function load_morehong(){
        if(dolock===1){
            return false;
        }
        dolock = 1;
        if(page<=0){
            return ;
        }
        $.ajax({
            type: 'GET',
            url: loadingurl+''+page,
            dataType: 'xml',
            success: function (data) {
                if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                var s = data.lastChild.firstChild.nodeValue;
                if(!s){
                    page = -1;
                    return ;
                }
                $("#hong_list").append(s);
                loading = false;
                console.log(page);
                page ++;
                dolock = 0;
            },
            error: function() {
                loading = false;
                dolock = 0;
            }
        });
    }
</script>
