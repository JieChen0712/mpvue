$(document).ready(function () {
    $('.bind').on('click', function () {
        $.ajax({
            url: bindWechatUrl,
            type: 'post',
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.code == 1) {
                    $.alert(data.msg);
                    setTimeout(function () {
                        window.location.href = loginUrl;
                    },2000)
                } else {
                    $.alert(data.msg);
                }
            }
        })
    })
    $('.unbind').on('click', function () {
        $.prompt('请输入密码解除绑定', function (value) {
            $.ajax({
                url: unbindWechatUrl,
                data: {
                    password: value
                },
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.code == 1) {
                        $.alert(data.msg);
                        // setTimeout(function () {
                        //     window.location.href = loginUrl;
                        // },2000)
                    } else {
                        $.alert(data.msg);
                    }
                }
            })
        })
    })
})