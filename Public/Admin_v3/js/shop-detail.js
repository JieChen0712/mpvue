// $.ajax({
// type: 'POST',
// url: get_shipping,
// data: {
//   id: p_id
// },
// async: true,
// success: function(data) {
//   if(data.code == 1) {
//     var temp = [];
//
//     $.each(data.info, function(key, value) {
//       if(value.shipping_way == 0 && $.inArray("快递", temp) == -1) {
//         temp.push('快递')
//         if($('#shipping-way').val() == "") {
//           $('#shipping-way').val(0)
//         }
//       } else if(value.shipping_way == 1 && $.inArray("EMS", temp) == -1) {
//         temp.push('EMS')
//         if($('#shipping-way').val() == "") {
//           $('#shipping-way').val(1)
//         }
//       } else if(value.shipping_way == 2 && $.inArray("邮政", temp) == -1) {
//         temp.push('邮政')
//         if($('#shipping-way').val() == "") {
//           $('#shipping-way').val(2)
//         }
//       }
//     });
//    
//     $.each(data.info, function(key, value) {
//       if(value.area_name.indexOf(province) > 0 && value.shipping_way == $('#shipping-way').val()) {
//         $('#shipping-id').val(value.id)
//       } else if(value.shipping_way == $('#shipping-way').val() && $('#shipping-id').val() == "") {
//         $('#shipping-id').val(value.id)
//       }
//     });
//     $('#picker-way').text(temp[0]);
//   } else {
//     alert(data.msg)
//   }
// }
// })

$(document).ready(function() {
    var cartflag = false
    var buyflag = false
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        zoom: true,
        preloadImages: false,
        lazyLoading: true,
        autoplay: 2500,
        effect: 'fade'
    });
    $("#numadd").click(function() {
        var num = Number(Number($("#shownum").val()) + 1);
        $("#shownum").val(parseInt(num));
        var sumnum = Number(num * default_shipping.product_parameter);
        if(default_shipping.reduce){
            if(default_shipping.need_num){
                if(num>=default_shipping.need_num && Number(num * default_shipping.order_price)>=default_shipping.need_money){
                    $('#picker-way').text('￥' + returnFloat(0));
                }else if(Number(default_shipping.first_num) < sumnum&&default_shipping.continue_num!=0) {
                    var overflow = (sumnum - default_shipping.first_num) / Number(default_shipping.continue_num);
                    if((overflow % 1) > 0) {
                        overflow = parseInt(overflow);
                        overflow++;
                    }
                    $('#picker-way').text('￥' + returnFloat((Number(default_shipping.first_fee) + Number(overflow * default_shipping.continue_fee))));
                }
            }else if(Number(default_shipping.first_num) < sumnum&&default_shipping.continue_num!=0) {
                var overflow = (sumnum - default_shipping.first_num) / Number(default_shipping.continue_num);
                if((overflow % 1) > 0) {
                    overflow = parseInt(overflow);
                    overflow++;
                }
                $('#picker-way').text('￥' + returnFloat((Number(default_shipping.first_fee) + Number(overflow * default_shipping.continue_fee))));
            }
        }

    });
    $("#reducenum").click(function() {
        var num = $("#shownum").val();
        if(num > 1) {
            num = Number(num) - 1;
            $("#shownum").val(num);
            var redcnum = Number(num * default_shipping.product_parameter);
            if(default_shipping.reduce){
                if(default_shipping.need_num){
                    if(num>=default_shipping.need_num && Number(num * default_shipping.order_price)>=default_shipping.need_money){
                        $('#picker-way').text('￥' + returnFloat(0));
                    }else if(Number(default_shipping.first_num) < (redcnum+Number(default_shipping.product_parameter))&&default_shipping.continue_num!=0) {
                        var overflow = ((Number(redcnum) - default_shipping.first_num) / default_shipping.continue_num);
                        if(overflow % 1 > 0) {
                            overflow = parseInt(overflow);
                            overflow++;
                        }
                        var redcfee = returnFloat((Number(default_shipping.first_fee) + Number(overflow * default_shipping.continue_fee)));

                        if(Number(redcfee) > default_shipping.first_fee){
                            $('#picker-way').text('￥' + redcfee);
                        }else{
                            $('#picker-way').text('￥' + returnFloat(default_shipping.first_fee));
                        }
                    }else{
                        $('#picker-way').text('￥' + returnFloat(default_shipping.first_fee));
                    }
                }
                else if(Number(default_shipping.first_num) < (redcnum+Number(default_shipping.product_parameter))&&default_shipping.continue_num!=0) {
                    var overflow = ((Number(redcnum) - default_shipping.first_num) / default_shipping.continue_num);
                    if(overflow % 1 > 0) {
                        overflow = parseInt(overflow);
                        overflow++;
                    }
                    var redcfee = returnFloat((Number(default_shipping.first_fee) + Number(overflow * default_shipping.continue_fee)));

                    if(Number(redcfee) > default_shipping.first_fee){
                        $('#picker-way').text('￥' + redcfee);
                    }else{
                        $('#picker-way').text('￥' + returnFloat(default_shipping.first_fee));
                    }
                }else{
                    $('#picker-way').text('￥' + returnFloat(default_shipping.first_fee));
                }
            }
        }
    });
    //点击购买
    $("#buy").click(function() {
        var num = Number($("#shownum").val());
        var id = $('input[name=id]').val();
        var shipping_way_id = $('#shipping-id').val();
        window.location.href = 'buy_detail?id=' + id + '&num=' + parseInt(num) + '&shipping_way_id=' + shipping_way_id;
    });

    //点击加入购物车
    $("#cart").click(function() {
        if(cartflag) {
            return
        }
        $(this).val('正在加入...').css({
            backgroundColor: "#a0a0a0"
        }).attr('disabled', 'disabled')
        cartflag = true
        var num = Number($("#shownum").val());
        var id = $('input[name=id]').val();
        $.post('add_shopping_cart', {
            id: id,
            num: num
        }, function(data) {
            $("#cart").val('加入购物车').css({
                backgroundColor: "#fc9c35"
            }).removeAttr('disabled')
            cartflag = false
            tusi(data.msg)

        });
    });

});

//取参
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if(r != null) return unescape(r[2]);
    return null; //返回参数值
}
// 四舍五入保留2位小数
function returnFloat(value) {
    var value = Math.round(parseFloat(value) * 100) / 100;
    var xsd = value.toString().split(".");
    if(xsd.length == 1) {
        value = value.toString() + ".00";
        return value;
    }
    if(xsd.length > 1) {
        if(xsd[1].length < 2) {
            value = value.toString() + "0";
        }
        return value;
    }
}