/*=== 默认为 standalone ===*/
$(function () {
//	console.log($(".btn-wrapper button"))
	var btn=$(".btn-wrapper button");
    btn.each(function (key, value) {
    
		var strp="";
		var plist=$(value).parent().siblings(".text-wrapper").html();
//		plist.each(function(key,value){
//			strp+=$(value).text();
//		});
		$(this).attr("data-clipboard-text",plist);
		console.log()
	});
	var clipboard = new Clipboard('.copytext');

    clipboard.on('success', function(e) {
         $.alert('复制成功！');
    });

    clipboard.on('error', function(e) {
         $.alert('复制失败，当前系统版本较低，不支持此功能！');
    });
    /*=== 默认为 standalone ===*/
    // var myPhotoBrowserStandalone = $.photoBrowser({
    //     photos: imgSrcs
    // });
    //点击时打开图片浏览器
    $(document).on('click', '.pb-standalone', function () {
        //存放src的数组
        var imgSrcs = [];
        var oImg = $(this).parent().find('img');
        oImg.each(function (key, val) {
            imgSrcs.push($(val).attr('src'));
        });
        $.photoBrowser({
            photos: imgSrcs
        }).open($(this).index());
    });
     
})