$(function(){
	var datas=window.location.search;
	datas=datas.replace(/\=/g,":").replace(/\?/g,"").trim();
	datas="{"+datas+"}";
	datas=eval("(" + datas + ")");
	var t=new Date(1506419858*1000)
	console.log(t.toLocaleDateString());
	$.post(recordurl,
				datas,
				function(data){
					console.log(data)
					if(data.code==1){
						var html='';
						if(data.info!=null||data.info!=undefined||data.info!=""){
							
							$(".content").html("")
							$.each(data.info.list, function(key,value) {
								if(window.location.search.indexOf("refund_id")!=-1){
									var time=new Date(value.created*1000);
									html='<div class="message"><div class="wrapper"><div class="avatar"><img src="'+value.dis_info.headimgurl+'" alt=""></div><p>'+value.dis_info.name+'</p>'+
	                		'</div><div class="money">-'+value.apply_money+'</div></div><div class="type"><p>类型:</p><span>提现</span></div>'+
	            				'<div class="withdraw-to"><p>提现到:</p><span>'+value.pay_type+'('+value.card_number+') '+value.card_name+'</span></div>' +'<div class="status"><p>提现方式:</p><span>'+value.refund_type_name+'</span></div>'+
										'<div class="status">'+
	                		'<p>状态:</p><span>'+value.status_name+'</span></div><div class="create-time"><p>申请时间:</p><span>'+time.toLocaleDateString()+'</span>'+
	            				'</div>';
								}else if(window.location.search.indexOf("bonus_id")!=-1){
									var time=new Date(value.time*1000);
									html='<div class="message"><div class="wrapper"><div class="avatar"><img src="'+value.x_id_info.headimgurl+'" alt=""></div><p>'+value.x_id_info.name+'</p>'+
	                		'</div><div class="money">'+value.money+'</div></div><div class="type"><p>类型:</p><span>佣金</span></div>'+
	            				'<div class="withdraw-to"><p>下单人姓名:</p><span>'+value.x_id_info.name+'</span></div><div class="status">'+
	                		'<p>订单号:</p><span>'+value.order_num+'</span></div><div class="withdraw-to"><p>佣金比例:</p><span>'+value.ratio+'</span></div>'+
	                		'<div class="withdraw-to"><p>下单金额:</p><span>'+value.order_money+'</span></div><div class="create-time"><p>时间:</p><span>'+time.toLocaleDateString()+'</span>'+
	            				'</div>';
								}
								
	            	$(".content").append(html);
							});
						}
					}else{
						console.log(data.msg);
					}
					
			})
});
