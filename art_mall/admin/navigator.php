<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
if(!empty($Aconf['priveMessage'])) {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

/* 导航系统内容 */
$Anavesys[0] = '选择模块';
while( @list( $k, $v ) = @each( $Aweb_url ) ) {
	if($v[3]){
		$strk =($Aconf["rewrite"])?$k.".html":$strk = $k.".php"; 
		$Anavesys[$strk] = $v[0] ;
	}
} 

$optNavesys = '<SELECT NAME="navesys" id="navesys_id" onchange="nave_sys(this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">';
while( @list( $k, $v ) = @each( $Anavesys) ) {
    $optNavesys .= '<OPTION VALUE="'.$k.'"  >'.$v. '</OPTION>';
}
$optNavesys .= '</SELECT>'; 
 
if(!empty($_POST["action"])){
	if(!isset($name)) $name=false; 
	if(!isset($vieworder)) $vieworder=false; 
	if(!isset($opennew)) $opennew=false; 
	if(!isset($url)) $url=false; 
	$ifbotton = ($ifbotton > 0)?($ifbotton-1):0;
	if($_FILES['url_logo']['size'] > 0 && $_FILES['url_logo']['size'] < 20480) {  
        if (!$image->check_img_type($_FILES['url_logo']['type']))
        { 
            $strMessage .=  ' 图标图片类型错误! ';
			$url_logo = $_POST['old_url_logo'];
        } else { 
	       if(!empty($_POST['old_url_logo'])) { 
			   @unlink('../data/weblogo/' . $_POST['old_url_logo']);
	       } 
		   $url_logo = basename($image->upload_image($_FILES['url_logo'],'weblogo')); 
		}
	} else {
		if ($_FILES['url_logo']['size'] >= 30720) {
            $strMessage .=  " 图标上传失败，不能大于30k！ ";
		}
		$url_logo = $_POST['old_url_logo'];
	} 

	if( $_POST["action"] == 'add'  ) {
		if (trim($name) == '') {
		   $strMessage =  "导航名不能为空！ ";
		} else {
			$Afields=array('name'=>trim($name),'ifbotton'=>$ifbotton,'vieworder'=>$vieworder,'opennew'=>$opennew,'url'=>$url,'url_logo'=>$url_logo,'top'=>$top,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre."nav",$Afields);
			$strMessage .=  " 导航添加成功!";
		}
		unset($_GET);
	}

	if( $_POST["action"] == 'edit'){
		$id = $id +0; 
		$Afields=array('name'=>trim($name),'ifbotton'=>$ifbotton,'vieworder'=>$vieworder,'opennew'=>$opennew,'url'=>$url,'url_logo'=>$url_logo,'top'=>$top,'domain_id'=>$Aconf['domain_id']);
		$condition = 'domain_id = '.$Aconf['domain_id'].' and id = '.$id;
		$oPub->update($pre."nav",$Afields,$condition);
		$strMessage .= " 导航成功修改！ ";
		unset($_GET);
	}
}

//get
if(!isset($action)) $action=false; 
if($action == 'edit'){
	$Anav = $oPub->getRow('SELECT * FROM '.$pre.'nav WHERE id = "'.$id. '" AND domain_id="'.$Aconf['domain_id'].'"'); 
}

if($action == 'del'){
    $condition = 'id='.$id;
    $oPub->delete($pre."nav",$condition); 
}

//page
$strWhere = ' WHERE top=0 and domain_id="'.$Aconf['domain_id'].'"';
$stylecss = $stylecss?$stylecss:1;
if($stylecss==1){
	$strWhere .= ' and ifbotton<1';
}elseif($stylecss==2){
	$strWhere .= ' and ifbotton=1';
}elseif($stylecss==3){
	$strWhere .= ' and ifbotton=2';
}
$count = $oPub->getOne('SELECT count( * ) AS count FROM '.$pre.'nav'.$strWhere);  
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('stylecss'=>$stylecss);  
$strOffSet = $page->OffSet();
$AnavAll = $oPub->select('SELECT * FROM '.$pre.'nav'.$strWhere.' ORDER BY vieworder,id asc limit '.$strOffSet); 

$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnavAll) ) {
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value["name"].'</TD>';

	   $url_logo = ($value["url_logo"])?'<IMG SRC="../data/weblogo/'.$value["url_logo"].'"  BORDER="0" width="'.$Aconf["nav_w"].'" height="'.$Aconf["nav_h"].'">':'';
       $StrtypeAll .= '<TD align=left>'.$url_logo.'</TD>';

       $StrtypeAll .= '<TD align=left>'.$value["url"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["vieworder"].'</TD>';
    
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= ($value["opennew"])?'是':'否';
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.PHP_SELF.'?id='.$value["id"].'&action=edit&stylecss='.$stylecss.'&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
	   $StrtypeAll .= '<a href="'.PHP_SELF.'?id='.$value["id"].'&action=del&stylecss='.$stylecss.'&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]" onclick="return(confirm(\'确定删除?\'))"></a></TD>';
       $StrtypeAll .= '</TR>';  
 
		$row = $oPub->select('SELECT * FROM '.$pre.'nav where top='.$value['id'].' ORDER BY vieworder,id asc'); 
		while( @list( $k, $v ) = @each( $row) ) 
		{
		   $StrtypeAll .= '<TR class='.$tmpstr.' style="color:#0000A8">';
		   $StrtypeAll .= '<TD align=left><span style="margin-left:40px">'.$v["name"].'</span></TD>';

		   $url_logo = ($v["url_logo"])?'<IMG SRC="../data/weblogo/'.$v["url_logo"].'"  BORDER="0" width="'.$Aconf["nav_w"].'" height="'.$Aconf["nav_h"].'">':'';
		   $StrtypeAll .= '<TD align=left>'.$url_logo.'</TD>';

		   $StrtypeAll .= '<TD align=left>'.$v["url"].'</TD>';
		   $StrtypeAll .= '<TD align=left>'.$v["vieworder"].'</TD>';
		
		   $StrtypeAll .= '<TD align=left>';
		   $StrtypeAll .= ($v["opennew"])?'是':'否';
		   $StrtypeAll .= '</TD>';
		   $StrtypeAll .= '<TD align=left><a href="'.PHP_SELF.'?id='.$v["id"].'&action=edit&stylecss='.$stylecss.'&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
		   $StrtypeAll .= '<a href="'.PHP_SELF.'?id='.$v["id"].'&action=del&stylecss='.$stylecss.'&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]" onclick="return(confirm(\'确定删除?\'))"></a></TD>';
		   $StrtypeAll .= '</TR>'; 
		} 
}
$ifbotton = ($stylecss > 0)?($stylecss-1):0;
$Ahome["mainnav"] = get_nav($ifbotton); //主导航选择

$Ahome["stylecss"]     = $stylecss;
$Ahome["optNavesys"]   = $optNavesys;
$Ahome["Anav"]		   = $Anav;
$Ahome["showpage"]     = $page->ShowLink();
$Ahome["StrtypeAll"]   = $StrtypeAll;
$Ahome["Anorm"]		   = $Anorm; 
$Ahome["nowName"]      = $nowName; 
$Ahome["strMessage"]   = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]); 
?>
 