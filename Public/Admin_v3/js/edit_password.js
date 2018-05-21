$().ready(function(){
	$("#pd-show").click(function(){
		$(this).children("img").toggleClass("active");
		if($(this).children("img").hasClass("active")){
			$("#pd-old").attr("type","text");
			$("#pd-new").attr("type","text");
			$("#pd-news").attr("type","text");
		}
		else{
			$("#pd-old").attr("type","password");
			$("#pd-new").attr("type","password");
			$("#pd-news").attr("type","password");
		}
	});
	// $("#pd-sub").click(function(event){
	// 	var pd1=$("#pd-new").val();
	// 	var pd2=$("#pd-news").val();
	// 	if(pd1==pd2){
	// 		alert("请确认密码填写正确！")
	// 	}
	// });
	// $("#pd-news").bind("input",function(){
	// 	var pd1=$("#pd-new").val();
	// 	var pd2=$("#pd-news").val();
	// 	if(pd1==pd2){
	// 		$(this).prev().css({color:"#303030"});
	// 	}else{
	// 		$(this).prev().css({color:"red"});
	// 	}
	// });
});
