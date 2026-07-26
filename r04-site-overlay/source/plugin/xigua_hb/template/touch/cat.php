<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958 微信 wxiguabbs'); ?>
<!--{template xigua_hb:common_header}--><!--{eval $orderby_list['zan'] = lang_hb('dzl', 0);}--><!--{if $newindex_list}--><style>.nav_expand_panel{top:auto}</style><!--{/if}-->

<style>
    .tb-item-new{
        margin-bottom: -10px;
        width: 100%;
        height: 100px;
        background: #fff;
        border-radius: 10px;
        position: relative;
    }
    .tb-item-new01{
        display: flex;
        align-items: center;
        /*    padding: 5px 13px;*/

        border-bottom: 1px solid rgba(0,0,0,.05);
        margin: 15px 13px;
        margin: 10px 9px!important;

    }
    .tb-item-new02{
        width: 105px;
        height: 100px;
        border-radius: 10px;
        overflow: visible;
        background-color: transparent;
    }
    .tb-item-new02 img{
        border-radius: 5px;
        width: 106px;
        height: 80px;
        object-fit: cover;
    }

    .tb-item-new03{


        align-items: flex-start;
        flex-direction: column;
        justify-content: space-between;

        width: 100%;
        height: 100%;


        line-height: 25px;
        margin-right: 5px;

    }

    .tb-item-new04{

        font-size: 14px;
        font-weight: 700;
        color: #010101;
        line-height: 19px;

    }
    .zhiding{


        -webkit-transform: scale(.94);
        transform: scale(.94);
        text-align:center;
        font-size: 0.4333rem;
        font-size:11px;
        width:25px;
        float: left;

        background-image: linear-gradient(160deg, #464466 0%, #5f5c88 51%);




        color:#fff;

        border-radius: 5px;
        margin-top: 0.1333rem;
        margin-right: 5px;



    }

    .RanmenBox_626a22 {
        position: absolute;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.5) 100%);
        bottom: 14px;
        pointer-events: none;
        overflow: hidden;
        width: 105px;
        border-radius: 5px;
    }

    .newRanmenBox_3d9607 .number_622bcf {
        left: 0.2rem;
        bottom: 0.2rem;
        height: auto;
        padding: 0 0.13333rem;
        border-radius: none;
        transform: scale(0.92);
        transform-origin: left bottom;
        background-color: transparent;
        z-index: 2;
    }

    .newRanmenBox_3d9607 .numberAlpha_bf049f {
        width: 105px;
        height: 100px;

        position: absolute;
        left: 0;
        bottom: 0;
    }


    .RanmenBox_626a22 .number_622bcf span {
        max-width: 2.26667rem;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        padding-left: 0.13333rem;
        font-size: 0.58667rem;
        color: #fff;
    }

.fineBorderBottom{
    position: relative;
}

.hotWord {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    justify-content: space-between;
    height: 43px;
    padding: 0 10px 0 8px;
}

.hotWord-rank.s-rank-1 {
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAD8AAAA/CAMAAABggeDtAAAB1FBMVEUAAAD/cUj/y8v/azX/WjX/bzT/Vyv/FhD/FBD/azT/QyX/YzL/LRz/FxP/XS3/FRD/VCn/IBT/azP/cDX/azP/OiD/bzX/Xi3/FBH/Uin/Syj/HxT/FxL/HBb/bzX/FA//VCr/HxP/FBD/XS3/Syf/Xi//Wy3/Dw3/QCL/LBn/QCH/IRX/Dg3/Wiz/VCr/EA7/PiH/bTT/ExD/aDL/ERD/MBr/WCz/ajT/aTT/Vy//Jxf/HRL/FQ//bzb/bjb/YzD/cDX/cDX/ZzL/cDX/QCL/Lxv/Mhv/azP/Dw7/SSX/LRn/cDb/DQ3/KBj/USn/Dg7/aTP/ZjL/DQ3/RSP/Lhr/SCX/OR//NyD/MR3/ZjP/////PSD/MBv/bzX/QiL/SSX/Kxn/Uin/QCH/IhX/VCr/Nx7/Nh3/KRj/RSP/Jxf/GhL/UCj/LRn/TCb/OR//NBz/JRb/IBT/HhP/bjT/Viv/RyT/Wy3/Tif/GBH/WSz/FhD/ZTD/FA//YC//Xy7/Mhv/azP/Yi//Eg7/Ox//ZzL/HBL/WCv/XS3/Dg3/39v/aTL/3Nr/qZ3/pJr/EA3/r6D/oJn/gWX/7u3/1c7/y8H/gmX/cFT/+Pf/zcr/lI//a0r/Yz8+pakbAAAAWnRSTlMABAEQCuSFg15HKygiG+jc2cm9tq+bmJGRaUZBOxb8+/Xv7+7k08zDuK+urKeknJqDbm1lWFdONjAW+vr49/f08O/s6+fi39bPzszLyb+5sqGEd3NqWTo4NB6Qs3nAAAADeUlEQVRIx82VZ0MTQRCGN6H33nsHaYKA2HvvXUkOCBxFOkkgdASkiBJFsf9ZZ3bvdjdLAiR88ZnZgw/z3Hu3d0lIAMLIsbBeTIsjx+GCVph5nGu4pmlaREzofoSmuTQt0xKy78ITuNLCQ/Qvgw247oW2CdZC8LG11JBuoZ2Gsz0IxW/SXGhjuU6F4KdqgitngtZPs2jjHtKD9tPgssE3CfY9inG9w+LcCu4ZhEUIdZvSFNSzT99+jyWYvxrEW2R5zBwfnhxdTxfWyvwKNHLpqM8wPBUFwaJBFt3XQ1+lNxWLEktYjER8BJVLWdaD35tEkEw2cW1Cb04DbYTEwZ/6g07QXobTyDQ4nFnsBkIiZ4GswHpcGTdwSWzMblQSS6J7w+12RwbcugpJoLixDJJJpFvXdVg5Afx6NEDg6NAgMIojdYeuOxy6ozjOr57LFF0ChqlhMow17Hjo971J5g62cKgm8EC3+fFzeASAx7/fd3d3Yf3xDHuwGaPQo+XhRMWazFM8UDC/18H4jNKoRNdoV87+u/fwBDb+o8P0UVC4Q1QaQPHJ2OM+U6amuqbwyIhVL79cSYB40zeUwalBwTPFj8WTY4rJzjfgJ/WZ0cnKoFrxX/AIKaXzA/qfOiV6cAHXFf8kjlNHhvk9oMjYemw2m/KFliFHcMHwbWCoKBt4wjfBgPn03zXoNdsaMLY2Brz09avlBC4wHw1BX98YVF+ukm9aFNNgPkzL9Pf1A6+U+1cSGMzvB0Mw3j+OHavsPzgCU6D+DjicZWiK8glqVQMozGeG3Q6Lc4P4Eg+GCDEN5tt9GLGPADVE4eayHQRYMh+pPyLTPdKNPFf9DCkBi8L8bpQMBgZgQcWr/muRIMap/wUFdAYYb6EekH3c7ZZh08w3JJnW/X4eKmYEjGMxH+Yn6JowSbGS/dRQBcFpHDd85vRO9HLyiR/iz04o9DJfeJO9k9i1xC9RNEOe/vUV+I0K4qQH52RJvH/fWssToE3H6cO6c/1cXsAf0BRDUhVgaAiaEk0CUpDkhGFmDEHLzEBjRZEDSEiRBDoOwAGZm5uZmylqJgdiyebGHDSWyQJ0SR45jOhS7iywFlQlkMMpyD4vjNWFVWhGUouVHImExqJVjnfV6/VubXmToi3kyBS01JVugWVyOyrfSoLDkt8c1fjoflVd9tPoBPLf8g+hKhv8RWXqGQAAAABJRU5ErkJggg==);
}

.hotWord-rank.s-rank-2 {
    background-image: url(data:image/png;base64,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);
}

.hotWord-rank.s-rank-3 {
    background-image: url(data:image/png;base64,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);
}

.ellipsis {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.hotWord-title {
    margin-right: 10px;
    font-size: 0.75rem;
    color: #333;
}


.hotWord-rank.s-rank-1, .hotWord-rank.s-rank-2, .hotWord-rank.s-rank-3 {
    height: 21px;
    font-size: 0;
    margin-top: -2px;
    background-size: 100%;
}
.hotWord-rank {
    -webkit-flex-shrink: 0;
    flex-shrink: 0;
    box-sizing: border-box;
    margin-right: 9px;
    width: 21px;
    font-family: Futura-Medium,sans-serif;
    text-align: center;
    color: #999;
    font-size: 0.8rem;
}
.hotWord-tag.s-tag-Hot {
    background: #fe5d10;
}


.fineBorderBottom:after {
    content: "";
    box-sizing: border-box;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 9;
    width: 100%;
    height: 100%;
    border: 1px solid #edeff3;
    pointer-events: none;
    -webkit-transform-origin: top left;
    transform-origin: top left;
}

.hotWord-tag {
    -webkit-flex-shrink: 0;
    flex-shrink: 0;
    margin-right: 7px;
    padding: 0 2.5px;
    font-size: 12px;
    text-align: center;
    color: #fff;
    border-radius: 3px;
}

.fineBorderBottom:after {
    width: 200%;
    height: 200%;
    -webkit-transform: scale(.5);
    transform: scale(.5);

    border-width: 0;
    border-bottom-width: 1px;

}

.hotWord-num {
    -webkit-flex-shrink: 0;
    flex-shrink: 0;
    margin-left: auto;
    font-size: 13px;
    color: #999;
}

.listdata-card-top, .listdata-card-bottom {
    padding: 12px;
    font-size: 0.75rem;
    color: #ff0000;
    font-weight: bold;
}

.listdata-card-top a {
    position: relative;
    display: flex;
    align-items: center;
}

.listdata-card-top font {
    margin-left: 10px;
}

  .listdata {

        background-color: #f1f4fb;
        background-repeat: no-repeat;

        width:100%!important;
        margin-top: 137px;
    }

    .listdata-card {
        background-color: #fff;
        box-shadow: 0px -1px 5px 0px rgba(0,0,0,0.1);
        border-radius: 15px;
        margin-bottom: 5px;
        height: 230px;
        margin-left: 15px;
        margin-right: 15px;

    }
    .listdata-card1 {
        background-color: #fff!important;
        box-shadow: 0 -1px 5px 0 rgba(0,0,0,0.1);
        border-radius: 15px;
        margin-bottom: 5px;
        height: 230px;

        margin-left: 15px;
        margin-right: 15px;}

    .listdata-card-top, .listdata-card-bottom {
        padding: 25px 15px;
        font-size: 0.75rem;
        color: #ff0000;
        font-weight: bold;
    }

    .listdata-card-top img {
        vertical-align: middle;
        width: 20px;
    }
    .font{
       color:#4b4b4b;font-weight:450;font-size:13px;
       top:2px;
       position: relative;
    }
   .font1{
    right:-55%!important;
    float: right!important;
    position: absolute;
}

</style>




<div class="page__bd">
<!--{if $_G['cache']['hb_ext_config']['hide_floatfix']}--><style>.fix_float_fix,.weui-navbar{display:none}</style><!--{/if}-->
<!--{if $catinfo['share_pic']}--><div style="width:0;height:0;overflow:hidden;display:none"><img src="$catinfo['share_pic']" /></div><!--{/if}-->
<!--{if IN_MAGAPP}--><style>.nav_expand_panel{position:absolute;height:auto!important}.nav_expand_panel .weui-flex__item{height:auto;overflow:hidden!important}.backtotop_show{transform:translate3d(0,-10rem,0)}</style><!--{/if}-->
    <!--{template xigua_hb:common_nav}-->
<!--{eval
$keyworden = urlencode($keyword);
$filteren =  urlencode($filter);
$provinceen = urlencode($_GET[province]);
$cityen = urlencode($city);
$disten = urlencode($dist);
$adwhere = array();
$adwhere[] = 'types=\'cat\'';
if($_GET['cat_id']):
    $adwhere[] = '( FIND_IN_SET('.intval($_GET['cat_id']).' , catids) OR FIND_IN_SET(-1, catids) )';
else:
    $adwhere[] = '( FIND_IN_SET(-1, catids) )';
endif;
$index_list =  C::t('#xigua_hb#xigua_hb_index')->list_by_where($adwhere);
$newindex_list = array();
if($index_list):
    foreach ($index_list as $index => $item):
        $newindex_list[$item['style']][] = $item;
    endforeach;
endif;
}-->
    <!--{template xigua_hb:touch/ad_in}-->
    <!--{if !$hide_nav}-->
    <!--{if !$newindex_list}--><div class="cl fix_float_fix"></div><!--{/if}-->
    <!--{else}-->
    <!--{if !$newindex_list}--><div class="cl fix_float_fix"></div><!--{/if}-->
    <style>.fix_float{top:0}.nav_expand_panel{top:2.45rem}#dist_show_4{height:calc(100vh - 4.75rem)}</style>
    <!--{/if}-->

    <div class="dist_show" style="margin-top:-20px;">
        <div id="dist_show_1" class="nav_expand_panel border_top">
            <div class="weui-flex">
                <div class="weui-flex__item" <!--{if $_G['cache']['hb_ext_config']['hidearea1']}-->style="display:none"<!--{/if}-->>
                    <ul>
                        <li class="first_check border_bfull <!--{if !$_GET[province]}-->checked main_color<!--{/if}-->"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province=&city=&orderby=$orderby&keyword=$keyworden&lat=$lat&lng=$lng&filter={$filteren}{$urlext}">{lang xigua_hb:quanbu}</a></li>
                        <!--{loop $dist0 $v}-->
                        <li class="first_check border_bfull <!--{if $_GET[province]==$v[name]}-->checked main_color<!--{eval $city_id=$v['id'];}--><!--{/if}-->" data-id="$v[id]" data-link="{$v[link]}"><a>$v[name]</a></li>
                        <!--{/loop}-->
                    </ul>
                </div>
                <div class="weui-flex__item checked">
                    <!--{loop $dist0 $k $v}-->
                    <ul class="sub_cheker <!--{if $_GET[province]!=$v['name']}-->none<!--{else}-->checked<!--{/if}-->" id="sub_cheker_$v[id]">
                        <li class="sub_check border_bfull"><a data-href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={echo urlencode($v[name]);}&city=&orderby=$orderby&keyword=$keyworden&lat=$lat&lng=$lng&filter={$filteren}{$urlext}" class="choose color-red">{lang xigua_hb:quan}{$v[name]} <i class="iconfont icon-coordinates_fill f14 "></i></a></li>
                        <!--{loop $v[child] $vv}-->
                        <li class="sub_check border_bfull <!--{if $_GET[province]==$v['name'] && $city==$vv[name]&&$_GET[city]}-->checked main_color autotrigger<!--{/if}-->"><a data-href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={echo urlencode($v[name]);}&city={echo urlencode($vv[name]);}&orderby=$orderby&keyword=$keyworden&lat=$lat&lng=$lng&filter={$filteren}{$urlext}" id="sub_check{$vv[id]}" data-id="$vv[id]" onclick="hb_getnext($vv[id], '{$vv[name]}','$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&orderby=$orderby&keyword=$keyworden&lat=$lat&lng=$lng&filter={$filteren}{$urlext}','{$vv[link]}','$v[name]')">$vv[name]</a></li>
                        <!--{/loop}-->
                    </ul>
                    <!--{/loop}-->
                </div>
                <div class="weui-flex__item checked" id="ajaxbox"> <ul class="ajaxbox_cheker"></ul> </div>
            </div>
        </div>
        <div id="dist_show_2" class="nav_expand_panel border_top">
            <div class="weui-flex">
                <div class="weui-flex__item <!--{if $_GET['hidecat']}-->none<!--{/if}-->">
                    <ul>
                        <li class="first_cat_check border_bfull <!--{if !$cat_id}-->checked main_color<!--{/if}-->"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=&keyword=$keyworden&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=$orderby&lat=$lat&lng=$lng&filter={$filteren}{$urlext}">{lang xigua_hb:quanbu}</a></li>
                        <!--{loop $cat_tree $k $v}-->
                        <!--{if !$v[cat_link]}-->
                        <li class="first_cat_check border_bfull <!--{if $cat_id==$v[id]||$pid==$v[id]}-->checked main_color<!--{/if}-->"<!--{if $v[child]}--> data-id="$v[id]"<!--{/if}-->><a <!--{if !$v[child]}--> href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$v[id]&keyword=$keyworden&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=$orderby&lat=$lat&lng=$lng&filter={$filteren}{$urlext}"<!--{/if}-->><!--{if $_GET['hidecat']&&$v[icon]}--><img class="iconct" src="{$v[icon]}" /><!--{/if}-->$v[name]</a></li>
                        <!--{else}-->
                        <li class="first_cat_check border_bfull <!--{if $cat_id==$v[id]||$pid==$v[id]}-->checked main_color<!--{/if}-->"<!--{if $v[child]}--> data-id="$v[id]"<!--{/if}-->><a <!--{if !$v[child]}--> href="$v[cat_link]"<!--{/if}-->><!--{if $_GET['hidecat']&&$v[icon]}--><img class="iconct" src="{$v[icon]}" /><!--{/if}-->$v[name]</a></li>
                        <!--{/if}-->
                        <!--{/loop}-->
                    </ul>
                </div>
                <div class="weui-flex__item checked">
                    <!--{loop $cat_tree $k $v}-->
                    <ul class="sub_cat_cheker <!--{if !($cat_id==$v[id]||$pid==$v[id])}-->none<!--{/if}-->" id="sub_cat_cheker_$v[id]">
                        <li class="sub_cat_check border_bfull"><a <!--{if $cat_id==$v[id]}-->class="checked main_color"<!--{/if}--> href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$v[id]&keyword=$keyworden&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=$orderby&lat=$lat&lng=$lng&filter={$filteren}{$urlext}">{lang xigua_hb:quanbu}</a></li>
                        <!--{loop $v[child] $vv}-->
                        <li class="sub_cat_check border_bfull"><a <!--{if $cat_id==$vv[id]}-->class="checked main_color"<!--{/if}--> href="<!--{if $vv[cat_link]}-->$vv[cat_link]<!--{else}-->$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$vv[id]&keyword=$keyworden&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=$orderby&lat=$lat&lng=$lng&filter={$filteren}{$urlext}<!--{/if}-->"><!--{if $_GET['hidecat']&&$vv[icon]}--><img class="iconct" src="{$vv[icon]}" /><!--{/if}-->$vv[name]</a></li>
                        <!--{/loop}-->
                    </ul>
                    <!--{/loop}-->
                </div>
            </div>
        </div>
        <div id="dist_show_3" class="nav_expand_panel border_top">
            <div class="weui-flex">
                <div class="weui-flex__item">
                    <ul>
                        <li class="<!--{if !$orderby && !$_GET[hb]}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=&keyword=$keyworden&filter={$filteren}{$urlext}">$orderby_list['']</a></li>
                        <li class="<!--{if $orderby=='hot'}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=hot&keyword=$keyworden&filter={$filteren}{$urlext}">$orderby_list[hot]</a></li>
                        <li class="<!--{if $orderby=='new'}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=new&keyword=$keyworden&filter={$filteren}{$urlext}">$orderby_list[new]</a></li>
                        <li class="<!--{if $orderby=='img'}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=img&keyword=$keyworden&filter={$filteren}{$urlext}">$orderby_list[img]</a></li>
                        <li class="<!--{if $orderby=='zan'}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=zan&keyword=$keyworden&filter={$filteren}{$urlext}">{lang xigua_hb:dzl}</a></li>
                        <!--{if $_G['cache']['plugin']['xigua_hs'] && $config[showfj]}-->
                        <li class="<!--{if $orderby=='near'}-->checked main_color<!--{/if}--> border_bfull"><a id="near_xinxi" data-href="$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=near&keyword=$keyworden&filter={$filteren}{$urlext}">{lang xigua_hb:near}</a></li>
                        <!--{/if}-->
                        <li class="<!--{if $_GET[hb]}-->checked main_color<!--{/if}--> border_bfull"><a href="$SCRITPTNAME?id=xigua_hb&hb=1&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=&keyword=$keyworden&filter={$filteren}{$urlext}">{lang xigua_hb:hb}</a></li>
                    </ul>
                </div>

            </div>
        </div>

        <!--{if $filtervars}-->
        <div id="dist_show_4" class="nav_expand_panel border_top">
            <!--{loop $filtervars $_k $_v}-->
            <!--{eval $extra = trim($_v[extra]);}-->
            <div class="weui-cells__title c3" style="font-size: .8rem; color: #333; line-height: 1.35rem; margin-bottom: .6rem; font-weight: 500;">{echo $_v[title]?$_v[title]:$_v[bc][title]}</div>
            <div class="weui-cells before_none after_15">
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <!--{if $_v[data]}--><div class="main_color cat_m" data-title="{$_k}" id="cat_tit_{$_k}" style="display:none"><span>{lang xigua_hb:yx}</span><span id="cat_tit_in_{$_k}"></span><span style="font-weight:bold">X</span></div><!--{/if}-->
                        <div class="post-tags cl gray-tags">
                        <!--{if $extra}-->
                            <!--{eval $extratmp = explode("\n", $extra);}-->
                            <!--{loop $extratmp $__k $extra_string}-->
                            <!--{eval list($tmp1, $tmp2) = explode('=', trim($extra_string));}-->
                            <!--{if $_v['type']=='select' && !$tmp1}--><!--{eval continue;}--><!--{/if}-->
                            <!--{if $_v['type']=='mselects' && strpos($tmp1,'.')===false && strpos($extra, '.')!==false}-->
                            <div style="width:100%" class="c9 f13 mb8 z">{$tmp2}</div><!--{eval continue;}-->
                            <!--{/if}-->
                            <a class="weui-btn weui-btn_mini weui-btn_default <!--{if in_array($tmp1, $_filter[$_k])}-->tag-on<!--{/if}-->" data-title="{$_k}" data-value="$tmp1" href="javascript:;" <!--{if $_v['type']=='selects'||$_v['type']=='mselects'}-->data-multi="1"<!--{/if}-->>$tmp2</a>
                            <!--{/loop}-->
                            <!--{elseif $_v[data]}-->
                            <!--{loop $_v[data] $sub0}-->
                            <a class="weui-btn weui-btn_mini weui-btn_default <!--{if in_array($sub0[index], $_filter[$_k])}-->tag-on<!--{/if}--> level_1_{$_k}" data-lv="$sub0[index]" data-title="{$_k}" data-value="$sub0[index]" href="javascript:;">$sub0[name]</a>
                            <!--{if $sub0[sub]}--><!--{loop $sub0[sub] $sub1}-->
                            <a style="display:none" class="weui-btn weui-btn_mini weui-btn_default <!--{if in_array($sub1[index], $_filter[$_k])}-->tag-on<!--{/if}--> level_2_{$_k}"  data-lv="$sub1[index]" data-title="{$_k}" data-value="$sub1[index]" href="javascript:;">$sub1[name]</a>
                            <!--{if $sub1[sub]}--><!--{loop $sub1[sub] $sub2}-->
                            <a style="display:none" class="weui-btn weui-btn_mini weui-btn_default <!--{if in_array($sub2[index], $_filter[$_k])}-->tag-on<!--{/if}--> level_3_{$_k}"  data-lv="$sub2[index]" data-title="{$_k}" data-value="$sub2[index]" href="javascript:;">$sub2[name]</a>
                            <!--{/loop}--><!--{/if}-->
                            <!--{/loop}--><!--{/if}-->
                            <!--{/loop}-->
<!--{elseif $_v[jiaoyi]==1}-->
<!--{loop $fliter_price_ary $___k $___v}-->
<a class="weui-btn weui-btn_mini weui-btn_default <!--{if in_array($___k, $_filter[$_k])}-->tag-on<!--{/if}-->" data-one="1" data-title="{$_k}" data-value="$___k" href="javascript:;">{$___v}<!--{if $___k>1}-->{$_v[unitnew]}<!--{/if}--></a>
<!--{/loop}-->
<!--{/if}-->
                        </div>
                    </div>
                </div>
            </div>
            <!--{/loop}-->
            <div class="weui-flex">
                <input type="button" id="filtervar_clear" class="weui-btn weui-btn_default " value="{lang xigua_hb:czhi}" style="margin:15px;color: #666;" />
                <input type="button" id="filtervar_btn" class="weui-btn weui-btn_default  main_color" value="{lang xigua_hb:queding}" style="margin: 15px;" />
            </div>
        </div>
        <!--{/if}-->
    </div>
    <!--{if $subcats}-->
    <!--{if !$config[submode]}-->
    <div class="banner_fix cl cl1">
        <div class="banner">
            <nav class="weui-flex tag_list">
                <a href="$SCRITPTNAME?id=xigua_hb&ac=cat&$query" class="pstyle2 pstyle2on main_bg">{lang xigua_hb:quanbu}</a>
                <!--{loop $subcats $_kk $subcat}-->
                <a class="pstyle2" href="<!--{if !$subcat[cat_link]}-->$SCRITPTNAME?id=xigua_hb&ac=cat&$query&cat_id=$subcat[id]<!--{else}-->$subcat[cat_link]<!--{/if}-->">$subcat[name]</a>
                <!--{/loop}-->
            </nav>
        </div>
    </div>
    <!--{else}-->
    <!--{eval
    $numi1 = $config['numi1'];
    if(!$numi1):
        $numi1 = 5;
    endif;
    $query = preg_replace('/hyid\=\d+/ies', '', $query);
    if($config[submode]==3):
        $knum = $numi1*2;
    else:
        $knum = $numi1;
    endif;
    $subcats2 = array_values($subcats);
    }-->
    <nav class="nav-list cl swipe transparent mt3 border_top" style="padding-top:.2rem">
        <div class="swipe-wrap">
            <div>
                <ul class="cl">
                    <!--{loop $subcats2 $_kk $subcat}-->
                    <!--{if $_kk && $_kk%$knum==0}-->
                </ul>
            </div>
            <div>
                <ul class="cl">
                    <!--{/if}-->
                    <li class="cl">
                        <a href="<!--{if !$subcat[cat_link]}-->$SCRITPTNAME?id=xigua_hb&ac=cat&$query&cat_id=$subcat[id]<!--{else}-->$subcat[cat_link]<!--{/if}-->">
                            <span>
                                <img src="{echo $subcat['icon'] ?$subcat['icon'] : 'source/plugin/xigua_hb/static/img/icon.png'}"/>
                            </span>
                            <em class="m-piclist-title <!--{if $_GET[cat_id]==$subcat[id]}-->main_color<!--{/if}-->">{$subcat['name']}</em>
                        </a>
                    </li>
                    <!--{/loop}-->
                </ul>
            </div>
        </div>
    </nav>
    <!--{/if}-->
    <!--{elseif $tag_childs}-->
    <div class="banner_fix cl">
        <div class="banner">
            <nav class="weui-flex tag_list">
                <a href="$SCRITPTNAME?id=xigua_hb&ac=cat&$query" class="pstyle2 <!--{if !$_GET[tag]}-->pstyle2on main_bg<!--{/if}-->">{lang xigua_hb:buxian}</a>
                <!--{loop $tag_childs $_kk $tag_child}-->
                <!--{eval $tag_child = trim($tag_child);}-->
                <a href="$SCRITPTNAME?id=xigua_hb&ac=cat&$query&tag={echo urlencode($tag_child);}" class="pstyle2 <!--{if $tag_child==$_GET[tag]}-->pstyle2on main_bg<!--{/if}-->">$tag_child</a>
                <!--{/loop}-->
            </nav>
        </div>
    </div>
    <!--{/if}-->

    <!--{if $catinfo['adimage']}-->
    <div class="">
        <a href="{eval echo $catinfo['adlink'] ? $catinfo['adlink'] : $SCRITPTNAME.'?id=xigua_hb&ac=cat&cat_id='.$cat_id}"><img src="$catinfo['adimage']" class="block" /></a>
    </div>
    <!--{/if}-->
    <!--{if $catinfo['customad']}-->{echo htmlspecialchars_decode($catinfo['customad'])}<!--{/if}-->

    <!--{if $_GET[showmap] && is_file(DISCUZ_ROOT.'source/plugin/xigua_hb/template/touch/map.php')}-->
    <!--{eval $mapjs=1;}-->
    <!--{template xigua_hb:map}-->
    <!--{else}-->
    <div id="list" class="mod-post x-postlist pt0"></div>
    <!--{template xigua_hb:loading}-->
    <!--{/if}-->
</div>

<div id="srh_popup" class="weui-popup__container" style="z-index:1000">
    <div class="weui-popup__overlay"></div>
    <div class="weui-popup__modal">
        <div class="fixpopuper">
            <form action="$SCRITPTNAME" method="get" id="searchForm" target="_blank">
                <input type="hidden" name="id" value="xigua_hb">
                <input type="hidden" name="ac" value="cat">
                <!--                <input type="hidden" name="cat_id" value="$_GET[cat_id]">-->
                <input type="hidden" name="st" value="$_GET[st]">
                <input type="hidden" name="idu" value="$_GET[idu]">
                <div class="weui-cells weui-cells_form"  id="searchBar">

                    <div class="weui-cell weui-cell_vcode">
                        <div class="weui-cell__hd">
                            <label class="weui-label" style="width:auto"><i class="c9 iconfont icon-sousuo vm"></i></label>
                        </div>
                        <div class="weui-cell__bd">
                            <input type="search" class="weui-input" id="searchInput" placeholder="$config[sousuoinput]" required="required" name="keyword" <!--{if $keyword}-->data-value="$keyword"<!--{/if}-->>
                        </div>
                        <div class="weui-cell__ft">
                            <button class="weui-vcode-btn" type="submit">{lang xigua_hb:sousuo}</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="footer_fix"></div>
            <div class="bottom_fix"></div>
        </div>
        <div class="fix-bottom">
            <a class="weui-btn weui-btn_default close-popup" >{lang xigua_hb:quxiao}</a>
        </div>
    </div>
</div>
<!--{if $hide_nav}-->
<a href="javascript:$('#srh_popup').popup()" class="backtotop backtotop_show" style="bottom:2rem"> <span class="icon-vertical-align-top"><i class="iconfont icon-sousuo"></i></span></a>
<!--{/if}-->

<!--{if 0 && $catinfo[name]}-->
<a href="javascript:;" data-id="$cat_id" id="guanzhu" style="position:fixed;bottom:3rem;left:.75rem;background: rgba(0,0,0,.4);padding:0 .25rem;color:#fff;border-radius:.5rem;text-align:center;z-index:498;font-size:.7rem">{lang xigua_hb:xtxxw}</a>
<script>$(document).on('click','#guanzhu', function () {
        var that = $(this);
        $.prompt({
            title: '{lang xigua_hb:qrtx}{$catinfo[name]}',
            input: '{lang xigua_hb:qrtx1}{$catinfo[name]}{lang xigua_hb:qrtx2}',
            empty: false,
            onOK: function (input) {
                $.ajax({
                    type: 'post',
                    url: _APPNAME+'?id=xigua_hb&ac=myaddr&do=inx&inajax=1',
                    data: {'formhash':'{FORMHASH}', 'input':input,'cat_id' : that.data('id'),'catname':'$catinfo[name]'},
                    dataType: 'xml',
                    success: function (data) {
                        $.hideLoading();
                        if (null == data) {
                            tip_common('error|' + ERROR_TIP);
                            return false;
                        }
                        var s = data.lastChild.firstChild.nodeValue;
                        tip_common(s);
                    },
                    error: function () {$.hideLoading();formlocknew = 0;}
                });
            },
            onCancel: function () {}
        });
    });</script><!--{/if}-->
<script>
    var loadingurl = window.location.href+(window.location.href.indexOf('?') >= 0 ? '&' : '?')+'ac=list_item&inajax=1&pagesize=20&page=';
    scrollto = 1;isrelclick=1;
    function hb_getnext(id, name, datahref, datalink, pname){
        if(datalink){
            hb_jump(datalink);
            return false
        }
        $('.sub_check a').removeClass('checked').removeClass('main_color');
        $('.sub_check a').parent().removeClass('checked').removeClass('main_color');
        $('#sub_check'+id).addClass('checked').addClass('main_color');
        $.ajax({
            type: 'get',
            url: _APPNAME + '?id=xigua_hb&province='+encodeURIComponent(pname)+'&name='+encodeURIComponent(name)+'&ctid='+id+'&datahref='+encodeURIComponent(datahref)+'&inajax=1',
            dataType: 'xml',
            success: function (data) {
                if(null==data){ tip_common('error|'+ERROR_TIP); return false;}
                var s = data.lastChild.firstChild.nodeValue;
                $('.ajaxbox_cheker').html(s);
                if(isrelclick){
                    var retli = $('.ajaxbox_cheker').find('li');
                    console.log(retli.length);
                    if(retli.length==1){
                        retli.find('a').trigger('click');
                    }
                }
                isrelclick = 1;
            }
        });
    }
    $(document).on('click','.choose', function () {
        if($(this).data('link')){
            hb_jump($(this).data('link'));
            return false
        }
        var that = $(this), c_jmpurl = '';
        if(that.data('href')){ c_jmpurl = that.data('href'); }
        if(that.data('ctid')){ c_jmpurl = $('#sub_check'+that.data('ctid')).data('href'); }
        window.location.href= c_jmpurl;
    });
    $(document).on('click','.dist_check', function () {$('.dist_check').removeClass('checked').removeClass('main_color'); $(this).addClass('checked').addClass('main_color');});
    $(document).on('click','.dist_nav', function () {
        if($('.autotrigger').length>0){
            isrelclick = 0;
            $('.autotrigger').find('a').trigger('click');
        }
    });
    $(document).on('click','.first_check', function () {
        if($(this).data('link')){
            hb_jump($(this).data('link'));
            return false
        }
        $('.ajaxbox_cheker').html('');
    });
    $(document).on('click','.gray-tags a', function () {
        var that = $(this), lv=that.data('lv'), dt=that.data('title'), dv=that.data('value');
        if(lv){
            $('#cat_tit_in_'+dt).append('<a style="margin-right:5px" class="tag-on" data-title="'+dt+'" data-value="'+dv+'">'+that.html()+'</a>')
            $('#cat_tit_'+dt).show();

            that.hide();
            that.siblings().hide();
            that.parent().find('a').each(function () {
                var tt = $(this);
                var ttlv = tt.data('lv')+'';
                if(ttlv.indexOf(lv+'.')===0){
                    if(that.hasClass('level_1_'+dt) && tt.hasClass('level_2_'+dt)){
                        tt.show();
                    }
                    if(that.hasClass('level_2_'+dt) && tt.hasClass('level_3_'+dt)){
                        tt.show();
                    }
                }
            });
        }else{
            // that.siblings().removeClass('tag-on');
            if(that.data('one')){
                that.siblings().removeClass('tag-on');
                that.toggleClass('tag-on');
            }else{
                that.toggleClass('tag-on');
            }
        }
    });
    $(document).on('click','.cat_m', function () {
        var that = $(this), dt=that.data('title');
        $('#cat_tit_in_'+dt).html('');
        $('#cat_tit_'+dt).hide();
        $('.level_1_'+dt).show();
        $('.level_2_'+dt).hide();
        $('.level_3_'+dt).hide();
    });
    $(document).on('click','#filtervar_btn', function () {
        var res = '';
        $('a.tag-on').each(function () {
            var that = $(this);
            res+= that.data('title')+'_'+that.data('value')+'__';
        });
        hb_jump('$SCRITPTNAME?id=xigua_hb&ac=cat&cat_id=$cat_id&province={$provinceen}&city={$cityen}&dist={$disten}&orderby=$orderby&keyword=$keyworden&lat=$lat&lng=$lng&filter='+encodeURIComponent(res));
    });
    $(document).on('click','#filtervar_clear', function () {
        $('.gray-tags a').removeClass('tag-on');
        $('.cat_m').trigger('click');
        $('#filtervar_btn').trigger('click');
    });
</script>
<link rel="stylesheet" href="source/plugin/xigua_hb/static/tgb-r04/discovery-light-grid-r04.css?v=20260726-r04-2">
<!--{eval $tabbar=1;}-->
<!--{template xigua_hb:common_footer}-->
<script src="source/plugin/xigua_hb/static/tgb-r04/discovery-r04.js?v=20260726-r04-2"></script>
<!--{if $_G['cache']['plugin']['xigua_hs'] && $config[showfj]}-->
<script>var HB_INWECHAT = '{HB_INWECHAT}',mkey = "{$_G['cache']['plugin']['xigua_hs'][mkey]}",HS_MULTIUPLOAD = "{$_G['cache']['plugin']['xigua_hb'][multiupload]}";</script>
<script src="source/plugin/xigua_hs/static/hs.js?{VERHASH}"></script>
<script type="text/javascript" src="https://mapapi.qq.com/web/mapComponents/geoLocation/v/geolocation.min.js?{VERHASH}"></script>
<script>$(document).on('click','#near_xinxi', function () {var that = $(this);var href= that.data('href');hs_getlocation(function (position) {
        var lat=(position.latitude||position.lat), lng=(position.longitude||position.lng);window.location.href= href+ '&lat='+lat+'&lng='+lng;});});</script>
<!--{/if}-->
<script>if($('.sub_cheker').length===$('.sub_cheker.none').length){$('.sub_cheker:first-child').removeClass('none');}
    if($('.banner').length>0){$('.banner')[0].scrollLeft = $('.banner').find('.main_bg').offset().left-100;}
    <!--{if $_GET['srch']}-->$('#srh_popup').popup();<!--{/if}--></script>
<!--{if $mapjs}--><!--{template xigua_hb:mapjs}--><!--{/if}-->
<!--{if $_GET[near_cat]}--><script>setTimeout(function () {$('#near_xinxi').trigger('click');if(typeof wx !=='undefined') {
        wx.ready(function () {
            $('#near_xinxi').trigger('click');
        });
    }}, 200);</script><!--{/if}-->
