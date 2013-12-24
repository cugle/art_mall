<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
$strKeys = '<span style="color:#00CC00;margin-left: 30px">关联产品，编辑添加</span><br/>';

/* 得到关联产品的prid 数组 */ 
$art_pro_type = 1;
$prid = $prid + 0;
$whereArid = ($prid > 0)?' AND arid <> "'.$prid.'"':' ';
$Aarid = array();
if(!empty($keys0))
{
	 $keys = getUtf8( "$keys0");
     $row = $oPub->select('SELECT arid FROM '.$pre.'arti_tag  WHERE `keys` = "'.$keys.'"  AND art_pro_type = "'.$art_pro_type.'" AND domain_id = "'.$Aconf['domain_id'].'"'.$whereArid.' ORDER BY atid DESC
			 LIMIT 5'); 
     while( @list( $k, $v) = @each( $row) ) {
		 if (!in_array($v['arid'], $Aarid)) {
			 array_push($Aarid, $v['arid']);    
         }	   
     }
}
if(!empty($keys1))
{
	 $keys = getUtf8( "$keys1");
     $row = $oPub->select('SELECT arid FROM '.$pre.'arti_tag  WHERE `keys` = "'.$keys.'"  AND art_pro_type = "'.$art_pro_type.'" AND domain_id = "'.$Aconf['domain_id'].'"'.$whereArid.' ORDER BY atid DESC
			 LIMIT 5'); 
     while( @list( $k, $v) = @each( $row) ) {
		 if (!in_array($v['arid'], $Aarid)) {
			 array_push($Aarid, $v['arid']);    
         }	   
     }

}
if(!empty($keys2))
{
	 $keys = getUtf8( "$keys2");
     $row = $oPub->select('SELECT arid FROM '.$pre.'arti_tag  WHERE `keys` = "'.$keys.'"  AND art_pro_type = "'.$art_pro_type.'" AND domain_id = "'.$Aconf['domain_id'].'"'.$whereArid.' ORDER BY atid DESC
			 LIMIT 5'); 
     while( @list( $k, $v) = @each( $row) ) {
		 if (!in_array($v['arid'], $Aarid)) {
			 array_push($Aarid, $v['arid']);    
         }	   
     }
}
/* 查询关联产品 */
$strArid = implode(',',$Aarid);
$cltionnum = ($Aconf['cltion'] > 0 )?$Aconf['cltion']:10;
$n = 0;
if($strArid  != '')
{ 
   $row = $oPub->select('SELECT prid ,name FROM '. $pre.'producttxt  
          WHERE  domain_id ="'. $Aconf['domain_id'].'" AND prid in('.$strArid.')  ORDER BY prid DESC'); 
   
   while( @list( $k, $v) = @each( $row ) ) {
       $strKeys .= '标题：<input type="text" name="keysname_product[]" value="'.$v['name'].'" size="50"/>';
       $strKeys .= '网址：<input type="text" name="keyshttp_product[]" value="product.php?id='.$v['prid'].'" size="50"/>';
       $strKeys .= '<A HREF="../product.php?id='.$v['prid'].'" target="_blank"> 详情>> </A><br/>';
	   $n ++ ;
	   if($n >= $cltionnum)
	   {
		   break;
	   }
   }
}

for($n; $n < $cltionnum; $n++)
{
    $strKeys .= '标题：<input type="text" name="keysname_product[]" value="" size="50"/>';
    $strKeys .= '网址：<input type="text" name="keyshttp_product[]" value="" size="50"/><br/>';
}

echo $strKeys;
?>