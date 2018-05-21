$(document).ready(function () {
    $(".login").click(function () {
        var name = $("input[name=name]").val();
        var password = $("input[name=password]").val();
        if (name == "") {
            tusi("请输入微信号");
            return false;
        }
        if (password == "") {
            tusi("请输入密码");
            return false;
        }
        $.post(loginUrl, {
            name: name,
            password: password
        }, function (data) {
            if (data == 'none') {
                tusi('微信号或者密码错误');
                return;
            } else if (data == 'nomanager') {
                tusi('经销商还未通过审核,不能进入后台');
                return;
            } else if (data == 'disable') {
                tusi('经销商已被禁用，请联系品牌负责人！');
                return;
            } else {
                //location.href = data['url'];
                location.href = indexUrl;
            }
        });
    });

    $('.input-wrapper input').focus(function () {
        $('.web-text').hide();
    })
    $('.input-wrapper input').blur(function () {
        $('.web-text').show();
    })
})