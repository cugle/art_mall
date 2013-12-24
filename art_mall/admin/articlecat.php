<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."articat";
//post
if( $_POST['action'] == 'add'  )
{
	$Afields=array('fid'=>$_POST['fid'],'next_node'=>'','name'=>$_POST['name'],'descs'=>$_POST['descs'],'keywords'=>$_POST['keywords'],'ifnav'=>$_POST['ifnav'],'allowjob'=>$_POST['allowjob'],'domain_id'=>$Aconf['domain_id']);
    $tacid = $oPub->install($db_table,$Afields);
    if($tacid) {

        $sql = "SELECT acid FROM ".$db_table." where fid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["acid"].',';
		}
		if(!empty($next_node )){
			$next_node  = substr($next_node ,0,-1);
			$Afields=array('next_node'=>$next_node);
			$condition = "acid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
			$oPub->update($db_table,$Afields,$condition);
		}
	    /* 导航条显示 */
	    if($_POST['ifnav'])
	    {
			$db_table = $pre."nav"; 
			$url = 'articles.php?acid='.$tacid;
			$Afields=array('name'=>$_POST['name'],'url'=>$url,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields);
	    }
	}
	unset($Anorm);
}

if( $_POST['action'] == 'edit' && $_POST["acid"] > 0 && $_POST['acid'] != $_POST['fid']){
	$_POST["acid"] = $_POST["acid"] + 0;
	$db_table = $pre."articat"; 
	$sql = "SELECT fid FROM ".$db_table.' where acid='.$_POST["acid"].' AND domain_id='.$Aconf['domain_id'];
	$old_fid = $oPub->getOne($sql); 
	$condition =' acid='.$_POST["acid"].' AND domain_id='.$Aconf['domain_id'];
	$Afields=array('fid'=>$_POST['fid'],'name'=>$_POST['name'],'descs'=>$_POST['descs'],'keywords'=>$_POST['keywords'],'ifnav'=>$_POST['ifnav'],'allowjob'=>$_POST['allowjob']); 
	$oPub->update($db_table,$Afields,$condition); 
	if($old_fid > 0){ 
        $sql = "SELECT acid FROM ".$db_table." where fid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["acid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "acid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($db_table,$Afields,$condition); 
	} 

	if($_POST['fid'] > 0){
        $sql = "SELECT acid FROM ".$db_table." where fid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["acid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "acid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($db_table,$Afields,$condition);

	}
 
	/* 导航条显示 */
	$db_table = $pre."nav"; 
	if($Anorm['ifnav']) {
		if(!$_POST['ifnav']) {
			$url = "articles.php?acid=".$_POST["acid"];
			$condition = 'url="'.$url.'" AND domain_id='.$Aconf['domain_id'];
			$oPub->delete($db_table,$condition);
		}
	} else {
		if($_POST['ifnav']) {
			$url = "articles.php?acid=".$_POST["acid"];
			$Afields=array('name'=>$_POST['name'],'url'=>$url,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($db_table,$Afields);
		}
	}

	 unset($Anorm);
	 unset($_GET);
}

//get
$db_table = $pre."articat";
if( $_GET['action'] == 'edit'){
	$sql = "SELECT * FROM ".$pre."articat where acid = ".$_GET['acid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	/*还有子分类将不能删除*/
	$strwhere = " where acid = ".$_GET['acid']." AND domain_id=".$Aconf['domain_id'];
	$sql = "SELECT next_node FROM ".$db_table.$strwhere;
	$Anorm = $oPub->getRow($sql);
	if($Anorm[next_node] == '' && $_GET['fid'] != 0) {
	  /* 上级分类标识整理 */
	  $strwhere = " where acid = ".$_GET['fid']." AND domain_id=".$Aconf['domain_id'];
	  $sql = "SELECT * FROM ".$db_table.$strwhere;
	  $Anorm = $oPub->getRow($sql);
	  $next_node = str_replace(','.$_GET['acid'],'',$Anorm['next_node']);
	  $next_node = str_replace($_GET['acid'],'',$next_node);
      $Afields=array('next_node'=>$next_node);
	  $condition = "acid = ".$Anorm['acid'];
	  $oPub->update($pre."articat",$Afields,$condition);
      unset($Anorm);

	  $condition = 'acid='.$_GET['acid']." AND domain_id=".$Aconf['domain_id'];
      $oPub->delete($db_table,$condition);
	} elseif($_GET['fid'] == 0 && $Anorm[next_node] == '') {
	  $condition = 'acid='.$_GET['acid'].' AND domain_id='.$Aconf['domain_id'];
      $oPub->delete($pre."articat",$condition);
	} else
	{
       $strMessage = '存在下级分类，不能删除。';
	}
} 

$ifnav_1 = ($Anorm[ifnav] == 1)? 'SELECTED':'';
$ifnav_0 = ($Anorm[ifnav] == 0)? 'SELECTED':''; 

$Strifnavopt = '<SELECT NAME="ifnav">';
$Strifnavopt .= '<OPTION VALUE="1" '.$ifnav_1.'>是</OPTION>';
$Strifnavopt .= '<OPTION VALUE="0" '.$ifnav_0.'>否</OPTION>';
$Strifnavopt .= '</SELECT>';

$allowjob_1 = ($Anorm['allowjob'] == 1)? 'SELECTED':'';
$allowjob_0 = ($Anorm['allowjob'] <  1)? 'SELECTED':''; 

$Strallowjobopt = '<SELECT NAME="allowjob">';
$Strallowjobopt .= '<OPTION VALUE="1" '.$allowjob_1.'>是</OPTION>';
$Strallowjobopt .= '<OPTION VALUE="0" '.$allowjob_0.'>否</OPTION>';
$Strallowjobopt .= '</SELECT>';

/* 找到所有的分类到select start*/
$sql = "SELECT * FROM ".$pre."articat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);
$Stropt = '<SELECT NAME="fid">';
$Stropt .= '<OPTION VALUE="0" >顶级分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_GET['fid'] == $value["acid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$_GET['fid'] );
	   }	   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/

$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$n.'、'.$value["name"].' ID:'.$value["acid"].'</TD>';

		//分类加密
		/* 找到所有的分类到select start*/ 
		$Auserstype = $oPub->select("SELECT name,orders FROM ".$pre."userstype where  domain_id=".$Aconf['domain_id']." ORDER BY orders ASC"); 
		$Stroptx = '<span id="utid_'.$value["acid"].'">';
		$Stroptx .= '<SELECT NAME="utid" onchange="acc_edit(\'utid\',\''.$value["acid"].'\',this.options[this.options.selectedIndex].value)">';  
		$tmp = $val["utid"] < 1 ?'selected':'';
		$Stroptx .= '<OPTION VALUE="0" '.$tmp.' >游客浏览</OPTION>';
		if($Auserstype)
		{
			foreach ($Auserstype AS $k=>$v)
			{
				$selected = ($v['orders'] == $value['utid'])? 'SELECTED':'';
				$Stroptx .= '<OPTION VALUE="'.$v['orders'].'" '.$selected.' >'.$v['name'].'</OPTION>'; 
			}
		}
		$Stroptx .= '</SELECT>';
		$Stroptx .= '</span>';
		/* 找到所有的分类到select end*/ 


	   //$StrtypeAll .= '<TD align=left>'.$Stroptx.'</TD>';

	   $StrtypeAll .= '<TD align=left>'.($value["allowjob"]?'是':'否').'</TD>';

	   $StrtypeAll .= '<TD align=left>'.$value["descs"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["keywords"].'</TD>';
	   $tmp = ($value["ifnav"])?'是':'否';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';

       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?acid='.$value["acid"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?acid='.$value["acid"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
       $StrtypeAll .= '</TR>';  
	   
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $StrtypeAll .= tab_next_node($value["next_node"],$value["acid"]);
	   }
	   /* 查找儿子 */ 
	   
}

?>

<?php
	include_once( "header.php");
	if ($strMessage != '') 
		echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>'; 
?>
<DIV class=content>
<TABLE width="100%" border=0>
  <TR class="odd" >
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="6">
        <span>文章分类:</span>
     	<input name="name" type="text" value="<?php echo ($Anorm['acid'])?$Anorm['name']:''?>" /> 
		<span>导航条显示:</span>
		<?php echo $Strifnavopt;?>
		<span>显示求职申请:</span>
		<?php echo $Strallowjobopt;?>		 
		<span>选择上级分类:</span> <?php echo $Stropt;?>
		<span>描述:</span>
     	<input name="descs" type="text" value="<?php echo ($Anorm['acid'])?$Anorm['descs']:''?>" size="30"/> 
		关键字:<input name="keywords" type="text" value="<?php echo ($Anorm['acid'])?$Anorm['keywords']:''?>" size="30"/>
        <input type="hidden" name="action" value="<?php echo ($Anorm['acid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['acid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="acid" value="<?php echo ($Anorm['acid'])?$Anorm['acid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="20%" align=left>文章分类</TD>
	<!--
	<TD width="20%" align=left title="注：此处设置文章分类没有层级关系，请直接选择到文章直接对应的最底层分类。但用户级别有层级关系，高级用户包含低级用户权限。">访问权限设置 ? </TD>
	-->
	<TD width="10%" align=left title="注：将在文章最终页，显示求职申请表接口。">显示求职申请 ? </TD>
	<TD width="20%" align=left>描述(用于搜索)</TD>
	<TD width="10%" align=left>关键字(用于搜索)</TD>
	<TD width="10%" align=left>导航条显示</TD>
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>  

</TABLE>
 
</DIV> 

<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<script type="text/javascript" language="JavaScript">
 

 function acc_edit(edit,acid,edit_val)
  {
     obj = edit + "_" + acid;
     var strTemp = "ajax_acc_edit.php?op=" + edit + "&acid=" + acid + "&edit_val=" + escape(edit_val);
	 //alert(strTemp);
	 send_request(strTemp);
  }
</script>

<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where acid = $v";
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
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre,$Aconf;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where acid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
				$Strtab  .= '<TR class='.$tmpstr.'>';

				$Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["name"].' ID:'.$Anorm["acid"].'</TD>';


				/* 找到所有的分类到select start*/ 
				$Auserstype = $oPub->select("SELECT name,orders FROM ".$pre."userstype where  domain_id=".$Aconf['domain_id']." ORDER BY orders ASC"); 
				$Stroptx = '<span id="utid_'.$Anorm["acid"].'">';
					$Stroptx .= '<SELECT NAME="utid" onchange="acc_edit(\'utid\',\''.$Anorm["acid"].'\',this.options[this.options.selectedIndex].value)">';  
					$tmp = $Anorm["utid"] < 1 ?'selected':'';
					$Stroptx .= '<OPTION VALUE="0" '.$tmp.' >游客浏览</OPTION>';
					if($Auserstype)
					{
						foreach ($Auserstype AS $kx=>$vx)
						{
							$selected = ($vx['orders'] == $Anorm['utid'])? 'SELECTED':'';
							$Stroptx .= '<OPTION VALUE="'.$vx['orders'].'" '.$selected.' >'.$vx['name'].'</OPTION>'; 
						}
					}
					$Stroptx .= '</SELECT>';
				$Stroptx .= '</span>';
				/* 找到所有的分类到select end*/ 
			 //$Strtab  .= '<TD align=left>'.$Stroptx.'</TD>';

			  $Strtab .= '<TD align=left>'.($Anorm["allowjob"]?'是':'否').'</TD>';

			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>'; 
			  $Strtab  .= '<TD align=left>'.$Anorm["keywords"].'</TD>';
	          $tmp = ($Anorm["ifnav"])?'是':'否';
	          $Strtab .= '<TD align=left>'.$tmp.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
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