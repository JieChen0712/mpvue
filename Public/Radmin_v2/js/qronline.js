$().ready(function() {
	var clipboard = new Clipboard('#copyurl');
	clipboard.on('success', function(e) {
		alert('复制成功！');
	});

	clipboard.on('error', function(e) {
		alert('复制失败，当前系统版本较低，不支持此功能！');
	});
	
	$("#deadline").val(getTimes(true));

});

function subStrs(str) {
	return srt = str.replace(/-/g, '');
}

function getTimes(flag) {
	var time = new Date();
	var year = time.getFullYear();
	var month = time.getMonth() + 1;
	var day = time.getDate() + 3;

	var temp = new Date(year, month, 0);

	if(day > temp.getDate()) {
		month = month + 1;
		day = day - temp.getDate();
	}
	var str = year.toString();
	if(flag) {
		str += "-" + (month < 10 ? 0 : "") + month.toString() + "-";
	} else {
		str += (month < 10 ? 0 : "") + month.toString();
	}
	str += (day < 10 ? 0 : "") + day.toString()

	return str;
}