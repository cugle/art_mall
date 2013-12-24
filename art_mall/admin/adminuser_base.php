<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once($ROOT_PATH . 'class/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."admin_user";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit')
{


	    /*处理图片*/
		 $my_photo = $_POST['old_my_photo'];
	    if($_FILES['my_photo']['size'] > 0 )
	    {
		    /* 判断图像类型 */
            if (!$image->check_img_type($_FILES['my_photo']['type']))
            {
                $strMessage =  '图片类型错误';
			   
            }
		    else
		    {

	           if(!empty($_POST['old_my_photo']))
	           {
                   $my_photo = basename($image->upload_image($_FILES['my_photo'],'myphoto',$_POST['old_my_photo']));
	           }
	           else
	           {
		           $my_photo = basename($image->upload_image($_FILES['my_photo'],'myphoto'));

	           }
		    }
	    }

  
      if($_POST['action'] == 'add')
      {

	      if (trim($_POST['password']) == '' || $_POST['user_name'] == '')
	      {
             $strMessage =  "用户名与密码不能为空！";
	      }
	      else
	      {
             $user_name = clean_html(trim($_POST['user_name']));
             $sql = "SELECT user_id FROM ".$db_table." 
	                WHERE `user_name` LIKE '".$user_name."'";
             $Auser = $oPub->getRow($sql);
             if($Auser['user_id'] > 0)
             {
  	              $strMessage = '添加失败：此帐号已经被使用！';
             }
	         else
	         {
	            $password  = md5(trim($_POST['password']));
		        $Afields=array('user_name'=>$user_name,'password'=>$password,'praid'=>$_POST['praid'],'domain_id'=>$Aconf['domain_id']);
                $user_id = $oPub->install($db_table,$Afields);
                $db_table = $pre."admin_userbase";
				$Afields=array('user_id'=>$user_id,'my_name'=>$_POST['my_name'],'idc'=>$_POST['idc'],'email'=>$_POST['email'],'address'=>$_POST['address'],'zipcode'=>$_POST['zipcode'],'sex'=>$_POST['sex'],'mymsn'=>$_POST['mymsn'],'qq'=>$_POST['qq'],'office_phone'=>$_POST['office_phone'],'home_phone'=>$_POST['home_phone'],'mobile_phone'=>$_POST['mobile_phone'],'my_photo'=>$my_photo,'domain_id'=>$Aconf['domain_id']);
                $oPub->install($db_table,$Afields);
		       $strMessage =  "用户添加成功!";
		     }
        }
     }
	unset($Auser);

    if( $_POST['action'] == 'edit'){

        $db_table = $pre."admin_user";
	    if (trim($_POST['password']) != '')
	    {
			$password  = md5(trim($_POST['password'])); 
		    $Afields=array('password'=>$password,'praid'=>$_POST['praid']);
	    }
		else
		{
			$Afields=array('praid'=>$_POST['praid']);
		}

	    if ($_SESSION['auser_id']== $_SESSION['domain_user_id'])
	    {
	       $condition = 'user_id='.$_POST[user_id].' AND domain_id = '.$Aconf['domain_id'];
		}else{
		   $condition = 'user_id='.$_SESSION['auser_id'].' AND domain_id = '.$Aconf['domain_id'];
		}
        $oPub->update($db_table,$Afields,$condition);
		$strMessage = "";

        /*查询基本资料表是否有此数据*/
        $db_table = $pre."admin_userbase";  
        $sql = "SELECT user_id  FROM ".$db_table."  WHERE  user_id = ".$_POST['user_id'];
        $tmp = $oPub->getRow($sql);
        if($tmp['user_id']>0)
	    {    
		    $Afields=array('my_name'=>$_POST['my_name'],'idc'=>$_POST['idc'],'email'=>$_POST['email'],'address'=>$_POST['address'],'zipcode'=>$_POST['zipcode'],'sex'=>$_POST['sex'],'mymsn'=>$_POST['mymsn'],'qq'=>$_POST['qq'],'office_phone'=>$_POST['office_phone'],'home_phone'=>$_POST['home_phone'],'mobile_phone'=>$_POST['mobile_phone'],'my_photo'=>$my_photo);
		    $condition = 'user_id='.$_POST['user_id'];
		    $oPub->update($db_table,$Afields,$condition);	
			$strMessage .= "修改成功！";
	    }
	    else
	    {
            $Afields=array('user_id'=>$_POST['user_id'],'my_name'=>$_POST['my_name'],'idc'=>$_POST['idc'],'email'=>$_POST['email'],'address'=>$_POST['address'],'zipcode'=>$_POST['zipcode'],'sex'=>$_POST['sex'],'mymsn'=>$_POST['mymsn'],'qq'=>$_POST['qq'],'office_phone'=>$_POST['office_phone'],'home_phone'=>$_POST['home_phone'],'mobile_phone'=>$_POST['mobile_phone'],'my_photo'=>$my_photo,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields);  
			$strMessage .= "基本资料添加成功！";
	    }	
		//超级用户，修改登陆账号 
		if($_SESSION['auser_name'] == 'admin')
		{
            $user_name = clean_html(trim($_POST['user_name']));
			$db_table = $pre."admin_user";
            $sql = "SELECT user_id FROM ".$db_table." 
	                WHERE `user_name` LIKE '".$user_name."'";
            $Auser = $oPub->getRow($sql);
            if($Auser['user_id'] > 0)
            {

			   if($Auser['user_id'] == $_POST['user_id'])
			   {	
			        //账号没变不用修改
			   }
			   else
			   {
  	               $strMessage = '存在相同账号用户！不能修改';
			   }
            }
			else
			{	
                    $Afields=array('user_name'=>$_POST['user_name']);
					$condition = 'user_id='.$_POST['user_id'];
					$oPub->update($db_table,$Afields,$condition);	
			}
		}
	    
    }

}
//get
$db_table = $pre."admin_user";
if( $_GET['action'] == 'edit'){
	$sql = "SELECT a.user_id,a.user_name,a.praid  
	       FROM ".$pre."admin_user as a  
		   WHERE  a.user_id = '".$_GET['user_id']. "' 
		   AND a.domain_id='".$Aconf['domain_id']."'";		   
	$Auser = $oPub->getRow($sql);
    $sql = "SELECT b.my_name,b.idc,b.email,b.address,b.zipcode,b.sex,
	       b.mymsn,b.qq,b.office_phone,b.home_phone,b.mobile_phone,b.my_photo   
	       FROM ".$pre."admin_userbase  as b   
		   WHERE  b.user_id = '".$Auser['user_id']."'";
    $Auser_base = $oPub->getRow($sql);
	if(is_array($Auser_base))
	{
		$Auser = $Auser + $Auser_base;
	}
	unset($Auser_base);
}


if( $_GET['action'] == 'del'){
	if($_GET['user_id'] != $_SESSION['domain_user_id'])
	{
	    $condition = 'user_id='.$_GET['user_id'];
         $oPub->delete($db_table,$condition);
	}
	else
	{
       $strMessage = "特权用户不能删除!";
	}
}

//page
$strWhere = " WHERE domain_id=".$Aconf['domain_id'];
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

$sql = "SELECT * FROM ".$db_table.$strWhere." ORDER BY user_id desc limit ".$strOffSet;
$AuserAll = $oPub->select($sql);

$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AuserAll) ) {
	   /*  查找此用户的基本资料 */
	   unset($Auser_base);
       $sql = "SELECT b.my_name,b.idc,b.email,b.address,b.zipcode,b.sex,
	       b.mymsn,b.qq,b.office_phone,b.home_phone,b.mobile_phone,b.my_photo  
	       FROM ".$pre."admin_userbase  as b   
		   WHERE  b.user_id = '".$value["user_id"]."'";
       $Auser_base = $oPub->getRow($sql);
	   $tmp = '';
	   if(!empty($Auser_base['my_photo']))
	   {	
		$tmp = '<A HREF="../data/myphoto/'.$Auser_base['my_photo'].'" target="_blank">'; 
		$tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查看图片"></A> ';
	   }	
	   $my_name = (!empty($Auser_base['my_name']))?'['.$value["user_name"].']'.$Auser_base['my_name'].$tmp:$value["user_name"];
	   $mylink = (!empty($Auser_base['idc']))?' 身份证：'.$Auser_base['idc'].'<br/>':'';
	   $mylink .= (!empty($Auser_base['email']))?' email：'.$Auser_base['email']:'';
	   $mylink .= (!empty($Auser_base['qq']))?' qq：'.$Auser_base['qq'].'<br/>':'';
	   $mylink .= (!empty($Auser_base['office_phone']))?'<br/>办公电话：'.$Auser_base['office_phone']:'';
	   $mylink .= (!empty($Auser_base['home_phone']))?' 家庭电话：'.$Auser_base['home_phone']:'';
	   $mylink .= (!empty($Auser_base['mobile_phone']))?' 手机：'.$Auser_base['mobile_phone']:'';
       /* 得到分公司名 */
	   $pra_name = '';
       $db_table = $pre.'pravail'; 
       $sql = "SELECT pra_name,cotype  FROM ".$db_table." 
            where praid ='".$value["praid"]."' and domain_id='".$Aconf['domain_id']."'";
       $Apav = $oPub->getRow($sql);
	   if($Apav['pra_name'])
	   {

	      if($Apav[cotype] == 1)
	      {
		      $cotype = '[供货商]';
	      }
	      elseif($Apav[cotype] == 2)
	      {
		       $cotype = '[代理商]';
	      }
		  else
		  {
              $cotype =  '[子公司]';
		   }

		   $mylink = $Apav['pra_name'].":".$mylink;
		    
	   }
        
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$my_name.'</TD>';
       $StrtypeAll .= '<TD align=left>'.$mylink.'</TD>';
	   $StrtypeAll .= '<TD align=left>';
	   $tmp = '';
       if ($value["action_list"] != '' && $value["action_list"] != 'all')
	   {		   
		   $Aaction_list = explode(',',$value["action_list"]);
           foreach( $Aaction_list as  $v ) {
                $tmp .= $Aprive[$v].',';
           }
		   $tmp = substr($tmp,0,-1);
	   }
	   $StrtypeAll .= $tmp;
       $StrtypeAll .= ($value["action_list"] == 'all')?'all':$strAction_list;
       $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?user_id='.$value["user_id"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?user_id='.$value["user_id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
       $StrtypeAll .= '</TR>';    
}

/* 得到分公司列表 */
$db_table = $pre."pravail";
/* 找到所有的分类到select start*/
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY praid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="praid">';
$Stropt .= '<OPTION VALUE="0" >选择绑定的公司</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   if($value[cotype] == 1)
	   {
		   $cotype = '供货商';

	   }
	   elseif($value[cotype] == 2)
	   {
		   $cotype = '代理商';
	   }
	   else
	   {
		   $cotype = '子公司';
	   }
       $selected = ($Auser['praid'] == $value["praid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["praid"].'" '.$selected.' >'.$n.'、'.$value["pra_name"].'</OPTION>';
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$Auser['praid'] );
	   }	   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/



?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
	 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>

<DIV class=content>
<TABLE width="99%" border=0>
  <TR class="odd" >
  <form name="form1" method="post" action="" enctype="multipart/form-data"> 
    <TD align="left" colspan="4">
	    <span style="font-weight: bold">登录帐号:</span>
		<?php if($Auser['user_id'] > 0)
		{ 
			if($_SESSION['auser_name'] != 'admin')
			{ 
				$strtmp = $Auser['user_name'];
			} else
			{
				$strtmp = '<input name="user_name" type="text" size="12" value="'.$Auser['user_name'].'" />';
			} 
		} else
		{
			$strtmp = '<input name="user_name" type="text" size="12" value="'.$Auser['user_name'].'" />';	
		}
		echo $strtmp;
		?>
        <span style="font-weight: bold">登陆密码:</span>
		<input name="password" type="text" size="18" value="" />
        <br/><span>　　姓名:</span>
		<input name="my_name" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['my_name']:''?>" />
	    <span>　　性别:</span>
		 <SELECT NAME="sex">
			<OPTION VALUE="1" <?php echo ($Auser['sex'] == 1)?'SELECTED':''?>>男</OPTION>
			<OPTION VALUE="0" <?php echo ($Auser['sex'] != 1)?'SELECTED':''?>>女</OPTION>
		</SELECT>
		<span>　身份证:</span>
		<input name="idc" type="text" size="20" value="<?php echo ($Auser['user_id'])?$Auser['idc']:''?>" />

		<br/><span>　邮　编:</span>
		<input name="zipcode" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['zipcode']:''?>" />
        <span>通讯地址:</span>
		<input name="address" type="text" size="62" value="<?php echo ($Auser['user_id'])?$Auser['address']:''?>" />

        <br/>
		<span>办公电话:</span>
        <input name="office_phone" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['office_phone']:''?>" />
		<span>家庭电话:</span>
		<input name="home_phone" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['home_phone']:''?>" />
		<span>手机:</span>
		<input name="mobile_phone" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['mobile_phone']:''?>" />

		<BR/>
		<span> 　EMAIL:</span>
		<input name="email" type="text" size="22" value="<?php echo ($Auser['user_id'])?$Auser['email']:''?>" />
		<span>　M S N:</span>
		<input name="mymsn" type="text" size="22" value="<?php echo ($Auser['user_id'])?$Auser['mymsn']:''?>" />
		   QQ:
		 <input name="qq" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['qq']:''?>" />

		<br/>
		<span style="font-weight: bold">个人照片:</span>
		<INPUT type="file" name="my_photo" size="20" /> 
		<?php
		if(!empty($Auser['my_photo']))
		{	
			$tmp = '<A HREF="../data/myphoto/'.$Auser['my_photo'].'" target="_blank">'; 
			$tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查看图片"></A> ';
			echo $tmp;
		}
		?>		
		(注：支持：.jpg .gif .png 格式)
		<INPUT type="hidden" name="old_my_photo"  value="<?php echo ($Auser['user_id'])?$Auser['my_photo']:'';?>" />
		<br/>
		<b>帐号属于经销商：</b>
		<?php echo $Stropt;?>[<A HREF="pravailability.php">经销商添加修改</A>]
		<br/>
        <input type="hidden" name="action" value="<?php echo ($Auser['user_id'])?'edit':'add'?>" />        
		<input type="hidden" name="user_id" value="<?php echo ($Auser['user_id'])?$Auser['user_id']:''?>" /> 
		<input type="submit" name="Submit" value="<?php echo ($Auser['user_id'])?'编辑修改':'增加新用户'?>" style="background-color: #FFCC66;margin-left: 50px""/>

    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="15%" align=left>用户名</TD>
	<TD width="20%" align=left>联系方法/所属经销商</TD>
    <TD width="55%" align=left>权限列表</TD>
	 <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="4" align=right><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<?php
/* OPTION 递归 分公司 */
function get_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre."pravail";
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
		$str .= '　';
		$tn = 0;
		while( @list( $k, $v ) = @each( $Agrad ) ) 
		{
			if ($v == 0 && $v =='')
			{
			  break;
			}		   
			$sql = "SELECT * FROM ".$db_table." where praid = $v";
			$Anorm = $oPub->getRow($sql);
			if( $Anorm["pra_name"] != '')
			{
				$tn ++;
				if($Anorm[cotype] == 1)
				{
					$cotype = '供货商'; 
				} elseif($Anorm[cotype] == 2)
				{
					$cotype = '代理商';
				}else
				{
					$cotype = '子公司';
				}
				$selected = ($fid == $v)? 'SELECTED':'';
				$Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["pra_name"].'</OPTION>';
				$Stropt .= get_next_node($Anorm["next_node"],$fid,$str);
			} 
		}
	}
	return $Stropt;
}
?>
<?php
include_once( "footer.php");
?>
