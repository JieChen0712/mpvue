$().ready(function() {
    //  微信绑定信息
    if(is_user != "") {
        $.toast(is_user);
    }

    //  页面状态
    $('.bar-tab').find('.tab-item').removeClass('active');
    $("a.login").addClass('active');

    var cortrol = getQueryString("control");

    if(cortrol == "register") {
        //      $('.login_wrapper').addClass('active');
        //      $('.register_wrapper').addClass('active');
        $('.register_wrapper').addClass('normal');
        $('.login_wrapper').hide();
    }

    //  手机号码登录
    $('#phone_login').bind('click', function() {
        var account = $('#account').val();
        var password = $('#password').val();
        var reg1 = new RegExp(/^1[3|4|5|8][0-9]\d{4,8}$/);
        var reg2 = new RegExp(/[^\u4e00-\u9fa5]+/);

        console.log(!(reg1.test(account)))
        if(account == "" || password == "") {
            $.toast("账号或密码不能为空！");
        } else if(!(reg1.test(account))) {
            $.toast("请输入正确的手机格式！");
        } else if(!(reg2.test(password))) {
            $.toast("密码格式错误！");
        } else {
            $.post(user_login, {
                account: account,
                password: password
            }, function(data) {
                if(data.code == 1) {
                    setTimeout(function() {
                        window.location.href = data.return_url
                    }, 2000);
                }
                $.toast(data.msg);
            });
        }

    });

    //  微信登陆
    $('.btn_wechat').bind('click', function() {
        if(is_user == "") {
            $.get(wechat_login, function(data) {
                if(data.code == 1) {
                    setTimeout(function() {
                        window.location.href = data.return_url;
                    }, 2000);
                }
                $.toast(data.msg);
            });
        }

    });

    //  注册页
    $('.btn_apply').bind('click', function() {
        $('.login_wrapper').addClass('active');
        $('.register_wrapper').addClass('active');
    })

    //  手机号码注册
    $('#phone_register').bind('click', function() {
        $.post(sign_up, {
            phone: $('#phone_num').val()
        }, function(data) {
            $.toast(data.msg);
            setTimeout(function() {
                window.location.href = changeURLArg(window.location.href, "reloadtime", new Date().valueOf());
            }, 2000);
        })
    });

});

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if(r != null) return unescape(r[2]);
    return null;
}
//设置url参
function changeURLArg(url, arg, arg_val) {
    var pattern = arg + '=([^&]*)';
    var replaceText = arg + '=' + arg_val;
    if(url.match(pattern)) {
        var tmp = '/(' + arg + '=)([^&]*)/gi';
        tmp = url.replace(eval(tmp), replaceText);
        return tmp;
    } else {
        if(url.match('[\?]')) {
            return url + '&' + replaceText;
        } else {
            return url + '?' + replaceText;
        }
    }
    return url + '\n' + arg + '\n' + arg_val;
}