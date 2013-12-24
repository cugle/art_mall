<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."prattcat";
//post
if( $_POST['action'] == 'add'  )
{
	$Afields=array('paname'=>$_POST['paname'],'enabled'=>$_POST['enabled'],'domain_id'=>$Aconf['domain_id']);
    $tpacid = $oPub->install($db_table,$Afields);
}

if( $_POST['action'] == 'edit'){
	$_POST['pacid'] = $_POST['pacid'] +0;
      $Afields=array('paname'=>$_POST['paname'],'enabled'=>$_POST['enabled']);
	  $condition = "pacid = ".$_POST['pacid']." AND domain_id=".$Aconf['domain_id'];
	  $oPub->update($db_table,$Afields,$condition);
	 unset($_GET);
}

//get
$db_table = $pre."prattcat";
if( $_GET['action'] == 'edit'){
	$_GET['pacid'] = $_GET['pacid'] +0;
	$sql = "SELECT * FROM ".$db_table." where pacid = ".$_GET['pacid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	$_GET['pacid'] = $_GET['pacid']+0;
	$condition = 'pacid='.$_GET['pacid'].' AND domain_id='.$Aconf['domain_id'];
	$db_table = $pre."prattri";
	$oPub->delete($db_table,$condition);

    $db_table = $pre."prattcat";
    $condition = 'pacid='.$_GET['pacid'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->delete($db_table,$condition);
}

if ($strMessage != '')
{
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}


/* 是否显示 */
$db_table = $pre."prattcat";
if($Anorm){
  $enabled_1 = ($Anorm[enabled] == 1)? 'SELECTED':'';
  $enabled_0 = ($Anorm[enabled] == 0)? 'SELECTED':'';
}else{
   $enabled_1 =  'SELECTED';
}

$Strenabledopt = '<SELECT name="enabled">';
$Strenabledopt .= '<OPTION VALUE="1" '.$enabled_1.'>是</OPTION>';
$Strenabledopt .= '<OPTION VALUE="0" '.$enabled_0.'>否</OPTION>';
$Strenabledopt .= '</SELECT>';

/* 只显示自己设定的属性 */
//$sql = "SELECT * FROM ".$db_table." WHERE domain_id=".$Aconf['domain_id']." ORDER BY pacid ASC";
/* 显示所有属性 */
//page
$strWhere = "";
$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();
$sql = "SELECT * FROM ".$db_table."  ORDER BY pacid ASC limit ".$strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value["paname"].'</TD>';
	   $tmp = ($value["enabled"])?'是':'否';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $StrtypeAll .= '<TD align=left><A HREF="product_attriblist.php?pacid='.$value["pacid"].'">属性列表</A></TD><TD align=left>';

       if($value["domain_id"] == $Aconf['domain_id'])
	  {
           $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?pacid='.$value["pacid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	       $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?pacid='.$value["pacid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
	  }
	  else
	  {
           $StrtypeAll .='公共属性'; 
	   }

       $StrtypeAll .= '</TD></TR>';  	   
}

?>

<?php
   include_once( "header.php");
?>

<DIV class=content>
<TABLE width="100%" border=0>
  <TR class="odd" >
  <form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>"> 
    <TD width="13%" align="left" colspan="4">
     	<input name="paname" type="text" value="<?php echo ($Anorm['pacid'])?$Anorm['paname']:''?>" />
		<span>显示:</span>
		<?php echo $Strenabledopt;?>
        <input type="hidden" name="action" value="<?php echo ($Anorm['pacid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['pacid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="pacid" value="<?php echo ($Anorm['pacid'])?$Anorm['pacid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="15%" align=left>分类</TD>
	<TD width="40%" align=left>显示</TD>
	<TD width="10%" align=left>属性列表</TD>
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="4" align=right><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<BR/> 
<?php
include_once( "footer.php");
?>