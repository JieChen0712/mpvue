$().ready(function() {
	var shflag=false;
	if(getCookie("condition")=="false"){
		shflag=false;
	}else if(getCookie("condition")=="true"){
		shflag=true
	}
	$("#btn-show").bind("click", function(event) {
		shflag=!shflag;
		setCookie("condition",shflag)
		console.log(document.cookie)
		if($(this).children("i").hasClass("glyphicon-chevron-left")) {
			hoverBtn(event)
			$(this).children("i").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
			$(".nav-action").children().hide(1);
//			$(".sidebar-menu").css({paddingLeft:'0px',width:'0px'});
			$(".sidebar").attr("class", "sidebar").css({
				width: "32px",
				padding: "20px 0px 20px 0"
			}).siblings(".container-fluid").children(".main").attr("class", "col-sm-12 main col-md-12");
//			$(".navbar-fixed-top").slideDown().parent("body").css({paddingTop:'0'});
			hoverShow(shflag,event);
		} else {
			hoverBtn(event)
			$(this).children("i").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
			$(".nav-action").children().show(1);
//			$(".sidebar-menu").css({paddingLeft:'28px'});
			$(".sidebar").attr({
				class: "col-sm-3 col-md-2 sidebar",
				style: "padding: 20px 0px"
			}).siblings(".container-fluid").children(".main").attr("class", "col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main");
//			$(".navbar-fixed-top").slideDown().parent("body").css({paddingTop:'50px'});	
			hoverShow(shflag,event);
		}
	});
	
	hoverShow(shflag,event)
});


function hoverShow(flag,event){
	if(!flag){
		$(".sidebar-menu").unbind("mouseenter");
		$(".sidebar-menu").unbind("mouseleave");
		return false;
	}else{
		hoverBtn(event)
		$(".sidebar-menu").bind("mouseenter",function(event){
			hoverBtn(event)
			$("#btn-show").children("i").removeClass("glyphicon-chevron-right").addClass("glyphicon-chevron-left");
			$(".nav-action").children().show(1);
//			$(".sidebar-menu").css({paddingLeft:'28px'});
			$(".sidebar").attr({
				class: "col-sm-3 col-md-2 sidebar",
				style: "padding: 20px 0px"
			}).siblings(".container-fluid").children(".main").attr("class", "col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main");
//			$(".navbar-fixed-top").slideDown().parent("body").css({paddingTop:'50px'});
			event.stopPropagation();
		});
		$(".sidebar-menu").bind("mouseleave",function(event){
			$("#btn-show").children("i").removeClass("glyphicon-chevron-left").addClass("glyphicon-chevron-right");
			$(".nav-action").children().hide(1);
//			$(".sidebar-menu").css({paddingLeft:'0px',width:"0px"});
			$(".sidebar").attr("class", "sidebar").css({
				width: "32px",
				padding: "20px 0px 20px 0"
			}).siblings(".container-fluid").children(".main").attr("class", "col-sm-12 main col-md-12");
//			$(".navbar-fixed-top").slideUp().parent("body").css({paddingTop:"0"});
			event.stopPropagation();
		});
	}
}


function hoverBtn(event){
	$("#btn-show").bind("mouseenter",function(event){
		event.stopPropagation();
		return false;
	});
	$("#btn-show").bind("mouseleave",function(event){
		event.stopPropagation();
		return false;
	});
	event.stopPropagation();
}


function setCookie(name,value){
	document.cookie=name+"="+value+";path=/";
}


function getCookie(name){
	if(document.cookie.length>0){
		var c_start=document.cookie.indexOf(name+"=");
		if(c_start!=-1){
			c_start=c_start+name.length+1;
			var c_end=document.cookie.indexOf(";",c_start);
			if(c_end==-1)
				c_end=document.cookie.length;
			return document.cookie.substring(c_start,c_end);
		}
	}
	return "";
}
