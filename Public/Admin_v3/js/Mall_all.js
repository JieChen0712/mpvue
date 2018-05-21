

$().ready(function(){

	var errorno=1;
	var loading=false;
	var loadings=false;
	var allpage=1;
	var page1=1;
	var page2=1;
	var page3=1;
	var page4=1;
	var stat = '';			//1：代审核	2：已发货		3：已收货
	var target=$("#tab1").children(".content-block");
	
	  //获取信息的方法
    addItems(target)

    function addItems(aim) {
    	var html = [];
    	var types = "take"
    	var count=0;
    	var page='';
    	
    	if(stat==''){
    		page=allpage;
    		allpage=allpage+1;
    	}else if(stat==1){
    		page=page1;
    		page1=page1+1;
    	}else if(stat==2){
    		page=page2;
    		page2=page2+1;
    	}else if(stat==3){
    		page=page3;
    		page3=page3+1;
    	}else if(stat==4){
    		page=page4;
    		page4=page4+1;
    	}
    	
    	$.post(allurl, {
    			status: stat,
    			type: types,
    			page_num: page
    		},
    		function(data) {
    			console.log(data)
    			if(data.code == 1) {
    				var str='';
    				if(data.info.list==null||data.info.list==undefined||data.info.list==""){
    					str='<dl class="no-data"><p style="text-align:center">暂无更多数据！</p></dl>';
    					$.detachInfiniteScroll($('.infinite-scroll'));
    					aim.find(".infinite-scroll-preloader").remove();
    					if(aim.children("dl").hasClass("no-data")){
    						return;
    					}
    					aim.append(str);
    					loadings=false;
    					aim.attr("data-flag","true");
    					return;
    				}
//  				console.log(data)
    				
    				 // 整体html
    					var html='';
    					var all=[];
    					
    				//获取对应的按钮
    					var btnHtml='';
    				// 只需获取一次
    					var userImg='';
    					var userName='';
    					var orderStatus='';
    					var total_num='';
    					var total_price='';
    					var orderNum='';
    				
    				$.each(data.info.list, function(key,value) {
    					orderNum=key;
    					var temp=[];
    					
    					count++;
    					for(var i=0;i<value.length;i++){
//  						console.log(value[i].templet.image)// 商品图片
//  						console.log(value[i].s_name)	// 名字
//  						console.log(value[i].status_name)	//	状态名
//  						console.log(value[i].price)	//	价格
//  						console.log(value[i].num)	//	数量
//  						console.log(value[i].templet.name)	//	商品名称
//  						console.log(value[i].total_price)	//	总价格
//  						console.log(value[i].total_num)	//	总数量
	    					userName=value[i].u_name;
	    					userImg=value[i].p_image;
    						goodsImg=ROOT+value[i].templet.image;
							userName=value[i].u_name;
							orderStatus=value[i].status_name;
							total_num=value[i].total_num;
							total_price=value[i].total_price;
							
							if(value[i].status==1){
	    						btnHtml='<div class="order-btn"><a class="external" href="'+APP+'/sale/mallwxpay/pay?order_num='+key+'"><input type="button" data-id="'+key+'" value="付款" class="button button-danger pay"><a>'
	    						+'<input type="button" value="取消订单" data-id="'+key+'" class="button button-dark cancel-order"></div></dl>';
	    					}else if(value[i].status==2){
//	    						btnHtml='<div class="order-btn"><input type="button" value="提醒卖家发货" data-id="'+key+'" class="button button-dark cancel-order"></div></dl>';
	    					}else if(value[i].status==3){
	    						btnHtml='<div class="order-btn"><input type="button" value="查看物流" class="button button-dark">'+
	                                    '<input type="button" data-id="'+key+'" value="确认收货" class="button button-danger shou"></div></dl>';
	    					}else if(value[i].status==4){
	    						btnHtml='<div class="order-btn"><input type="button" value="查看订单" class="button button-dark order-details" data-id="'+key+'"></div></dl>';
	    					}
	    					
	    					html='<dl class="all-detail"><dt style="line-height:35px">订单号：<span>'+key+'</span><p>'+orderStatus+'</p></dt><dd>';
	    					
	    					
	    					
							var str='<div class="all-list" data-id="'+key+'"><img src="'+goodsImg+'" alt=""><div class="goods-detail"><div class="details-top"><p>'+value[i].templet.name+'</p>'+
							'<p>￥'+value[i].price+'</p></div><div class="details-bottom"><p></p><p>x'+value[i].num+'</p></div></div></div>';
    						temp.push(str);
    					}
//  					var str1='<div class="all-list" data-id="'+orderNum+'">'
    					var str2='<p class="goods-sum"><span>共'+total_num+'件商品</span><span>合计:￥'+total_price+'（含运费:￥0.00）</span></p></dd>';
    						
    					html+=temp.join("")+str2+btnHtml;
    					all.push(html)
//  					console.log(all)
    			});
    				aim.prepend(all);
    				loadings=false;
    				if(count<3){
    					str='<dl class="no-data"><p style="text-align:center">暂无更多数据！</p></dl>';
    					$.detachInfiniteScroll($('.infinite-scroll'));
    					aim.find(".infinite-scroll-preloader").remove();
    					if(aim.children("dl").hasClass("no-data")){
    						return;
    					}
    					aim.append(str);
    					aim.attr("data-flag","true");
    					loadings=false;
    					return;
    				}
    				
    			} else {
    				console.log(data)
    				$.alert(data.msg)
    			}
    		});
    }
    
	$.attachInfiniteScroll($('.infinite-scroll'));

//监听滚动事件
$(document).on('infinite', '.infinite-scroll',function() {
		if(stat==''){
			target=$("#tab1").children(".content-block");
		}else if(stat==1){
			target=$("#tab2").children(".content-block");
		}else if(stat==2){
			target=$("#tab3").children(".content-block");
		}else if(stat==3){
			target=$("#tab4").children(".content-block");
		}else if(stat==4){
			target=$("#tab5").children(".content-block");
		}
        // 如果正在加载，则退出
        if (loading) return;
        // 设置flag
        loading = true;

        setTimeout(function() {
            loading = false;
			
//              $.detachInfiniteScroll($('.infinite-scroll'));
//              $('.infinite-scroll-preloader').remove();
//              return;
            
            if(target.attr("data-flag")=="true"){
							return;
						}else{
							addItems(target)
						}
        }, 1);
    });
    
$(".buttons-tab .tab-link").bind("click",function(){
	stat=$(this).attr("data-stat");
	console.log($(this).attr("data-stat"))
	$.attachInfiniteScroll($('.infinite-scroll'));
	if(stat==''){
		target=$("#tab1").children(".content-block");
	}else if(stat==1){
		target=$("#tab2").children(".content-block");
	}else if(stat==2){
		target=$("#tab3").children(".content-block");
	}else if(stat==3){
		target=$("#tab4").children(".content-block");
	}else if(stat==4){
		target=$("#tab5").children(".content-block");
	}
	if (loadings) return;
	console.log(target.attr("data-flag"))
	
	if(target.attr("data-flag")=="true"){
		return;
	}else{
		addItems(target)
		loadings=true;
	}
	
});
    
});
