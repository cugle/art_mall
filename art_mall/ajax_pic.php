<?php
define('IN_OUN', true);
include_once( "./includes/command.php");
//header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
$fileid = $fileid + 0;
if( $fileid) {
	$sql = "SELECT fileid,arid,filename,descs  FROM ".$pre."arti_file where fileid='".$fileid."' limit 1";
	$row = $oPub->getRow($sql); 
    //ajax_pic.php?fileid=
	if($row){
		$sql = "SELECT fileid FROM ".$pre."arti_file where arid='".$row['arid']."' and fileid > '".$fileid."' order by fileid asc limit 1";
		$prevfileid = $oPub->getOne($sql);

		$sql = "SELECT fileid FROM ".$pre."arti_file where arid='".$row['arid']."' and fileid < '".$fileid."' order by fileid desc limit 1";
		$nextfileid = $oPub->getOne($sql);
		$str ='';
		if($prevfileid){
			$str .= '<div class="next"><a onmousedown=showpic("'.$prevfileid.'") style="cursor:pointer"></a></div>';
		}
		if($nextfileid){
			$str .= '<div class="prev"><a onmousedown=showpic("'.$nextfileid.'") style="cursor:pointer"></a></div>'; 
		}
		
		$str .= '<div class="img"  id="article"><a href="'.$row["filename"].'" target="_blank"><img src="'.$row["filename"].'" border="0" id="main_img"  width="630"  title="点击查看原图"/></a></div>';
		$str .= '<div class="text">'.$row["descs"].'</div>';
	}else{
		$str = '操作错误!';
	}
} else {
   $str = '操作错误!';
}

echo $str;
?>