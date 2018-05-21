var loading = false;
var isload=true;
var rpage=1;
var bpage=1;
var rflag=false;
var bflag=false;
var type='withdrawal';
var page=1;

$(document).ready(function () {
  if(GetQueryString("type")!=null){
    type = GetQueryString("type");
    if(type=="rechange"){
      $('.message-list1').hide(1);
      $('.type span:first-child').text("充值申请");
    }else{
      $('.message-list2').hide(1);
      $('.type span:first-child').text("提现申请");
    }
  }
		
	
    $('.type').on('click', function (e) {
        var X = $(document).width()-115;
        var Y = e.pageY + 30;
        $('.mask-select .list').css({
            'top': Y + 'px',
            'left': X + 'px'
        })
        $('.mask-select').fadeIn();
    })
    $('.mask-select').on('click', function () {
        $(this).fadeOut();
    })
    $('.mask-select').on('click', '.list dd', function () {
        var text = $(this).text();
        $('.type span:first-child').text(text);
    })
    
    
    $("#select-btn dd").bind("click",function(){
//  	清空内容
			loading = false;
			$.attachInfiniteScroll($('.infinite-scroll'));
    	$(".message-list1 .assets-list").empty();
    	$(".message-list2 .assets-list").empty();
    	$(".infinite-scroll-preloader").show();
//  	console.log($(this))
    	type=$(this).attr("data-type");
    	if(type=='withdrawal'){
    		$(".message-list1").show(1);
    		$(".message-list2").hide(1);
    	}else if(type=='rechange'){
    		$(".message-list2").show(1);
    		$(".message-list1").hide(1);
    	}
    	page=1;
    	addItems();
    });
    
    
    addItems();
    
    //滚动加载
    $.attachInfiniteScroll($('.infinite-scroll'));
    $(document).on('infinite', '.infinite-scroll',function() {
        // 如果正在加载，则退出
        if (loading) return;

        // 设置flag
        loading = true;

        setTimeout(function() {
						addItems();
//          if (lastIndex >= maxItems) {
//              $.detachInfiniteScroll($('.infinite-scroll'));
//              $('.infinite-scroll-preloader').remove();
//              return;
//          }

//          addItems(itemsPerLoad, lastIndex);
        }, 1);
   });
})

function addItems(){
//	var pege=1;
	var Ourl='';
	var html='';
	var temp=[];
	var name='';
	var aim='';
	
	if(isload==false){
		return;
	}
	isload=false;
	
	if(type=='rechange'){
//		page=bpage;
		Ourl=rechange;
		name='充值申请';
		aim=$(".message-list2 .assets-list");
//		bpage++;
	}else if(type=='withdrawal'){
//		page=rpage;
		aim=$(".message-list1 .assets-list");
		Ourl=withdrawal;
		name='提现申请';
//		rpage++;
	}
	$.post(Ourl,
				{ page_num : page },
				function(data){
					if(data.code==1){
						if(data.info.list==null||data.info.list==""||data.info.list==undefined){
							stopLoad(aim);
							return;
						}
//						if(type=='withdrawal'){
							$.each(data.info.list, function(key,value) {
								var time = new Date(value.created*1000);
								var desc = type == 'withdrawal'?'-':'+';
								var str = type == 'withdrawal'?'提现':'充值';
								var classname= type == 'withdrawal'?'withdrawal':'rechange';
								html='<li class="'+classname+'" data-id="'+value.id+'"><div class="assets-left"><div class="avatar-detail"><img src="'+value.dis_info.headimgurl+'" alt="" /><p>'+value.dis_info.name+'</p></div>'+
								'<span>'+time.toLocaleDateString()+'</span></div><div class="assets-right"><p>'+str+'</p><p>'+desc+value.apply_money+'</p><p class="dealing">'+value.status_name+'</p></div></li>';
	                            temp.push(html);
							});
//						}else if(type=='rechange'){
//							$.each(data.info.list, function(key,value) {
//								var time=new Date(value.created*1000);
//								html='<li><div class="assets-left"><div class="avatar-detail"><img src="'+value.dis_info.headimgurl+'" alt="" /><p>'+value.dis_info.name+'</p></div>'+
//								'<span>'+time.toLocaleDateString()+'</span></div><div class="assets-right"><p>提现</p><p>+'+value.apply_money+'</p><p class="dealing">'value.status_name'</p></div></li>';
//	              temp.push(html);
//							});
//						}
						aim.append(temp);
						loading = false;
						if(data.info.list.length<10){
							stopLoad(aim);
							return;
						}
						page++;
					}else{
						// console.log(data.msg)
					}
				});
				isload=true;
}


function stopLoad(aim) {
    if ($('.assets-list li').length == 0) {
        if (!$(aim).find('.emptyImg').length > 0) { 
            var oImg = '<div class="emptyImg" style="margin-top: 150px;overflow:hidden; text-align: center;"><img src=' + imgSrc + ' style="width: 200px;"><div>'
            aim.append(oImg);
        } 
        $.detachInfiniteScroll($('.infinite-scroll'));
        $(".infinite-scroll-preloader").hide();
        return false;
    }
    // var str = ' <p style="text-align: center;">暂无更多数据</p>';
    $.detachInfiniteScroll($('.infinite-scroll'));
    $(".infinite-scroll-preloader").hide();
    // aim.append(str);
    if ($('.assets-list li').length > 10) {
        tusi('暂无更多数据！');
    }
    
}

function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}