$().ready(function(){
	var qrurl=$("#qrurl").text().trim();
	console.log(qrurl);
	var images=QRCode.generatePNG(qrurl,{
					ecclevel: "M",
				    format: "html",
				    fillcolor: "#FFFFFF",
				    textcolor: "#373737",
				    margin: 4,
				    modulesize: 8
				});
	$("#applyqr").attr("src",images);
//	$("#applyqr").
});
