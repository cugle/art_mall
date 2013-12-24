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
//推荐分类
$sql = "SELECT * FROM ".$pre."tjcat WHERE domain_id='".$Aconf['domain_id']."' ORDER BY id ASC";
$row = $oPub->select($sql);
while( @list( $key, $value ) = @each( $row ) ) {
	$Atype["$value[id]"] = $value["name"];
	$Aimgwidth["$value[id]"]   = $value["imgwidth"];
	$Aimgheight["$value[id]"]  = $value["imgheight"];
}
$db_table = $pre."tj";
//post
if( $_POST['action'] == 'add'  || $_POST['action'] == 'edit' ) { 
	/*处理图片*/
	if($_FILES['img']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['img']['type'])) {
            $strMessage =  '图片类型错误';
			$img_name = $_POST['old_img'];
        } else {

	       if(!empty($_POST['old_img'])) {
               $img_name = basename($image->upload_image($_FILES['img'],'tj',$_POST['old_img']));
	       } else {
		       $img_name = basename($image->upload_image($_FILES['img'],'tj'));
	       }
		}
	} else {
		$img_name = $_POST['old_img'];
	}

	if (trim($_POST['name']) == '') {
       $strMessage .=  " 标题名不能为空！ ";
	} else {
		if($_POST['url']){
		     $_POST['url'] = str_replace('http://','', $_POST['url']);
			 $_POST['url'] = 'http://'.$_POST['url'];
		}

		if(!$_POST['cgid']) {
			$oPub->query('UPDATE ' . $db_table . ' SET `orders`=99 WHERE   orders = "'.$_POST['orders'].'" and tjcatid="'.$_POST["tjcatid"].'"'); 

            $Afields=array('name'=>trim($_POST['name']),'ext'=>$_POST['ext'],'ex1'=>$_POST['ex1'],'ex2'=>$_POST['ex2'],'tjcatid'=>$_POST["tjcatid"], 'orders'=>$_POST['orders'],'url'=>$_POST['url'],'colors'=>$_POST['colors'],'img'=>$img_name,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields); 
		    $strMessage .=  " 数据添加成功! ";

		} else 
		{
            $Afields=array('name'=>trim($_POST['name']),'ext'=>$_POST['ext'],'ex1'=>$_POST['ex1'],'ex2'=>$_POST['ex2'],'tjcatid'=>$_POST["tjcatid"], 'orders'=>$_POST['orders'],'url'=>$_POST['url'],'colors'=>$_POST['colors'],'img'=>$img_name,'domain_id'=>$Aconf['domain_id']);
	        $condition = 'domain_id = '.$Aconf['domain_id'].' and cgid = '.$_POST[cgid];
            $oPub->update($db_table,$Afields,$condition);
	        $strMessage .= " 数据成功修改！ "; 
			$oPub->query('UPDATE '. $db_table .' SET `orders`=99  WHERE `cgid` <> "'.$_POST['cgid']. '" and  orders = "'.$_POST['orders'].'" and tjcatid="'.$_POST["tjcatid"].'"');  
		} 
    }
	unset($_GET);
}


//get
if( $_GET['action'] == 'edit'){
	$Anav = $oPub->getRow('SELECT * FROM '.$db_table.'  WHERE cgid = "'.$_GET['cgid'].'" AND domain_id="'.$Aconf['domain_id'].'"'); 
}

if( $_GET['action'] == 'del'){
	//删除图片
		$condition = 'cgid='.$_GET['cgid']; 
		$sql = "SELECT img  FROM " . $pre."tj WHERE ".$condition;
		$img = $oPub->getOne($sql);
		if (is_file('../data/tj/' . $img))  @unlink('../data/tj/' . $img);  
		$oPub->query("delete from  ".$db_table."  WHERE ".$condition);   
	
}
$where = '';
if($tjcatid) {
   $where = ' AND tjcatid='.$tjcatid;
}
//page
$strWhere = ' WHERE domain_id="'.$Aconf['domain_id'].'"'.$where;
$row = $oPub->getRow('SELECT count( * ) AS count FROM '.$db_table.$strWhere); 
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize =30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('tjcatid'=>$tjcatid); 
$strOffSet = $page->OffSet();

$AnavAll = $oPub->select('SELECT * FROM '.$db_table.$strWhere.' ORDER BY orders asc,cgid desc  limit '.$strOffSet); 

$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnavAll) ) {
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>'; 
			$StrtypeAll .= $n.'）['.$Atype[$value["tjcatid"]].'] <A HREF="'.$value["url"].'" style="color:'.$value['colors'].'">'.$value['name'].'</A>'; 
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>'; 
			$StrtypeAll .= $value["url"];
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>'; 
		    $StrtypeAll .= sub_str($value["ext"],20);  
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>'; 
		    $StrtypeAll .= $value["ex1"] ;  
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>'; 
		    $StrtypeAll .= $value["ex2"];  
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>'; 
	   if($value["img"]){
		    $StrtypeAll .= '<img src="../data/tj/'.$value["img"].'" width="30" height="30" />'; 
	   }
	   $StrtypeAll .='</TD>';
	   $StrtypeAll .= '<TD align=left>';

	   $StrtypeAll .= '<span id="tj_'.$value["cgid"].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$value[orders].'" size="2" onDblClick=tj_list_edit(\'tj\',\''.$value["cgid"].'\',this.value,\''.$value["tjcatid"].'\') />'; 
	   $StrtypeAll .= '</span>';


	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?cgid='.$value["cgid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?cgid='.$value["cgid"].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
       $StrtypeAll .= '</TR>';    
}
?>
<?php
   include_once( "header.php");
   if ($strMessage != '') {
    echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
  }

  if(!$Anav["tjcatid"]) {
		$Anav["tjcatid"] = $tjcatid;
  }
?>


  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <TR >
  <form name="form1" method="post" action="" enctype="multipart/form-data"> 
    <TD width="13%" align="left" colspan="0">
        <span style="font-weight:bold">推荐标题:</span>
   		<input name="name"  type="text" size="40" value="<?php echo ($Anav['cgid'])?$Anav['name']:''?>"   id="status" onkeydown='countChar("status","counter");' onkeyup='countChar("status","counter");' />输入<span id="counter">40</span>字

        <span style="font-weight:bold">颜色:</span>
		<SELECT NAME="colors">
			<OPTION VALUE=""  <?php echo (!$Anav['colors'])?'SELECTED':'';?>>默认</OPTION>
			<OPTION VALUE="#FF0000" style="color:#FF0000" <?php echo ($Anav['colors']=='#FF0000')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#00FF00" style="color:#00FF00" <?php echo ($Anav['colors']=='#00FF00')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#0000FF" style="color:#0000FF" <?php echo ($Anav['colors']=='#0000FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#000000" style="color:#000000" <?php echo ($Anav['colors']=='#000000')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#FF6600" style="color:#FF6600" <?php echo ($Anav['colors']=='#FF6600')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#33CC00" style="color:#33CC00" <?php echo ($Anav['colors']=='#33CC00')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#0066FF" style="color:#0066FF" <?php echo ($Anav['colors']=='#0066FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#CC3333" style="color:#CC3333" <?php echo ($Anav['colors']=='#CC3333')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#3399FF" style="color:#3399FF" <?php echo ($Anav['colors']=='#3399FF')?'SELECTED':'';?>>■■■</OPTION>
			<OPTION VALUE="#CC6666" style="color:#CC6666" <?php echo ($Anav['colors']=='#CC6666')?'SELECTED':'';?>>■■■</OPTION>
		</SELECT> 
		<br/> 
        <input type="hidden" name="action" value="<?php echo ($Anav['cgid'])?'edit':'add'?>" />        
		<input type="hidden" name="cgid" value="<?php echo ($Anav['cgid'])?$Anav['cgid']:''?>" />

        <span style="font-weight:bold">所属位置:</span>
		<SELECT NAME="tjcatid" >
		<?php 
		$stropt = ''; 
		while( @list( $key, $value ) = @each( $Atype ) )
		{
			$select = ($Anav['tjcatid']==$key)?'SELECTED':'';
			if($Aimgwidth[$key] > 0 ){
				$strtmp = ' 图片宽高:'.$Aimgwidth[$key].'px * '.$Aimgheight[$key];
			}else{
				$strtmp = '';
			}
			$stropt .= '<OPTION VALUE="'.$key.'" '.$select.'>'.$value.$strtmp.'</OPTION>';
		}
		echo $stropt;
		?>
		</SELECT>
		<span style="font-weight: bold">图片:</span>
		<INPUT type="file" name="img" size="20" />
		<input type="hidden" name="old_img" value="<?php echo $Anav['img'];?>" />

		<span id="logo_show">
         <?php 
		 if($Anav["img"]) {
			 $tmp = '<A HREF="../data/tj/'.$Anav["img"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> ';
 			 echo $tmp;
		 }	
		 ?>
		 </span>
		  <span id="show_img">注意图片高宽</span>
		<br/>
		<span style="font-weight:bold">顺序:</span>
		<input name="orders" id="name_id" type="text" size="2" value="<?php echo ($Anav['orders'])?$Anav['orders']:99?>" /> 

        <span style="font-weight:bold">http地址:</span>
		<input name="url" id="url_id" type="text" size="40" value="<?php echo ($Anav['cgid'])?$Anav['url']:'http://'?>" /> 
 
		<input type="submit" name="Submit" value="<?php echo ($Anav['cgid'])?'编辑':'增加'?>" style="background-color: #FFCC66;margin-left: 20px"/>
		
    </TD>
    </form>
    </tr>
  </table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
<tr>	
  <TR class=bg5>
	<TD align=left>标题
		<?php 
		$stropt = '';
		if($Atype)
		{
			foreach ($Atype AS $k=>$v) {
				$select = ($_REQUEST['tjcatid']==$k)?'SELECTED':'';
				$stropt .= '<OPTION VALUE="'.$k.'" '.$select.'>'.$v.'</OPTION>';
			} 
		}
		?>
		<SELECT NAME="tjcatid" onchange="return select( this.value )">
		 <OPTION VALUE="0" >所有</OPTION>';
		<?php echo $stropt;?>
		</SELECT>	
	</TD>
	<TD align=left>URL</TD>
	<TD align=left>简介</TD>
	<TD align=left>时间</TD>
	<TD align=left>地点</TD>
	<TD align=left>图片</TD>
	<TD align=left>排序</TD>
	<TD align=left>编辑/删除</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="8" align=right><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<SCRIPT language="JavaScript"> 

 function select(a)
 {
	 obj = a;
	 location="<?php echo $_SERVER["PHP_SELF"];?>?tjcatid=" + obj;
 }
  
 
 function tj_list_edit(edit,cgid,edit_val,tjcatid)
  {
     obj = edit + "_" + cgid;
     var strTemp = "ajax_tj_edit.php?op=" + edit + "&cgid=" + cgid + "&edit_val=" + escape(edit_val) + "&tjcatid=" + tjcatid; 
	 send_request(strTemp);
  }
 
function countChar(textareaName,spanName) 
{   
	document.getElementById(spanName).innerHTML =  document.getElementById(textareaName).value.length; 
} 

</SCRIPT>
<?php
include_once( "footer.php");
?>