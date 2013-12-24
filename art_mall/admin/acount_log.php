<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if(!empty($Aconf['priveMessage']))
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

/*------------------------------------------------------ */
//-- 批量删除文章记录
/*------------------------------------------------------ */  
if ($action == 'del') {	
	if(!isset($_GET['log_id'])) $_GET['log_id']=false; 
	$_GET['log_id'] = $_GET['log_id'] + 0;
    if (isset($_POST['checkboxes'])) {
        $count = 0;		
        foreach ($_POST['checkboxes'] AS $key => $id) {	 
           $condition = "log_id='".$id."' AND domain_id='".$Aconf['domain_id']."'";
           $oPub->delete($pre."account_log",$condition);
        }
		$tmpID = implode(",",$_POST['checkboxes']);
        $strMessage =  "批量删除成功!";
   } else if(isset($_GET['log_id'])) {
        $id = $_GET['log_id'];
	   $condition = "log_id='".$id."' AND domain_id='".$Aconf['domain_id']."'";
	   $oPub->delete($pre."account_log",$condition); 
		$tmpID = $id; 
		$strMessage =  "删除成功!";
   } else {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

   $db_table = $pre.'account_log';
   $change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
   $change_desc .= ' | '.$_SESSION['auser_name'].' 日志删除:'.$tmpID;
   $Afields=array('user_id'=>$_SESSION['auser_id'] ,'type'=>'artiDel','change_desc'=>$change_desc,'states'=>1,'domain_id'=>$Aconf['domain_id']);
   $oPub->install($db_table,$Afields);
}

/* 查询条件 */
if(!isset($_REQUEST["acid"])) $_REQUEST["acid"]=false; 
if(!isset($filter['sort_by'])) $filter['sort_by']=false; 
$_REQUEST["acid"] = $_REQUEST["acid"] +0;
$db_table = $pre."account_log";
$where = "states=0 AND domain_id = '".$Aconf['domain_id']."'"; 
$count = $oPub->getOne("SELECT COUNT(*) as count FROM ".$pre."account_log AS a WHERE 1 AND ". $where); 

$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('acid'=>$_REQUEST["acid"],'sort_by'=>$filter['sort_by']); 
$strOffSet = $page->OffSet();

$row = $oPub->select("SELECT * FROM ".$pre."account_log WHERE  $where ". " ORDER BY log_id DESC LIMIT ". $strOffSet); 
$StrtypeAll = '';
$n = 0;
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["log_id"].']" value="'.$val["log_id"].'" />';
	   $StrtypeAll .= '</TD>';

	   $db_table = $pre.'admin_user';
	   $sql = "SELECT user_name FROM ". $db_table ."  where user_id= ".$val["user_id"];
	   $rowuser_name = $oPub->getRow($sql);

	   $StrtypeAll .= '<TD align=left>'.$rowuser_name["user_name"].'</TD>';

       $StrtypeAll .= '<TD align=left>'.$val["type"].'</TD>';

	   $StrtypeAll .= '<TD align=left>'.$val["change_desc"].'</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="'.$PHP_SELF.'?log_id='.$val["log_id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       $StrtypeAll .= '</TD></TR>';    
} 


$Ahome["showpage"]      = $page->ShowLink();
$Ahome["page"]			= $pagenew;
$Ahome["StrtypeAll"]    = $StrtypeAll; 
$Ahome["nowName"]       = $nowName; 
$Ahome["strMessage"]    = $strMessage;  
assign_template($Aconf); 
$smarty->assign('home', $Ahome );  
$smarty->display($Aconf["displayFile"]); 
?>
 
