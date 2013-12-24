<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 

if( $_SESSION['apraid'] < 1)
{
   $strMessage = '此账号没有绑定经销商，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
   echo  showMessage($strMessage);
   exit;
}

 $praid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1"); 
 if( $praid < 1)
 {
	$strMessage = '此经销商已不存在，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
	echo  showMessage($strMessage);
	exit;
}
/*------------------------------------------------------ */
//-- 批量删除文章记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;		
        foreach ($_POST['checkboxes'] AS $key => $id)
        {	
		  $id = $id+0;
          $db_table = $pre."pravail_artitxt";
          $sql = "UPDATE ".$db_table." SET states='1' ".
                 " WHERE arid=$id AND domain_id='".$Aconf['domain_id']."'";
          $oPub->query($sql);

		  $db_table = $pre."pravail_article";
          $sql = "UPDATE ".$db_table." SET states='1' ".
                 " WHERE arid=$id AND domain_id='".$Aconf['domain_id']."'";
           $oPub->query($sql);
        }
		$tmpID = implode(",",$_POST['checkboxes']);
        $strMessage =  "批量删除成功!";
   }
   else if(isset($_GET['arid']))
   {
	     $_GET['arid'] = $_GET['arid']+0;
        $id = $_GET['arid'];
		$db_table = $pre."pravail_artitxt";
        $sql = "UPDATE ".$db_table." SET states='1' ".
               " WHERE arid=$id AND domain_id='".$Aconf['domain_id']."'";
        $oPub->query($sql);
		$db_table = $pre."pravail_article";
        $sql = "UPDATE ".$db_table." SET states='1' ".
                 " WHERE arid=$id AND domain_id='".$Aconf['domain_id']."'";
        $oPub->query($sql);

		$tmpID = $id;

		$strMessage =  "删除成功!";
   }
   else
   {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

}

/* 查询条件 */
$db_table = $pre."pravail_artitxt";
$filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'dateadd' : trim($_REQUEST['sort_by']);
$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

$where = "states=0 AND praid = '".$_SESSION['apraid']."' AND domain_id = '".$Aconf['domain_id']."'";



$db_table = $pre."pravail_artitxt";
$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('acid'=>$_REQUEST[acid],'sort_by'=>$filter['sort_by']); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
       " ORDER BY ".$filter['sort_by']." ".$filter['sort_order'].
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["arid"].']" value="'.$val["arid"].'" />';
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left>'.$val["name"].'</TD>';

	   $StrtypeAll .= '<TD align=left>'.date("m月d日",$val["dateadd"]).'</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="../shop_a.php?id='.$_SESSION['apraid'].'&arid='.$val["arid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读"></a> _ ';
	   $StrtypeAll .= '<a href="prav_articlesend.php?arid='.$val["arid"].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?arid='.$val["arid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
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
 
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="middle">
   <span style="float: left"><a href="prav_articlelist.php"> [促销信息文章列表]</a>        
   <?php echo $Stropt;?>
   </span>
   <span style="float: right"><a href="prav_articlesend.php" style="color:#FF0000">  [添加促销信息]</a> </span>
 </td>
</tr>
</table>
<TABLE width="100%" border=0>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="5%" align=left>序号</TD>
    <TD width="50%" align=left>标题</TD>
	<TD width="15%" align=left>时间</TD>
	<TD width="30%" align=center>操作</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="4">
	<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #FF9900">
	<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CCFF99"> 
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
  </form>
 </table>
<?php
include_once( "footer.php");
?>
