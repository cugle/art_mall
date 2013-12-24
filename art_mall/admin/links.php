<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."links";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit' )
{
	/*处理图片*/
	if($_FILES['lk_logo']['size'] > 0 )
	{
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['lk_logo']['type']))
        {
            $strMessage =  '图片类型错误';
			$img_name = $_POST['old_lk_logo'];
        }
		else
		{

	       if(!empty($_POST['old_lk_logo']))
	       {
               $img_name = basename($image->upload_image($_FILES['lk_logo'],'links',$_POST['old_lk_logo']));
	       }
	       else
	       {
		       $img_name = basename($image->upload_image($_FILES['lk_logo'],'links'));

	       }
		}
	}
	else
	{
		$img_name = $_POST['old_lk_logo'];
	}
	$_POST['site_url']= str_replace('http://','', $_POST['site_url']); 
	$_POST['site_url'] = 'http://'.$_POST['site_url'];

	if($_POST['action'] == 'add' && $_POST['site_url'] && $_POST['lk_name'])
	{
	    $sql = "SELECT lkid FROM ".$db_table." 
	            where (lk_name = '".$_POST['lk_name']."' 
			    OR  site_url = '".$_POST['site_url']."')  
			    AND domain_id=".$Aconf['domain_id'].
			    " LIMIT 1";
	    $Anorm = $oPub->getRow($sql);
	    if($Anorm[lkid] > 0 )
	    {
		    $strMessage = '此连接已存在,不能重复添加';
	    }
	    else
	    {
	        $Afields=array('lk_name'=>$_POST['lk_name'],'lk_logo'=>$img_name,'lk_desc'=>$_POST['lk_desc'],'site_url'=>$_POST['site_url'],'sort_order'=>$_POST['sort_order'],'colors'=>$_POST['colors'],'domain_id'=>$Aconf['domain_id']);
            $tlkid = $oPub->install($db_table,$Afields);
		    $strMessage = '添加成功';
	    }
	    
	} else if($_POST['action'] == 'edit' && $_POST['lkid'] ) {
          $Afields=array('lk_name'=>$_POST['lk_name'],'lk_logo'=>$img_name,'lk_desc'=>$_POST['lk_desc'],'site_url'=>$_POST['site_url'],'sort_order'=>$_POST['sort_order'],'colors'=>$_POST['colors']);
	      $condition = "lkid = ".$_POST['lkid']." AND domain_id=".$Aconf['domain_id'];
	      $oPub->update($db_table,$Afields,$condition);
	      
	}
	unset($Anorm);unset($_POST);
}

//get
$db_table = $pre."links";
if( $_GET['action'] == 'edit'){
	$sql = "SELECT * FROM ".$db_table." where lkid = ".$_GET['lkid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_REQUEST['action'] == 'del'){
    $db_table = $pre."links"; 
    if (isset($_POST['checkboxes']))
    {
        $count = 0;
		$strid = '';
        foreach ($_POST['checkboxes'] AS $key => $id)
        {	
			$id = $id+0;
			$condition = "lkid='".$id."' AND domain_id='".$Aconf['domain_id']."'"; 
			/* 删除缩图 */
			$sql = "SELECT lk_logo  FROM " . $db_table . " WHERE ".$condition;
			$lk_logo = $oPub->getOne($sql);
			if (is_file('../data/links/' .$lk_logo)) {
			@unlink('../data/links/' .$lk_logo);
			} 
			$oPub->delete($db_table,$condition);
			$strid .= $id.',';;
        }
        $tmpID = ($strid)?substr($strid,0,-1):''; 
		$change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
		$change_desc .= ' | '.$_SESSION['auser_name'].' 连接批量删除:'.$tmpID;
		$Afields=array('user_id'=>$Auser['user_id'],'type'=>'linksDel','change_desc'=>$change_desc,'states'=>0,'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."account_log",$Afields);
   } else if(isset($_GET['lkid']))
   {

		$condition = 'lkid='.$_GET['lkid'].' AND domain_id='.$Aconf['domain_id'];
		$sql = "SELECT lk_logo  FROM " . $db_table . " WHERE ".$condition;
		$lk_logo = $oPub->getOne($sql);
		if (is_file('../data/links/' .$lk_logo)) {
			@unlink('../data/links/' .$lk_logo);
		} 
		$oPub->delete($db_table,$condition);
		$tmpID = $_GET['lkid']; 
	 
		$change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
		$change_desc .= ' | '.$_SESSION['auser_name'].' 日志删除:'.$tmpID;
		$Afields=array('user_id'=>$Auser['user_id'],'type'=>'linksDel','change_desc'=>$change_desc,'states'=>0,'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre."account_log",$Afields);
   }
}


 
//page
$strWhere = " WHERE domain_id=".$Aconf['domain_id'];
$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table." WHERE domain_id=".$Aconf['domain_id']." ORDER BY sort_order,lkid ASC limit ".$strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["lkid"].']" value="'.$value["lkid"].'" />';
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left><span style="color: '.$value["colors"].'">'.$value["lk_name"].'</span></TD>';
	   $tmp = ($value["lk_logo"] != '')?'<IMG SRC="../data/links/'.$value["lk_logo"].'" WIDTH="88" HEIGHT="32" BORDER="0" ALT="编辑">':'';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["site_url"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["lk_desc"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["colors"].'</TD>';
 
	   $StrtypeAll .= '<TD align=left>'.$value["sort_order"].'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?lkid='.$value["lkid"].'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?lkid='.$value["lkid"].'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
       $StrtypeAll .= '</TR>';  	   
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
<form name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]?>" style="margin: 0">
<TABLE width="100%" border=0>
  <TR> 
    <TD align="left">

        <span style="font-weight: bold">网站名:</span>
     	<input name="lk_name" type="text" value="<?php echo ($Anorm['lkid'])?$Anorm['lk_name']:''?>" size="10" />
        <span style="font-weight: bold">网址:</span>
     	<input name="site_url" type="text" value="<?php echo ($Anorm['lkid'])?$Anorm['site_url']:'http://'?>" size="35" />
        <span style="font-weight: bold">排序:</span>		
     	<input name="sort_order" type="text" value="<?php echo ($Anorm['lkid'])?$Anorm['sort_order']:0;?>" size="3" />
        <span style="font-weight:bold">颜色:</span>
		<SELECT NAME="colors">
			<OPTION VALUE=""  <?php echo (!$Anorm['colors'])?'SELECTED':'';?>>默认</OPTION>
			<OPTION VALUE="#FF0000" style="color:#FF0000" <?php echo ($Anorm['colors']=='#FF0000')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#00FF00" style="color:#00FF00" <?php echo ($Anorm['colors']=='#00FF00')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#0000FF" style="color:#0000FF" <?php echo ($Anorm['colors']=='#0000FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#000000" style="color:#000000" <?php echo ($Anorm['colors']=='#000000')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#FF6600" style="color:#FF6600" <?php echo ($Anorm['colors']=='#FF6600')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#33CC00" style="color:#33CC00" <?php echo ($Anorm['colors']=='#33CC00')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#0066FF" style="color:#0066FF" <?php echo ($Anorm['colors']=='#0066FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#CC3333" style="color:#CC3333" <?php echo ($Anorm['colors']=='#CC3333')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#3399FF" style="color:#3399FF" <?php echo ($Anorm['colors']=='#3399FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#CC6666" style="color:#CC6666" <?php echo ($Anorm['colors']=='#CC6666')?'SELECTED':'';?>>■■■</OPTION>
		</SELECT>
		<br/>
        <span style="font-weight: bold">&nbsp;&nbsp;&nbsp;&nbsp;描述:</span>		
     	 <TEXTAREA NAME="lk_desc" ROWS="2" COLS="20"><?php echo ($Anorm['lkid'])?$Anorm['lk_desc']:''?></TEXTAREA>
 
		<br/>
		<span style="font-weight: bold">LOGO:</span>
		<INPUT type="file" name="lk_logo" size="20" /> (注：logo尺寸为 88*32px 支持：.jpg .gif .png 格式)
		<INPUT type="hidden" name="old_lk_logo"  value="<?php echo ($Anorm['lkid'])?$Anorm['lk_logo']:'';?>" />
        
		<br/>
        <input type="hidden" name="action" value="<?php echo ($Anorm['lkid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['lkid'])?' 连接编辑 ':' 连接增加 ' ?>" style="background-color: #FFCC66;margin-left:50px"/>
		<input type="hidden" name="lkid" value="<?php echo ($Anorm['lkid'])?$Anorm['lkid']:'0'?>" />  
    </TD> 
  </TR>
</TABLE>  
</form>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self" style="margin: 0">
<TABLE width="100%" border=0>
  <TR class=bg5>
		<TD align=left>序号</TD>
		<TD align=left>网站名</TD>
		<TD align=left>LOGO</TD>
		<TD align=left>网址</TD>
		<TD align=left>描述</TD>
		<TD align=left>颜色</TD>
		<TD align=left>排序</TD>
		<TD align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>


</TABLE> 

<TABLE width="100%" border=0>
  <TR class=bg5>
    <TD align=right>
  	<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" >
	<INPUT TYPE="reset" name="reset" value="恢复"> 
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
</TABLE>
</form> 
</DIV>
<script type="text/javascript" language="JavaScript"> 
function selectAll(){
	xx = listForm.check_all.checked
	for(var i=0;i<listForm.length;i++)
	{
		if(listForm.elements[i].type=="checkbox")
			listForm.elements[i].checked=xx;
	}
} 
</script>
<?php
include_once( "footer.php");
?>
