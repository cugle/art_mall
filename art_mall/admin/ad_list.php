<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
/* 临时用此方法限制普通用户对此模块的访问 */

/*------------------------------------------------------ */
//-- 批量删除产品记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del')
{
    if(isset($_REQUEST['ad_id']))
   {
         $id = $_REQUEST['ad_id'];
		 $db_table = $pre."ad";

		$condition = "ad_id='".$id."'"; 
		$sql = "SELECT ad_code  FROM " . $db_table . " WHERE ".$condition;
		$ad_code = $oPub->getOne($sql);
		if (is_file('../data/abcde/' . $ad_code))  @unlink('../data/abcde/' . $ad_code); 

		$oPub->query("delete from  ".$db_table."  WHERE ".$condition);  

		$strMessage =  "删除成功!";
   }
   else
   {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

}

/* 查询条件 */
$Amedia_type = array(1=>'图片',2=>'Flash',3=>'代码',4=>'文字');

$db_table = $pre."ad";
$where = " AND domain_id = '".$Aconf['domain_id']."'";
$sql = "SELECT COUNT(*) as count FROM ".$db_table."  WHERE 1 ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE 1 $where ".
       " ORDER BY ad_id ASC " .
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row )
{ 
	
    foreach ($row AS $key=>$val)
    {
		$db_table = $pre."ad_position";
        $sql = "SELECT position_name  FROM ".$db_table." WHERE position_id =$val[position_id]";
		$position_name =  $oPub->getOne($sql);
        $row[$key]['position_name'] = ($position_name)?$position_name:'站外广告';
		$row[$key]['media_type']    =  $Amedia_type[$val[media_type]];	
		
		$db_table = $pre."sysconfig";
        $sql = "SELECT main_domin,header_title FROM ".$db_table." WHERE scid=$val[domain_id]";
		$row2 = $oPub->getRow($sql);
		$row[$key]['main_domin'] = $row2[main_domin];
		$row[$key]['header_title'] = ($row2[header_title])?$row2[header_title]:$row2[main_domin];
    }
}
$StrtypeAll = '';
$n = 0;

if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left><A HREF="http://'.$val["main_domin"].'/'.$SUBPATH.'" target="_blank">'.$val["header_title"].'</A></TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["ad_name"].'</TD>';

	   $StrtypeAll .= '<TD align=left>'.$val["position_name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["media_type"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["start_date"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["end_date"].'</TD>';
	   $StrtypeAll .= '<TD align=left><A HREF="ad_affiche.php?ad_id='.$val["ad_id"].'" target="_blank">'.$val["click_count"].'</A></TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="ad_send.php?ad_id='.$val["ad_id"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?ad_id='.$val["ad_id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       $StrtypeAll .= '</TD></TR>';    
}




?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
	
<TABLE width="99%" border=0>
  <TR class=bg5 style=" font-weight:bold ">
    <TD width="10%" align=left>来源</TD>
    <TD width="20%" align=left>广告名称</TD>
	<TD width="20%" align=left>广告位置</TD>
	<TD width="10%" align=left>类型</TD>
	<TD width="10%" align=left>开始日期</TD>
	<TD width="10%" align=left>结束日期</TD>
	<TD width="10%" align=left>统计</TD>
	<TD width="10%" align=left>操作</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="8">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>
<?php
include_once( "footer.php");
?>