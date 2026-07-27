<?php exit('Author: https://addon.dismall.com/?@xigua 西瓜先生 客服QQ 1628585958'); ?>
<!--{eval include_once DISCUZ_ROOT.'source/plugin/xigua_hh/include/c_fansli.php';}-->
<!--{loop $list $v}-->
<!--{eval $hhme = C::t('#xigua_hh#xigua_hh_member')->fetch_prepare($v[fansuid]);}-->
<!--{eval  $xiaomy_certification = C::t('#xiaomy_certification#xiaomy_certification')->fetch_first_field_data("rescodebdres","where rescodebdres =1 AND uid=".$v['fansuid']." order by dateline desc"); }-->
<div data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" class="weui-cell weui-cell_access <!--{if $_GET['do']!='up'}-->fans_li
<!--{else}-->fans_li2<!--{/if}-->" id="li_{$v[fansuid]}">
  
    <div class="weui-cell__hd opcls team-avatar" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}">
        <img src="{avatar($v[fansuid], 'small', true)}" />
    </div>
    <div class="weui-cell__bd team-member-body">
        
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
        
        <div class="team-member-title">
          <font class="opcls team-member-name" data-href="$SCRITPTNAME?id=xigua_hb&ac=member&uid=$v[fansuid]" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}">{$v[fans][username]}</font>
          
          <!-- 实名认证徽章 - 红橙 -->
          <!--{if $xiaomy_certification['rescodebdres']==1}-->
          <span class="team-verified">
            <span>✓</span>
          </span>
          <!--{/if}-->
          
          <!-- 会员等级徽章 - 红橙渐变 -->
          {if $hhme[joininfo][name] == "MK会员"}
          <span class="team-level team-level-primary">
            <span>推广宝会员</span>
          </span>
          {elseif $hhme[joininfo][name] == "星益会员"}
          <span class="team-level team-level-mint">
            <span>星益</span>
          </span>
          {elseif $hhme[joininfo][name] == "商业会员"}
          <span class="team-level team-level-violet">
            <span>商业会员</span>
          </span>
          {/if}
        </div>
        
        <div class="opcls team-member-meta" data-uid="{$v[uid]}" data-hh="{$hhme[joininfo][name]}" data-fuchi="{$hhme[fuchi]}" data-fansuid="{$v[fansuid]}" data-username="{$v[fans][username]}">
          {eval $userlianghao = userlianghao($v['fans']['uid']);}
          {if $userlianghao}
          <span class="lianghao-text-1 team-id team-id-special">靓号: $userlianghao</span>
          {else}
          <span class="team-id">UID: {$v[fans][uid]}</span>
          {/if}
        </div>
        
     
        
  
    
        <!-- 用户日期独立显示 -->
        <div class="team-member-footer">
          <div class="team-member-spacer"></div>
          <div class="team-member-date">
            绑定日期：$v[crts_u]
          </div>
        </div>
        
    </div>
</div>
<!--{/loop}-->
