$(function(){
	var h = document.getElementById("vv").getElementsByClassName("swiper-slide").length-1;
	var b = a.split("`");
	var ba= b.length;
	for (var bb = 0;bb < ba;bb++){
		var c = b[bb].split("@");
		var cc = c.length;
		for(var bc = 0;bc < cc;bc++){
			var d = c[bc].split("^");
			text = '<span class="ui-widget-header" id="movespan" style="padding:0px;margin:0px;position:absolute;left:'+d[1]+';top:'+d[2]+';color:'+d[3]+';font-size:'+d[4]+';font-family:'+d[5]+';z-index:20;">'+d[0]+'</span>';
			$("#bnm"+bb).append(text);
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
				$("#bnm"+ib).append(img);
			}
		}
	}
	
})
//音乐控制 
$(function(){
			window.onload = function(){
				initAudio("bgm");
			}
			var audio;
			function initAudio(id){
				audio =  document.getElementById(id);
			}
			document.addEventListener('touchmove',function(event){
				event.preventDefault(); },false);
		//控制音乐播放停止和音乐ico图标变换
			$("#audioPlay").click(function(){
				if(audio.paused){
					audio.play();
					this.style.backgroundImage="url("+pub+"/Index/images/play.png)";
				}else{
					audio.pause();
					this.style.backgroundImage="url("+pub+"/Index/images/pause.png)";
				}
			});
		})
var firstTouch = true;
$('body').bind("touchstart",function(e){
    if ( firstTouch ) {
        firstTouch = false;
        document.getElementById('bgm').play();
    }else{
        return;
    }
});
$(".bgm-btn1").bind("touchstart",function(e){  
    //e.preventDefault();
    //e.stopPropagation();
    var dom = document.getElementById('bgm');
    if( dom.paused ){
        dom.play();
        $(".bgm-btn1").removeClass("mut");
    }else{
        dom.pause();
        $(".bgm-btn1").addClass("mut");
    }
});
