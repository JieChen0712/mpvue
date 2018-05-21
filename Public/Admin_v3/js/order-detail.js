$().ready(function(){
	$("#order-all").fadeIn();
	$(".state_bar li").bind("click",function(){
		var aim='#'+$(this).data("content");
		hideAllcontent(aim,this);
	});
});

function hideAllcontent(aim,lis){
	$(lis).children("span").addClass("active").parent().siblings().children("span").removeClass("active");
	$(".order-detail .content").hide();
	$(aim).fadeIn();
}
