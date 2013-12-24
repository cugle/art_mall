<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']); 

if( $_POST['action'] == 'edit'){
    $_POST['user_id'] = $_POST['user_id'] +0;
	/*处理图片*/
	$my_photo = $_POST['old_my_photo'];
	if($_FILES['my_photo']['size'] > 0 ) { 
        if (!$image->check_img_type($_FILES['my_photo']['type'])) {
				$strMessage =  '图片类型错误';			
        } else { 
			if(!empty($_POST['old_my_photo'])) {
				$my_photo = basename($image->upload_image($_FILES['my_photo'],'myphoto',$_POST['old_my_photo']));
			}  else {
				$my_photo = basename($image->upload_image($_FILES['my_photo'],'myphoto')); 
			}
		}
	}

	if (trim($_POST['password']) != '') { 
		$password  = md5(trim($_POST['password'])); 
		$Afields=array('password'=>$password);
        $db_table = $pre."admin_user";
	    if ($Aconf['domain_id']== $_SESSION['domain_user_id'])
	    {
	         $condition = 'user_id='.$_POST['user_id'].' AND domain_id = '.$Aconf['domain_id'];
		}else{
			 $condition = 'user_id='.$Aconf['domain_id'].' AND domain_id = '.$Aconf['domain_id'];
		}
        $oPub->update($db_table,$Afields,$condition);
	}

    /*查询基本资料表是否有此数据*/ 
    $tmp = $oPub->getRow("SELECT user_id  FROM ".$pre."admin_userbase  WHERE  user_id = '".$Aconf['domain_id']."'"); 
    if($tmp['user_id']>0) {    
		$Afields=array('my_name'=>$_POST['my_name'],'idc'=>$_POST['idc'],'email'=>$_POST['email'],'address'=>$_POST['address'],'zipcode'=>$_POST['zipcode'],'sex'=>$_POST['sex'],'mymsn'=>$_POST['mymsn'],'qq'=>$_POST['qq'],'office_phone'=>$_POST['office_phone'],'home_phone'=>$_POST['home_phone'],'mobile_phone'=>$_POST['mobile_phone'],'my_photo'=>$my_photo);
	    if ($Aconf['domain_id']== $_SESSION['domain_user_id']) {
	         $condition = "user_id='".$_POST['user_id']."'";
		}else{
			 $condition = "user_id='".$Aconf['domain_id']."'";
		} 
		$oPub->update($db_table,$Afields,$condition);		
	} else {
        $Afields=array('user_id'=>$Aconf['domain_id'],'my_name'=>$_POST['my_name'],'idc'=>$_POST['idc'],'email'=>$_POST['email'],'address'=>$_POST['address'],'zipcode'=>$_POST['zipcode'],'sex'=>$_POST['sex'],'mymsn'=>$_POST['mymsn'],'qq'=>$_POST['qq'],'office_phone'=>$_POST['office_phone'],'home_phone'=>$_POST['home_phone'],'mobile_phone'=>$_POST['mobile_phone'],'my_photo'=>$my_photo,'domain_id'=>$Aconf['domain_id']);
        $oPub->install($db_table,$Afields);        
	}	
	$strMessage .= "修改设置成功！";
} 
 
$Auser = $oPub->getRow("SELECT a.user_id,a.user_name  FROM ".$pre."admin_user as a WHERE  a.user_id = '".$Aconf['domain_id']. "' AND a.domain_id='".$Aconf['domain_id']."'"); 
$Auser_base = $oPub->getRow("SELECT b.my_name,b.idc,b.email,b.address,b.zipcode,b.sex, b.mymsn,b.qq,b.office_phone,b.home_phone,b.mobile_phone,b.my_photo  FROM ".$pre."admin_userbase  as b WHERE  b.user_id = '".$Auser['user_id']."'"); 
if(is_array($Auser_base)) {
	$Auser = $Auser + $Auser_base;
}
unset($Auser_base);
$Ahome["Auser"]	  = $Auser;
$Ahome["nowName"] = $nowName; 
$Ahome["strMessage"] = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display("mytest.html");  
?>
 
