<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if(!empty($Aconf['priveMessage']))
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."pravail";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit' ) {
	$cotype = 0; 
	if(!isset($fid))       $fid=false; 
	if(!isset($praid))   $praid=false;
	if(!isset($ifshow)) $ifshow=false;
	if(!isset($cotype)) $cotype=false;
	if(!isset($pra_name)) $pra_name=false;
	if(!isset($sort_order)) $sort_order=false;
	if(!isset($password)) $password=false;
	if(!isset($user_name)) $user_name=false;
	if(!isset($my_name)) $my_name=false;
	if(!isset($user_id)) $user_id=false;
	if(!isset($add_time)) $add_time=false;
	if(!isset($action_list)) $action_list=false; 

	if($_POST['action'] == 'add' && $_POST['pra_name']) {  
        $pra_name = clean_html(trim($pra_name));
        $tmppraid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE `pra_name` LIKE '".$pra_name."'"); 
        if($tmppraid > 0) {
  	             $strMessage = '此厂商已存在，不能添加';
        } else {
			$sql = "INSERT INTO " . $db_table . "(fid,cotype,pra_name,dateadd,sort_order,domain_id)" .
				 "VALUES ('$fid','$cotype','$pra_name',  '".gmtime()."','$sort_order','".$Aconf['domain_id']."')"; 
			$oPub->query($sql);
			$praid = ($_POST['action'] == 'add') ? $oPub->insert_id() : $praid;
			if($praid) {
				$fid = $fid + 0;
				$Anorm = $oPub->getRow("SELECT next_node FROM ".$db_table." where praid = ".$fid." AND domain_id=".$Aconf['domain_id']); 
				$next_node = ($Anorm["next_node"] == '' || $Anorm["next_node"] == 0) ?$praid:$Anorm["next_node"].','.$praid;
				$Afields=array('next_node'=>$next_node);
				$condition = "praid = ".$fid." AND domain_id=".$Aconf['domain_id'];
				$oPub->update($db_table,$Afields,$condition); 
			}
			unset($Anorm);
			$strMessage = '添加成功';
		}
	    
	} else if($_POST['action'] == 'edit' && $praid ) { 
	    $strwhere = " where praid = ".$praid." AND domain_id=".$Aconf['domain_id'];
	    $Anorm = $oPub->getRow("SELECT fid,next_node FROM ".$db_table.$strwhere); 
	    if($Anorm['fid'] != $fid) {
         /* 修改原有节点标识 */
	      $strwhere = " where praid = ".$Anorm['fid'];
	      $Aupn = $oPub->getRow("SELECT * FROM ".$db_table.$strwhere); 
	      $next_node = str_replace(','.$praid,'',$Aupn['next_node']);
	      $next_node = str_replace($praid.',','',$next_node);
	      $next_node = str_replace($praid,'',$next_node);
          $Afields=array('next_node'=>$next_node);
	      $condition = "praid = ".$Anorm['fid']." AND domain_id=".$Aconf['domain_id'];
          $oPub->update($db_table,$Afields,$condition);
          unset($Aupn);
	     /* 插入新节点 */
          $Anins = $oPub->getRow("SELECT next_node FROM ".$db_table." where praid = ".$fid." AND domain_id=".$Aconf['domain_id']); 
	      $next_node = ($Anins[next_node] == '' || $Anins[next_node] == 0) ?$praid:$Anins[next_node].','.$praid;
          $Afields=array('next_node'=>$next_node);
	      $condition = "praid = ".$fid." AND domain_id=".$Aconf['domain_id'];
	      $oPub->update($db_table,$Afields,$condition);
	      unset($Anins);
	    } 
		$oPub->query("UPDATE " . $db_table . " SET  fid= '$fid', cotype='$cotype', pra_name= '$pra_name', sort_order = '$sort_order', ifshow = '$ifshow' 
		       WHERE `praid` =".$praid." and domain_id=".$Aconf['domain_id']); 
		$strMessage = '修改成功';
    
	}
    
	//判断是否添加管理账号 start 
	if($praid) {

	   if (trim($password) == '' || $user_name == '') {
            $strMessage =  "商家添加成功，请添加管理账号与初始密码！";
	   } else { 
			$user_name = clean_html(trim($user_name)); 
			$sql = "SELECT id FROM ".$pre."users WHERE `user_name` LIKE '".$user_name."'";
			$Auser = $oPub->getRow($sql);
           if($Auser['id'] > 0) {
  	             $strMessage = '此帐号已经被其它用户使用！请添加不同的管理账号';
           } else {
				$action_list = implode(",",$admin_nave["list_br_3"]); 

				$password  =  trim($password); 
				$Afields=array('user_name'=>$user_name, 'password'=>mkmd5($password),'email'=>$email,'reg_time'=>gmtime(),'ifmanger'=>1,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre.'users',$Afields);
				$user_id =  $oPub->insert_id();

				$Afields=array('user_id'=>$user_id,'user_name'=>$user_name,'add_time'=>gmtime(),'praid'=>$praid,'action_list'=>$action_list,'domain_id'=>$Aconf['domain_id']);
				$oPub->install($pre."admin_user",$Afields); 

				$strMessage =  "管理账号添加成功!";
		  }
	   } 
    }
   //判断是否添加管理账号 end
	unset($Anorm);unset($_POST);
}

//get
$db_table = $pre."pravail";
if( $_GET['action'] == 'edit'){
	$praid = $praid +0;
	$Anorm = $oPub->getRow("SELECT * FROM ".$db_table." where praid = ".$praid." AND domain_id=".$Aconf['domain_id']); 
	//查找所有能管理此经销商的账号  
	$row = $oPub->select("SELECT user_id,user_name,add_time,last_login FROM ".$pre."admin_user where praid = ".$praid." AND domain_id=".$Aconf['domain_id']);
	$struser = ''; 
    while( @list( $k, $v ) = @each( $row ) ) {
		$struser .= $v["user_name"].' ['.date("Y-m-n",$v["last_login"]).'] ';
    } 
	$Anorm["struser"] = $struser;
}

$db_table = $pre."pravail";
if( $_GET['action'] == 'del'){
	/*还有子分类将不能删除*/
	$praid = $praid + 0;
	$_GET['fid'] = $_GET['fid'] + 0;
	$strwhere = " where praid = ".$praid." AND domain_id=".$Aconf['domain_id'];
	$sql = "SELECT next_node FROM ".$db_table.$strwhere;
	$Anorm = $oPub->getRow($sql);
	if($Anorm["next_node"] == '' && $_GET['fid'] != 0) {
		/* 上级分类标识整理 */
		$strwhere = " where praid = ".$_GET['fid']." AND domain_id=".$Aconf['domain_id'];
		$sql = "SELECT * FROM ".$db_table.$strwhere;
		$Anorm = $oPub->getRow($sql);
		$next_node = str_replace(','.$praid,'',$Anorm['next_node']);
		$next_node = str_replace($praid,'',$next_node);
		$Afields=array('next_node'=>$next_node);
		$condition = "praid = ".$Anorm['praid'];
		$oPub->update($db_table,$Afields,$condition);
		unset($Anorm);

		$condition = 'praid='.$praid." AND domain_id=".$Aconf['domain_id'];
		$oPub->delete($db_table,$condition);
	} elseif($_GET['fid'] == 0 && $Anorm["next_node"] == '') {
	  $condition = 'praid='.$praid.' AND domain_id='.$Aconf['domain_id'];
      $oPub->delete($db_table,$condition);
	} else {
       $strMessage = '存在下级分类，不能删除。';
	}
}

 
$db_table = $pre."pravail";
$count = $oPub->getOne("SELECT COUNT(*) as count FROM ".$db_table); 
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table." WHERE fid=0 and domain_id=".$Aconf['domain_id']." ORDER BY  sort_order ASC,praid DESC LIMIT ". $strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
$db_table = $pre."admin_user";
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<A HREF="../shop.php?id='.$value["praid"].'" target="_blank">'.$n.'.'.$value["pra_name"].'</A>';
	   $StrtypeAll .= '</TD>';
	  //查找所有能管理此经销商的账号	   
	   $row = $oPub->select("SELECT user_id,user_name,add_time,last_login FROM ".$db_table." where praid = ".$value['praid']." AND domain_id=".$Aconf['domain_id']);
	   $struser = ''; 
       while( @list( $k, $v ) = @each( $row ) ) {
		   $struser .= $v["user_name"].','; 
       } 
	   if($struser) {
           $struser = substr($struser,0,-1);
	   }

	   $StrtypeAll .= '<TD align=left>'.$struser.'</TD>';
       if($value["descs"]) {
	       $descs = sub_str(clean_html($value["descs"]),10);
	   } else { 
           $descs = '请通过管理账号登陆后，修改经销商基本资料';
	   }

	   $StrtypeAll .= '<TD align=left>'.$descs.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["sort_order"].'</TD>'; 
       $StrtypeAll .= '<TD align=left><a href="'.PHP_SELF.'?praid='.$value["praid"].'&action=edit&fid=0&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.PHP_SELF.'?praid='.$value["praid"].'&action=del&fid=0&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a></TD>';
       $StrtypeAll .= '</TR>';  	
	   
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $StrtypeAll .= tab_next_node($value["next_node"],$value["praid"]);
	   }
	   /* 查找儿子 */	   
}

$Ahome["Auser"]         =$Auser;
$Ahome["showpage"]      = $page->ShowLink();
$Ahome["page"]			= $pagenew;
$Ahome["StrtypeAll"]    = $StrtypeAll;
$Ahome["Anorm"]		    = $Anorm; 
$Ahome["nowName"]       = $nowName; 
$Ahome["strMessage"]    = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]); 
?>

 

<?php
/* OPTION 递归 经销商 */
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
		        $cotype = '[供货商]';

	          }
	          elseif($Anorm[cotype] == 2)
	          {
		        $cotype = '[代理商]';
		      }
	          else
	          {
		        $cotype = '[子公司]';
	           }
			   $selected = ($fid == $v)? 'SELECTED':'';
		      $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$cotype.$Anorm["pra_name"].'</OPTION>';
              $Stropt .= get_next_node($Anorm["next_node"],$fid,$str);
		   }
		   
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre."pravail";
   $Agrad = explode(',',$next_node);
   $Strtab = '';
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
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["pra_name"].'</TD>';

	          if($Anorm[cotype] == 1)
	         {
		        $tmp = '供货商';

	         }
	         elseif($Anorm[cotype] == 2)
	         {
		        $tmp = '代理商';
		     }
	         else
	         {
		        $tmp = '子公司';
	          }
              $Strtab  .= '<TD align=left>';
	          $Strtab  .= $tmp;
	          $Strtab  .= '</TD>';
	          $Strtab  .= '<TD align=left>'.$Anorm["contact"].'</TD>';
	          $Strtab  .= '<TD align=left>'.$Anorm["pra_url"].'</TD>';
	          $descs = sub_str(clean_html($Anorm[descs]),10);
	          $Strtab  .= '<TD align=left>'.$descs.'</TD>';
	          $Strtab  .= '<TD align=left>'.$Anorm["sort_order"].'</TD>'; 
              $Strtab  .= '<TD align=left><a href="'.PHP_SELF.'?praid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= ' _ <a href="'.PHP_SELF.'?praid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str );
			}
	   }
	}
	return $Strtab;
}
?> 