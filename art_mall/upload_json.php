<?php
define('IN_OUN', true); 
include_once( "./includes/command.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

include_once( ROOT_PATH."kindeditor/php/JSON.php");  
 
$ext_arr = array(
	'image' => array('gif', 'jpg', 'jpeg', 'png'),
	'flash' => array('swf', 'flv'),
	'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
	'file'  => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
);

$dir_name = empty($dir) ? 'image' : trim($dir);
if (empty($ext_arr[$dir_name])) {
	alert("目录名不正确。");
} 

//有上传文件时
if (empty($_FILES) === false)
{   
	//原文件名
	$file_name = $_FILES['imgFile']['name'];
	//服务器上临时文件名
	$tmp_name = $_FILES['imgFile']['tmp_name'];
	//文件大小
	$file_size = $_FILES['imgFile']['size'];

	//获得文件扩展名
	$temp_arr = explode(".", $file_name);
	$file_ext = array_pop($temp_arr);
	$file_ext = trim($file_ext);
	$file_ext = strtolower($file_ext);
	$type = $jsonop; 
	//检查扩展名
	if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
		alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
	}

	$thumb_url = '';
	if($dir_name == 'image')
	{
		//生成缩图 
		$thumb_url = $image->make_thumb($_FILES["imgFile"]['tmp_name'], $Aconf['min_thumb_w'],  $Aconf['min_thumb_h']);
		if($type == 'products' || $type == 'pproducts')
		{
			$shop_thumb= $image->make_thumb($_FILES["imgFile"]['tmp_name'], $Aconf['big_thumb_w'],  $Aconf['big_thumb_h']); 
		}
		//上传图片、增加水印
		$img_original = $image->upload_image($_FILES["imgFile"]); 
		if(empty($img_original))
		{
			$img_original = $image->upload_image($_FILES["imgFile"]['tmp_name']);
		}

		$target_file  = $filename = ROOT_PATH.$img_original;
		$watermark    = ROOT_PATH.'data/weblogo/'.$Aconf['watermark']; 
		if(file_exists($watermark)){ 
			$image->add_watermark($filename, $target_file, $watermark,5,80 ); 
		} 
		/*
		$str = '';
		while( @list( $k, $v ) = @each( $_REQUEST ) ) {
			$str .=$k." --- ". $v."\n";
		}
		$str .= $img_original;
		log_result($str);
		*/
	} else { 
		$img_original = $image->upload_image($_FILES['imgFile']); 
	} 

	$A = explode(".",$_FILES['imgFile']['name']);
	$img_desc = $A[0]; 
	
	if($type == 'descs')
	{
		if($arid < 1)
		{
			$arid = $Aconf['domain_id'];
		}else {
			$arid = $arid + 0;
		}
		$oPub->query( "INSERT INTO " . $pre."arti_file (arid,user_id,type, filename,thumb_url,descs,domain_id)VALUES (".$arid.",'".$_SESSION['auser_id']."','$type', '$img_original', '$thumb_url','$img_desc',".$Aconf['domain_id'].")"); 
	}elseif($type == 'products')
	{ 
		$prid = $prid<1?$Aconf['domain_id']:$prid; 
		$sql = "INSERT INTO " .$pre."product_file (prid, user_id,filename,thumb_url,shop_thumb,descs,domain_id) " .
				"VALUES ('$prid','".$_SESSION['auser_id']."', '$img_original', '$thumb_url','$shop_thumb','$img_desc','".$Aconf['domain_id']."')";
		$oPub->query($sql);
	}elseif($type == 'pproducts')
	{  
		$prid = $prid<1?$Aconf['domain_id']:$prid; 
		$sql = "INSERT INTO " .$pre."pravail_product_file(prid, user_id,filename,thupmb_url,shop_thumb,descs,domain_id) " .
				"VALUES ('$prid','".$_SESSION['auser_id']."', '$img_original', '$thumb_url','$shop_thumb','$img_desc','".$Aconf['domain_id']."')";
		$oPub->query($sql);
	}elseif($type == 'particle')
	{  
		$arid = $arid<1?$Aconf['domain_id']:$arid;  
        $oPub->query('INSERT INTO '.$pre.'pravail_arti_file(arid,user_id, filename,thumb_url,descs,domain_id)VALUES ("'.$arid.'","'.$_SESSION['auser_id'].'","'.$img_original.'","'.$thumb_url.'","'.$img_desc.'","'.$Aconf['domain_id'].'")'); 

	}  else
	{
		if($arid < 1)
		{
			$arid = $Aconf['domain_id'];
		}else {
			$arid = $arid + 0;
		}
		$oPub->query( "INSERT INTO " . $pre."arti_file (arid,user_id,type, filename,thumb_url,descs,domain_id)VALUES (".$arid.",'".$_SESSION['auser_id']."','$type', '$img_original', '$thumb_url','$img_desc',".$Aconf['domain_id'].")"); 
	}
  
	$img_original = $Aconf['domain_url'].$img_original;
 
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 0, 'url' => $img_original));
	exit;
 
} 

function alert($msg) {
	header('Content-type: text/html; charset=UTF-8');
	$json = new Services_JSON();
	echo $json->encode(array('error' => 1, 'message' => $msg));
	exit;
}
?>