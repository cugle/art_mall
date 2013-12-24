<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');  
 
 if( $op == 'show' && $id > 0){  
 
	$Anorm = $oPub->getRow('SELECT * FROM '.$pre.'users_job where id ='.$id.' AND domain_id="'.$Aconf['domain_id'].'"'); 
	$Rusers = '';
	$Rusers = $oPub->getRow('SELECT avatar,user_name FROM '.$pre.'users where id ="'.$Anorm['users_id'].'" AND domain_id="'.$Aconf['domain_id'].'" limit 1');  
	$avatar = ($Rusers["avatar"] > 0 )?'<IMG SRC="../data/userimg/avatar_big/'.$Rusers["avatar"].'_big.jpg"  BORDER="0">':'';

	//bieyexx gongzuojl {|} [|]
	$Abieyexx = explode(";",$Anorm['bieyexx']);
	$bieyexx = '<tr class="evenhr">';
	$bieyexx .= '<td class="hltd3">学历</td>';
	$bieyexx .= '<td class="hltd3">起止时间</td>';
	$bieyexx .= '<td class="hltd3">毕业院校</td>';
	$bieyexx .= '<td class="hltd3">专业</td>';
	$bieyexx .= '<td class="hltd3">外语程度</td>';
	$bieyexx .= '</tr>';
	while( @list( $k, $v ) = @each( $Abieyexx) ) 
	{ 
		if(!empty($v))
		{ 
			$bieyexx .= '<tr class="oddhr">';
			$At = array();
			$At = explode("{|}",$v);
			$n = 0;
			while( @list( $kx, $vx ) = @each( $At ) ) 
			{ 
				$A = explode("[|]",$vx);
				$bieyexx .= '<td class="hltd3">'.$A[1].'</td>'; 
				$n ++ ;
				if($n >= 5) break;
			}
			$bieyexx .= '</tr>';
		} 
	} 
	//$gongzuojl
 	$Agongzuojl = explode(";",$Anorm['gongzuojl']);
	$gongzuojl = '<tr class="evenhr">
					<td class="hltd3">起止时间</td>
					<td class="hltd3">工作单位</td>
					<td class="hltd3">职务</td>
					<td class="hltd3">离职原因</td>
					<td class="hltd3">证明人</td>
					<td class="hltd3">联系方式</td>
				</tr>';
	while( @list( $k, $v ) = @each( $Agongzuojl) ) 
	{ 
		if(!empty($v))
		{ 
			$gongzuojl .= '<tr class="oddhr">';
			$At = array();
			$At = explode("{|}",$v);
			$n = 0;
			while( @list( $kx, $vx ) = @each( $At ) ) 
			{ 
				$A = explode("[|]",$vx);
				$gongzuojl .= '<td class="hltd3">'.$A[1].'</td>'; 
				$n ++ ;
				if($n >= 6) break;
			}
			$bieyexx .= '</tr>';
		} 
	} 

	$str = '
			<table width="700" border="0" id="wdt" cellpadding="2" cellspacing="0">  
			  <tr>
				<td colspan="4" class="httd">一、基本信息</td>
			    <td rowspan="8" align="center" valign="middle" class="httd" style="background-color: #F3F0FD"> 
				
					<div style="float:left;text-align: center;width:170px;padding:0;overflow:hidden;">
						'.$avatar.'
					</div> 

				</td>
		      </tr> 
			  <tr class="oddhr">
				<td class="hltd1">姓名</td>
				<td class="hrtd1">'.$Anorm["xingming"].'</td> 
				<td class="hltd1">性别</td>
				<td class="hrtd1">'.$Anorm["sex"].'</td>
			  </tr>

			  <tr class="evenhr">
				<td class="hltd1">民族</td>
				<td class="hrtd1">'.$Anorm["mingzu"].'</td>
				<td class="hltd1">婚姻状况</td>
				<td class="hrtd1">'.$Anorm["hunyingzk"].'</td>
			  </tr>

			  <tr class="oddhr">
				<td class="hltd1">出生日期</td>  
				<td class="hrtd1">'.$Anorm["shengri"].'</td>
				<td class="hltd1">电子邮箱</td>
				<td class="hrtd1">'.$Anorm["email"].'</td>
			  </tr>
			  <tr class="evenhr">
				<td class="hltd1">联系电话</td>
				<td class="hrtd1">'.$Anorm["tel"].'</td>
				<td class="hltd1">身份证号码</td>
				<td class="hrtd1">
					'.$Anorm["idc"].'</td>
			  </tr>
			  <tr class="oddhr">
				<td class="hltd1">紧急情况联系电话</td>
				<td class="hrtd1">'.$Anorm["jingjitel"].'</td>
				<td class="hltd1">详细地址</td>
				<td class="hrtd1">'.$Anorm["addres"].'</td>
			  </tr> 
			  <tr class="evenhr">
				<td class="hltd1">应聘职位</td>
				<td class="hrtd1">'.$Anorm["yingpingzw"].'</td>
				<td class="hltd1">目前状态</td>
				<td class="hrtd1">'.$Anorm["jobstate"].'</td>
			  </tr>
			  <tr class="oddhr">
				<td class="hltd1">期望薪资</td>
				<td class="hrtd1">'.$Anorm["qiwangxz"].'</td>
				<td class="hltd1">到岗时间</td>
				<td class="hrtd1">'.$Anorm["daogangtime"].' </td>
			  </tr>
			</table> 

			<table width="700" border="0" id="wdt" cellpadding="2" cellspacing="0">  
			  <tr>
				<td colspan="5" class="httd" title="最近写在最前面">二、最近毕业学校 ? </td>
			  </tr>
				'.$bieyexx.' 
			</table>

 

 			<table width="700" border="0" id="wdt" cellpadding="2" cellspacing="0">  
			  <tr>
				<td colspan="6" class="httd" title="最近写在最前面">三、最近工作经历 ? </td>
			  </tr>
				'.$gongzuojl.' 
			</table>
			<table width="700" border="0" id="wdt" cellpadding="2" cellspacing="0">  
			  <tr>
				<td colspan="5" class="httd">五、备注(特长、荣誉、其它要求...)</td>
			  </tr>
				<tr class="evenhr">
					<td class="hltd3">'.$Anorm["descs"].'</td>
				</tr> 
			</table> 
	';
 
} else {
	$str = "";
}

 

$table = '<div style="width:98%;margin:2px 20px 5px 2px;font-weight: bold;color:#0000FF;font-size: 14px;"><span style="float:right;cursor:pointer;color:#00FF00" onmousedown=show_job("hidden")>关闭显示</span>'; 
$table .= '</div>'; 
$table .= '<table width="100%" border=1>'; 
 
$table .= $str;  
$table .= '</table>';
$str = $table; 
if(!empty($str)){
	echo $str;
}

 
 


 
?>