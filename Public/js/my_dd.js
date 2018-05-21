
function jian(vel, min_multiple)
{
    if (min_multiple == 0 || min_multiple == null) {
        min_multiple = 1;
    }
    
    
    var old_money = $('.money').html();
    //alert(old_money);
    
    var old_s = parseInt(document.getElementById('s' + vel).value);
    
    var a = min_multiple;
    var s = old_s - parseInt(a);
    
    var is_checked = $("#c"+vel).is(':checked');
    
    if (s < min_multiple)
    {
        s = min_multiple;
        alert('该产品数量不能少于'+min_multiple);
    } else if( is_checked ){
        
        //money = Number(money) - Number($('.p' + vel + '.on .price' + vel).html());
        var money = Number(old_money) - Number(min_multiple) * Number($('.price'+vel).html());
//        money = Number(old_money) - min_multiple*Number($('.price'+vel).html());
        
        //alert(money);
        //alert(s+','+$('.price'+vel).html());
        
        money = money.toFixed(2);
        $('.money').html(money);
//        document.getElementById('s' + vel).value = s;
        $("#s" + vel).val(s);
    }
    else{
//        document.getElementById('s' + vel).value = s;
        $("#s" + vel).val(s);
    }
}
function jia(vel, min_multiple)
{
    if (min_multiple == 0 || min_multiple == null) {
        min_multiple = 1;
    }
    
    var old_money = $('.money').html();
    //alert(old_money);

    var old_s = parseInt(document.getElementById('s' + vel).value);
    var a = min_multiple;
    var is_checked = $("#c"+vel).is(':checked');
    
    var s = old_s + parseInt(a);
    
    if (s < min_multiple)
    {
        s = min_multiple;
        alert('该产品数量不能少于' + min_multiple);
    } else if( is_checked ){
        
        //money = Number(money) + Number($('.p' + vel + '.on .price' + vel).html());
        var money = Number(old_money) + Number(min_multiple) * Number($('.price'+vel).html());
        //money = Number(old_money) + min_multiple*Number($('.price'+vel).html());
        
        //alert(s+','+$('.price'+vel).html());

        money = money.toFixed(2);
        $('.money').html(money);
//        document.getElementById('s' + vel).value = s;
        $("#s" + vel).val(s);
    }
    else{
//        document.getElementById('s' + vel).value = s;
        $("#s" + vel).val(s);
    }
}

$(function () {
    $('.li1').click(function () {
        $(this).removeClass('on');
        $(".li2").removeClass('on');
        $('.dd1').hide();
        $('.dd2').show();

    })
})
$(function () {
    $('.li2').click(function () {
        $(this).addClass('on');
        $(".li1").addClass('on');
        $('.dd1').show();
        $('.dd2').hide();
    })
})

function csbtm(v) {
    var nane = confirm("确定取消订单吗？？");
    if (nane != false) {
        $('.dle' + v).remove();
    }
}

$(function () {
    $('.dd_bg').click(function () {
        $('.mask').show();
    })
    $('.box1 button').click(function () {
        $('.mask').hide();
    })
})
