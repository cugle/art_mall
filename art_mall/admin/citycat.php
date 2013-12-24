<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."citycat";
//post
if( $_POST['action'] == 'add'  )
{
	$_POST['name'] = str_replace('，',',',$_POST['name']);
    $Aname = explode(",",$_POST['name']);
    foreach( $Aname as  $vn )
	{ 
		$Afields=array('fid'=>$_POST['fid'],'next_node'=>'','name'=>trim($vn),'descs'=>$_POST['descs'],'allow'=>1,'domain_id'=>$Aconf['domain_id']);
		$tccid = $oPub->install($db_table,$Afields);
 
        $sql = "SELECT ccid FROM ".$db_table." where fid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["ccid"].',';
		}
		if(!empty($next_node )){
			$next_node  = substr($next_node ,0,-1);
			$Afields=array('next_node'=>$next_node);
			$condition = "ccid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
			$oPub->update($db_table,$Afields,$condition);
		}

		unset($Anorm);
	}
}

if( $_POST['action'] == 'edit' && $_POST['ccid'] != $_POST['fid']){
	$_POST['ccid'] = $_POST['ccid'] +0;
	$db_table = $pre."citycat";
	$sql = "SELECT fid FROM ".$db_table.' where ccid='.$_POST["ccid"].' AND domain_id='.$Aconf['domain_id'];
	$old_fid = $oPub->getOne($sql); 
	$condition =' ccid='.$_POST["ccid"].' AND domain_id='.$Aconf['domain_id'];
	$Afields=array('fid'=>$_POST['fid'],'name'=>$_POST['name'],'descs'=>$_POST['descs']);
	$oPub->update($db_table,$Afields,$condition); 

	if($old_fid > 0){ 
        $sql = "SELECT ccid FROM ".$db_table." where fid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["ccid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "ccid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($db_table,$Afields,$condition); 
	} 

	if($_POST['fid'] > 0){
        $sql = "SELECT ccid FROM ".$db_table." where fid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["ccid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "ccid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($db_table,$Afields,$condition);

	}
	 unset($Anorm);
	 unset($_GET);
}

//get
$db_table = $pre."citycat";
if( $_GET['action'] == 'edit'){
	$sql = "SELECT * FROM ".$db_table." where ccid = ".$_GET['ccid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	/*还有子分类将不能删除*/
	$strwhere = " where ccid = ".$_GET['ccid']." AND domain_id=".$Aconf['domain_id'];
	$sql = "SELECT  next_node FROM ".$db_table.$strwhere;
	$Anorm = $oPub->getRow($sql);
	if($Anorm[next_node] == '' && $_GET['fid'] > 0) {
		/* 上级分类标识整理 */
		$condition = 'ccid='.$_GET['ccid']." AND domain_id=".$Aconf['domain_id'];
		$oPub->delete($db_table,$condition);

		$strwhere = " where fid = ".$_GET['fid']." AND domain_id=".$Aconf['domain_id'];
		$sql = "SELECT ccid FROM ".$pre."citycat ".$strwhere;
		$row = $oPub->select($sql);
		$next_node = '';
		while( @list( $key, $value ) = @each( $row ) ) {
			$next_node .= $value[ccid].',';
		}
		if(!empty($next_node)){
			$next_node .= substr($next_node,0,-1);
		}
 
		$Afields=array('next_node'=>$next_node);
		$condition = "ccid = ".$_GET['fid'];
		$oPub->update($db_table,$Afields,$condition);
		unset($Anorm); 
	} elseif($_GET['fid'] < 1 && $Anorm[next_node] == '') {
	  $condition = 'ccid='.$_GET['ccid'].' AND domain_id='.$Aconf['domain_id'];
      $oPub->delete($db_table,$condition);
	} else {
       $strMessage = '存在下级分类，不能删除。';
	}
} 

/* 找到所有的分类到select start*/
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY ccid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="fid">';
$Stropt .= '<OPTION VALUE="0" >顶级分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_GET['fid'] == $value["ccid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["ccid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$_GET['fid'] );
	   }	   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/

$where = "fid = 0 AND domain_id=".$Aconf['domain_id'];
$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage; 
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table." where $where ORDER BY ccid ASC LIMIT ". $strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.' >';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?ccid='.$value["ccid"].'&fid=0&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?ccid='.$value["ccid"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a></TD>';

       $StrtypeAll .= '<TD align=left>'.$n.'、';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?ccid='.$value["ccid"].'">';
	   $StrtypeAll .= $value["name"].'</a>';
	   $StrtypeAll .= '</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["descs"].'</TD>'; 
       $StrtypeAll .= '</TR>';  
	   
	   /* 查找儿子 */
	   $StrtypeAll .= '<div style="display:none">';
       if($value["next_node"] != ''){          
           $StrtypeAll .= tab_next_node($value["next_node"],$value["ccid"]);
	   }
	   $StrtypeAll .='</div>'; 
	   /* 查找儿子 */
}

?>

<?php
   include_once( "header.php");
   if ($strMessage != '')
{
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<DIV class=content>
<TABLE width="100%" border=0>
  <TR>
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="3">
        <span style="font-weight: bold">城市分类:</span>
     	<input name="name" type="text" value="<?php echo ($Anorm['ccid'])?$Anorm['name']:''?>" size="80"/><U>[注:如果一次添加多个分类，每个分类请用小写逗号","分割]</U>
		<br/>
		<?php echo ($_GET[fid] == 0 && $_GET[action] == 'edit')?'':'<span style="font-weight: bold">选择上级分类:</span>'.$Stropt;?><span style="font-weight: bold">分类描述:</span>
     	<input name="descs" type="text" value="<?php echo ($Anorm['ccid'])?$Anorm['descs']:''?>" size="50"/>
        <input type="hidden" name="action" value="<?php echo ($Anorm['ccid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['ccid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="ccid" value="<?php echo ($Anorm['ccid'])?$Anorm['ccid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="10%" align=left>操作</TD>
    <TD width="30%" align=left>城市分类</TD>
	<TD width="60%" align=left>描述(用于搜索)</TD>  
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="3" align=right><?php  echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　　')
{
   global $oPub,$pre;
   $db_table = $pre.'citycat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
         if ($v == 0 && $v =='') {
              break;
		 }		   
		 $sql = "SELECT * FROM ".$db_table." where ccid = $v";
         $Anorm = $oPub->getRow($sql);
		 if( $Anorm["name"] != ''){
			$tn ++;
			$selected = ($fid == $v)? 'SELECTED':'';
			$Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
			$Stropt .= get_next_node($Anorm["next_node"],$fid,$str);
		 } 
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　　')
{
   global $oPub,$pre;
   $db_table = $pre.'citycat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where ccid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?ccid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?ccid='.$v.'&fid='.$fid.'&action=del" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["name"].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>'; 

              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str);
			}
	   }
	}
	return $Strtab;
}
?>	

<?php
include_once( "footer.php");
?>