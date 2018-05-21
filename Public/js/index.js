/**
 *	WHMASK经销商管理系统js文件
 */
//代理级别升级
function upgrade(id, level, did, audited) {
    var textname = "级别升级";
    $('.form h3').html(textname);
    if (level == 1) {
        layer.msg(level_name1 + '不能再升级');
        return;
    }
    if (audited == 0) {
        layer.msg('未审核不能升级');
        return;
    }
    if (level == 2 && did != 0) {
        layer.msg('没权限升级' + level_name2 + '!');
        return;
    }
    $('input[name=managerid]').val(id);
    $('input[name=type]').val('up');
    var selectHtml = '<option value="0">请选择代理级别</option>';
    if (did == 0) {
        selectHtml = selectHtml + '<option value="1">' + level_name1 + '</option>';
    }
    if (level >= 3 && level_num >= 3) {
        selectHtml = selectHtml + '<option value="2">' + level_name2 + '</option>';
    }
    if (level >= 4 && level_num >= 4) {
        selectHtml = selectHtml + '<option value="3">' + level_name3 + '</option>';
    }
    if (level >= 5 && level_num >= 5) {
        selectHtml = selectHtml + '<option value="4">' + level_name4 + '</option>';
    }
    if (level >= 6 && level_num >= 6) {
        selectHtml = selectHtml + '<option value="5">' + level_name5 + '</option>';
    }

    $('#pdlevel').html(selectHtml);

    //弹出升级窗口
    $('<div id="windowBG"></div>').css({
        width: $(document).width(),
        height: $(document).height(),
        position: 'absolute',
        top: 0,
        left: 0,
        zIndex: 998,
        opacity: 0.3,
        filter: 'Alpha(Opacity = 30)',
        backgroundColor: '#000000'
    }).appendTo('body');

    var obj = $('.form');
    obj.css({
        left: ($(window).width() - obj.width()) / 2,
        top: $(document).scrollTop() + ($(window).height() - obj.height()) / 2
    }).fadeIn();
}

//降级
function downgrade(id, level, audited) {
    var textname = "级别降级";
    $('.form h3').html(textname);
    if (level == 1) {
        layer.msg(level_name1 + '不能降级!');
        return;
    }
    if (audited == 0) {
        layer.msg('未审核不能降级');
        return;
    }
    if (level == level_num) {
        layer.msg('最低级别不能再降级!');
        return;
    }
    $('input[name=managerid]').val(id);
    $('input[name=type]').val('down');

    var selectHtml = '新代理级别:<select name="level">';
    if (level <= 5 && level_num >= 6) {
        selectHtml = selectHtml + '<option value="6">' + level_name6 + '</option>';
    }
    if (level <= 4 && level_num >= 5) {
        selectHtml = selectHtml + '<option value="5">' + level_name5 + '</option>';
    }
    if (level <= 3 && level_num >= 4) {
        selectHtml = selectHtml + '<option value="4">' + level_name4 + '</option>';
    }
    if (level <= 2 && level_num >= 3) {
        selectHtml = selectHtml + '<option value="3">' + level_name3 + '</option>';
    }
    if (level <= 1 && level_num >= 2) {
        selectHtml = selectHtml + '<option value="2">' + level_name2 + '</option>';
    }
    /*if(level <= 1) {
    	selectHtml = selectHtml + '<option value="2">大区总监</option>';
    }*/
    selectHtml = selectHtml + '</select>';

    $('#selectlevel').html(selectHtml);

    //弹出升级窗口
    $('<div id="windowBG"></div>').css({
        width: $(document).width(),
        height: $(document).height(),
        position: 'absolute',
        top: 0,
        left: 0,
        zIndex: 998,
        opacity: 0.3,
        filter: 'Alpha(Opacity = 30)',
        backgroundColor: '#000000'
    }).appendTo('body');

    var obj = $('.form');
    obj.css({
        left: ($(window).width() - obj.width()) / 2,
        top: $(document).scrollTop() + ($(window).height() - obj.height()) / 2
    }).fadeIn();
}

//提交级别升级
function submit() {
    var mid = $('input[name=managerid]').val();
    var level = $('select[name=level]').val();
    var type = $('input[name=type]').val();
    var b_id = $('select[name=receive_id]').val();
    var b_name = $('.' + b_id).html();
    $.post(upgradeUrl, {
        mid: mid,
        level: level,
        type: type,
        b_id: b_id,
        b_name: b_name
    }, function (res) {
        if (res.status == 1) {
            layer.msg("代理升级成功！");
        } else if (res.status == 2) {
            layer.msg("代理降级成功！");
        } else {
            layer.msg("操作失败，请重新操作！");
        }
        //关闭升级窗口
        $('.form').fadeOut('slow', function () {
            $('#windowBG').remove();
        });
        //刷新页面
        location.reload();
    }, 'json');
}

//取消级别升级
function cancel() {
    var textone = '新代理级别:<select name="level" id="pdlevel" onchange="plevel()"></select>上&nbsp;级&nbsp;代&nbsp;理:<select name="levelone" id="uppdlevel" onchange="updlevel()"><option value="">请选择代理级别</option></select><select name="receive_id" style="margin-left:71px;" class="mylevel"><option value="0">请选择代理</option></select>';
    //关闭升级窗口
    $('.form').fadeOut('slow', function () {
        $('#windowBG').remove();
        $('#selectlevel').html(textone);
    });
}

//验证码图像转换
function change_code(obj) {
    $("#code").attr("src", URL + Math.random());
    return false;
}

//管理员登录
function login() {
    var username = $('input[name=username]').val();
    var password = $('input[name=password]').val();
    var code = $('input[name=code]').val();

    if (username == '') {
        layer.msg('请填写用户名！');
        return;
    }
    if (password == '') {
        layer.msg('请填写密码');
        return;
    }
    if (code == '') {
        layer.msg('请填写验证码！');
        return;
    }

    $.post(logUrl, {
        username: username,
        password: password,
        code: code
    }, function (res) {
        if (res.status == 1) {
            layer.msg('验证码错误！');
            return;
        } else if (res.status == 2) {
            layer.msg('用户名或密码错误！');
        } else {
            location.href = indexUrl;
        }
    }, 'json');
}

//订单审核
function orderaudit() {
    var managers = document.getElementsByName("mid");
    var mids = "";
    for (var i = 0; i < managers.length; i++) {
        if (managers[i].checked) {
            mids = mids + '_' + managers[i].value;
        }
    }
    if (mids == '') {
        layer.msg('请选择至少一个订单！');
        return;
    }
    $.post(orderauditUrl, {
        mids: mids
    }, function (res) {
        if (res.status) {
            layer.msg('订单审核成功！');
            setTimeout(function () {
                location.reload();
            }, 1500)
        } else {
            layer.msg('订单审核失败！');
        }
    }, 'json');
}
//发货审核
function fkaudit() {
    var managers = document.getElementsByName("mid");
    var mids = "";
    for (var i = 0; i < managers.length; i++) {
        if (managers[i].checked) {
            mids = mids + '_' + managers[i].value;
        }
    }
    if (mids == '') {
        layer.msg('请选择至少一个订单！');
        return;
    }
    $.post(fkauditUrl, {
        mids: mids
    }, function (res) {
        if (res.status) {

            layer.msg('发货成功！');
            location.reload();
        } else {
            layer.msg('发货失败！');
        }
    }, 'json');
}

//删除品牌合伙人申请
function del() {
    var managers = document.getElementsByName("mid");
    var mids = ""; //存放品牌合伙人ID

    for (var i = 0; i < managers.length; i++) {
        if (managers[i].checked) {
            mids = mids + '_' + managers[i].value;
        }
    }
    if (mids == '') {
        layer.msg('请选择至少一个代理！');
        return;
    }
    layer.load();
    $.post(delUrl, {
        mids: mids
    }, function (res) {
        layer.close(layer.load());
        if (res.status) {
            layer.msg('删除成功！');
            setTimeout(function () {
                location.reload();
            }, 1500)

        } else {
            layer.msg('删除失败！');
        }
    }, 'json');
}

/*//搜索代理
function checkkeyword(id){
	var keyword = $("#keyword").val();
	 "" == keyword ?layer.msg("搜索关键字不能为空！ "):location = encodeURI("/qiduoyun/jingxiaoshan/index.php/Radmin/Manager/search?id=" + id + "&keyword=" + keyword);
}*/

function checkkeyword(id) {
    var keyword;
    if (id == 1) {
        keyword = $("#keyword1").val();
    } else {
        keyword = $("#keyword2").val();
    }
    searchUrl = searchUrl.replace('8', id);
    searchUrl = searchUrl.replace('9', keyword);
    "" == keyword ? layer.msg("搜索关键字不能为空！ ") : location.href = searchUrl;
}
$(function () {
    $('.xgmm').click(function () {
        var pd = confirm("您确定要为该用户重置密码（123456）吗？");
        if (pd) {
            var id = $('input[name=id]').val();
            $.post(urlxgmm, {
                id: id
            }, function (data) {
                if (data == 1) {
                    layer.msg('重置密码成功！');
                } else {
                    layer.msg("重置密码失败！原因：原来密码就是123456！");
                }
            });
        }
    });
});
