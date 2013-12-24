<?php
//文章顶踩
define('IN_OUN', true);
include_once( "./includes/command.php"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 

//ajax_article_vote.php?arid=" + b + "&types=" + a  DnumIn/CnumIn
$arid = $arid + 0 ;
if($types == 'DnumIn')
{
    $sqlupdate = 'support=support+1';
    $sqlselect = 'support';
} elseif($types == 'CnumIn')
{
    $sqlupdate = 'against=against+1';
    $sqlselect = 'against';
} else
{
    $sqlupdate = $sqlselect = false;
}

$showMessage ='';
if($arid && $sqlupdate)
{ 
	$types = 2;//文章类ip 1/2/ 文章/顶踩
	$ip    = real_ip();	
	$count = $oPub->getOne('SELECT count(*) as count FROM '. $pre.'artitxt_ip where types = '.$types.' AND ip = "'.$ip.'" AND arid = "'.$arid.'" AND domain_id="'.$Aconf['domain_id'].'"'); 
	if(!$count) { 
        $oPub->query('UPDATE '.$pre.'artitxt SET '.$sqlupdate.' WHERE arid = "'.$arid.'"'); 
		//记录当前ip  
        $oPub ->query('INSERT INTO '.$pre.'artitxt_ip(arid,types,ip,domain_id )VALUES ("'.$arid.'","'.$types.'", "'.$ip.'", "'.$Aconf['domain_id'].'" );'); 
	} else
	{  
        //不定时清理ip记录表,随机数方法1--20  如果数据数等于8,则自动清空此表
		$rand_number= rand(1,20);
		if($rand_number == 8) {
            $oPub ->query('truncate table '.$pre.'artitxt_ip'); 
		} 
		$showMessage = '不能连续操作！';
	} 
} else 
{
   $showMessage = '系统繁忙，请稍后再试！'; 
}
// 查找当前顶踩数量 
if($sqlupdate)
{
	$number = $oPub->getOne('SELECT '.$sqlselect.'  FROM '.$pre.'artitxt  where arid  = "'.$arid.'" AND domain_id="'.$Aconf['domain_id'].'"');  
}
echo ($showMessage)?$number.' '.$showMessage:$number;


?>