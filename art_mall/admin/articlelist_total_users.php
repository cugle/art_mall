<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if(!empty($Aconf['priveMessage'])) {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

if ( $_SESSION['aaction_list'] != 'all' and empty($_SESSION['aarticlecat_list'])) {
   echo showMessage("文章分类权限没有指定，不能查阅文章列表，请与管理员联系");
   exit;  
}

//发帖统计
/* 查询条件 */

$where = "states=0 AND domain_id = '".$Aconf['domain_id']."'";  
if(!empty($start_time)) {
	$start_time = $start_time;
	$end_time = $end_time;
    $start_time = local_strtotime($start_time." 00:00:01");
    $end_time   = local_strtotime($end_time."  23:59:59");
	$where .= " AND dateadd >= '".$start_time."' AND dateadd <= '".$end_time."'";
} 
if(!empty($end_time)) {
	$end_time   = local_strtotime($end_time."  23:59:59");
}else{
	$end_time   = time();
}

$StrtypeAll ='';
$db_table = $pre."artitxt";
$sql = "SELECT user_id FROM ".$db_table." WHERE 1 AND ". $where." group by user_id";
$row_usr_id = $oPub->select($sql);
if($row_usr_id) {
   $n = 0 ;
   foreach ($row_usr_id AS $key=>$val) {
	     $user_id = $val["user_id"];
	     $where2 = ' AND user_id='.$val["user_id"];
		 $db_table = $pre."artitxt";
         $sql = "SELECT COUNT(*) as count,sum(hots) as sum_hots,sum(support) as sum_support,sum(against) as sum_against,sum(comms) as sum_comms FROM ".$db_table." WHERE ". $where.$where2;
		 
         $row = $oPub->getRow($sql);

         $Asum["sum_count"]   = $row["count"];
         $Asum["sum_hots"]    = $row["sum_hots"];
         $Asum["sum_support"] = $row["sum_support"];
         $Asum["sum_against"] = $row["sum_against"];
         $Asum["sum_sa"]      = $row["sum_support"] + $row["sum_against"];
         $Asum["sum_comms"]   = $row["sum_comms"];

 
         $user_name = $oPub->getOne("SELECT b.user_name FROM ".$pre."admin_user   as b  WHERE  b.user_id = '".$user_id."'");  

	     $tmpstr = ($n % 2 == 0)?"even":"odd";
	     $n ++ ;
         $StrtypeAll .= '<TR class='.$tmpstr.'>';
		 $StrtypeAll .= '<TD align=left>';
	     $StrtypeAll .= $user_name;
		 $StrtypeAll .= '</TD>';
		 $StrtypeAll .= '<TD align=left>';
	     $StrtypeAll .= $Asum["sum_count"];
		 $StrtypeAll .= '</TD>';

	     $StrtypeAll .= '<TD align=left>';
         $StrtypeAll .=$Asum["sum_hots"] ;
	     $StrtypeAll .= '</TD>';
	     $tmp = $Asum["sum_support"] + $Asum["sum_against"];
	     $StrtypeAll .= '<TD align=left>';

         $StrtypeAll .=$Asum["sum_support"];
	     $StrtypeAll .= '+';

         $StrtypeAll .=$Asum["sum_against"];
	     $StrtypeAll .= '=';
	     $StrtypeAll .= $tmp;
	     $StrtypeAll .= '</TD>';
	     $StrtypeAll .= '<TD align=left>'. $Asum["sum_comms"].'</TD>'; 
   } 
}  
$Ahome["start_time"]    =$start_time>0?date("Y-m-d",$start_time):'';
$Ahome["end_time"]      =$end_time>0?date("Y-m-d",$end_time):date("Y-m-d");
$Ahome["StrtypeAll"]    = $StrtypeAll; 
$Ahome["nowName"]       = $nowName; 
$Ahome["strMessage"]    = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]);
?>
 
