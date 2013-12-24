<?php
define('IN_OUN', true);
include_once( "./includes/command.php");

 
/* 调查列表 查询条件 */
$vtid = $vtid < 1?$id:$vtid; 
$vtid = $vtid + 0;
if(!$vtid){
   header("Location: votes.php");
   exit;
}
$show_vote = false; $strMessage = ''; 
/* 投票显示结果 */
if($op == 'poll') {
	if($op && $vtid) //投票
	{
		$ip = real_ip(); 

		$row = $oPub->getRow("SELECT a.xianz,a.xianz_num  FROM ".$pre."vote_title AS a 
		WHERE a.states=0 AND a.is_show = 1 and a.vtid='".$vtid."' AND	 a.domain_id = ".$Aconf['domain_id']." limit 1");
		$xianz_num = $row['xianz_num'];
		$xianz     = $row['xianz']; 

		if( $xianz < 1)
		{
			$add_ip = true;
		}else {
			/* 判断是否已经投过票 24 =86400 小时限制 */
			$times = 86400;
			$tmp    = gmtime() - $times;
			$oPub ->query("delete from ".$pre."vote_ip where add_time < ".$tmp);

			 if($xianz == 1) {
				//启用IP限制及电脑名限制
				$Aip['count'] = $oPub->getOne("SELECT count(*) as count FROM ".$pre."vote_ip where vtid = ".$vtid." AND ip = '$ip' AND domain_id='".$Aconf['domain_id']. "'"); 				

				if($Aip['count'] >= $xianz_num) 
				{
					$add_ip     = false; 
					$strMessage = '超过投票次数，一天只允许票数:'.$xianz_num;
				}else{
					$add_ip = true;
				}

			} elseif($xianz == 2) {
				//只允许登录帐号投票 
				if($_SESSION['user_id'] > 0 ) {
					$Aip['count'] = $oPub->getOne("SELECT count(*) as count FROM ".$pre."vote_ip where vtid = ".$vtid." AND users_id = '".$_SESSION['user_id']."' AND domain_id='".$Aconf['domain_id']. "'"); 
					if($Aip['count'] >= $xianz_num) 
					{
						$add_ip     = false; 
						$strMessage = '超过投票次数，一天只允许票数:'.$xianz_num;
					}else{
						$add_ip = true;
					} 
				}else {
					$add_ip = false;
					$strMessage = '登录后才能投票';
				} 
			}
		}
 

        if($add_ip) {
			$time = gmtime();
			$sql = "INSERT INTO  ".$pre."vote_ip( vtid,ip,users_id,add_time,domain_id ) 
				   VALUES ('".$vtid."', '".$ip."','".$_SESSION['user_id']."', '".$time."','".$Aconf['domain_id']."' );";
			$oPub ->query($sql); 
			
			if($vote_item){
				foreach ($vote_item AS $key => $val) {	 
					if(!empty($val)){
						$oPub ->query("INSERT INTO  ".$pre."vote_poll( vtid,viid,descs,ip,computer,users_id,user_name,add_time,domain_id ) 
							   VALUES ('".$vtid."','".$key."','".$val."', '".$ip."', '".$_SERVER["HTTP_USER_AGENT"]."','".$_SESSION['user_id']."','".$_SESSION['user_name']."','".$time."','".$Aconf['domain_id']."' )"); 
						$oPub->query("UPDATE ".$pre."vote_item SET vi_nums = vi_nums + 1 ".
							 " WHERE viid=$key AND vtid = ".$vtid." AND domain_id='".$Aconf['domain_id']."'");
					}
				}
			}

			if($vote_vgid_radio){
				foreach ($vote_vgid_radio AS $key => $val) {
					if(!empty($val)){
						$sql = "INSERT INTO  ".$pre."vote_poll( vtid,viid,descs,ip,computer,users_id,user_name,add_time,domain_id ) 
							   VALUES ('".$vtid."','".$val."','".$val."', '".$ip."','".$_SERVER["HTTP_USER_AGENT"]."','".$_SESSION['user_id']."','".$_SESSION['user_name']."', '".$time."','".$Aconf['domain_id']."' );";
						$oPub ->query($sql); 
						  
						$sql = "UPDATE ".$pre."vote_item SET vi_nums = vi_nums + 1 ".
							 " WHERE viid=$val AND vtid = ".$vtid." AND domain_id='".$Aconf['domain_id']."'";
						$oPub->query($sql);
					}
				}
			}
			/* 重新计算总票数 */  
			$sql = "SELECT sum( `vi_nums` ) AS sumnums FROM ".$pre."vote_item 
			   where vtid = ".$vtid." 
			   AND is_show = 1 
			   AND states = 0 
			   AND domain_id=".$Aconf['domain_id'];
			$Anum = $oPub->getRow($sql);
			$sumnums = $Anum['sumnums'];

			$Afields=array('vt_nums'=>$sumnums);
			$condition = "vtid = ".$vtid." AND domain_id=".$Aconf['domain_id'];
			$oPub->update($pre."vote_title",$Afields,$condition);
			//记录可选组票数 
			$sql = "SELECT vgid,sum( `vi_nums` ) AS vg_nums FROM ".$pre."vote_item 
			   where vtid = ".$vtid." 
			   AND is_show = 1 
			   AND states = 0 
			   AND domain_id=".$Aconf['domain_id'].
			   " group by vgid ";
			$row = $oPub->select($sql); 
			while( @list( $k, $v) = @each($row) ) { 
				$Afields=array('vg_nums'=>$v['vg_nums']);
				$condition = "vgid = ".$v['vgid'];
				$oPub->update($pre."vote_group",$Afields,$condition);
			}
		   unset($Anum);
		} else {
			
			if($Aconf['rewrite']){ 
				$url = 'vote-'.$vtid.'-0.html';
			}else{ 
				$url = 'vote.php?id='.$vtid;
			}  
			echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!');top.location='".$url."';</script>";

		}
	}
	/* 结果 */
	$show_vote = true; 
}

if($op == 'show'){
	$show_vote = true;
}


 
if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}
/* 调用模板 */
/*------------------------------------------------------ */
//-- 判断是否存在缓存，如果存在则调用缓存，反之读取相应内容
/*------------------------------------------------------ */
/* 缓存编号 */ 

$cache_id = sprintf('%X', crc32($Aconf['domain_id'].$vtid.$show_vote));
if (!$smarty->is_cached($Aconf["displayFile"], $cache_id)) {

	include_once( ROOT_PATH."includes/item_set.php"); 
	/* 产品页显示的模块 */   
	$Aconf['header_title'] = $Aweb_url['vote'][0]."|".$Aconf["web_title"];  

	/* 显示调查内容 */
	$sql = "SELECT a.vtid,a.vt_name,a.vt_desc,a.showtype FROM ".$pre."vote_title AS a 
		WHERE a.states=0 AND a.is_show = 1 and a.vtid='".$vtid."' AND	 a.domain_id = ".$Aconf['domain_id']." limit 1";
	$row = $oPub->getRow($sql);
	$rowarticle["vote_title"] = $row;
	if($row) {
		if($row['showtype'] >0){
			//在同一页显示
			$rowarticle["for_vote_group_show"]= vote_show($row["vtid"]);
			$rowarticle["for_vote_group"]= vote($row["vtid"]); 
		} else {
			if($show_vote)
			{
				$rowarticle["for_vote_group_show"]= vote_show($row["vtid"]);
			} else {
				$rowarticle["for_vote_group"]= vote($row["vtid"]); 
			} 
		}

	}else
	{
	   $strMessage = '此调查已经关闭，或者删除';
	   echo "<SCRIPT language='javascript'>\nalert('".$strMessage."!!');top.location='votes.php';</script>";
	   exit;
	}
	$Ahome["show_vote"] = $show_vote;
	$Ahome["nowNave"]  = '<li><A HREF="./">'.$Aweb_url["index"][0].'</A> '.$Aconf['nav_symbol'].'</li><li><a href="votes.php">'.$Aweb_url["votes"][0].'</a> '.$Aconf['nav_symbol'].'</li><li>'.$row["vt_name"].'</li><li><span style="font-weight:lighter"> 详情</span></li>'; 
	$Aconf['header_title'] = $row["vt_name"].'|'.$Aconf["header_title"]; 
	$Ahome["vote"] = $rowarticle;unset($rowarticle);unset($row);

	assign_template($Aconf); 
	$smarty->assign('home', $Ahome );
	$smarty->assign('user', $_SESSION ); 
	unset($Ahome); 
}
$smarty->display($Aconf["displayFile"], $cache_id);

?>
