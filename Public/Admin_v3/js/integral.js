$().ready(function() {
	var loading=false;
	
	$("#datetime-picker").val(getNowDay());
	$("#datetime-picker").datetimePicker({
		toolbarTemplate: '<header class="bar bar-nav"><button class="button button-link pull-left close-picker">取消</button><button class="button button-link pull-right close-picker" id="sure">确定</button><h1 class="title">选择日期和时间</h1></header>'
	});
	subDate($("#datetime-picker"));
	$("#datetime-picker").bind("click", function() {
		$(".picker-items-col-divider").hide().prev().hide().next().hide().next().hide();
		$(".picker-modal").css({
			background: "white"
		});
		$(".picker-items-col").css({
			margin: "0 2rem"
		});
		subDate(this);
		$(this).change(function() {
			subDate(this);
		});
	});
	$(".header-integral button").bind("click",function(){
		$(".content section").slideUp();
		if($(this).data("name")=="earn"){
			$(".earn-integral").slideDown();
			
		}else if($(this).data("name")=="main"){
			$(".main-integral").slideDown();
		}
	});
	
	$(document).on("click","#sure",function(){
		getScore = 0;
        useScore = 0;
		page=1;
		month=$("#datetime-picker").val().substring(0,7).replace('-','').trim();
		isNull=false;
		$("#integral-list dd").remove();
		$.attachInfiniteScroll(".infinite-scroll");
		var temp='<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
		if($(".main-integral").has("div>.infinite-scroll-preloader")){
			$(".main-integral").append(temp);
        }
        getScore = 0;
        useScore = 0;
		getIntegral();
		console.log(month)
	});
	
	
	$(document).on('infinite', '.infinite-scroll',function() {

        // 如果正在加载，则退出
        if (loading) return;

        // 设置flag
        loading = true;

        setTimeout(function() {
            loading = false;

            if (isNull) {
                $.detachInfiniteScroll($('.infinite-scroll'));
                $('.infinite-scroll-preloader').remove();
                return;
            }
           	getIntegral();
        }, 1000);
    });
	
});

function subDate(aim) {
	var str = $(aim).val();
	$(aim).val(str.substring(0, 7));
}

function getMouths(aim){
	var month=aim.val().replace('-','');
	return month;
}
