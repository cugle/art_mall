<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");   
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image();

if(!empty($Aconf['priveMessage'])) {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$stylecss = $stylecss?$stylecss:1;
$db_table = $pre."sysconfig"; 
if( $action == 'add' || $action == 'edit' ) { 
	/*处理logo图片*/
	if($_FILES['logo']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['logo']['type'])) {
            $strMessage =  '图片类型错误';
			$logo = $_POST['old_logo'];
        } else {
           /* 删除原有的 LOGO */
	       if(!empty($_POST['old_logo'])) {
			   @unlink('../data/weblogo/' . $_POST['old_logo']);
	       }
		   $logo = basename($image->upload_image($_FILES['logo'],'weblogo')); 
		}
	} else {
		$logo = $_POST['old_logo'];
	}
	/*处理shop_logo图片*/
	if($_FILES['shop_logo']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['shop_logo']['type'])) {
            $strMessage =  '图片类型错误';
			$shop_logo = $old_shop_logo;
        } else {
           /* 删除原有的 shop_logo */
	       if(!empty($old_shop_logo)) {
			   @unlink('../data/weblogo/' . $old_shop_logo );
	       }
		   $shop_logo = basename($image->upload_image($_FILES['shop_logo'],'weblogo'));
		}
	} else {
		$shop_logo = $old_shop_logo;
	}
	/*处理水印图片*/
	if($_FILES['watermark']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['watermark']['type'])) {
            $strMessage =  '图片类型错误';
			$watermark = $old_watermark;
        } else {
           /* 删除原有的 shop_logo */
	       if(!empty($old_watermark)) {
			   @unlink('../data/weblogo/' . $old_watermark );
	       }
		   $watermark = basename($image->upload_image($_FILES['watermark'],'weblogo'));
		}
	} else {
		$watermark = $old_watermark;
	}
    if(!empty($main_domin)) {
		$main_domin = str_replace('http://','', $main_domin); 
		$main_domin = str_replace('/','',$main_domin);
		/* 找域名所有者帐号 */
        $sql = "select user_name FROM ".$db_table." WHERE scid= '".$Aconf['domain_id']."'";
		$Ascuser_name = $oPub->getRow($sql); 
        if(strpos($main_domin,$Aconf['mail_url'] ) ) { 
             $main_domin = ($main_domin == 'www.'.$Aconf['mail_url'])?$main_domin:$Ascuser_name['user_name'].'.'.$Aconf['mail_url'];
		} else {
            /*查询 $main_domin 是否被使用 */
			$Atmp = explode(".",$main_domin);
			if(count($Atmp ) < 2){
				$main_domin = $Ascuser_name['user_name'].'.'.$Aconf['mail_url'];
			}else{
				$sql = "select COUNT(*) as count FROM ".$db_table." WHERE `main_domin` LIKE '".$main_domin."' AND user_name <> '".$Ascuser_name['user_name']."'";
				$Asc = $oPub->getRow($sql);
				if($Asc['count'] > 0) {
					$strMessage =  $main_domin.' 域名已被其它用户使用，请与管理员联系';
					$main_domin = $Ascuser_name['user_name'].'.'.$Aconf['mail_url'];
				}
			}
		}
	}

    /* 基本配置信息插入前的整理 */
	$strSets = '';
	if($sets){
		foreach ($sets AS $k => $v) {
			if($k == 'tongji')
			{ 
				$v = base64_encode($v);  
			}
			$strSets .= $k.'[|]'.$v.'{|}';
		}
	}
	$sets = $strSets;
	//地区分类 start
	if(!empty($ccid_5)){
		$ccid = $ccid_5;
	}elseif(!empty($ccid_4)){
		$ccid = $ccid_4;
	}elseif(!empty($ccid_3)){
		$ccid = $ccid_3;
	}elseif(!empty($ccid_2)){
		$ccid = $ccid_2;
	}elseif(!empty($ccid_1)){
		$ccid = $ccid_1;
	}
	$ccid = $ccid;
	//行业分类  start inducat_1
	if(!empty($inducat_5)){
		$inducatid = $inducat_5;
	}elseif(!empty($inducat_4)){
		$inducatid = $inducat_4;
	}elseif(!empty($inducat_3)){
		$inducatid = $inducat_3;
	}elseif(!empty($inducat_2)){
		$inducatid = $inducat_2;
	}elseif(!empty($inducat_1)){
		$inducatid = $inducat_1;
	}
	$inducatid = $inducatid; 
	if(!isset($rewrite)) $rewrite=0;  
    if( $action == 'add' && $scid == 0){
		$scid = $oPub->query('INSERT INTO '. $pre.'sysconfig (user_name,main_domin,sets,header_title,logo,shop_logo,watermark,ccid,inducatid,rewrite,user_template)VALUES ("'.$_SESSION['auser_name'].'","'.$main_domin.'","'.$sets.'","'.$_POST[header_title].'","'.$logo.'","'.$shop_logo.'","'.$watermark.'","'.$ccid.'","'.$inducatid.'","'.$rewrite.'","'.$user_template.'")'); 
 		$scid = $oPub->query('INSERT INTO '. $pre.'sysconfigfast (scid,user_name,main_domin)VALUES ("'.$scid.'","'.$_SESSION['auser_name'].'","'.$main_domin.'")'); 
     }

	if( $action == 'edit' && $scid > 0){
		$scid = $scid + 0; 
        clear_all_files();
        if($_POST['user_template']){
		   /* 复制目录 */
		   $target_dir = ROOT_PATH.'templates/user_themes/'.$Aconf['domain_id'];
		   $source_dir = ROOT_PATH.'themes/'.$Aconf['template'].'/';
		   if (!dir_copy($source_dir,$target_dir)){
               $strMessage .=  "模板目录创建失败";
		   }
		}  
		
        $oPub->query('UPDATE '. $pre.'sysconfig SET 
                user_name="'.$_SESSION['auser_name'].'",main_domin="'.$main_domin.'",sets="'.$sets.'",header_title="'.$_POST[header_title].'",logo="'.$logo.'",shop_logo="'.$shop_logo.'",watermark="'.$watermark.'",ccid="'.$ccid.'",inducatid="'.$inducatid.'",user_template="'.$user_template.'",rewrite="'.$rewrite.'" WHERE  scid="'.$Aconf['domain_id'].'"');  
        $oPub->query('UPDATE '. $pre.'sysconfigfast SET user_name="'.$_SESSION['auser_name'].'",main_domin="'.$main_domin.'",main_domin="'.$main_domin.'" WHERE  scid="'.$Aconf['domain_id'].'"'); 
		//修改 邮箱验证 start  
		if($Aconf['allow_home'] == $_SESSION['auser_id'])
		{ 
			$syssmtpid = $oPub->getOne("select id FROM ".$pre."syssmtp WHERE domain_id= '".$Aconf['domain_id']."' limit 1");  
			if($syssmtpid > 0)
			{
				$oPub->query('UPDATE '. $pre.'syssmtp SET smtpusermail="'.$smtpusermail.'",smtppass="'.$smtppass.'",smtpserver="'.$smtpserver.'",smtpport="'.$smtpport.'" WHERE  domain_id="'.$Aconf['domain_id'].'"');
			}else
			{ 
				$oPub->query('INSERT INTO '. $pre.'syssmtp(smtpusermail,smtppass,smtpserver,smtpport,domain_id)VALUES ("'.$smtpusermail.'","'.$smtppass.'","'.$smtpserver.'","'.$smtpport.'","'.$Aconf['domain_id'].'")'); 
			}
		}
		//修改 邮箱验证 end
        $spt = '<script type="text/javascript" src="http://www.osunit.com/record.php?';
        $spt .= "main_url=" .$Aconf['domain_url'];
        $spt .= '"></script>'; 

		$strMessage = '编辑成功!';
   } 

	/*  添加QQ 在线客户 */
	$strqq = '';
	if($qq){ 
		$oPub->query("delete from ".$pre."qq where domain_id='".$Aconf['domain_id']."'");
		foreach ($qq AS $k => $v) 
		{
			if(!empty($v))
			{
				$Afields=array('qq'=>$v,'qq_name'=>$qq_name[$k],'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."qq",$Afields); 
			}
		}
	}//if($qq) 
}
/* 网站配置信息 */  
 
$sql = "select * FROM ".$pre."sysconfig WHERE scid = '".$Aconf['domain_id']."' LIMIT 1";
$Anorm = $oPub->getRow($sql);
if($Anorm)
{
	$Asets = explode("{|}",$Anorm['sets']);
	if(count($Asets))
	{
		foreach ($Asets AS $v)
		{
			$At = array();
			$At = explode("[|]",$v);
			if($At[0])
			{
				if($At[0] == 'tongji')
				{
					$At[1] = base64_decode($At[1]);
					$At[1] = str_replace('\"','"',$At[1]);
				}
			   $Anorm[$At[0]] = $At[1];
			}
		} 
	}
	//QQ 联系方式
	$Rqq = $oPub->select("select * FROM ".$pre."qq WHERE domain_id= '".$Aconf['domain_id']."'"); 
	if($Rqq){
		$Anorm["Rqq"] = $Rqq;
	}else{
		$Anorm["Rqq"] = false;
	}
	unset($Rqq);
	//邮箱验证 start  smtpusermail	 smtppass	 smtpserver	 smtpport
	if($Aconf['allow_home'] == $_SESSION['auser_id'])
	{  
		$Rsyssmtp = $oPub->getRow("select * FROM ".$pre."syssmtp WHERE domain_id= '".$Aconf['domain_id']."'"); 
		$Anorm["smtpusermail"] = $Rsyssmtp['smtpusermail'];
		$Anorm["smtppass"]     = $Rsyssmtp['smtppass'];
		$Anorm["smtpserver"]   = $Rsyssmtp['smtpserver'];
		$Anorm["smtpport"]     = $Rsyssmtp['smtpport']; 
		unset($Rsyssmtp);
	} 
} else
{
   echo "sys error！";
   exit;
}

/* 城市列表 */
$Acitycat["citycatOpt1"]=$Acitycat["citycatOpt2"]=$Acitycat["citycatOpt3"]=$Acitycat["citycatOpt4"]=$Acitycat["citycatOpt5"] = '';
if($Anorm["ccid"] > 0){
	//找到所有的上级分类start
	$fid= $oPub->getOne('SELECT fid FROM '.$pre.'citycat where ccid = "'.$Anorm["ccid"].'" limit 1'); 
	if($fid){
		$preCcid = pre_node_orders($fid,$pre."citycat","ccid");
		$preCcid = $preCcid.','.$Anorm["ccid"];
	}else{
		$preCcid = $Anorm["ccid"];
	} 
	$Accid = explode(",",$preCcid);
	$ccidNum = count($Accid);
	//分类选择
	while( @list( $k, $v ) = @each( $Accid) )
	{ 
		if($k < 1){
 			$sql = 'SELECT * FROM '.$pre.'citycat where fid = 0 AND domain_id="'.$Aconf['domain_id'].'"';
		}else
		{
			$fid = $oPub->getOne('SELECT fid FROM '.$pre.'citycat where ccid = "'.$v.'" AND domain_id="'.$Aconf['domain_id'].'" limit 1'); 
			if(!$fid){
				break;
			}else
			{
				$sql = 'SELECT * FROM '.$pre.'citycat where fid = "'.$fid.'" AND domain_id="'.$Aconf['domain_id'].'"';
			}
		}
		$AnormAll = $oPub->select($sql);
		$j = $k + 1;
		$keyc = "citycatOpt".$k;
		$Acitycat[$keyc] = '<SELECT NAME="ccid" onchange="selectsAjax(this.value,\'citycat\',\'show\',\'divccid\','.$j.')">'; 
		$Acitycat[$keyc] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
		$n = 0;
		while( @list( $key, $value ) = @each( $AnormAll) )
		{
			$n ++;
			$selected = ($value['ccid'] == $v)? 'SELECTED':'';
			$Acitycat[$keyc] .= '<OPTION VALUE="'.$value["ccid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
		}
		$Acitycat[$keyc] .= '</SELECT>'; 
	}
}else
{
	$AnormAll = $oPub->select('SELECT * FROM '.$pre.'citycat where fid = 0'); 
	$Acitycat["citycatOpt0"] = '<SELECT NAME="ccid" onchange="selectsAjax(this.value,\'citycat\',\'show\',\'divccid\',1)">';
	$Acitycat["citycatOpt0"] .= '<OPTION VALUE="0" >选择地域分类</OPTION>';
	$n = 0;
	while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
		$Acitycat["citycatOpt0"] .= '<OPTION VALUE="'.$value["ccid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
	}
	$Acitycat["citycatOpt0"] .= '</SELECT>';
}
 
/* 所属行业 */
$Ainducat["inducatOpt1"]=$Ainducat["inducatOpt2"]=$Ainducat["inducatOpt3"]=$Ainducat["inducatOpt4"]=$Ainducat["inducatOpt5"]='';
if($Anorm["inducatid"] > 0){
	//找到所有的上级分类start
	$fid= $oPub->getOne('SELECT fid FROM '.$pre.'inducat where inducatid = "'.$Anorm["inducatid"].'" limit 1'); 
	if($fid){
		$preinducatid = pre_node_orders($fid,$pre."inducat","inducatid");
		$preinducatid = $preinducatid.','.$Anorm["inducatid"];
	}else{
		$preinducatid = $Anorm["inducatid"];
	} 
	$Ainducatid = explode(",",$preinducatid);
	$inducatidNum = count($Ainducatid);
	//分类选择
	while( @list( $k, $v ) = @each( $Ainducatid) ) { 
		if($k < 1){
 			$sql = 'SELECT * FROM '.$pre.'inducat where fid = 0 AND domain_id="'.$Aconf['domain_id'].'"';
		}else{
			$fid = $oPub->getOne('SELECT fid FROM '.$pre.'inducat where inducatid = "'.$v.'" AND domain_id="'.$Aconf['domain_id'].'" limit 1'); 
			if(!$fid){
				break;
			}else{
				$sql = 'SELECT * FROM '.$pre.'inducat where fid = "'.$fid.'" AND domain_id="'.$Aconf['domain_id'].'"';
			}
		}
		$AnormAll = $oPub->select($sql);
		$j = $k + 1;
		$keyc = "inducatOpt".$k;
		$Ainducat[$keyc] = '<SELECT NAME="inducatid" onchange="selectsAjax(this.value,\'inducat\',\'show\',\'divinducatid\','.$j.')">'; 
		$Ainducat[$keyc] .= '<OPTION VALUE="0" >选择行业分类</OPTION>';
		$n = 0;
		while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
			$selected = ($value['inducatid'] == $v)? 'SELECTED':'';
			$Ainducat[$keyc] .= '<OPTION VALUE="'.$value["inducatid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
		}
		$Ainducat[$keyc] .= '</SELECT>'; 
	}
}else{
	$AnormAll = $oPub->select('SELECT * FROM '.$pre.'inducat where fid = 0'); 
	$Ainducat["inducatOpt0"] = '<SELECT NAME="inducatid" onchange="selectsAjax(this.value,\'inducat\',\'show\',\'divinducatid\',1)">';
	$Ainducat["inducatOpt0"] .= '<OPTION VALUE="0" >选择行业分类</OPTION>';
	$n = 0;
	while( @list( $key, $value ) = @each( $AnormAll) ) {
		$n ++;
		$Ainducat["inducatOpt0"] .= '<OPTION VALUE="'.$value["inducatid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
	}
	$Ainducat["inducatOpt0"] .= '</SELECT>';
}
/* 找到所有的分类到select end*/ 
$Ahome["stylecss"]     = $stylecss;
$Ahome["spt"]		= $spt;
$Ahome["Anorm"]		= $Anorm;
$Ahome["Ainducat"]  = $Ainducat;
$Ahome["Acitycat"]  = $Acitycat;
$Ahome["nowName"]   = $nowName; 
$Ahome["strMessage"] = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]); 

?> 
