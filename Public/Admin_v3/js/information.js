$().ready(function() {
	var flag = false;
	$.get(signurl, function(data) {
		if(data.is_sign_up == 1 && flag == false) {
			$(".signin").fadeIn("slow")
			$(".signin").css({
				backgroundColor: "grey"
			}).find("span").text("已签到");
			flag = true;
		} else if(data.is_sign_up == 2 && flag == false) {
			$(".signin").fadeIn("slow").bind("click", function() {
				$.get(signedurl, function(data,res) {
					if(!(data==null||data==undefined||data=="")){
						if(data.code != 1) {
							$.alert(data.msg);
						} else {
							$(".signin").css({
								backgroundColor: "grey"
							}).find("span").text("已签到");
							flag = true;
						}
					}else{
						$.alert("数据传输错误，签到失败！")	;
					}
				});
				if(flag){
					$(".signin").unbind("click");
				}
			});
		} else {
			return false;
		}
	});
	
})