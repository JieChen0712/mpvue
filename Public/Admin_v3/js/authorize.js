//()(jQuery)

$(document).ready(function() {
	//加载授权书图片
	loadImg();
})
//		绘画证书的canvas
//function draw(img, data) {
//	var canvas = document.getElementById("thecanvas");
//	var ctx = canvas.getContext("2d");
//	//			context.drawImage(img,sx,sy,swidth,sheight,x,y,width,height);
//	//			context.drawImage(规定要使用的图像、画布或视频,截取图片开始的x轴,截取图片开始的y轴,图片显示内容的宽度,图片显示内容的高度,在canvas上的开始坐标x,在canvas上的开始坐标y,图片缩放 的宽度/决定图片在画布上的宽度,图片缩放的高度/决定图片在画布上的高度);
//	//			四个值的时候默认为开始的x,y坐标,图片在画布的大小width,height(默认是整张图片在画布上的缩放)
//	ctx.drawImage(img, 0, 0, 652, 920);
//	//	ctx.fillStyle = "#0cf";
//	//	ctx.fillRect(25, 25, 100, 100);
//	ctx.fillStyle = "black"; // black color
//	ctx.font = "bold 20px Noto Sans S Chinese Medium";
//	ctx.fillText(data.agentName, 210, 365);
//	ctx.fillText(data.agentWeChatId, 400, 365);
//	ctx.fillText(data.agentIDcard, 210, 420);
//	ctx.fillStyle = "orange";
//	ctx.font = "bold 32px Noto Sans S Chinese Medium";
//	ctx.fillText(data.agentLevelName, 260, 530);
//	ctx.fillStyle = "black"; // black color
//	ctx.font = "bold 16px Noto Sans S Chinese Medium";
//	ctx.fillText(data.certificateId, 310, 748);
//	ctx.fillText(data.dateTime, 310, 790);
//	ctx.restore(); //存储画布状态
//	canvas.style.display = "none";
//	var type = 'png'; //你想要什么图片格式 就选什么吧
//	var d = document.getElementById("thecanvas");
//	var imgdata = d.toDataURL(type);
//	var png = new Image();
//	png.src = imgdata;
//	$(png).attr("id", "imgtrue");
//	if(!$(".certificate").children("#imgtrue").length > 0) {
//		$(".certificate").append(png);
//	}
//
//}

//加载授权书图片
//function loadImg() {
//	var agentInf = {
//		certificateImg: agentimg,
//		agentName: '',
//		agentWeChatId: 123,
//		agentIDcard: 440181,
//		agentLevelName: "董事",
//		certificateId: 123456,
//		dateTime: "2017-08-13"
//	}
//	agentInf.agentName = $(".agent-name").text();
//	agentInf.agentWeChatId = $(".agent-wechat").text();
//	agentInf.agentIDcard = $(".agent-idcard").text();
//	agentInf.agentLevelName = $(".agent-job").text();
//	agentInf.certificateId = $(".authorize-num").text();
//	agentInf.dateTime = $(".authorize-date").text();
//	//			通过ajax获取数据,并给上面的变量赋值存储
//	//			$.get(url,function(data){
//	//
//	//			})
//	//			将上面获取的图片地址创建成DOM元素
//	//		alert({$agentRow.name})
//	var img = new Image();
//	img.src = agentInf.certificateImg;
//	console.log(img);
//	//			将图片设置为隐藏，并添加到页面上渲染
//	$(img).hide()
//	$('.certificate').append(img);
//	//			在证书背景图完全加载完后才开始绘画证书,和添加点击监听事件
//	img.onload = function() {
//		console.log(img);
//		draw(img, agentInf);
//	}
//	return false;
//}


