<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");   
$_GET[arid] = $_GET[arid] + 0;
$_GET[fileid] = $_GET[fileid] + 0;

if($_GET[arid] && $_GET[fileid])
{
    $db_table = $pre.'pravail_arti_file';
    $sql = "SELECT filename,thumb_url FROM " . $db_table . " WHERE arid = '$_GET[arid]' and fileid='$_GET[fileid]'";
    $row = $oPub->getRow($sql);
    if ($row['thumb_url'] != '' && is_file('../' . $row['thumb_url']))
    {
        @unlink('../' . $row['thumb_url']);
    }
    if ($row['filename'] != '' && is_file('../' . $row['filename']))
    {
        @unlink('../' . $row['filename']);
    }
    /* 判断是否删除标题缩图 */
	$db_table = $pre.'pravail_artitxt';
    $sql = "SELECT arid,arti_thumb  FROM " . $db_table . " 
		   WHERE `arid`    ='".$_GET[arid]."'  
		   and `min_thumb` ='".$row['thumb_url']."' 
		   and `domain_id` ='".$Aconf['domain_id']."'";
    $row = $oPub->getRow($sql);
	if($row['arid'] > 0 )
	{
        if (is_file('../' . $row['arti_thumb']))
        {
             @unlink('../' . $row['arti_thumb']);
        }
        $sql = "UPDATE " . $db_table . " SET 
		   `arti_thumb`= '',
		   `min_thumb` = '' 
		   WHERE `arid` ='".$_GET[arid]."'";
        $oPub->query($sql);
	}


    /* 删除数据 */
	$db_table = $pre.'pravail_arti_file';
    $sql = "DELETE FROM " . $db_table . " WHERE arid = '$_GET[arid]' and fileid='$_GET[fileid]' LIMIT 1";
    $oPub->query($sql);
    
	$str = '';
    $sql = "SELECT * FROM " . $db_table . " WHERE arid = '$_GET[arid]'";
    $img_list = $oPub->select($sql);
    while( @list( $k, $v ) = @each( $img_list) )
	 {
              $str .= '<div id="gallery_'.$v['fileid'].'" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">';
              $str .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) dropImg(\''.$v['fileid'].'\',\''.$v['arid'].'\')" title="删除">[-]</a><br />';
              $str .= '<a href="../'.$v['filename'].'" target="_blank">';
			  if($v['thumb_url'] != '')
		      {
		   	      $str .= '<img src="../'.$v['thumb_url'].'" width="60" height="60"  border="0" />';
			  }
			  else
		      {
                  $str .= '<div style="width:60;height:60"><br/><br/>查阅>></div>';
		      }
              $str .= '</a><br />';
              $str .= '<input type="text" value="'.$v['descs'].'" size="15" name="old_img_desc['.$v['fileid'].']" />';
              $str .= '</div>';
	 }
	 echo $str;
}
/* 单独删除268×198px缩图 */
if($_GET[arid] > 0 && $_GET[arti_thumb_file] != '')
{
	$arti_thumb = $_GET[arti_thumb_file];
	$db_table = $pre.'pravail_artitxt';
	$arid = $_POST['arid'];
    $sql = "UPDATE " . $db_table . " SET 
		   `arti_thumb`= '',
		   `min_thumb` = '' 
		   WHERE `arid` =".$_GET[arid]." and `domain_id`=".$Aconf['domain_id'];
     $oPub->query($sql);

    if (is_file('../' . $arti_thumb))
    {
        @unlink('../' . $arti_thumb);
    }
	$tmp ='<input type="hidden" name="old_arti_thumb" value="" />';
	$tmp .= '<input type="hidden" name="old_min_thumb" value="" />';
	echo $tmp;
}
?>