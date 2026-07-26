<?php exit('new'); ?>
<!--{if $config['showpubsucd'] && $_GET['pubid'] && !getcookie('disable_'.$_GET['pubid'])}-->
<!--{eval
dsetcookie('disable_'.$_GET['pubid'], 1, 86400);
dheader("Location: $SCRITPTNAME?id=xigua_hb&ac=succeed&pubid=".$_GET['pubid'].$urlext);
}-->
<!--{/if}-->
<!--{template xigua_hb:common_header}-->



<style>
    .po-cmt{
        padding-left: 6.2rem;
        padding-right: .75rem;

    }
    .view_jump {

    }
    .po-avt{
        width: 7em;
        height: 4rem;
        border-radius: 10px;
    }
    .mod-feed-text{
        margin:0px;
        font-size:15px!important;
        font-weight: 700;
    }
    .mod-lv{
        transform: scale(.94);
        margin:0px;
    }
    .opqy {


        margin-top: 120px;
        margin-left: 5px;
        border-top: 2px solid #f7f7f7;

        padding-top: 12px;
        margin-right: 5px;
    }

    .opqy a.weui-btn{
        width: 2.1rem;
        height: 1.3rem;
        line-height: 1.3rem;
        background-color: #000!important;
        color: #666!important;
        margin-bottom: 1.1rem;

    }
    #list .li{
            padding: .15rem 0;
    }

    }
    #list, #list .li, .po-hd, .post {
        overflow: hidden;
    }
    #list {
        padding: 0;
        font-size: .7rem;
    }
    .listdata {
        margin: 15px 15px;
        background-color: #fff0;
    }
    .mod-post {
        background: #fafafa;
        overflow: hidden;
    }

    .pt0 {
        padding-top: 10px !important;
    }

    .listdata-1 {
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 0px;


    }

    .listdata-card {
        background-color: #ffead9;
        background-image: linear-gradient(180deg, #ffead9 0%, #ffffff 100%);
        border-radius: 10px;
        margin-bottom: 15px;
        height: 260px;
    }

    .listdata-card-top, .listdata-card-bottom {
        padding: 12px;
        font-size: 0.75rem;
        color: #ff0000;
        font-weight: 550;
    }

    .weui-cells-new {
        background-color: #fff0;
        margin-top: 0px;
        font-weight: bold;
        font-size: 0.75rem;
    }

    .placeholder-new {
        /* text-align: center; */
        display: flex;
        align-items: center;
    }

    #list{
        padding:10px;
    }

    .li{
        border-radius: 10px;
    }
    .weui-btn:after{
        border:0px;
    }

    .btn-new01{
        width: 4.52rem!important;
        background-image: linear-gradient(90deg, #f7f7f7 1%, #f7f7f7 99%);
        color: #fff!important;
        padding: 0px 10px;
        height: 40px!important;
        line-height: 40px!important;
        border-radius: 20px;
    }

</style>
<style data-tgb-r05-lane-b="my-publications">
body { background:#f4f7fb!important; color:#405166!important; font-family:"PingFang SC","Microsoft YaHei",system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif!important; }
.page__bd { box-sizing:border-box; min-height:100vh; padding:12px 0 calc(76px + env(safe-area-inset-bottom,0px)); background:#f4f7fb!important; }
.page__bd #list { box-sizing:border-box; margin:0; padding:0 16px!important; background:transparent!important; color:#405166; }
.page__bd #list .li, .page__bd #list .listdata-1, .page__bd #list .listdata-card { box-sizing:border-box; height:auto!important; margin:0 0 12px!important; border:1px solid #d8e1ec; border-radius:8px!important; background:#fff!important; background-image:none!important; box-shadow:0 4px 14px rgba(12,27,51,.05); }
.page__bd #list .post { box-sizing:border-box; max-width:100%; border:0!important; background:transparent!important; box-shadow:none!important; }
.page__bd #list .listdata, .page__bd #list .weui-cells-new { margin:0!important; background:transparent!important; color:#405166!important; font-weight:500!important; }
.page__bd #list .mod-feed-text, .page__bd #list .mod-feed-text a { color:#0e1b2a!important; font-size:15px!important; line-height:23px!important; overflow-wrap:anywhere; }
.page__bd #list .mod-lv, .page__bd #list .item_tags span, .page__bd #list .bftag { border-color:#bfd0e3!important; border-radius:6px!important; background:#edf3fa!important; background-image:none!important; color:#2176c7!important; }
.page__bd #list .weui-btn, .page__bd #list .weui-btn_mini, .page__bd #list .btn-new01 { box-sizing:border-box; min-width:76px; min-height:44px!important; padding:0 12px!important; border:1px solid #bfd0e3!important; border-radius:8px!important; background:#fff!important; background-image:none!important; color:#2764ff!important; font-size:13px!important; line-height:42px!important; box-shadow:none!important; }
.page__bd #list .c_opt, .page__bd #list .showfull, .page__bd #list .c-icon { box-sizing:border-box; display:inline-flex; min-width:44px; min-height:44px; align-items:center; justify-content:center; color:#2764ff!important; }
.page__bd #list .touch-panel a, .page__bd #list .po-act a { box-sizing:border-box; min-height:44px; }
.page__bd #list .po-act, .page__bd #list .opqy { border-color:#d8e1ec!important; background:#fff!important; }
.page__bd #list img, .page__bd #list video { max-width:100%; }
.page__bd #list .mod-feed-text, .page__bd #list .time, .page__bd #list .ipadr, .page__bd #list .da { overflow-wrap:anywhere; word-break:break-word; }
.page__bd #list .feed-preview-pic { max-width:100%; overflow:hidden; }
.page__bd #list .feed-preview-pic img { width:100%; height:100%; object-fit:cover; }
.page__bd #list .tgb-r05-traffic-mark { display:inline-flex; width:24px; height:24px; margin-right:4px; align-items:flex-end; justify-content:center; gap:2px; vertical-align:middle; }
.page__bd #list .tgb-r05-traffic-mark:before { content:""; width:3px; height:8px; border-radius:2px 2px 0 0; background:#19b8a9; box-shadow:5px -4px 0 #2764ff,10px -9px 0 #7657ff; }
.page__bd .weui-loadmore, .page__bd .weui-loadmore__tips { color:#718096!important; }
</style>


<div class="page__bd" style="margin-top:35px;">
    <!--{template xigua_hb:common_nav}-->
   
    <div id="list" class="mod-post x-postlist "></div>
    <!--{template xigua_hb:loading}-->
</div>

<script>
    var loadingurl = window.location.href+'&newac={$_GET['ac']}&ac=list_item&is_my=1&inajax=1&page=';
    scrollto = 0;
</script>
<!--{eval $tabbar=1;}-->
<!--{template xigua_hb:common_footer}-->
<script>
    <!--{if $_GET['pubid'] && !getcookie('disable_'.$_GET['pubid'])}-->
    <!--{eval dsetcookie('disable_'.$_GET['pubid'], 1, 86400);}-->
    setTimeout(function(){
        if($('#pubitem_$_GET[pubid]').length>0){
            showansi($('#pubitem_$_GET[pubid]')[0]);
        }
    }, 2000);
    <!--{/if}-->
</script>
