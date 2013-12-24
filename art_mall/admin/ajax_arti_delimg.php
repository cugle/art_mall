<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
if(!isset($arid)) $arid=false;
if(!isset($fileid)) $fileid=false;
$arid = $arid + 0;
$fileid = $fileid + 0; 
if($arid && $fileid)
{  
    $row = $oPub->getRow("SELECT filename,thumb_url FROM " . $pre."arti_file  
	WHERE arid = '$arid' and fileid='$fileid'"); 
    if ($row['thumb_url'] != '' && is_file('../' . $row['thumb_url']))
    {
        @unlink('../' . $row['thumb_url']);
    }
    if ($row['filename'] != '' && is_file('../' . $row['filename']))
    {
        @unlink('../' . $row['filename']);
    }
    /* 判断是否删除标题缩图 */ 
    $row = $oPub->getRow("SELECT arid,arti_thumb  FROM " . $pre."artitxt 
		   WHERE `arid`    ='".$arid."'  and `min_thumb` ='".$row['thumb_url']."' and `domain_id` ='".$Aconf['domain_id']."'"); 
	if($row['arid'] > 0 )
	{
        if (is_file('../' . $row['arti_thumb']))
        {
             @unlink('../' . $row['arti_thumb']);
        }
        $oPub->query("UPDATE " . $pre."artitxt SET `arti_thumb`= '', `min_thumb` = '' WHERE `arid` ='".$arid."'"); 
	} 
    /* 删除数据 */ 
    $oPub->query("DELETE FROM " . $pre."arti_file WHERE arid = '$arid' and fileid='$fileid' LIMIT 1"); 
    
	$str = '';
    $img_list = $oPub->select("SELECT * FROM " . $pre."arti_file WHERE arid = '$arid'"); 
    while( @list( $k, $v ) = @each( $img_list) )
	 {
              $str .= '<div id="gallery_'.$v['fileid'].'" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">';
              $str .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) dropImg(\''.$v['fileid'].'\',\''.$v['arid'].'\')" title="删除">[-]</a><br />';
              $str .= '<a href="../'.$v['filename'].'" target="_blank">';
			  if($v['thumb_url'] != '')
		      {
		   	      $str .= '<img src="../'.$v['thumb_url'].'" width="'.$Aconf["min_thumb_w"].'" height="'.$Aconf["min_thumb_h"].'"  border="0" />';
			  }
			  else
		      {
                  $str .= '<div style="width:'.$Aconf["min_thumb_w"].'px;height:'.$Aconf["min_thumb_h"].'px"><br/><br/>查阅>></div>';
		      }
              $str .= '</a><br />';
              $str .= '<input type="text" value="'.$v['descs'].'" size="15" name="old_img_desc['.$v['fileid'].']" />';
              $str .= '</div>';
	 }
	 echo $str;
}
/* 删除公告 关于我们 图片 */
if($fileid && ($action == 'notice' || $action == 'pra_notices' || $action == 'pra_descs' || $action == 'about'))
{ 
    $row = $oPub->getRow("SELECT filename,thumb_url FROM " . $pre."arti_file WHERE fileid='$fileid'"); 
    if ($row['thumb_url'] != '' && is_file('../' . $row['thumb_url']))
    {
        @unlink('../' . $row['thumb_url']);
    }
    if ($row['filename'] != '' && is_file('../' . $row['filename']))
    {
        @unlink('../' . $row['filename']);
    }
    $oPub->query("DELETE FROM " . $pre."arti_file WHERE  fileid='$fileid' LIMIT 1"); 
	$str = '';
    $img_list = $oPub->select("SELECT * FROM " . $pre."arti_file WHERE type = '$action' and domain_id=".$Aconf['domain_id']);  
    while( @list( $k, $v ) = @each( $img_list) )
	 {
		  $str .= '<div id="gallery_'.$v['fileid'].'" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;width:122px;height:130px">';
		  $str .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) dropImg(\''.$v['fileid'].'\',\''.$v['arid'].'\')" title="删除">[-]</a> ';
		  $str .= '<a href="../'.$v['filename'].'" target="_blank">[>]</a>';  
		  if($v['thumb_url'] != '')
		  {
			  $str .= '<img src="../'.$v['thumb_url'].'" width="'.$Aconf["min_thumb_w"].'" height="'.$Aconf["min_thumb_h"].'"  border="0" onclick="insertHtml(\''.$Aconf['domain_url'].$v['filename'].'\',\''.$v['descs'].'\')" />';
		  }  else  {
			  $str .= '<div style="width:'.$Aconf["min_thumb_w"].'px;height:'.$Aconf["min_thumb_h"].'px;background-color:#E4E4E4"><br/><br/>查阅>></div>';
		  }

		  $str .= '<input type="text" value="'.$v['descs'].'" size="15" name="old_img_desc['.$v['fileid'].']" />';
		  $str .= '</div>';
	 }
	 echo $str;
}
/* 单独删除268×198px缩图 */
if(!isset($arti_thumb_file)) $arti_thumb_file=false;
if($arid > 0 && $arti_thumb_file != '')
{
	$arti_thumb = $arti_thumb_file;
	$db_table = $pre."artitxt";
	$arid = $arid;
    $oPub->query("UPDATE " .$pre."artitxt SET `arti_thumb`= '', `min_thumb` = '' WHERE `arid` =".$arid." and `domain_id`=".$Aconf['domain_id']); 
    if (is_file('../' . $arti_thumb))
    {
        @unlink('../' . $arti_thumb);
    }
	$tmp ='<input type="hidden" name="old_arti_thumb" value="" />';
	$tmp .= '<input type="hidden" name="old_min_thumb" value="" />';
	echo $tmp;
}
?>