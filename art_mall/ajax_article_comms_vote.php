<?php
//文章顶踩
define('IN_OUN', true);
include_once( "./includes/command.php");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

$arcid = $arcid + 0 ;
if($types == 'DnumIn_comm') {
    $sqlupdate = 'support=support+1';
    $sqlselect = 'support';
}
elseif($types == 'CnumIn_comm')
{
    $sqlupdate = 'against=against+1';
    $sqlselect = 'against';
}
else
{
    $sqlupdate = false;
	$sqlupdate = $sqlselect = false;
}

$showMessage ='';
if($arcid > 0   && $sqlupdate)
{
	$db_table = $pre.'artitxt_ip';
	$types    = 2;//文章类ip 1/2/ 文章/顶踩
	$ip = real_ip();	
	$sql = "SELECT count(*) as count FROM ".$db_table." 
		       where types = ".$types." 
			   AND ip = '$ip' 
			   AND arid = '".$arid."'
			   AND domain_id='".$Aconf['domain_id']."'";
    $count = $oPub->getOne($sql);
	if(!$count) {
	    $db_table = $pre."arti_comms ";
        $sql = "UPDATE ". $db_table." SET $sqlupdate  WHERE arcid = '".$arcid."'";
        $oPub->query($sql);
		//记录当前ip 
		$db_table = $pre.'artitxt_ip';
        $sql = "INSERT INTO  ".$db_table."(arid,types,ip,domain_id ) 
                   VALUES ('$arid','$types', '".$ip."', '".$Aconf['domain_id']."' );";
        $oPub ->query($sql);
	} else
	{  
        //不定时清理ip记录表,随机数方法1--20  如果数据数等于8,则自动清空此表
		$rand_number= rand(1,20);
		if($rand_number == 8) {
			$db_table = $pre.'artitxt_ip';
            $sql = "truncate table ".$db_table;
			$oPub ->query($sql);
		} 
		$showMessage = '不能连续操作！';
	}  
} else {
   $showMessage = '系统繁忙，请稍后再试！';
}
// 查找当前顶踩数量
if($sqlupdate)
{
   $db_table = $pre."arti_comms ";
   $sql = "SELECT $sqlselect  FROM ".$db_table." where arcid  = '".$arcid."' AND domain_id='".$Aconf['domain_id']."'";
   $number = $oPub->getOne($sql);
}

echo ($showMessage)?$number.' '.$showMessage:$number;
?>