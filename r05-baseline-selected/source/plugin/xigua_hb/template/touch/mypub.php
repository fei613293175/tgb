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