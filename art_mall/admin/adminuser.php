<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."user";
//post
if( $action == 'add'  )
{
	if (trim($password) == '' || $user_name == '')
	{
       $strMessage =  "用户名与密码不能为空！";
	} else
	{

         $user_name = clean_html(trim($user_name));
         $sql = "SELECT id FROM ".$pre."users WHERE `user_name` LIKE '".$user_name."'";
         $Auser = $oPub->getRow($sql);
         if($Auser['id'] > 0)
         {
  	          $strMessage = '添加失败：此帐号已经被使用！';
         }
	     else
	    {

	       $action_list_str = '';
           if(count($action_list) > 0 )
	       {
		       foreach( $action_list as  $v )
			   {
			       $action_list_str .= $v.',';
		       }
               $action_list = substr($action_list_str,0,-1);
	       }
           
           $articlecat_list_str = '';
	       if(count($articlecat_list) > 0)
	       {
		 

		       foreach( $articlecat_list as  $v )
			   {
			       $articlecat_list_str .= $v.',';
		       }
               $articlecat_list = substr($articlecat_list_str,0,-1);
           }

			$Afields=array('user_name'=>$user_name, 'password'=>mkmd5($password),'email'=>$email,'reg_time'=>gmtime(),'ifmanger'=>1,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre.'users',$Afields);
			$user_id =  $oPub->insert_id();
 
			$Afields=array('user_id'=>$user_id,'user_name'=>$user_name,'add_time'=>gmtime(),'praid'=>$praid,'action_list'=>$action_list,'articlecat_list'=>$articlecat_list,'domain_id'=>$Aconf['domain_id']);
			$oPub->install($pre."admin_user",$Afields); 
			/* 增加姓名 */
			$strMessage =  "用户添加成功!";
	  }
	 unset($Auser);
    }
}

$db_table = $pre."admin_user";
if( $action == 'edit'){
	$action_list_str = '';
    if(count($action_list) > 0 && $_SESSION['domain_user_id'] != $user_id)
	{
		foreach( $action_list as  $v ) {
			$action_list_str  .= $v.',';
		}
        $action_list = substr($action_list_str ,0,-1);
	}

    $articlecat_list_str = '';
    if(count($articlecat_list) > 0) { 
		foreach( $articlecat_list as  $v ) {
			$articlecat_list_str .= $v.',';
		} 
        $articlecat_list = substr($articlecat_list_str,0,-1);
    }

	if($user_id == $_SESSION['domain_user_id'])
	{
        $action_list = 'all';
	}

	$Afields=array('praid'=>$praid,'action_list'=>$action_list,'articlecat_list'=>$articlecat_list);
	$condition = 'user_id='.$user_id.' AND domain_id = '.$Aconf['domain_id'];
	$oPub->update($pre."admin_user",$Afields,$condition);
	$strMessage = "权限设置成功 ";

	if(!empty($password)) {
		$Afields=array('password'=>mkmd5($password));
		$condition = 'id='.$user_id.' AND domain_id = '.$Aconf['domain_id'];
		$oPub->update($pre."user",$Afields,$condition);
		$strMessage .= " 密码成功修改 ";
	} 
}

//get
if( $action == 'edit'){
	$sql = "SELECT * FROM ".$pre."admin_user WHERE user_id = ".$user_id. " AND domain_id=".$Aconf['domain_id'];
	$Auser = $oPub->getRow($sql);
}

if( $action == 'del'){
	if($user_id != $_SESSION['domain_user_id'])
	{
		$Afields=array('ifmanger'=>0); 
		$condition = "id = ".$user_id." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($pre.'users',$Afields,$condition);

	    $condition = 'user_id='.$user_id;
        $oPub->delete($pre."admin_user",$condition);
		$tmpID = $user_id;
	} else {
       $strMessage = "特权用户不能删除!";
	   $tmpID = 0;
	}
   $db_table = $pre.'account_log';
   $change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
   $change_desc .= ' | '.$_SESSION['auser_name'].' 用户删除:'.$tmpID;
   $Afields=array('user_id'=>$Auser['user_id'],'type'=>'userDel','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
   $oPub->install($db_table,$Afields);
}


//page
$strWhere = '';
if($user_name && $action == 'serarchxy') {
  $strWhere = " and b.user_name  LIKE '%".$user_name."%'"; 
}
 
$strWhere = " WHERE b.ifmanger=1 and a.user_id = b.id and a.domain_id=".$Aconf['domain_id'].$strWhere;
$sql = "SELECT count( * ) AS count FROM ".$pre."admin_user as a,".$pre."users as b ".$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT a.*,b.user_name FROM ".$pre."admin_user as a,".$pre."users as b ".$strWhere." ORDER BY b.id desc limit ".$strOffSet;
$AuserAll = $oPub->select($sql);

$StrtypeAll = '';
$n = 0;
if($AuserAll)
foreach ($AuserAll AS $key => $value)
{	

 
      $tmpstr = ($n % 2 == 0)?"even":"odd";
	  //账号属性
       $user_type = ($value["user_type"])?' 记者':' 网编';
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value['user_name'] .$user_type .'</TD>';
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
       /* 查询文章分类权限 start */       
	   $acidname = $strAcid = '';
       if($value["articlecat_list"])
		{
			$Aarticlecat_list_t = explode(",",$value["articlecat_list"]);
			//通过提交的分类查找包含的下级分类
			$db_table = $pre."articat";
		    foreach ($Aarticlecat_list_t AS  $v)
		    {
                  $strAcid .= $v.','.next_node_all($v,$db_table,'acid',true).',';
		    }
            $Aarticlecat_list = explode(',',$strAcid);
		    $Aarticlecat_list = array_unique($Aarticlecat_list);
		    $articlecat_list = '';
	 		foreach ($Aarticlecat_list AS  $v)
		    {
               if($v > 0 )
			    {
                      $articlecat_list .= $v.',';
			    }
		     } 
			 $articlecat_list = substr($articlecat_list,0,-1);
		    //查找包含的下级分类 end 
            $sql = "SELECT name FROM ".$db_table." where ifshow = 1 AND domain_id=".$Aconf['domain_id']." AND acid in(".$articlecat_list.") ORDER BY acid ASC";
            $row = $oPub->select($sql);
		    while( @list( $k, $v ) = @each( $row) ) 
		    {
               $acidname .= $v[name].',';
		    }
	   }
	   if($acidname)
	   {
		   $acidname = substr($acidname,0,-1);
	   }
	   $StrtypeAll .= '<TD align=left><A TITLE="'.$acidname.'">'.sub_str($acidname,18).'</A></TD>';
	   /* 查询文章分类权限 end */ 

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

		   $pra_name = $Apav['pra_name'];
	   }
	   $StrtypeAll .= '<TD align=left>'.$pra_name .'</TD>';
       $StrtypeAll .= '<TD align=left>';

       $StrtypeAll .= '<a href="'.PHP_SELF.'?user_id='.$value["user_id"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
	   $StrtypeAll .= '<a href="'.PHP_SELF.'?user_id='.$value["user_id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a>';
       $StrtypeAll .= '</TD></TR>';    
}

/* 权限复选框 */
$strPrive = '';
$j = 0;
$Auser_Prive['action_list'] = ($Auser['user_id'] && $Auser['action_list'] != '')?explode(",",$Auser['action_list']):array();
foreach( $Aprive as $k => $v ) 
{
   $strChecked = (in_array($k,$Auser_Prive['action_list']))?'checked':'';
   if($v)
	{
	   //$strk = 'list_br_'.$j;
	   $tmp = substr($k,0,7);
	   if('list_br' == $tmp )
		{
             $strPrive   .= '<br/><B  style="background-color:#FFE3C8">'.$v.':</B>&nbsp;&nbsp;';
 
		} else
		{
             $strPrive   .= '<INPUT TYPE="checkbox"  name="action_list[]" value="'.$k.'" '.$strChecked.'>'.$v.' ';
		}

      
	   if($k == 'article_comms')
	   { 
		   //文章分类复选框
		   $strPrive   .='<p style="margin-left: 30px;margin-top: 5px;line-height: 20px;background-color:#E0E4FC"><B>可以管理的文章分类</B>:(<U>注:需要同时选择文章添加与文章列表权限</U>)<br/>';
           $db_table = $pre."articat";
           $sql = "SELECT acid,name FROM ".$db_table." where fid = 0 AND ifshow = 1 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
           $AnormAll = $oPub->select($sql);
           $Aarticlecat_list = explode(",",$Auser["articlecat_list"]);
           while( @list( $k, $v ) = @each( $AnormAll) ) 
          {
             $strChecked = (in_array($v[acid],$Aarticlecat_list))?'checked':'';

			 $strPrive .= ' <INPUT TYPE="checkbox"  name="articlecat_list[]" value="'.$v[acid].'" '.$strChecked.'><span style="color:#cc0000;font-weight:bold; background-color:#C4C4FF">'.$v[name].'</span>';    
	         $strAcid = next_node_all($v[acid],$db_table,'acid',true);
	         if($strAcid)
	         {
	            $Aacids = explode(",",$strAcid);
                while( @list( $key, $value ) = @each( $Aacids ) )
               {  
		          if($value)
		          { 
			         $strChecked = (in_array($value,$Aarticlecat_list))?'checked':'';
			         $acidtmp = $oPub->getrow("SELECT fid,name FROM ".$db_table." where acid = '".$value."' AND domain_id=".$Aconf['domain_id']." limit 1");  
					 $fid = $acidtmp[fid];
					 $name = $acidtmp[name];
					 $fidname = '';
					 if($fid > 0)
					  {
			             $fidname = $oPub->getOne("SELECT  name FROM ".$db_table." where acid = '".$fid."' AND domain_id=".$Aconf['domain_id']." limit 1"); 
						 $fidname = '<span style="color: #6E6E6E">'.$fidname.":</span>";
					  }
		             $strPrive  .=' <INPUT TYPE="checkbox"  name="articlecat_list[]" value="'.$value.'" '.$strChecked.'>'.$fidname.$name;
		          }
	           }
	        }
			$strPrive .='<br/>';
          } 
		  /* 关联文章分类 end */
          $strPrive   .='</p>';
	   }
	}
	 else
	{
       
	   $strPrive   .= '<br/>';
	}

}
/* 得到分公司列表 */
$db_table = $pre."pravail";
/* 找到所有的分类到select start*/
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY praid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="praid">';
$Stropt .= '<OPTION VALUE="0" >不属于经销商</OPTION>';
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

<div class="box" style="padding: 0;">
<TABLE width="100%" border=0 style="padding: 0;">
  <TR  > 
    <TD align="left" colspan="1">
	<form name="form1" method="post" action="" >
	   <div class="button" style="padding: 0;">
			<div class="line"></div>
			<a href="<?php echo PHP_SELF?>">[增加新管理员]</a>    
			<span style="font-weight: bold">登录帐号:</span>
			<?php
			if($Auser['user_id'] > 0)
			{
				echo $Auser['user_name'];
			} else
			{
			?>
			<input name="user_name" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['user_name']:''?>" />
			<span style="font-weight: bold">邮箱:</span>
			<input name="email" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['email']:''?>" />
			<?php			
			}
			?>
			<span style="font-weight: bold">密码:</span>
			<input name="password" type="text" size="12" value="" />
			
			<?php echo ($Auser['user_id'])?'如果不需重设密码，请保持为空':''?>
		</div>
		
		<?PHP
		if($Aconf['cqt_articles'])
		{
			//启用文章审核才显示账号属性
			$user_typeChecked1 = ($Auser['user_type'])?'CHECKED':'';
			$user_typeChecked0 = ($Auser['user_type'])?'':'CHECKED';
			$str = '<b>账号属性:</b>';
			$str .= '记者<INPUT TYPE="radio" NAME="user_type" value="1" '.$user_typeChecked1.'>';
			$str .= '网络编辑<INPUT TYPE="radio" NAME="user_type" value="0" '.$user_typeChecked0.'>';
			$str .= '<br/>';
			echo $str;
		} 
		?> 
		<b style="background-color:#C4C4FF">设置管理权限：</b>
		<div  style="margin-left: 50px;margin-top:-20px;line-height:28px;">
		<?php echo $strPrive;?>
		</div>
		<input type="hidden" name="action" value="<?php echo ($Auser['user_id'])?'edit':'add'?>" />        
		<input type="hidden" name="user_id" value="<?php echo ($Auser['user_id'])?$Auser['user_id']:''?>" />  
		<b  style="background-color:#C4C4FF">指定管理经销商：</b>
		<?php echo $Stropt;?>[<A HREF="pravailability.php">经销商添加修改</A>]
		<br/>
		<input type="submit" name="Submit" value="<?php echo ($Auser['user_id'])?'编辑修改':'确认增加新管理员'?>" style="background-color: #FFCC66;margin-left:50px"/>
	</form>
    </TD>
    
  </TR>
</TABLE>
<TABLE  width="100%" border=0 class="button">
  <TR >
    <TD align=middle>
		<div class="line"></div>
		<form name="form3" method="post" action="" style="margin: 0px"> 
		<input name="user_name" type="text" size="12" value="<?php echo ($Auser['user_id'])?$Auser['user_name']:''?>" />
		<input type="hidden" name="action" value="serarchxy" />
		<input type="submit" name="Submit" value="用户名[帐号]搜索" style="background-color: #FFCC66;"/>
		</form>
	</TD>
  </TR>
</TABLE>
<TABLE  width="100%" border=0>
  <TR class=bg5>
    <TD align=left>
	用户名[帐号]	
	</TD>
    <TD align=left width="50%">文件权限列表</TD>
	<TD align=left>文章分类权限列表</TD>
	<TD align=left>管理的经销商</TD>
	<TD align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="5" align=right><?php echo $showpage = $page->ShowLink();?></TD>
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
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where praid = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm["pra_name"] != ''){
			   $tn ++;
	          if($Anorm[cotype] == 1)
	          {
		        $cotype = '供货商';

	          }
	          elseif($Anorm[cotype] == 2)
	          {
		        $cotype = '代理商';
		      }
	          else
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