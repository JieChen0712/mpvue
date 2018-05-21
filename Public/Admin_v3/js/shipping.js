$(document).ready(function () {
    /*切换icon */
    $('#scan-code div').on('click', function () {
        $(this).toggleClass('active')
    })

    /* 点击#name-choose按钮显示 .mask-username */
    $(document).on('click', '#name-choose', function () {
    		u_page=1;
    		var str='<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
    		$('#agent_item').html("")
    		if(u_flag){
    			$('#agent_item').after(str);
    		}
    		addUser();
    		$.attachInfiniteScroll($('.name-scroll'));
        $('.mask-username').addClass('active');
    })
		
		var u_loading=false;
		$(document).on('infinite', '.name-scroll',function() {
        // 如果正在加载，则退出
        if (u_loading) return;
        // 设置flag
        u_loading = true;

        setTimeout(function() {
          addUser();
          u_loading = false;
        }, 1000);
    });


    /* 点击#order-choose按钮显示.mask-order */
    $(document).on('click', '#order-choose', function () {
    		o_page=1;
    		var str='<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
    		$('#order_item').html("")
    		if(o_flag){
    			$('#order_item').after(str);
    		}
    		addOrder();
    		$.attachInfiniteScroll($('.order-scroll'));
        $('.mask-order').addClass('active');
    })
    var o_loading=false;
		$(document).on('infinite', '.order-scroll',function() {
        // 如果正在加载，则退出
        if (o_loading) return;
        // 设置flag
        o_loading = true;

        setTimeout(function() {
          addOrder();
          o_loading = false;
        }, 1000);
    });
    
    
    /* 点击关闭图标关闭遮罩层 */
    $(document).on('click', '.mask-close', function () {
        $(this).parent().removeClass('active');
    })
    $(document).on('click', '.mask-username', function () {
        $('.mask-close');
        $(this).removeClass('active');
    })
    $(document).on('click', '.mask-order', function () {
        $('.mask-close');
        $(this).removeClass('active');
    })
    /*点击删除内容*/
    $('.message-detail').find('li:last-child').on('click', function () {
        $('.mask-close');
        $(this).parent().remove();
    })

    /* 点击遮罩层里的内容映射到页面上 */
    $(document).on('click', '.mask-username .detail', function () {
    		o_id=$(this).attr("data-id");
        $('.order-detail .name').html($(this).find('li').eq(1).html());
//      $('.order-detail .num').html($(this).find('li').eq(0).html());
        $('.mask-close').parent().removeClass('active');
    })
    $(document).on('click', '.mask-order .detail', function () {
        $('.order-detail .name').html($(this).find('li').eq(1).html());
        $('.order-detail .num').html($(this).find('li p').eq(0).html());
        $('.mask-close').parent().removeClass('active');
    })

    /*选择列表获取内容*/
    selectList();

    function selectList() {
        var oSelect = $('.shipping-detail .search-wrapper .select');
        var oSelectList = $('.popover .select-list');
        oSelectList.find('li').click(function () {
            var oText = $(this).html();
            oSelect.find('span').text(oText);
            //改变input中placeholder的值
            $('#search').attr('placeholder', $(this).data('search'));
        })
    }

    $('.bar-code').on('click', '.select', function (e) {
        var X = 5;
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
        $('.bar-code .select span').text(text).attr("data-id",$(this).data('id'));
    })

})


function addUser(){
	$.post(agenturl,
				{page_num:u_page},
				function(data){
					var html='';
					var temp=[];
					if(data.code==1){
						if(data.info.list==null||data.info.list==""||data.info.list==undefined){
							$.detachInfiniteScroll($('.name-scroll'));
							$('#agent_item').siblings('.infinite-scroll-preloader').remove();
							u_flag=true;
							var str='<ul class="detail"><p style="text-align:center;width:100%">暂无更多数据！</p>';
							$("#agent_item").append(str);
							return;
						}else{
							$.each(data.info.list, function(key, value) {
								html='<ul class="detail" data-id='+value.id+'><li>'+value.authnum+'</li><li>'+value.name+'</li><li>'+value.levname+'</li></ul>';
								temp.push(html);
							});
							$("#agent_item").append(temp);
						}
						if(data.info.list.length<10){
							$.detachInfiniteScroll($('.name-scroll'));
							$('#agent_item').siblings('.infinite-scroll-preloader').remove();
							u_flag=true;
							var str='<ul class="detail"><p style="text-align:center;width:100%">暂无更多数据！</p>';
							$("#agent_item").append(str);
							return;
						}
						u_page++;
					}else{
						console.log(data.msg);
					}
				});
}


function addOrder(){
	$.post(orderurl,
				{page_num:o_page,agent_id:o_id},
				function(data){
					var lengths=0;
					var html='';
					var temp=[];
					if(data.code==1){
						if(data.info.list==null||data.info.list==""||data.info.list==undefined){
							var str='<ul class="detail"><p style="text-align:center;width:100%">暂无更多数据！</p>';
							$("#order_item").append(str);
							$.detachInfiniteScroll($('.order-scroll'));
							$('#order_item').siblings('.infinite-scroll-preloader').remove();
							o_flag=true;
							return;
						}else{
							$.each(data.info.list, function(key, value) {
								$.each(value,function(k,v){
									var time=new Date(v.time*1000);
									html='<ul class="detail"><li><p>'+key+'</p><p>'+time.toLocaleDateString()+'</p></li><li>'+v.s_name+'</li></ul>';
									temp.push(html);
									lengths++;
								});
							});
							$("#order_item").append(temp);
						}
						if(lengths<10){
							$.detachInfiniteScroll($('.order-scroll'));
							$('#order_item').siblings('.infinite-scroll-preloader').remove();
							o_flag=true;
							var str='<ul class="detail"><p style="text-align:center;width:100%">暂无更多数据！</p>';
							$("#order_item").append(str);
							return;
						}
						o_page++;
					}else{
						console.log(data.msg);
					}
				});
}

