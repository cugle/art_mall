<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
//ajax_cars.php?op=" + op + "&prid=" + prid + "&nums=" + nums;  
$str = '';
if( !empty($op) && $_SESSION['user_id'] > 0)
{
	$prid = $prid + 0;$nums = $nums + 0; 
	if($prid > 0 && $nums > 0){
		$row = $oPub->getRow("SELECT prid,shop_number,shop_price FROM ".$pre."producttxt WHERE prid = $prid and states <> 1 and shop_number >= $nums   LIMIT 1"); 
		if($row['prid'] > 0 )
		{  
			 $oPub->query('delete from '.$pre.'carts where users_id='.$_SESSION['user_id'].' and prid='.$prid);
 			 $Afields=array('users_id'=>$_SESSION['user_id'],'prid'=>$prid,'nums'=>$nums,'sellprice'=>$row['shop_price'],'prices'=>$row['shop_price'],'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
			 $oPub->install($pre.'carts',$Afields);
			 $str = 'successful....';
		}else
		{
			$str = 'it is seldout,unsuccessful...';
		}
	}

	if($op == 'proufv' && $prid > 0)
	{
		//收藏
		$oPub->query('delete from '.$pre.'ufv where users_id='.$_SESSION['user_id'].' and prid='.$prid);
		$Afields=array('users_id'=>$_SESSION['user_id'],'prid'=>$prid,'dateadd'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre.'ufv',$Afields);
		$str = 'successful...'; 
		$str = '<div style="color:#B70000;font-weight: bold">'.$str.'</div>'; 
		$str .='<div style="width:180px;font-weight: bold;padding-top:5px;"><a href="javascript:;" onclick="buyhidden();">continue shopping...</A></div>';
		echo $str;
	}

	if($op == 'probuy')
	{
		$str = '<div style="color:#B70000;font-weight: bold">'.$str.'</div>'; 
		$str .= '<div style="width:280px;font-weight: bold;padding-top:5px;"><A HREF="user.php?o=car">go to cart</A>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="buyhidden();">continue shopping</A></div>';
		echo $str;
	}
} 
?>