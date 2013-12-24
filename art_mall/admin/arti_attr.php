<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."arti_attr";
//post
if( $_POST['action'] == 'add'  )
{
	$Afields=array('attr_name'=>$_POST['attr_name'],'domain_id'=>$Aconf['domain_id']);
    $taaid = $oPub->install($db_table,$Afields);
	unset($Anorm);
}

if( $_POST['action'] == 'edit'){
	$_POST["aaid"] = $_POST["aaid"] + 0;
	 $Afields=array('attr_name'=>$_POST['attr_name']);
	 $condition = 'aaid='.$_POST["aaid"].' AND domain_id='.$Aconf['domain_id'];
     $oPub->update($db_table,$Afields,$condition);
	 unset($Anorm);
	 unset($_GET);
}

//get
if( $_GET['action'] == 'edit'){
	$sql = "SELECT * FROM ".$db_table." where aaid = ".$_GET['aaid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	/*还有子属性将不能删除*/
	$condition = 'aaid='.$_GET['aaid'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->delete($db_table,$condition);
}

if ($strMessage != '')
{
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}

/* 是否显示 */

$sql = "SELECT * FROM ".$db_table." WHERE domain_id='".$Aconf['domain_id']."' ORDER BY aaid ASC";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) )
{
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value['attr_name'].'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?aaid='.$value["aaid"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?aaid='.$value["aaid"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
       $StrtypeAll .= '</TR>';     
}

?>

<?php
   include_once( "header.php");
?>
<DIV class=content>
<TABLE width="99%" border=0>
  <TR class="odd" >
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="2">
        <span>文章属性:</span>
     	<input name="attr_name" type="text" value="<?php echo ($Anorm['aaid'])?$Anorm['attr_name']:''?>" />	
        <input type="hidden" name="action" value="<?php echo ($Anorm['aaid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['aaid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="aaid" value="<?php echo ($Anorm['aaid'])?$Anorm['aaid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="60%" align=left>文章属性</TD>
    <TD width="40%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="2" align=right><?php //echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<BR/> 

<?php
include_once( "footer.php");
?>