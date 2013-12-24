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

$db_table = $pre."probrand";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit' )
{
	/*处理图片*/
	if($_FILES['brand_logo']['size'] > 0 )
	{
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['brand_logo']['type']))
        {
            $strMessage =  '图片类型错误';
			$img_name = $_POST['old_brand_logo'];
        }
		else
		{

	       if(!empty($_POST['old_brand_logo']))
	       {
               $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo',$_POST['old_brand_logo']));
	       }
	       else
	       {
		       $img_name = basename($image->upload_image($_FILES['brand_logo'],'brandlogo'));

	       }
		}
	}
	else
	{
		$img_name = $_POST['old_brand_logo'];
	}
   
   if($_POST['site_url'])
   {
        $_POST['site_url'] = 	 str_replace('http://','', $_POST['site_url']);
		$_POST['site_url'] = "http://".$_POST['site_url'];
   }
   

	if($_POST['action'] == 'add'  && $_POST['brand_name'])
	{
	    $sql = "SELECT prbid FROM ".$db_table." 
	            where brand_name = '".$_POST['brand_name']."' 
			    AND domain_id=".$Aconf['domain_id'].
			    " LIMIT 1";
	    $Anorm = $oPub->getRow($sql);
	    if($Anorm[prbid] > 0 )
	    {
		    $strMessage = '此艺术家已存在,不能重复添加，it is exist,can not be add again';
	    }
	    else
	    {
	        $Afields=array('brand_name'=>$_POST['brand_name'],'brand_logo'=>$img_name,'brand_desc'=>$_POST['brand_desc'],'site_url'=>$_POST['site_url'],'sort_order'=>$_POST['sort_order'],'is_show'=>$_POST['is_show'],'domain_id'=>$Aconf['domain_id']);
            $tprbid = $oPub->install($db_table,$Afields);
		    $strMessage = '添加成功 add successful';
	    }

		//pprocat 品牌与分类对应列表
		$db_table = $pre."probrand_procat";
        while( @list( $k, $v ) = @each( $_POST[pprocat]) ) 
        {
 	        $Afields=array('prbid'=>$tprbid,'pcid'=>$v,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields);
        }
	    
	}
	else if($_POST['action'] == 'edit' && $_POST['prbid'] )
	{
		$db_table = $pre."probrand";
		$_POST['prbid'] = $_POST['prbid'] +0;
          $Afields=array('brand_name'=>$_POST['brand_name'],'brand_logo'=>$img_name,'brand_desc'=>$_POST['brand_desc'],'site_url'=>$_POST['site_url'],'sort_order'=>$_POST['sort_order'],'is_show'=>$_POST['is_show']);
	      $condition = "prbid = ".$_POST['prbid']." AND domain_id=".$Aconf['domain_id'];
	      $oPub->update($db_table,$Afields,$condition);

        /* 删除 品牌与分类对应列表 */
        $db_table = $pre."probrand_procat";
        $condition = 'prbid='.$_POST['prbid'].' AND domain_id='.$Aconf['domain_id'];
        $oPub->delete($db_table,$condition);
	    /* 重新添加 */
        while( @list( $k, $v ) = @each( $_POST[pprocat]) ) 
        {
 	        $Afields=array('prbid'=>$_POST['prbid'],'pcid'=>$v,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields);
        }
		
	}
	unset($Anorm);unset($_POST);
}

//get
$Apcid = array(); //品牌对应产品分类 pcid
$db_table = $pre."probrand";
if( $_GET['action'] == 'edit'){
	$_GET['prbid'] = $_GET['prbid'] + 0;
	$sql = "SELECT * FROM ".$db_table." where prbid = ".$_GET['prbid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
	/* 查找品牌对应产品分类 */
	$db_table = $pre."probrand_procat";
    $sql = "SELECT  pcid  FROM ".$db_table." where prbid = '".$_GET['prbid']."' AND domain_id=".$Aconf['domain_id']." ORDER BY pcid ASC";
    $row = $oPub->select($sql);	
    while( @list( $k, $v ) = @each( $row) ) 
    {
       array_push($Apcid, $v[pcid]);
	}
}

if( $_GET['action'] == 'del'){
    $db_table = $pre."probrand";
	$_GET['prbid'] = $_GET['prbid'] + 0;
    $condition = 'prbid='.$_GET['prbid'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->delete($db_table,$condition);
}

/* 品牌所属大类，只选择顶级分类，为复选框 */
$db_table = $pre."productcat";
$sql = "SELECT pcid,name FROM ".$db_table." where fid = 0 AND ifshow = 1 AND domain_id=".$Aconf['domain_id']." ORDER BY pcid ASC";
$AnormAll = $oPub->select($sql);
$strPro_pprocat_check = '';
while( @list( $k, $v ) = @each( $AnormAll) ) 
{
    $strChecked = (in_array($v[pcid],$Apcid))?'checked':'';
	$strPro_pprocat_check  .= '<INPUT TYPE="checkbox"  name="pprocat[]" value="'.$v[pcid].'" '.$strChecked.'>'.$v[name].' ';
}



/* 是否显示 */
$db_table = $pre."probrand";

if($Anorm){
   $is_show_1 = ($Anorm[is_show] == 1)? 'SELECTED':'';
   $is_show_0 = ($Anorm[is_show] == 0)? 'SELECTED':'';
}else{
   $is_show_1 =  'SELECTED';
}

$Stris_showopt = '<SELECT name="is_show">';
$Stris_showopt .= '<OPTION VALUE="1" '.$is_show_1.'>是</OPTION>';
$Stris_showopt .= '<OPTION VALUE="0" '.$is_show_0.'>否</OPTION>';
$Stris_showopt .= '</SELECT>';

//page
$strwhere = " domain_id='".$Aconf['domain_id']."'";
$sql = "SELECT count( * ) AS count FROM ".$db_table." where ".$strwhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table." where ".$strwhere." ORDER BY sort_order,prbid ASC limit ".$strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value["brand_name"].'</TD>';
	   $tmp = ($value["brand_logo"] != '')?'<IMG SRC="../data/brandlogo/'.$value["brand_logo"].'" WIDTH="88" HEIGHT="32" BORDER="0" ALT="编辑edit">':'';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["site_url"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["brand_desc"].'</TD>';
 
	   /* 查找品牌对应产品分类 */
/*	   $db_table = $pre."probrand_procat";
       $sql = "SELECT  b.name  FROM ".$db_table." as a,".$pre."productcat as b 
	        where a.prbid = '".$value['prbid']."' 
			AND a.domain_id=".$Aconf['domain_id']." 
			AND a.pcid = b.pcid 
			ORDER BY a.pcid ASC";
       $row = $oPub->select($sql);	
       while( @list( $k, $v ) = @each( $row) ) 
       {
           $strTmp .= $v[name].',';
	   }       
	   $StrtypeAll .= '<TD align=left>'.$strTmp.'</TD>';*/
	   $strTmp = '';

	   $tmp = ($value["is_show"])?'是yes':'否no';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["sort_order"].'</TD>';

       $StrtypeAll .= '<TD align=left>';
       if($value["domain_id"] == $Aconf['domain_id'])
	  {   
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?prbid='.$value["prbid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑edit"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?prbid='.$value["prbid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除delete"></a>';
	  }
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
<DIV class=content>
<TABLE width="100%" border=0>
  <TR>
  <form name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]?>"> 
    <TD align="left" colspan="8">
        <span style="font-weight: bold">艺术家artist:</span>
     	<input name="brand_name" type="text" value="<?php echo ($Anorm['prbid'])?$Anorm['brand_name']:''?>" size="20" />
        <span style="font-weight: bold"><br />
        <br />
        艺术家网址  artist url:</span>
     	<input name="site_url" type="text" value="<?php echo ($Anorm['prbid'])?$Anorm['site_url']:''?>" size="40" />
		<br />
		<br/>
       <!-- <span style="font-weight: bold">所属大类:</span>
        <?php echo $strPro_pprocat_check;?>
        <br/>-->
        <span style="font-weight: bold">艺术家简介artist resume:</span><br />		
     	 <TEXTAREA NAME="brand_desc" ROWS="3" COLS="20"><?php echo ($Anorm['prbid'])?$Anorm['brand_desc']:''?></TEXTAREA>
		 <br />
		 <br/>
        <span style="font-weight: bold">排序 order :</span>		
     	<input name="sort_order" type="text" value="<?php echo ($Anorm['prbid'])?$Anorm['sort_order']:5;?>" size="3" />
		<span style="font-weight: bold">是否显示display:</span>
		<?php echo $Stris_showopt;?>
		<span style="font-weight: bold"><br />
		<br />
		图片
		pic:</span>
		<INPUT type="file" name="brand_logo" style="width:140px" /> <br/>
		(注：图片尺寸为 125*90px 支持：.jpg .gif .png 格式，)
		<INPUT type="hidden" name="old_brand_logo"  value="<?php echo ($Anorm['prbid'])?$Anorm['brand_logo']:'';?>" />
        
		<br/>
        <input type="hidden" name="action" value="<?php echo ($Anorm['prbid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['prbid'])?' 编辑edit ':' 增加add ' ?>" style="background-color: #FFCC66;margin-left:60px"/>
		<input type="hidden" name="prbid" value="<?php echo ($Anorm['prbid'])?$Anorm['prbid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="13%" align=left>艺术家</TD>
	<TD width="13%" align=left>pic</TD>
	<TD width="18%" align=left>艺术家网址artist url</TD>
	<TD width="24%" align=left>简介 resume</TD>
<!--	<TD width="25%" align=left>所属大类</TD>-->
	<TD width="9%" align=left>显示display</TD>
	<TD width="7%" align=left>排序order</TD>
    <TD width="16%" align=left>操作 operate</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="8" align=right><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<?php
include_once( "footer.php");
?>