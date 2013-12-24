<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
$_GET[prid] = $_GET[prid] +0;
$_GET[fileid] = $_GET[fileid] +0;
if($_GET[prid] && $_GET[fileid])
{
    $db_table = $pre.'product_file';
    $sql = "SELECT filename,thumb_url FROM " . $db_table . " WHERE prid = '$_GET[prid]' and fileid='$_GET[fileid]'";
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
	$db_table = $pre.'producttxt';
    $sql = "SELECT prid,shop_thumb  FROM " . $db_table . " 
		   WHERE `prid`    ='".$_GET[prid]."'  
		   and `min_thumb` ='".$row['thumb_url']."' 
		   and `domain_id` ='".$Aconf['domain_id']."'";
    $row = $oPub->getRow($sql);
	if($row['prid'] > 0 )
	{
        if (is_file('../' . $row['shop_thumb']))
        {
             @unlink('../' . $row['shop_thumb']);
        }
        $sql = "UPDATE " . $db_table . " SET 
		   `shop_thumb`= '',
		   `min_thumb` = '' 
		   WHERE `prid` ='".$_GET[prid]."'";
        $oPub->query($sql);
	}

    /* 删除数据 */
	$db_table = $pre.'product_file';
    $sql = "DELETE FROM " . $db_table . " WHERE prid = '$_GET[prid]' and fileid='$_GET[fileid]' LIMIT 1";
    $oPub->query($sql);
    
	$str = '';
    $sql = "SELECT * FROM " . $db_table . " WHERE prid = '$_GET[prid]'";
    $img_list = $oPub->select($sql);
    while( @list( $k, $v ) = @each( $img_list) )
	 {
              $str .= '<div id="gallery_'.$v['fileid'].'" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">';
              $str .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) dropImg(\''.$v['fileid'].'\',\''.$v['prid'].'\')">[-]</a><br />';
              $str .= '<a href="../'.$v['filename'].'" target="_blank">';
			  if($v['thumb_url'] != '')
		      {
		   	      $str .= '<img src="../'.$v['thumb_url'].'" width="60" height="60"  border="0" />';
			  }
			  else
		      {
                  $str .= '<div style="width:60;height:60"><br/>查阅>></div>';
		      }
            $str .= '</a><br />';
            $str .= '<input type="text" value="'.$v['descs'].'" size="15" name="old_img_desc['.$v['fileid'].']" />';
            $str .= '</div>';
	 }
	 echo $str;
}
/* 单独删除缩图记录 */
if($_GET[prid] > 0 && $_GET[prod_thumb_file] != '')
{

	$db_table = $pre.'producttxt';
    $sql = "UPDATE " . $db_table . " SET 
	       `min_thumb`= '',
		   `shop_thumb`= '' 
		   WHERE `prid` =".$_GET[prid]." and `domain_id`=".$Aconf['domain_id'];
     $oPub->query($sql);
	$prod_thumb_file = $_GET[prod_thumb_file];

    if (is_file('../' . $prod_thumb_file))
    {
        @unlink('../' . $prod_thumb_file);
    }

	$str = '<input type="hidden" name="old_shop_thumb" value="" />';
	$str .= '<input type="hidden" name="old_min_thumb" value="" />';
	echo $str;
}
?>