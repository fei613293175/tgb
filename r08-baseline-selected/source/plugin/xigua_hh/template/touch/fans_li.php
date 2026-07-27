<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958'); ?>
<!--{eval include_once DISCUZ_ROOT.'source/plugin/xigua_hh/include/c_fansli.php';}-->
<!--{loop $list $v}-->
<!--{eval $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[fansuid]);}-->
<!--{eval  $xiaomy_certification = C::t('#xiaomy_certification#xiaomy_certification')->fetch_first_field_data("rescodebdres","where rescodebdres =1 AND uid=".$v['fansuid']." order by dateline desc"); }-->
<div data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" class="weui-cell weui-cell_access <!--{if $_GET['do']!='up'}-->fans_li
<!--{else}-->fans_li2<!--{/if}-->" style="background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255,190,90,0.35); margin:8px auto; border-radius:22px; padding:18px 15px; box-shadow: 0 20px 45px rgba(255,140,30,0.10), 0 4px 12px rgba(0,0,0,0.03); transition:all 0.25s ease; width: calc(100% - 20px); max-width: 600px; position:relative; overflow:hidden; margin-bottom:15px;" id="li_{$v[fansuid]}">
  
  <!-- 背景装饰元素 - 红橙光晕 -->
  <div style="position:absolute; top:-40px; right:-40px; width:100px; height:100px; border-radius:50%; background:radial-gradient(circle, rgba(255,123,0,0.08) 0%, rgba(255,123,0,0) 70%); z-index:0;"></div>
  
    <div class="weui-cell__hd opcls" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}" style="position: relative; margin-right: 18px; z-index:1;">
        <img src="{avatar($v[fansuid], 'small', true)}" style="width:60px; height:60px; display:block; border-radius:14px; border:2px solid rgba(255,123,0,0.4); box-shadow:0 4px 12px rgba(0,0,0,0.08); background: rgba(255,245,235,0.7);"  />
    </div>
    <div class="weui-cell__bd" style="z-index:1; position:relative; color:#3d2b1a;">
        
        <!--{eval}-->
        
            $showhhmename ="";
        
          $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[fans][uid]);
            
            if($hhme['status'] == 1){
                $showhhmename = $hhme[joininfo][name];
            }else{

                $oldback  = $hhme['oldback'];
                $oldjoin = unserialize($oldback);
                $oldjoin = unserialize($oldjoin['joininfo']);
                $showhhmename = $oldjoin['name'];
            }
            
            
            
           if($v['fans']['uid']){
           
            $isjh  = 5;
           
           if($showhhmename == "MK会员"){
                     $isjh  =  1;
           
           }else{
           
           
                        $isjh  =  C::t('#tb_jjd#tb_jjd_jh_log')->fetch_first_field_data("id,uid","where touid=".$v['fans']['uid']);}
                        
                        if($isjh['uid'] == $_G['uid']){
                           $isjh  =  2;
                        
                        }elseif($isjh){
                            $isjh  =  3;
                        }
                        
                }
          
    
  $sjtlevel = DB::fetch_first("SELECT MAX(tlevel) AS sjtlevel 
    FROM %t t 
    LEFT JOIN %t tl ON t.id = tl.taskid  
    WHERE tl.uid = %d 
      AND DATE(FROM_UNIXTIME(tl.dateline)) = CURDATE() 
    ORDER BY tl.id DESC", 
    array('tb_jjd_task', 'tb_jjd_user_log', $v['fans']['uid']));
$sjtlevel = $sjtlevel['sjtlevel'] ? $sjtlevel['sjtlevel'] : 0;
          
        <!--{/eval}-->
        
        <div style="margin-bottom: 10px; display: flex; align-items: center; flex-wrap: wrap;" >
          <font class="opcls" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}" style="font-weight:600; font-size:17px; color:#3d2b1a; margin-right:8px; line-height:1.4;">{$v[fans][username]}</font>
          
          <!-- 实名认证徽章 - 红橙 -->
          <!--{if $xiaomy_certification['rescodebdres']==1}-->
          <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background: linear-gradient(135deg, #ff7b00, #e63946); margin-right:6px; vertical-align:middle; box-shadow:0 2px 8px rgba(255,123,0,0.35); position:relative;">
            <span style="color:#fff; font-size:11px; font-weight:bold;">✓</span>
          </span>
          <!--{/if}-->
          
          <!-- 会员等级徽章 - 红橙渐变 -->
          {if $hhme[joininfo][name] == "MK会员"}
          <span style="display:inline-flex; align-items:center; justify-content:center; padding:3px 10px; border-radius:16px; background: linear-gradient(135deg, #ff7b00, #e63946); margin-right:6px; vertical-align:middle; box-shadow:0 2px 8px rgba(255,123,0,0.35);">
            <span style="color:#fff; font-size:11px; font-weight:bold; white-space:nowrap;">签米会员</span>
          </span>
          {elseif $hhme[joininfo][name] == "星益会员"}
          <span style="display:inline-flex; align-items:center; justify-content:center; padding:3px 10px; border-radius:16px; background: linear-gradient(135deg, #ff7b00, #e63946); margin-right:6px; vertical-align:middle; box-shadow:0 2px 8px rgba(255,123,0,0.35);">
            <span style="color:#fff; font-size:11px; font-weight:bold; white-space:nowrap;">星益</span>
          </span>
          {elseif $hhme[joininfo][name] == "商业会员"}
          <span style="display:inline-flex; align-items:center; justify-content:center; padding:3px 10px; border-radius:16px; background: linear-gradient(135deg, #ff7b00, #e63946); margin-right:6px; vertical-align:middle; box-shadow:0 2px 8px rgba(255,123,0,0.35);">
            <span style="color:#fff; font-size:11px; font-weight:bold; white-space:nowrap;">商业会员</span>
          </span>
          {/if}
        </div>
        
        <div class="opcls" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}" style="font-weight:500; font-size:13px; color:#8b6f5c; margin-bottom:10px; display:flex; align-items:center; flex-wrap:wrap;">
          {eval $userlianghao = userlianghao($v['fans']['uid']);}
          {if $userlianghao}
          <span class="lianghao-text-1" style="font-size:13px; color:#d35400; font-weight:600; margin-right:8px; background:rgba(255,123,0,0.08); padding:2px 10px; border-radius:12px; border:1px solid rgba(255,123,0,0.2);">靓号: $userlianghao</span>
          {else}
          <span style="color:#8b6f5c; font-size:13px; background:rgba(255,245,235,0.7); padding:2px 12px; border-radius:12px; margin-right:8px; border:1px solid rgba(255,200,120,0.35);">UID: {$v[fans][uid]}</span>
          {/if}
        </div>
        
     
        
  
    
        <!-- 用户日期独立显示 -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; margin-top:-10px;border-top:1px solid rgba(255,200,120,0.35);">
          <div style="flex:1;"></div>
          <div style="color:#8b6f5c; font-size:12px; font-weight:400; background:rgba(255,245,235,0.7); padding:4px 10px; border-radius:12px; border:1px solid rgba(255,200,120,0.35);">
            绑定日期：$v[crts_u]
          </div>
        </div>
        
    </div>
</div>
<!--{/loop}-->