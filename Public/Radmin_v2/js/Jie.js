;(function($){
	$.fn.myWay=function(str){
		alert(str);
		this.css({color:"red"});
	}
	$.fn.qrCodess=function(urls){
		var images=QRCode.generatePNG(urls, {
						ecclevel: "M",
						format: "html",
						fillcolor: "#FFFFFF",
						textcolor: "#373737",
						margin: 4,
						modulesize: 8
					});
		this.attr("src",images);
	}
})(jQuery)
