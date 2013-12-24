<?php
/* 公共AJAX调用模块 
   // a 选择的值   b :数据库名  c:显示出来的样式名 d:操作类型del show install edit  
   obj = c; 
   var strTemp = "selectsajax.php?op=" + d + "&cstyle=" + c + "&bdatebase=" + b + "&avalue=" + escape(a);
*/
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false); 

$value		= getUtf8( $_GET["avalue"]);
$datebase	= $_GET['bdatebase'];
$op			= $_GET['op'];
$str = '';
if($op ) {
	$db_table = $pre.$datebase; 
    if($op == 'show'){
		//sysconfig.php地区AJAX调用 1级 start
		if($value &&  $_GET["cstyle"] == 'divccid')
		{
			$AnormAll = $oPub->select('SELECT * FROM '.$db_table.' where fid = "'.$value.'"'); 
			if($AnormAll)
			{
				$num = $_GET["cstyleend"] + 1;  
				$str .= '<SELECT NAME="ccid_'.$_GET["cstyleend"].'" onchange="selectsAjax(this.value,\'citycat\',\'show\',\''.$_GET["cstyle"].'\','.$num.')">';
				$str .= '<OPTION VALUE="0" >下级分类</OPTION>';
				$n = 0;
				while( @list( $key, $value ) = @each( $AnormAll) ) {
					$n ++;
					$str .= '<OPTION VALUE="'.$value["ccid"].'">'.$n.'、'.$value["name"].'</OPTION>'; 
				}
				$str .= '</SELECT>';
			}
		}//$_GET["cstyle"] == 'divccid_1' end 
		//sysconfig.php 所属行业 AJAX调用 1级 start
		if($value &&  $_GET["cstyle"] == 'divinducatid')
		{
			$AnormAll = $oPub->select('SELECT * FROM '.$db_table.' where fid = "'.$value.'"'); 
			if($AnormAll)
			{
				$num = $_GET["cstyleend"] + 1;  
				$str .= '<SELECT NAME="inducat_'.$_GET["cstyleend"].'" onchange="selectsAjax(this.value,\'inducat\',\'show\',\''.$_GET["cstyle"].'\','.$num.')">';
				$str .= '<OPTION VALUE="0" >下级分类</OPTION>';
				$n = 0;
				while( @list( $key, $value ) = @each( $AnormAll) ) {
					$n ++;
					$str .= '<OPTION VALUE="'.$value["inducatid"].'">'.$n.'、'.$value["name"].'</OPTION>'; 
				}
				$str .= '</SELECT>';
			}
		}//$_GET["cstyle"] == 'divccid_1' end 
	}////////////// $op == show end

	//////////////////删除操作 
	if($op == 'del'){ 
 
		if($value && $_GET["cstyle"] == 'logo_show') 
		{//删除logo
			if (is_file('../data/weblogo/' . $value))
			{
				@unlink('../data/weblogo/' .$value);
			} 
			$sql = "UPDATE " . $pre."sysconfig SET  logo='' WHERE  scid='".$Aconf['domain_id']."'";
			$oPub->query($sql); 
		}

		if($value && $_GET["cstyle"] == 'shop_logo_show') {//删除shop_logo
			if (is_file('../data/weblogo/' . $value)) {
				@unlink('../data/weblogo/' .$value);
			} 
			$sql = "UPDATE " . $pre."sysconfig SET  shop_logo='' WHERE  scid='".$Aconf['domain_id']."'";
			$oPub->query($sql);
		}
		if($value && $_GET["cstyle"] == 'watermark_show') {//删除s水印
			if (is_file('../data/weblogo/' . $value)) {
				@unlink('../data/weblogo/' .$value);
			} 
			$sql = "UPDATE " . $pre."sysconfig SET  watermark='' WHERE  scid='".$Aconf['domain_id']."'";
			$oPub->query($sql);
		}
		if($value && $_GET["cstyle"] == 'shop_prav_show') {//删除经销商logo
 
			if (is_file('../' . $value)) {
				@unlink('../' .$value);
				$sql = "UPDATE " . $pre."pravail SET  shop_logo='' WHERE   shop_logo='".$value."'"; 
				$oPub->query($sql);
			}  
		}
		if($value && $_GET["cstyle"] == 'nav_show') {//删除导航条图标
			if (is_file('../data/weblogo/' . $value)) {
				@unlink('../data/weblogo/' .$value);
			} 
			$sql = "UPDATE " . $pre."nav SET  url_logo='' WHERE  domain_id='".$Aconf['domain_id']."' and url_logo='".$value."'";
			$oPub->query($sql);
		}
	}

	//////////////////
}else{
	$str = '无对应值！';
}
echo $str;
?>