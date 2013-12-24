/*
==轮播{对象|对象属性}==
对象属性{宽度|高度|文字大小|自动切换时间}
*/
function dk_slideplayer(object,config){
	this.obj = object;
	this.config = config ? config : {width:"300px",height:"200px",fontsize:"12px",right:"10px",bottom:"10px",time:"5000"};
	this.pause = false;
	var _this = this;
	if(!this.config.right){
		this.config.right = "0px"
	}
	if(!this.config.bottom){
		this.config.bottom = "3px"
	}
	if(this.config.fontsize == "12px" || !this.config.fontsize){
		this.size = "12px";
		this.height = "21px";
		this.right = "6px";
		this.bottom = "10px";
	}else if(this.config.fontsize == "14px"){
		this.size = "14px";
		this.height = "23px";
		this.right = "6px";
		this.bottom = "15px";
	}
	this.count = jQuery(this.obj + " li").size();
	this.n =0;
	this.j =0;
	var t;
	this.factory = function(){
		jQuery(this.obj).css({position:"relative",zIndex:"0",margin:"0",padding:"0",width:this.config.width,height:this.config.height,overflow:"hidden"})
		jQuery(this.obj).prepend("<div style='position:absolute;z-index:20;right:"+this.config.right+";bottom:"+this.config.bottom+"'></div>");
		jQuery(this.obj + " li").css({width:"100%",height:"100%",overflow:"hidden"}).each(function(i){jQuery(_this.obj + " div").append("<a>"+(i+1)+"</a>")});

		jQuery(this.obj + " img").css({border:"none",width:"100%",height:"100%"})

		this.resetclass(this.obj + " div a",0);

		jQuery(this.obj + " p").each(function(i){			
			jQuery(this).parent().append(jQuery(this).clone(true));
			jQuery(this).html("");
			jQuery(this).css({position:"absolute",margin:"0",padding:"0",zIndex:"1",bottom:"0",left:"0",height:_this.height,width:"100%",background:"#000",opacity:"0.4",overflow:"hidden"})
			jQuery(this).next().css({position:"absolute",margin:"0",padding:"0",zIndex:"2",bottom:"0",left:"0",height:_this.height,lineHeight:_this.height,textIndent:"5px",width:"100%",textDecoration:"none",fontSize:_this.size,color:"#FFFFFF",background:"none",zIndex:"1",opacity:"1",overflow:"hidden"})
			if(i!= 0){jQuery(this).hide().next().hide()}
		});

		this.slide();
		this.addhover();
		t = setInterval(this.autoplay,this.config.time);
	}
	
	this.slide = function(){
		jQuery(this.obj + " div a").mouseover(function(){
			_this.j = jQuery(this).text() - 1;
			_this.n = _this.j;
			if (_this.j >= _this.count){return;}
			jQuery(_this.obj + " li").hide();
			jQuery(_this.obj + " p").hide();
			jQuery(_this.obj + " li").eq(_this.j).fadeIn("slow");
			jQuery(_this.obj + " li").eq(_this.j).find("p").show();
			_this.resetclass(_this.obj + " div a",_this.j);
		});
	}

	this.addhover = function(){
		jQuery(this.obj).hover(function(){clearInterval(t);}, function(){t = setInterval(_this.autoplay,_this.config.time)});
	}
	
	this.autoplay = function(){
		_this.n = _this.n >= (_this.count - 1) ? 0 : ++_this.n;
		jQuery(_this.obj + " div a").eq(_this.n).triggerHandler('mouseover');
	}
	
	this.resetclass =function(obj,i){
		jQuery(obj).css({float:"left",marginRight:"3px",width:"15px",height:"14px",lineHeight:"15px",textAlign:"center",fontWeight:"800",fontSize:"12px",color:"#000",background:"#FFFFFF",cursor:"pointer"});
		jQuery(obj).eq(i).css({color:"#FFFFFF",background:"#FF7D01",textDecoration:"none"});
	}

	this.factory();
}


/**************************************************   
名称: 图片轮播类   
创建时间: 2007-11-12   
示例:   
        页面中已经存在名为imgPlayer(或者别的ID也行)的节点.   
        PImgPlayer.addItem( "test", "http://osunit.com", "http://osunit.com/images/wy.jpg");   
        PImgPlayer.addItem( "test2", "http://osunit.com", "http://osunit.com/images/wy.jpg");   
        PImgPlayer.addItem( "test3", "http://osunit.com", "http://osunit.com/images/wy.jpg");   
        PImgPlayer.init( "imgPlayer", 200, 230 );   
备注:   
        适用于一个页面只有一个图片轮播的地方.   
***************************************************/   
var PImgPlayer = {   
        _timer : null,   
        _items : [],   
        _container : null,   
        _index : 0,   
        _imgs : [],   
		
        intervalTime : 6000,        //轮播间隔时间   
        init : function( objID, w, h, time ){   
                this.intervalTime = time || this.intervalTime;   
                this._container = document.getElementById( objID );   
                this._container.style.display = "block";   
                this._container.style.width = w + "px";   
                this._container.style.height = h + "px";   
                this._container.style.position = "relative";   
                this._container.style.overflow = "hidden";   
                //this._container.style.border = "1px solid #fff";   
                var linkStyle = "display: block; TEXT-DECORATION: none;";   
                if( document.all ){   
                        linkStyle += "FILTER:";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Barn(duration=0.5, motion='out', orientation='vertical') ";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Barn ( duration=0.5,motion='out',orientation='horizontal') ";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Blinds ( duration=0.5,bands=10,Direction='down' )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.CheckerBoard()";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Fade(duration=0.5,overlap=0)";   
                        linkStyle += "progid:DXImageTransform.Microsoft.GradientWipe ( duration=1,gradientSize=1.0,motion='reverse' )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Inset ()";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Iris ( duration=1,irisStyle=PLUS,motion=out )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Iris ( duration=1,irisStyle=PLUS,motion=in )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Iris ( duration=1,irisStyle=DIAMOND,motion=in )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Iris ( duration=1,irisStyle=SQUARE,motion=in )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Iris ( duration=0.5,irisStyle=STAR,motion=in )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.RadialWipe ( duration=0.5,wipeStyle=CLOCK )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.RadialWipe ( duration=0.5,wipeStyle=WEDGE )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.RandomBars ( duration=0.5,orientation=horizontal )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.RandomBars ( duration=0.5,orientation=vertical )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.RandomDissolve ()";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Spiral ( duration=0.5,gridSizeX=16,gridSizeY=16 )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Stretch ( duration=0.5,stretchStyle=PUSH )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Strips ( duration=0.5,motion=rightdown )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Wheel ( duration=0.5,spokes=8 )";   
                        linkStyle += "progid:DXImageTransform.Microsoft.Zigzag ( duration=0.5,gridSizeX=4,gridSizeY=40 ); width: 100%; height: 100%";   
                }   
                //   
                var ulStyle = "margin:0;width:"+w+"px;position:absolute;z-index:999;FILTER:Alpha(Opacity=50,FinishOpacity=50, Style=1);overflow: hidden;bottom:3px;right:5px;height:16px; border-right:1px solid #fff;";   
                //   
                var liStyle = "margin:0;list-style-type:none; padding:0; float:right;";   
                //   
                var baseSpacStyle = "clear:both; display:block; width:18px; height:14px;line-height:14px; font-size:12px; FONT-FAMILY:'宋体';opacity: 0.6;";   
                baseSpacStyle += "border:1px solid #fff;border-right:0;";   
                baseSpacStyle += "color:#fff;text-align:center; cursor:pointer; ";   
                //   
                var ulHTML = "";   
                for(var i = this._items.length -1; i >= 0; i--){   
                        var spanStyle = "";   
                        if( i==this._index ){   
                                spanStyle = baseSpacStyle + "background:#f54100;";   
                        } else {                                   
                                spanStyle = baseSpacStyle + "background:#000;";   
                        }   
                        ulHTML += "<div style=\""+liStyle+"\">";   
                        ulHTML += "<span onmouseover=\"PImgPlayer.mouseOver(this);\" onmouseout=\"PImgPlayer.mouseOut(this);\" style=\""+spanStyle+"\" onclick=\"PImgPlayer.play("+i+");return false;\" herf=\"javascript:;\" title=\"" + this._items[i].title + "\">" + (i+1) + "</span>";   
                        ulHTML += "</div>";   
                }   
                //   
                var html = "<a href=\""+this._items[this._index].link+"\" title=\""+this._items[this._index].title+"\" target=\"_blank\" style=\""+linkStyle+"\"></a><ul style=\""+ulStyle+"\">"+ulHTML+"</ul>";   
                this._container.innerHTML = html;   
                var link = this._container.getElementsByTagName("A")[0];           
                link.style.width = w + "px";   
                link.style.height = h + "px";   
                link.style.background = 'url(' + this._items[0].img + ') no-repeat center center';   
                //   
                this._timer = setInterval( "PImgPlayer.play()", this.intervalTime );   
        },   
        addItem : function( _title, _link, _imgURL ){   
                this._items.push ( {title:_title, link:_link, img:_imgURL } );   
                var img = new Image();   
                img.src = _imgURL;   
                this._imgs.push( img );   
        },play : function( index ){   
                if( index!=null ){   
                        this._index = index;   
                        clearInterval( this._timer );   
                        this._timer = setInterval( "PImgPlayer.play()", this.intervalTime );   
                } else {   
                        this._index = this._index<this._items.length-1 ? this._index+1 : 0;   
                }   
                var link = this._container.getElementsByTagName("A")[0];           
                if(link.filters){   
                        var ren = Math.floor(Math.random()*(link.filters.length));   
                        link.filters[ren].Apply();   
                        link.filters[ren].play();   
                }   
                link.href = this._items[this._index].link;   
                link.title = this._items[this._index].title;   
                link.style.background = 'url(' + this._items[this._index].img + ') no-repeat center center';   
                //   
                var liStyle = "margin:0;list-style-type: none; margin:0;padding:0; float:right;";   
                var baseSpacStyle = "clear:both; display:block; width:18px; height:14px;line-height:14px; font-size:12px; FONT-FAMILY:'宋体'; opacity: 0.6;";   
                baseSpacStyle += "border:1px solid #fff;border-right:0;";   
                baseSpacStyle += "color:#fff;text-align:center; cursor:pointer; ";   
                var ulHTML = "";   
                for(var i = this._items.length -1; i >= 0; i--){   
                        var spanStyle = "";   
                        if( i==this._index ){   
                                spanStyle = baseSpacStyle + "background:#f54100;";   
                        } else {                                   
                                spanStyle = baseSpacStyle + "background:#000;";   
                        }   
                        ulHTML += "<div style=\""+liStyle+"\">";   
                        ulHTML += "<span onmouseover=\"PImgPlayer.mouseOver(this);\" onmouseout=\"PImgPlayer.mouseOut(this);\" style=\""+spanStyle+"\" onclick=\"PImgPlayer.play("+i+");return false;\" herf=\"javascript:;\" title=\"" + this._items[i].title + "\">" + (i+1) + "</span>";   
                        ulHTML += "</div>";   
                }   
                this._container.getElementsByTagName("UL")[0].innerHTML = ulHTML;           
        },   
        mouseOver : function(obj){   
                var i = parseInt( obj.innerHTML );   
                if( this._index!=i-1){   
                        obj.style.color = "#f54100";   
                }   
        },   
        mouseOut : function(obj){   
                obj.style.color = "#fff";   
        }   
}   


function login_showhide() {
	//return true;
	$('#login2').toggle();
	$('#login1').toggle();
	return false;
}


 