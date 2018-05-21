 $(document).ready(function() {
 	var swiper = new Swiper('.swiper-container', {
 		pagination: '.swiper-pagination',
 		paginationClickable: true,
 		zoom: true,
 		preloadImages: false,
 		lazyLoading: true,
 		autoplay: 2500,
 		effect: 'fade'
 	});
 	$("#numadd").click(function(){
   		$("#shownum").val(Number($("#shownum").val())+1);
 	});
 	$("#reducenum").click(function(){
        var num = $("#shownum").val();
 		if(num > 1){
   			$("#shownum").val(Number(num)-1);
 		}
 	});
    //点击购买
    $("#buy").click(function(){
 		var num =Number($("#shownum").val());
        var id = $('input[name=id]').val();
        window.location.href = buy_url+'?id='+id+'&num='+parseInt(num);
 	});
    
     //点击加入购物车
    $("#cart").click(function(){
 		var num =Number($("#shownum").val());
        var id = $('input[name=id]').val();
        $.post(cart_url, {id:id, num:num}, function(data) {
            tusi(data.msg)
        });
    });
     
     
 });