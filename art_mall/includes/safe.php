<?php
 
$getfilter="'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq){ 
	if(is_array($StrFiltValue))
	{
		$StrFiltValue=implode($StrFiltValue);
	}  
	if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1){   
		   $str = "<br><br>QQ:16953292 :".$_SERVER["REMOTE_ADDR"]."<br>time: ".strftime("%Y-%m-%d %H:%M:%S")."<br>php_self:".$_SERVER["PHP_SELF"]."<br>type: ".$_SERVER["REQUEST_METHOD"]."<br>key: ".$StrFiltKey."<br>vaue: ".$StrFiltValue ;
			exit();
	}      
}  
//$ArrPGC=array_merge($_GET,$_POST,$_COOKIE);
foreach($_GET as $key=>$value){ 
	StopAttack($key,$value,$getfilter);
}
foreach($_POST as $key=>$value){ 
	StopAttack($key,$value,$postfilter);
}
foreach($_COOKIE as $key=>$value){ 
	StopAttack($key,$value,$cookiefilter);
}
//if (file_exists('update360.php')) {
	//echo "请重命名文件update360.php，防止黑客利用<br/>";
    //die();
//}

?>