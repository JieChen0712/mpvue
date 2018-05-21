$(document).ready(function () {

    /* input的值改变时 */
    $('.shownum').change(function () {
        changeAllString(this);
    });
//加按钮点击
    $(".numadd").click(function() {
        var count = $(this).siblings(".shownum").val(Number($(this).siblings(".shownum").val()) + 1);
        changeAllString(this);
    });
    //减按钮点击
    $(".reducenum").click(function() {
        var num = $(this).siblings(".shownum").val();
        if(num > 1) {
            var count = $(this).siblings(".shownum").val(Number(num) - 1);
            changeAllString(this);
            
        }
    });
    //选择按钮点击
    
    $('#btn_sure').bind('click', function() {
        $('#mask').fadeOut();
        setCookie('citypicker',$('#city-picker').val(),3);
        setCookie('re_detail',$('#address_detail').val(),3);
        setCookie('re_phone',$('#re_phone').val(),3);
        setCookie('re_name',$('#re_name').val(),3);
        
        $('#address_text').text(($('#city-picker').val()).trim() + ' / ' + $('#address_detail').val());
    });
    $('#add_address').bind('click', function() {
        $('#mask').fadeIn();
    });
    
    $('#city-picker').val(getCookie("citypicker"));
    $('#address_detail').val(getCookie("re_detail"));
    $('#re_phone').val(getCookie("re_phone"));
    $('#re_name').val(getCookie("re_name"));
    
    if(($('#city-picker').val()).trim()!=""&& $('#address_detail').val()!=""){
        $('#address_text').text(($('#city-picker').val()).trim() + ' / ' + $('#address_detail').val());
    }

    $(".btn-select").bind("click", function () {
        var selflag = true;
        if ($(this).attr("id") != "btn-selall") {
            $(this).toggleClass("active");
            $(".btn-select").each(function (key, value) {
                if (!$(value).hasClass("active")) {
                    selflag = false;
                }
            });
            if (!selflag && $("#btn-selall").hasClass("actived")) {
                $("#btn-selall").removeClass("actived");
            }
            if (selflag) {
                $("#btn-selall").addClass("actived");
            }
        }
    });

    $(".btn-sel").bind("click", function () {
        if ($(this).hasClass("actived")) {
            $(".btn-select").removeClass("active");
            $(this).toggleClass("actived").removeClass("active");
        } else {
            $(".btn-select").addClass("active");
            $(this).toggleClass("actived").removeClass("active");
        }
    });
    
    //优惠商城下单
    $('#submit-order3').click(function () {
        var p_ids = new Array();
        var p_nums = new Array();
        var order_num = new Date().getTime();
        var address_id = $('#address_id').val();
        //让按钮不可点击
        $(this).attr('disabled', 'true');

        var product_id = $('input[name=product_id]').val();
        var num = $('input[name=shownum]').val();
        p_ids = product_id.split("|");
        p_nums = num.split("|");
        // p_ids[0] = $('input[name=product_id]').val();
        // p_nums[0] = $('input[name=shownum]').val();
        var cart_ids = $('input[name=cart_ids]').val();
        if (address_id == '') {
            tusi('请必须填写收货信息！');
            return false;
        }
        if (!p_ids) {
            tusi('未找到该产品！');
            return false;
        }
        if (p_nums <= 0) {
            tusi('购买数量必须大于0！');
            return false;
        }


        $.post("orderhand", {
            "p_ids": p_ids,
            "p_nums": p_nums,
            order_num: order_num,
            cart_ids: cart_ids,
        }, function (data) {
            console.log(data);
//          alert(data)
            if (data.code == 1) {
                 window.location.href = root + "/sale/mallwxpay/pay?order_num="+data.order_num;
//                tusi('下单成功');
//                var return_url = 'all';
//
//                if (data.return_url != '' && data.return_url != undefined) {
//                    return_url = data.return_url;
//                }
//
//                setTimeout(function () {
//                    window.location.href = return_url
//                }, 1500);
            } else {
                var msg = '下单失败！';
                if (data.msg != null) {
                    msg = data.msg;
                }
                tusi(msg);
            }
            //让按钮恢复点击
            $('#submit-order3').removeAttr('disabled');
        });
    });
    
    
    //积分商城下单
    $('#submit-order4').click(function () {
        var p_ids = new Array();
        var p_nums = new Array();
        var order_num = new Date().getTime();
        var address_id = $('#address_id').val();
        //让按钮不可点击
        $(this).attr('disabled', 'true');

        p_ids[0] = $('input[name=product_id]').val();
        p_nums[0] = $('input[name=shownum]').val();
        var cart_ids = $('input[name=cart_ids]').val();
        if (address_id == '') {
            tusi('请必须填写收货信息！');
            return false;
        }
        if (!p_ids) {
            tusi('未找到该产品！');
            return false;
        }
        if (p_nums <= 0) {
            tusi('购买数量必须大于0！');
            return false;
        }
        $.post("orderhand", {
            "p_ids": p_ids,
            "p_nums": p_nums,
            order_num: order_num,
            cart_ids: cart_ids,
        }, function (data) {
            if (data.code == 1) {
//                 window.location.href = "/sale/mallwxpay/pay?order_num="+data.order_num;
                tusi('下单成功');
                var return_url = 'all?part=1';

                if (data.return_url != '' && data.return_url != undefined) {
                    return_url = data.return_url;
                }
                
                setTimeout(function () {
                    window.location.href = return_url
                }, 1500);
            } else {
                var msg = '下单失败！';
                if (data.msg != null) {
                    msg = data.msg;
                }
                tusi(msg);
            }
            //让按钮恢复点击
            $('#submit-order4').removeAttr('disabled');
        });
    });
    
    //店中店下单
    $('#submit-order5').click(function() {
        var p_ids = new Array();
        var p_nums = new Array();
        var order_num = new Date().getTime();
        var re_phone = $('#re_phone').val();
        var re_name = $('#re_name').val();
        var re_address_detail = $('#address_detail').val();
        var re_address= $('#city-picker').val()+" ";
        
        if(re_address_detail==""||re_phone==""||re_address==""){
          return tusi("请填写订单信息");
        }
        
        //      var address_id = $('#address_id').val();
        //让按钮不可点击
        $(this).attr('disabled', 'true');

        var product_id = $('input[name=product_id]').val();
        var num = $('input[name=shownum]').val();
        p_ids = product_id.split("|");
        p_nums = num.split("|");
        // p_ids[0] = $('input[name=product_id]').val();
        // p_nums[0] = $('input[name=shownum]').val();
        //      var cart_ids = $('input[name=cart_ids]').val();
        //      if (address_id == '') {
        //          tusi('请必须填写收货信息！');
        //          return false;
        //      }
        if(!p_ids) {
            tusi('未找到该产品！');
            $(this).removeAttr('disabled');
            return false;
        }
        if(p_nums <= 0) {
            tusi('购买数量必须大于0！');
            $(this).removeAttr('disabled');
            return false;
        }

        $.post("orderhand", {
            p_ids: p_ids,
            p_nums: p_nums,
            order_num: order_num,
            s_phone:re_phone,
            s_name:re_name,
            s_address:re_address,
            s_address_detail:re_address_detail
            //          cart_ids: cart_ids,
        }, function(data) {
            $('#submit-order5').removeAttr('disabled');
            console.log(data);
            //          alert(data)
            if(data.code == 1) {
                tusi(data.msg);
                if(getCookie('orderNum')!=undefined){
                    setCookie('orderNum',getCookie('orderNum')+','+data.order_num,5);
                    setTimeout(function(){
                      window.location.href = root + '/sale/shop/index?uid=' + getCookie('uid');
                    },2000);
                }else{
                    setCookie('orderNum',data.order_num,5);
                    setTimeout(function(){
                      window.location.href = root + '/sale/shop/index?uid=' + getCookie('uid');
                    },2000);
                }
                
//              setTimeout(function(){window.location.href = root + "/sale/shop/index?uid="+getCookie('uid');},2000);

                //                var return_url = 'all';
                //
                //                if (data.return_url != '' && data.return_url != undefined) {
                //                    return_url = data.return_url;
                //                }
                //
                //                setTimeout(function () {
                //                    window.location.href = return_url
                //                }, 1500);
            } else {
                var msg = '下单失败！';
                if(data.msg != null) {
                    msg = data.msg;
                }
                tusi(msg);
            }
            //让按钮恢复点击
            $('#submit-order5').removeAttr('disabled');
        });
    });

    //结算

    $(document).on("click", "#settlement", function () {
        var str = "";
        $(".active").each(function (key, val) {
            str += $(this).data("id") + "|";
        });
        console.log(str);
        if (str == "") {
            tusi('请至少选择一个商品');
            return false;
        }
        window.location.href = buy_cart_url + '?cart_ids=' + str;
    });

});

//强转两位小数
function changeTwoDecimal_f(x) {
    var f_x = parseFloat(x);
    if (isNaN(f_x)) {
        return x;
    }
    var f_x = Math.round(x * 100) / 100;
    var s_x = f_x.toString();
    var pos_decimal = s_x.indexOf('.');
    if (pos_decimal < 0) {
        pos_decimal = s_x.length;
        s_x += '.';
    }
    while (s_x.length <= pos_decimal + 2) {
        s_x += '0';
    }
    return s_x;
}


/* 当客户输入商品数量时，总金额和总积分也随之改变*/
function changeAllString(_this) {
    var goodsPrice = Number($(_this).parent().siblings('.goods-infs').find(".priceval").text());
//  var goodsIntegral = parseFloat($('#goods-integral').text());
    var amount = parseInt($(_this).siblings(".shownum").val());
    var prev_total = Number($("#countprice").text());
    if(isNaN(amount)){
        amount = parseInt($(_this).val());
    }
    console.log(amount);
//  if($("#countprice").length!=0){
    if($(_this).attr('class') == "numadd"){
        $("#countprice").text(changeTwoDecimal_f(goodsPrice + prev_total));
        console.log(goodsPrice * amount+prev_total)
    }else if($(_this).attr('class') == "reducenum"){
        $("#countprice").text(changeTwoDecimal_f(prev_total - goodsPrice));
    }else if($(_this).attr('class') == "shownum"){
        $("#countprice").text(changeTwoDecimal_f(goodsPrice * amount));
    }
      
//  }else{
//    $("#all-integral").text('+' + goodsIntegral * amount + '积分');
//  }
}

//读取Cookie的方法  
function getCookie(key) {
    var arr1 = document.cookie.split('; ');
    for(var i = 0; i < arr1.length; i++) {
        var arr2 = arr1[i].split('=');
        if(arr2[0] == key) {
            return decodeURI(arr2[1]);
        }
    }
}

function setCookie(key, value, t) {
    var oDate = new Date();
    console.log(t)
    oDate.setDate(oDate.getDate() + t);
    document.cookie = key + '=' + encodeURI(value) + ';expires=' + oDate.toGMTString();
}