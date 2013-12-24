	function $(id){
		return document.getElementById(id);
	}
	function showmenu(id,classname){
		if($(id+"_menu").style.display==""){
			$(id).className=classname;
			$(id+"_menu").style.display="none";
		}else{
			$(id).className=classname+"_b";
			$(id+"_menu").style.display="";
		}
	}
	function putvalue(id,liid,cid,m,sid){
		$(id).innerHTML=$(liid).innerHTML;
		$(id+"_value").value=cid;
        $(id+"_value").focus();
        $(m).style.display='none';
        $(sid).style.display='none';
	}

    function loadstype(m,id){
        var scrolltop=document.documentElement.scrollTop;
		var scrollheight=document.documentElement.scrollHeight;
        $(m).style.width="100%";
        $(m).style.height=scrollheight+"px";
        $(m).style.display='block';
        $(id).style.top=(scrolltop+94)+'px';
		$(id).style.display='block';
    }

    function closestype(m,id){
        $(m).style.display='none';
        $(id).style.display='none';
    }