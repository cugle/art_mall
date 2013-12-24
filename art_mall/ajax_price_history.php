<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
//header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
$prid =  $prid + 0;
$praid = $praid + 0;
$praid = ($praid)?$praid:0;
if($prid)
{
	if($praid>0) {
		$praid = $praid;
		/* 经销商价格 */
		//praid prid shop_price dateadd domain_id 
	    $db_table = $pre."price_history";
	    $sql = "SELECT shop_price,dateadd     
	        FROM ".$db_table." 
			where  prid = '$prid'
			AND praid = '$praid'
			AND domain_id = '".$Aconf['domain_id']."' 
			ORDER BY  dateadd DESC";
        $row = $oPub->select($sql);
	} else
	{
	    $db_table = $pre."price_history";
	    $sql = "SELECT shop_price,dateadd     
	        FROM ".$db_table." 
			where  prid = '$prid'
			AND praid = '0'
			AND domain_id = '".$Aconf['domain_id']."' 
			ORDER BY  dateadd DESC";
        $row = $oPub->select($sql);
	}

	if($row)
	foreach($row as  $v)
	{
      $str .= date("m月d日 h:i", $v['dateadd']).' '.$v['shop_price'].'<br/>'; 
	}
    

}

$showstr = "<div style='margin:2px;padding:2px;width:350px;background-color:#F4FAFF;border:1px solid #FF9900;color: #000000'>";
$showstr .= "<span style='float:right'><A  href='JavaScript:Hidden(".$praid.",".$prid.");' target='_self' style='color: #FF6633;font-size: 9px'>[关闭]</A></span>";
$showstr .= $str;
$showstr .=  "</div>";
echo $showstr;
?>