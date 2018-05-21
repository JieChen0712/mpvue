$().ready(function(){
	scrollMsg($(".show-awinf"));
	loadMsg($(".show-awinf"));
//	showaddress(true)

        showfollow(flag1);
	loadMap();
});

function scrollMsg(aim){
	var smallh=aim.children(".winning").height()
	aim.css({top:smallh+'px'});
	var speed=-10;
	var h=aim.height();
	var scrollh=smallh;
	aim.append(aim.html())
	setInterval(function(){
		scrollh+=speed;
		if(Math.abs(scrollh)<h){
			aim.animate({
				top:scrollh+'px'
			},500,'linear');
		}else{
			aim.animate({
				top:smallh+'px'
			},1,'linear');
			scrollh=smallh;
		}
	},500);
}
function loadMsg(aim){
	$.get("XXX.php",function(data){
		var items=[];
		$.each(data["data"],function(key,val){
			items.push('<div class="winning"><p><img src="'+data.imgsrc+'" alt="" /><span>恭喜'+data.userid+'</span><span>获得'+XXXXXXXX+'</span></p></div>');
		});
		aim.append(items);
	});
}

function showfollow(flag){
	if(flag=='0'){
            $("#mask").fadeIn().children("#follow").fadeIn();
	}
}

function showscan(flag){
	if(flag==true){
		$("#mask").fadeIn().children("#scan").fadeIn();
	}
}
function showaward(flag){
	if(flag==true){
		$("#mask").fadeIn().children("#mask-award").fadeIn();
	}
}
function showaddress(flag){
	if(flag==true){
		$("#mask").fadeIn().children("#address").fadeIn();
	}
}

function loadMap() {
	var selectArea = new MobileSelectArea();
	selectArea.init({
		trigger: '#txt_area',
		value: $('#hd_area').val(),
		default: 0,
		data: areadata,
		position: "bottom"
	});
}