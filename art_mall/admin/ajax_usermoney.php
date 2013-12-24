<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//op=" + op + "&id=" + id
//op=" + edit_price + "&arcid=" + arcid 
$str = '';
if($_SESSION['auser_id'] > 0)
{
	if($op == 'check')
	{
		//id users_id bankname remmoney payname paynums dateadd checked checkdesc domain_id
		$row = $oPub->getRow('SELECT a.users_id,a.remmoney,b.user_name,b.money FROM '.$pre.'udetail as a,'.$pre.'users as b WHERE a.id="'.$id.'" and a.checked < 1 and a.users_id=b.id limit 1'); 
		if($row['remmoney'] > 0 )
		{
			$str = $checkdesc = $_SESSION['auser_name'].' 为：'.$row['user_name'] .'确认充值'.$row['remmoney'].',充值前余额为：'.$row['money'].' 充值后余额为：'.($row['remmoney'] + $row['money']).' 时间：'.date("Y年m月d日 H:i");
			$oPub->query('UPDATE '. $pre.'udetail SET checked=1,checkdesc="'.$checkdesc.'" WHERE  id="'.$id.'" limit 1');
			$oPub->query('UPDATE '. $pre.'users SET money= money + '.$row['remmoney'].' WHERE  id="'.$row['users_id'].'"  limit 1');
			
			$str = '<a title="'.$str.'">'.sub_str($str,4).'</a>';
		}else
		{
			$str = '已经确认过，不需要操作';
		}
	}

	if($op == 'userlist')
	{
		//$row = $oPub->getRow('SELECT a.users_id,a.remmoney,b.user_name,b.money FROM '.$pre.'udetail as a,'.$pre.'users as b WHERE a.id="'.$id.'" and a.users_id=b.id limit 1');
		//$row = $oPub->select('SELECT a.users_id,a.remmoney,b.user_name,b.money FROM '.$pre.'udetail as a,'.$pre.'users as b WHERE a.id="'.$id.'" and a.checked < 1 and a.users_id=b.id limit 1');  
		$str ='以后补上';
	}

}

echo $str;
?>