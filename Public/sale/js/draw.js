var turnplate = {
    restaraunts: [],
    colors: [],
    outsideRadius: 180,
    textRadius: 155,
    insideRadius: 68,
    startAngle: 0,
    bRotate: false
};
window.onload = function(){ 
    $.ajax({
    type: "get",
    url: lunpanurl,
    async: false,
    success: function (data) {
        if (data.code != 1) {
            alert(data.msg);
            return false;
        }
        
        var imgsrc = [];
        var awardid = [];
        turnplate.restaraunts.push("谢谢参与");
        $.each(data.info, function (key, val) {
            //			alert(val.img)
            imgsrc.push(val.img);
            awardid.push(val.id);
            turnplate.restaraunts.push(val.name);
        });
        //		var imgsrc = ["../public/sale/img/goods1.jpg", "../public/sale/img/goods2.jpg", "../public/sale/img/goods3.jpg"];
        //动态添加大转盘的奖品与奖品区域背景颜色
//		turnplate.restaraunts = ["谢谢参与,再接再励", "100M流量", "100M流量"];
            
        
        for (var i = 0; i < imgsrc.length; i++) {
            $("body").append('<img src="' + imgsrc[i] + '" style="display:none;"/>');
        }

        $(document).ready(function () {
            //根据数量创建转盘颜色数组
            turnplate.colors = new Array();
            if (turnplate.restaraunts.length % 2 == 0) {
                var dcolors = ["#FFF4D6", '#feda9c'];
                for (var i = 0; i < turnplate.restaraunts.length / 2; i++) {
                    turnplate.colors = turnplate.colors.concat(dcolors);
                }
            } else {
                var dcolors = ["#FFF4D6", '#feda9c', '#fdce52'];
                for (var i = 0; i < turnplate.restaraunts.length / 3; i++) {
                    turnplate.colors = turnplate.colors.concat(dcolors);
                }
            }
            //页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
            $(function () {
                drawRouletteWheel(imgsrc)
            });
            $('.pointer').one("click",function () {
                var item = 0;
                $.get(lotteryurl, function (data) {

                    if( data.code != 1 ){
                        alert(data.msg);
                        return;
                    }

                    item = data.win_id;
                    $("#p_id").val(item);
                    $("#record_id").val(data.record_id);

                    if (turnplate.bRotate)
                        return;
                    turnplate.bRotate = !turnplate.bRotate;
                    //获取随机数(奖品个数范围内)
                    //这里设置中奖的物品
                    //				var item = rnd(1, turnplate.restaraunts.length);
										//					var infid=findall(awardid,item);


                    var infid = awardid.indexOf(String(item));

                    if( infid == '-1' ){
                        alert('轮盘抽奖出错！');return;
                    }
                    var infid_num = infid + 1;
                    parseInt(infid_num);
                    rotateFn(infid_num-1, turnplate.restaraunts[infid_num]);
                })

            });
            var rotateTimeOut = function () {
                $('#wheelcanvas').rotate({
                    angle: 0,
                    animateTo: 2160,
                    duration: 8000,
                    callback: function () {
                        alert('抽奖失败,网络超时，请检查您的网络设置！');
                    }
                });
            };

            //旋转转盘 item:奖品位置; txt：提示语;
            var rotateFn = function (item, txt) {
                if(item==''||item==null||item==0){
                	return;
                }
                //这里的item就是奖品数组中的奖品位置
                //这里的概率可以用一个数据加随机数实现
                var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length * 2));
                if (angles < 270) {
                    angles = 270 - angles;
                } else {
                    angles = 360 - angles + 270;
                }
                $('#wheelcanvas').stopRotate();
                $('#wheelcanvas').rotate({
                    angle: 0,
                    animateTo: angles + 1800,
                    duration: 8000,
                    callback: function () {
                        $("#mask").fadeIn().children("#mask-award").fadeIn();
                        if( txt == '谢谢参与' ){
                            $("#result").after('<p>谢谢您的参与！祝您下次抽中大奖哦！</span></p >');
                            $("#fill-inf").hide();
                        }
                        else{
                            $("#result").after('<p>恭喜您！抽中了<span>“' + txt + '”</span>，赶紧来填写收货信息吧！</p >');
                            $("#fill-inf").show();
                        }
                        
                        
                        $("#fill-inf").bind("click", function () {
                            $("#mask-award").fadeOut().siblings("#address").fadeIn();
                        });
                        turnplate.bRotate = !turnplate.bRotate;
                    }
                });
            };
        });
        
    }
});
}


function rnd(n, m) {
    var random = Math.floor(Math.random() * (m - n + 1) + n);
    return random;
}

function drawRouletteWheel(imgsrc) {
//  	var imgsrc = ["../public/sale/img/goods1.jpg", "../public/sale/img/goods2.jpg", "../public/sale/img/goods3.jpg"];
    var canvas = $("#wheelcanvas")[0];
    if (canvas.getContext) {
        //计算圆周角度
        var arc = Math.PI / (turnplate.restaraunts.length / 2);
        var ctx = canvas.getContext("2d");
        //清空矩形
        ctx.clearRect(0, 0, 422, 422);
        //strokeStyle 属性设置颜色  
        ctx.strokeStyle = "#FFBE04";
        //font 属性设置字体属性
        ctx.font = '16px Microsoft YaHei';
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;
        ctx.shadowBlur = 2;
        ctx.shadowColor = 'rgba(255, 255, 255, 0.5)';
        for (var i = 0; i < turnplate.restaraunts.length; i++) {
            var angle = turnplate.startAngle + i * arc -	Math.PI/2-arc/2;
            ctx.fillStyle = turnplate.colors[i];
//          if(i==0){
//          	ctx.fillStyle = 'grey'
//          }
            ctx.beginPath();
            //arc(圆心x,圆心y,半径r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
            ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);
            ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
            ctx.stroke();
            ctx.fill();
            ctx.save();
            //----绘制奖品开始----
            ctx.fillStyle = "#E5302F";
            var text = turnplate.restaraunts[i];
            var line_height = 17;
            ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);

            //rotate方法旋转当前的绘图
            ctx.rotate(angle + arc / 2 + Math.PI / 2);
            if (text.length > 5) {
                text = text.substring(0, 5) + "||" + text.substring(5);
                var texts = text.split("||");
                for (var j = 0; j < texts.length; j++) {
                    ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
                }
            } else {
            		if(text=="开始"){
            			ctx.font = "30px bolder";
            			ctx.fillStyle="orangered"
            			ctx.fillText(text, -ctx.measureText(text).width / 2, 20);
            		}else{
            			ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
            		}
                
            }
            var img = imgsrc[i];
            img = $('.hidden[src="'+img+'"]').get(0);
            ctx.drawImage(img, -25, 20, 50, 50);
            ctx.restore();
            //----绘制奖品结束----
        }
    }
}
function findall(a, x) {
    var results = [],
            len = a.length,
            pos = 0;
    while (pos < len) {
        pos = a.indexOf(x, pos);
        if (pos === -1) { //未找到就退出循环完成搜索
            break;
        }
        results.push(pos); //找到就存储索引
        pos += 1; //并从下个位置开始搜索
    }
    return results;
}


