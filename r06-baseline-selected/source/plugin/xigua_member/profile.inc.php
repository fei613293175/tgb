<?php
/**
 * Created by PhpStorm.
 * User: yzg
 * Date: 2014/11/24
 * Time: 19:35
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_NOTICE);
loaducenter();

include_once libfile('function/profile');
include_once DISCUZ_ROOT.'./source/plugin/wechat/wechat.lib.class.php';
include_once DISCUZ_ROOT.'./source/plugin/xigua_member/function.php';

loadcache('plugin');
$config = $_G['cache']['plugin']['xigua_member'];
$wechat = unserialize($_G['setting']['mobilewechat']);
$siteid = $wechat['wsq_siteid'];
$title = xul('title', false);
dsetcookie('mobile', '', -1);

if($_G['uid'] <1){
    include dirname(__FILE__) .'/login.php';
    exit;
}

class uploadUcAvatar1 {

    public static function upload($uid, $localFile) {

        global $_G;
        if(!$uid || !$localFile) {
            return false;
        }
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $file1 = DISCUZ_ROOT.'uc_server/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_avatar_small.jpg';
        $file2 = DISCUZ_ROOT.'uc_server/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_avatar_middle.jpg';
        $file3 = DISCUZ_ROOT.'uc_server/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).'_avatar_big.jpg';
        dmkdir(DISCUZ_ROOT.'uc_server/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/', 0777);
        file_put_contents($file1, file_get_contents($localFile));
        file_put_contents($file2, file_get_contents($localFile));
        file_put_contents($file3, file_get_contents($localFile));
        return true;

        list($width, $height, $type, $attr) = getimagesize($localFile);
        if(!$width) {
            return false;
        }

        if($width < 10 || $height < 10 || $type == 4) {
            return false;
        }

        $imageType = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
        $fileType = $imgType[$type];
        if(!$fileType) {
            $fileType = '.jpg';
        }
        $avatarPath = $_G['setting']['attachdir'];
        $tmpAvatar = $avatarPath.'./temp/upload'.$uid.$fileType;
        file_exists($tmpAvatar) && @unlink($tmpAvatar);
        file_put_contents($tmpAvatar, file_get_contents($localFile));

        if(!is_file($tmpAvatar)) {
            return false;
        }

        $tmpAvatarBig = './temp/upload'.$uid.'big'.$fileType;
        $tmpAvatarMiddle = './temp/upload'.$uid.'middle'.$fileType;
        $tmpAvatarSmall = './temp/upload'.$uid.'small'.$fileType;

        $image = new image;
        if($image->Thumb($tmpAvatar, $tmpAvatarBig, 200, 250, 1) <= 0) {
            return false;
        }
        if($image->Thumb($tmpAvatar, $tmpAvatarMiddle, 120, 120, 1) <= 0) {
            return false;
        }
        if($image->Thumb($tmpAvatar, $tmpAvatarSmall, 48, 48, 2) <= 0) {
            return false;
        }

        $tmpAvatarBig = $avatarPath.$tmpAvatarBig;
        $tmpAvatarMiddle = $avatarPath.$tmpAvatarMiddle;
        $tmpAvatarSmall = $avatarPath.$tmpAvatarSmall;

        $avatar1 = self::byte2hex(file_get_contents($tmpAvatarBig));
        $avatar2 = self::byte2hex(file_get_contents($tmpAvatarMiddle));
        $avatar3 = self::byte2hex(file_get_contents($tmpAvatarSmall));

        $extra = '&avatar1='.$avatar1.'&avatar2='.$avatar2.'&avatar3='.$avatar3;
        $result = self::uc_api_post_ex('user', 'rectavatar', array('uid' => $uid), $extra);

        @unlink($tmpAvatar);
        @unlink($tmpAvatarBig);
        @unlink($tmpAvatarMiddle);
        @unlink($tmpAvatarSmall);

        return true;
    }

    public static function byte2hex($string) {
        $buffer = '';
        $value = unpack('H*', $string);
        $value = str_split($value[1], 2);
        $b = '';
        foreach($value as $k => $v) {
            $b .= strtoupper($v);
        }

        return $b;
    }

    public static function uc_api_post_ex($module, $action, $arg = array(), $extra = '') {
        $s = $sep = '';
        foreach($arg as $k => $v) {
            $k = urlencode($k);
            if(is_array($v)) {
                $s2 = $sep2 = '';
                foreach($v as $k2 => $v2) {
                    $k2 = urlencode($k2);
                    $s2 .= "$sep2{$k}[$k2]=".urlencode(uc_stripslashes($v2));
                    $sep2 = '&';
                }
                $s .= $sep.$s2;
            } else {
                $s .= "$sep$k=".urlencode(uc_stripslashes($v));
            }
            $sep = '&';
        }
        $postdata = uc_api_requestdata($module, $action, $s, $extra);
        return uc_fopen2(UC_API.'/index.php', 500000, $postdata, '', TRUE, UC_IP, 20);
    }
}
$space = getuserbyuid($_G['uid']);
space_merge($space, 'field_home');
space_merge($space, 'profile');

$rms = XG_rule();
$rm  = $rms['allow'];
$tip = $rms['tip'];

if(submitcheck('avatar')){
    $outputfile = DISCUZ_ROOT . './source/plugin/xigua_member/'.$_G['uid'].'.jpg';
    $base64=base64_decode($_GET['avatar']);

    $ifp = fopen( $outputfile, "wb" );
    fwrite( $ifp, $base64 );
    fclose( $ifp );

    if(uploadUcAvatar1::upload($_G['uid'], $outputfile)){
        //完成任务
        if(file_exists(DISCUZ_ROOT . './source/plugin/tb_task/tb_task_hook.php')) {
            require DISCUZ_ROOT . './source/plugin/tb_task/tb_task_hook.php';
            check_task("new",1);
        }
        echo '1';
    }
    @unlink($outputfile);
    exit;
}

function chkbm($string){
    $bm = array('ASCII', 'GBK', 'UTF-8', 'BIG5');
    foreach($bm as $c){
        if( $string === iconv('UTF-8', $c.'//IGNORE', iconv($c, 'UTF-8//IGNORE', $string))){
            return $c;
        }
    }
    return null;
}

function ___aiconv2UTF8($string, $realcharset){
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[ $key ] = ___aiconv2UTF8($val, $realcharset);
        }
    } else {
        $string = diconv($string, $realcharset, CHARSET);
    }
    return $string;
}
$charset = strtoupper(CHARSET);

if(submitcheck('formsubmit')){
    unset($_GET['undefined']);
    if(empty($_G['cache']['profilesetting'])) {
        loadcache('profilesetting');
    }

    $realcharset = chkbm(serialize($_GET));
    if($realcharset && $realcharset != $charset && $charset != 'UTF-8'){
        $_GET = ___aiconv2UTF8($_GET, $realcharset);
    }

    $forum = $setarr = $verifyarr = $errorarr = array();
    $censor = discuz_censor::instance();

    if(isset($_GET['resideprovince'])) {
        $initcity = array('resideprovince', 'residecity', 'residedist', 'residecommunity');
        foreach($initcity as $key) {
            $_GET[''.$key] = $_GET[$key] = !empty($_GET[$key]) ? $_GET[$key] : '';
        }
    }
    foreach($_GET as $key => $value) {
        $field = $_G['cache']['profilesetting'][$key];
        if(in_array($field['formtype'], array('text', 'textarea')))
        {
            $censor->check($value);
            if($censor->modbanned() || $censor->modmoderated())
            {
                XG_showmessage($key, lang('spacecp', 'profile_censor'));
            }
        }
        if($field && !$field['available']) {
            continue;
        }
        if(empty($field)) {
            continue;
        } elseif(profile_check($key, $value, $space)) {
            $setarr[$key] = dhtmlspecialchars(trim($value));
        }

        if($field['formtype'] == 'file') {
            unset($setarr[$key]);
        }
        if(isset($setarr[$key]) && $_G['cache']['profilesetting'][$key]['needverify']) {
            unset($setarr[$key]);
        }
    }
    if($newusername = trim($_GET['newusername'])){
        unset($_GET['newusername']);
        if($newusername != $_G['username']){
            if(!$rm){
                XG_showmessage('cant_rename');
            }
            $censor->check($newusername);
            if($censor->modbanned() || $censor->modmoderated())
            {
                XG_showmessage('username', lang('spacecp', 'profile_censor'));
            }
            $ucresult = XG_renameuser($_G['uid'], $newusername);
        }
    }

    if(isset($_GET['birthmonth']) && ($space['birthmonth'] != $_GET['birthmonth'] || $space['birthday'] != $_GET['birthday'])) {
        $setarr['constellation'] = get_constellation($_GET['birthmonth'], $_GET['birthday']);
    }
    if(isset($_GET['birthyear']) && $space['birthyear'] != $_GET['birthyear']) {
        $setarr['zodiac'] = get_zodiac($_GET['birthyear']);
    }
    if($setarr) {
        C::t('common_member_profile')->update($_G['uid'], $setarr);
    }
    if($password = trim($_GET['password'])){
        if($password != addslashes($password)) {
            XG_showmessage('profile_passwd_illegal');
        }
        if(strlen($password)<=3){
            XG_showmessage('profile_passwd_tooshort');
        }
        loaducenter();
        $ucresult = uc_user_edit(addslashes($_G['username']), '', $password, '', 1);
        if($ucresult<0){
            XG_showmessage('profile_passwd_failed');
        }
    }

    manyoulog('user', $_G['uid'], 'update');
    $operation = 'base';
    include_once libfile('function/feed');
    feed_add('profile', 'feed_profile_update_'.$operation, array('hash_data'=>'profile'));
    countprofileprogress();
    XG_showmessage('succeed');
    exit;
}

$avatar = avatar($_G['uid'], 'middle', true, 0 , 1);
$avatar = $avatar.(strpos($avatar, '?')===false ? '?':'&'). '_='.time();
$birthcityhtml = showdistrict(
    array(0,0,0),
    array('resideprovince', 'residecity', 'residedist'),
    'residecitybox', 3, 'reside');
$html = XG_birthhtml($space['birthyear'], $space['birthmonth'], $space['birthday']);

$style = '';
if($config['topbar']){
    $style = 'background-image:url('.$config['topbar'].');';
}
if($config['topbar_height']){
    $config['topbar_height'] = $config['topbar_height']<100 ? 100: $config['topbar_height'];
    $style .= 'height:'.intval($config['topbar_height']).'px;';
}
$wechat = unserialize($_G['setting']['mobilewechat']);
$return =  'javascript:window.history.go(-1);';
$username = dhtmlspecialchars($space['username']);
dsetcookie('mobile', '', -1);
if(stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')||stripos($_SERVER['HTTP_USER_AGENT'], 'ipad')||stripos($_SERVER['HTTP_USER_AGENT'], 'ios')){
    $config['charset'] = 'utf-8';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta content="telephone=no" name="format-detection" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <title><?php echo $title;?></title>
    <link href="source/plugin/xigua_member/images/xigua.css?201503013" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="source/plugin/xigua_member/images/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="source/plugin/xigua_member/images/jquery.form.js"></script>
</head>
<body>
<form method="post" action="" id="saveprofile" onsubmit="return false;" <?php if($config['charset']){echo 'accept-charset="'.$config['charset'].'"';}?>>
<input name="formhash" type="hidden" id="formhash" value="<?php echo FORMHASH?>">
<input name="formsubmit" type="hidden" value="<?php echo FORMHASH?>">
<div class="warp">
    <div class="header blur" style="<?php echo $style?>;">
        <?php if($config['showback']){?>
        <a class="home-return" style="margin-top:30px;" target="_top" href="<?php echo $return?>"><i class="fa fa-arrowleft fa-ad"></i><?php xul('return')?></a>
        <?php }?>
<!--        <a class="home-return" href="home.php?mod=spacecp&amp;ac=profile&amp;op=base&amp;mobile=2" style="right: 0;left: auto;">��������</a>-->
        <div class="avatar clear" style="margin-top:40px;">
            <div class="clear prelouter">
            <div class="clear prel">
                <img class="avatar_big" src="<?php echo $avatar?>" onerror="this.onerror=null;this.src='source/plugin/xigua_member/images/noavatar_middle.gif'" />
                <input type="file" name="avatar" class="upavatar" onchange="return uploadavatar(this);" />
            </div>
                <i class="fa fa-photo changeavatar"></i>
            </div>
        </div>
        <h2 class="name"><?php echo $username?></h2>
    </div>
    <div class="divbtn" style="margin:15px;border-radius:10px;">
        <table class="sec" style="border-radius:10px;">
        <tr style="margin-bottom:15px;">
            <td style="font-weight:550;font-size:16px;"><?php xul('username')?>：</td>
            <td>
                <p>
                    <span id="usernametext"><?php echo $username?></span>
                    <?php if($rm){?><input type="hidden" id="newusername"/><i class="fa fa-post" id="usernameedit"></i><?php }?>
                </p>
            </td>
        </tr>
        <tr>
            <td style="font-weight:550;font-size:16px;"><?php xul('password')?>：</td>
            <td>
                <p>
                    <input type="password" style="border-radius:50px;" autocomplete="off" class="input" name="password" value="" placeholder="<?php xul('password_tip')?>" />
                </p>
            </td>
        </tr>

    </table>
    </div>
    <div class="divbtn mt10">
        <input class="btn1" style="border-radius:50px;padding:10px 10px;width:90%;background-color:#fff;" type="submit" name="dosubmit" id="dosubmit" value="<?php xul('save')?>" />
    </div>
</div>
</form>
<div class="share_ok"><table><tr><td id="share_ok"></td></tr></table></div>
<?php if($rm){?>
<div id="xg_box">
<div id="xg_box_inner">
    <div id="xg_box_title"><?php echo $tip?></div>
    <div id="xg_box_content" class="clear">
        <input id="newhold" style="width:100%" placeholder="<?php xul('username')?>" type="text" class="input" value="<?php echo $username?>" />
    </div>
    <div id="xg_box_button" class="ajw">
        <div class="aji tc"><a class="btn2" id="cancela"><?php xul('cancel')?></a></div>
        <div class="aji tc"><a class="btn_default" id="deal"><?php xul('deal')?></a></div>
    </div>
</div>
</div>
<?php }?>
<div id="iread" style="display:none"></div>
<script type="text/javascript" src="source/plugin/xigua_member/images/jquery.min.js"></script>
<script type="text/javascript">
var loading = 0;
var cookiepre = '<?php echo $_G['config']['cookie']['cookiepre']?>',
    cookiedomain = '<?php echo $_G['config']['cookie']['cookiedomain']?>',
    cookiepath = '<?php echo $_G['config']['cookie']['cookiepath']?>';
function setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
    if(cookieValue == '' || seconds < 0) {
        cookieValue = '';
        seconds = -2592000;
    }
    if(seconds) {
        var expires = new Date();
        expires.setTime(expires.getTime() + seconds * 1000);
    }
    domain = !domain ? cookiedomain : domain;
    path = !path ? cookiepath : path;
    document.cookie = encodeURIComponent(cookiepre + cookieName) + '=' + encodeURIComponent(cookieValue)
        + (expires ? '; expires=' + expires.toGMTString() : '')
        + (path ? '; path=' + path : '/')
        + (domain ? '; domain=' + domain : '')
        + (secure ? '; secure' : '');
}
function getcookie(name, nounescape) {
    name = cookiepre + name;
    var cookie_start = document.cookie.indexOf(name);
    var cookie_end = document.cookie.indexOf(";", cookie_start);
    if(cookie_start == -1) {
        return '';
    } else {
        var v = document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length));
        return !nounescape ? decodeURIComponent(v) : v;
    }
}
//WSQ.hideBtmBar();
//WSQ.initPlugin({name:'<?php //echo $title?>//'});
$(function() {
    $(".btn1").on("touchstart", function () {
        $(this).addClass('btn1_active');
    }).on("touchend", function () {
        $(this).removeClass('btn1_active');
    });
    $('.gender').click(function(){
        $('.gender').removeClass('gender_current');
        $(this).addClass('gender_current');
        if($(this).attr('id') == 'man'){
            $('#gender').val('1');
            $('.gender1').show();$('.gender2').hide();
        }else if($(this).attr('id') == 'woman'){
            $('#gender').val('2');
            $('.gender1').hide();$('.gender2').show();
        }else{
            $('#gender').val('0');
            $('.gender1').hide();$('.gender2').hide();
        }
    });
    showdistrict('residecitybox', ['resideprovince', 'residecity', 'residedist'], 3, '', 'reside');

    $('form#saveprofile').on('submit', function(){
        $('#newusername').removeAttr('name');
        $('#newusername').removeAttr('value');
        saveprofilef();
    });
<?php if($rm){?>
    var ne =$('#newusername');
    $('#usernameedit').on('click',function(){
        var h = Math.max($(document).height(), $(window).height());
        $('#xg_box').css('height',h+'px').fadeIn();
        $('#xg_box_inner').css('opacity',0).show().addClass('fadeInTop');
    });
    $('#cancela').on('click', function(){
        ne.removeAttr('name');
        ne.removeAttr('value');
        $('#usernametext').text('<?php echo $username?>');
        $('#newhold').val('<?php echo $username?>');
        close_box();
    });
    $('#deal').on('click', function(){
        var lastusername = $.trim($('#newhold').val());
        if(lastusername != '<?php echo $username?>'){
            ne.attr('name', 'newusername');
            ne.attr('value', lastusername);

            var unlen = lastusername.replace(/[^\x00-\xff]/g, "**").length;
            if(unlen<3){
                show_tip('<?php xul('profile_username_tooshort')?>', true);
                return;
            }
            if(unlen>15){
                show_tip('<?php xul('profile_username_toolong')?>', true);
                return;
            }
            if(lastusername.match(/(\{|\}|\[|\]|\+|\=|,|\/|-|`|%|\.|\*|\?|>|'|<|;|\\|")/ig)){
                show_tip('<?php xul('profile_username_illegal')?>', true);
                return;
            }
            saveprofilef();
        }else{
            close_box();
        }
    });
<?php }?>
});

function close_box(){
    $('#xg_box_inner').addClass('fadeInBottom');
    $('#xg_box').fadeOut();
    setTimeout(function(){
        $('#xg_box_inner').removeClass('fadeInBottom').removeClass('fadeInTop');
    }, 350);
}

function show_tip(html, autohide, timeout){
    var tip = $('#share_ok');
    tip.html(html);
    $('.share_ok').show();
    if(autohide){
        setTimeout(function(){
            $('.share_ok').fadeOut('normal');
            loading = 0;
        }, (timeout ? timeout :1500));
    }
}
function saveprofilef(){
    if(loading){return;}
    loading = 1;
    show_tip('<a class="loading"></a>', false);
    $('#dosubmit').attr('disabled', 1);
    $.ajax({
        type:'POST',
        url : 'plugin.php?id=xigua_member:profile',
        data:$('#saveprofile').serialize() ,
        success:function(data){
            if(data == '<?php xul('succeed')?>'){
                setTimeout(function(){
                    window.location.href = window.location.href;
                },1000);
            }
            show_tip(data, true);
            $('#dosubmit').removeAttr('disabled');
        }
    });

//    $('#saveprofile').ajaxSubmit({
//        type:'post',
//        success: function (data) {
//            if(data == '<?php //xul('succeed')?>//'){
//                setTimeout(function(){
//                    window.location.href = window.location.href;
//                },1000);
//            }
//            show_tip(data, true);
//            $('#dosubmit').removeAttr('disabled');
//        }
//    });
//    submitit();
}
function _$(id) {
    return document.getElementById(id);
}

function submitit(){

    var ajaxframeid = 'ajaxframe';
    var ajaxframe = document.getElementById(ajaxframeid);
    if(ajaxframe == null) {
        ajaxframe = document.createElement("iframe");
        ajaxframe.name = ajaxframeid;
        ajaxframe.id = ajaxframeid;
        ajaxframe.style.display = 'none';
        $("body").append(ajaxframe);
    }
    document.getElementById('saveprofile').target = ajaxframeid;

    ajaxpostHandle = [ajaxframeid];
    _attachEvent(ajaxframe, 'load', ajaxpost_load);
    if($('#saveprofile').attr('accept-charset')){
        document.charset=$('#saveprofile').attr('accept-charset');
    }
    document.getElementById('saveprofile').submit();
}

function trim(string){
    return string.replace(/(^\s*)|(\s*$)/g, "");
}

function _attachEvent(obj, evt, func, eventobj) {
    eventobj = !eventobj ? obj : eventobj;
    if(obj.addEventListener) {
        obj.addEventListener(evt, func, false);
    } else if(eventobj.attachEvent) {
        obj.attachEvent('on' + evt, func);
    }
}
function ajaxpost_load() {
    var data = $(document.getElementById('ajaxframe').contentWindow.document.documentElement).text();

    if(data == '<?php xul('succeed')?>'){
        setTimeout(function(){
            window.location.href = window.location.href;
        },1000);
    }
    show_tip(data, true);
    $('#dosubmit').removeAttr('disabled');
}

function showbirthday(){
    var el = _$('birthday');
    var birthday = el.value;
    el.length=0;
    el.options.add(new Option('<?php echo lang('space', 'day')?>', ''));
    for(var i=0;i<28;i++){
        el.options.add(new Option(i+1, i+1));
    }
    if(_$('birthmonth').value!="2"){
        el.options.add(new Option(29, 29));
        el.options.add(new Option(30, 30));
        switch(_$('birthmonth').value){
            case "1":
            case "3":
            case "5":
            case "7":
            case "8":
            case "10":
            case "12":{
                el.options.add(new Option(31, 31));
            }
        }
    } else if(_$('birthyear').value!="") {
        var nbirthyear=_$('birthyear').value;
        if(nbirthyear%400==0 || (nbirthyear%4==0 && nbirthyear%100!=0)) el.options.add(new Option(29, 29));
    }
    el.value = birthday;
}
function showdistrict(container, elems, totallevel, changelevel, containertype) {
    var getdid = function(elem) {
        var op = elem.options[elem.selectedIndex];
        return op['did'] || op.getAttribute('did') || '0';
    };
    var pid = changelevel >= 1 && elems[0] && _$(elems[0]) ? getdid(_$(elems[0])) : 0;
    var cid = changelevel >= 2 && elems[1] && _$(elems[1]) ? getdid(_$(elems[1])) : 0;
    var did = changelevel >= 3 && elems[2] && _$(elems[2]) ? getdid(_$(elems[2])) : 0;
    var coid = changelevel >= 4 && elems[3] && _$(elems[3]) ? getdid(_$(elems[3])) : 0;
    var url = "<?php echo $_G['siteurl']?>home.php?&mod=misc&ac=ajax&op=district&container="+container+"&containertype="+containertype
        +"&province="+elems[0]+"&city="+elems[1]+"&district="+elems[2]+"&community="+elems[3]
        +"&pid="+pid + "&cid="+cid+"&did="+did+"&coid="+coid+'&level='+totallevel+'&handlekey='+container+'&inajax=1'+(!changelevel ? '&showdefault=1' : '');
    $.ajax({
        type: "GET",
        url: url,
        cache:false,
        dataType:'xml',
        success: function(data){
            setcookie('mobile', '', -1);
            var content = $(data).find("root").text();
            content = content.replace(/&nbsp;&nbsp;/ig, " ");
            $('#'+container).html(content);
        }
    });
}
</script>

<script src="source/plugin/xigua_member/images/js/lib/mobileFix.mini.js?v=ad62f13"></script>
<script src="source/plugin/xigua_member/images/js/lib/exif.js?v=dd609b9"></script>
<script src="source/plugin/xigua_member/images/js/lrz.js?v=3d33fcf"></script>
<script type="text/javascript">
 function uploadavatar (obj) {
     lrz(obj.files[0], {width:200,height:200, quality:10}, function (results) {
         var clearBase64 =  results.base64.substr(results.base64.indexOf(',') + 1);
         $.ajax({
             type:'POST',
             url : '<?php echo $_G['siteurl'].'plugin.php?id=xigua_member%3Aprofile'?>',
             data: {
                 formhash:'<?php echo FORMHASH?>',
                 avatar:clearBase64
             },
             success:function(data){
                 if(data == 1){
                     alert('<?php xul('avatar_success') ?>');
                     window.location.href =window.location.href;
                 }
             }
         });
     });
     return false;
 }
</script>
</body>
</html>