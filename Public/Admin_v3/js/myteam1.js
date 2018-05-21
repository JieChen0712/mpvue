$().ready(function() {
	//推荐的变量
	var upage_num = 1;
	//直属的变量
	var rpage_num = 1;
	//代理的类型
	var type = $(".buttons-tab").children(".active").data("type");
	console.log(type)
	//是否到结尾
	var agentflag = false;

	var isfirst = true;
	//滚动加载的变量
	var loading = false;
	//  var maxItems = 100;
	//  var itemsPerLoad = 20;

	//          var html = '';
	//          for (var i = lastIndex + 1; i <= lastIndex + number; i++) {
	//              html += '<li><img style="height: 60px;width: 60px;" src="{$vo.headimgurl}" alt="" /><div class="myteam-detail">'+
	//						'<div class="myteam-litems"><p><span>名</span>字：{$vo.name}</p><p><span>等</span>级：{$vo.levname}</p>'+
	//						'<p>授权号：{$vo.authnum}</p><p><span>手</span>机：{$vo.phone}</p></div><i class="icon icon-right"></i></div></li>';
	//          }
	//          $('#items1').append(html);

	//滚动加载的方法
	function addItems() {
		var page;
		var aim;
		var nowflag;
		var nowTab;
		if(isfirst) {
			$.post(agenturl, {
					type: "under",
					page_num: upage_num
				},
				function(data) {
					console.log(page)
					var items = new Array();
					var str;
					if(data.info.list == null || data.info.list == undefined || data.info.list == "") {
						str = '<li style="font-size:1rem;border:none;"><span style="margin: 0 auto;">共' + data.info.count + '个代理，暂无更多数据！</span></li>';
						items.push(str);
						agentflag = true;
						$("a[href='#tab2']").attr("data-flag", agentflag);
						$.detachInfiniteScroll($('.infinite-scroll'));
						$('.infinite-scroll-preloader').remove();
					} else {
						$.each(data.info.list, function(key, value) {
							str = '<li><img style="height: 60px;width: 60px;" src="' + value.headimgurl + '" alt="" /><div class="myteam-detail" data-id="'+value.id+'">' +
								'<div class="myteam-litems"><p><span>名</span>字：' + value.name + '</p><p><span>等</span>级：' + value.levname + '</p>' +
								'<p>授权号：' + value.authnum + '</p><p><span>手</span>机：' + value.phone + '</p></div><i class="icon icon-right"></i></div></li>';
							items.push(str);
						});
					}
					$("#items2").append(items);
					upage_num = 2;
				});
			$.post(agenturl, {
					type: "recommend",
					page_num: rpage_num
				},
				function(data) {
					console.log(page)
					var items = new Array();
					var str;
					if(data.info.list == null || data.info.list == undefined || data.info.list == "") {
						str = '<li style="font-size:1rem;border:none;"><span style="margin: 0 auto;">共' + data.info.count + '个代理，暂无更多数据！</span></li>';
						items.push(str);
						agentflag = true;
						$("a[href='#tab1']").attr("data-flag", agentflag);
						$.detachInfiniteScroll($('.infinite-scroll'));
						$('.infinite-scroll-preloader').remove();
					} else {
						$.each(data.info.list, function(key, value) {
							str = '<li><img style="height: 60px;width: 60px;" src="' + value.headimgurl + '" alt="" /><div class="myteam-detail" data-id="'+value.id+'">' +
								'<div class="myteam-litems"><p><span>名</span>字：' + value.name + '</p><p><span>等</span>级：' + value.levname + '</p>' +
								'<p>授权号：' + value.authnum + '</p><p><span>手</span>机：' + value.phone + '</p></div><i class="icon icon-right"></i></div></li>';
							items.push(str);
						});
					}

					$("#items1").append(items);
					rpage_num = 2;
				});
			isfirst = false;
		} else {
			if(type == 'under') {
				page = upage_num;
				aim = $("#items2");
				nowTab = $("a[href='#tab2']");
				upage_num = upage_num + 1;
			} else {
				page = rpage_num;
				aim = $("#items1");
				nowTab = $("a[href='#tab1']");
				rpage_num = rpage_num + 1;
			}
			console.log(upage_num + '*' + rpage_num);
			$.post(agenturl, {
					type: type,
					page_num: page
				},
				function(data) {
					console.log(page)
					var items = new Array();
					var str;
					if(data.info.list == null || data.info.list == undefined || data.info.list == "") {
						str = '<li style="font-size:1rem;border:none;"><span style="margin: 0 auto;">共' + data.info.count + '个代理，暂无更多数据！</span></li>';
						items.push(str);
						agentflag = true;
						nowTab.attr("data-flag", agentflag);
						$.detachInfiniteScroll($('.infinite-scroll'));
						$('.infinite-scroll-preloader').remove();
					} else {
						$.each(data.info.list, function(key, value) {
							str = '<li><img style="height: 60px;width: 60px;" src="' + value.headimgurl + '" alt="" /><div class="myteam-detail" data-id="'+value.id+'">' +
								'<div class="myteam-litems"><p><span>名</span>字：' + value.name + '</p><p><span>等</span>级：' + value.levname + '</p>' +
								'<p>授权号：' + value.authnum + '</p><p><span>手</span>机：' + value.phone + '</p></div><i class="icon icon-right"></i></div></li>';
							items.push(str);
						});
					}
					aim.append(items)
				});
		}

	}

	addItems();

	//切换类别的时候改变type
	$(".tab-link").click(function() {
		type = $(this).data("type");
		console.log(type);
		if($(this).attr("href") == "#tab2") {
			if($(this).data("flag") == "false" && !$(".infinite-scroll-preloader").length > 0) {
				agentflag = false;
				$.attachInfiniteScroll($('.infinite-scroll'));
				var str = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
				$(".infinite-scroll").append(str);
			}
		} else {
			if($(this).data("flag") == "false" && !$(".infinite-scroll-preloader").length > 0) {
				agentflag = false;
				$.attachInfiniteScroll($('.infinite-scroll'));
				var str = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
				$(".infinite-scroll").append(str);
			}
		}
		console.log(upage_num + '-' + rpage_num)
	});

	//  var lastIndex = 20;
	$.attachInfiniteScroll($('.infinite-scroll'));
	$(document).on('infinite', '.infinite-scroll', function() {
		// 如果正在加载，则退出
		if(loading) return;
		// 设置flag
		loading = true;
		setTimeout(function() {
			loading = false;
			addItems();
		}, 1000);
	});

	//搜索框的判定
	$(".search-wrap").click(function() {
		$(this).find("i").hide().siblings("form").children("input").focus();
		$(".agent-search").blur(function() {
			if($(this).val() == '') {
				$(this).parent().siblings("i").show();
			}
		});
	});



});
