$(function(){
	var h = document.getElementById("vv").getElementsByClassName("swiper-slide").length-1;
	var b = a.split("`");
	var ba = b.length;
	var bc = 0;
	for (var bb = 0;bb < ba;bb++){
		var c = b[bb].split("@");
		var cc = c.length;
		for(var bd = 0;bd < cc;bd++){
			var d = c[bd].split("^");
			text = '<div class="d-ui-widget-header uio" style="padding:0px;margin:0px;position:absolute;left:'+d[1]+';top:'+d[2]+';z-index:20;" id="divliae_'+bc+'"><span id="divlia_'+bc+'" onclick="scbtm('+bc+')" class="ui-widget-header" style="color:'+d[3]+';font-size:'+d[4]+';font-family:'+d[5]+';">'+d[0]+'</span><img class="tp'+bc+'" src="'+pub+'/Index/images/sc.png" style="position:absolute;top:-30px;right:-35px;display:none;z-index:20;"></div>';
			$("#ll"+bb).append(text);
			$("#divliae_"+bc+"").die().draggable({containment:"parent"});
			bc++;
		}
	}
	var e = p.split("`");
	var ia = e.length;
	for (var ib = 0;ib < ia;ib++){
		var i = e[ib].split("@");
		var ii = i.length;
		for(var ic = 0;ic < ii;ic++){
			var y = i[ic].split("^");
			if(y != ""){
				img = '<img class="cut" src="'+y[2]+'" style="position:absolute;left:'+y[0]+';top:'+y[1]+';z-index:15;"/>';
				$("#ll"+ib).append(img);
			}
		}
	}
	
})