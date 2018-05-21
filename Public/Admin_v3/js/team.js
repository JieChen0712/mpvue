$().ready(function() {
    loadHtml();
    $("a.team").children("img").attr('src', TP_PUBLIC + '/Admin_v3/images/footer1.jpg').siblings(".text").css({
        color: "#1d4a78"
    });

    $(document).on('click', ".agent-select .achild", function(event) {

        if($(this).data("click") == 1) {
            $(this).attr({
                "data-click": "2"
            });
        } else if($(this).attr("data-click") == 2) {
            $(this).parent().html('<input type="radio" data-click="1" id="" value="" class="achild button-warning" name="pend" id="pend" data-agentid="{$vo.id}"/>');
        }
        console.log($('.agent-select .achild:checked'));
    });

    $("a[href='#page=1']").click(function() {
        showDiffcontent(1);
    });
    $("a[href='#page=4']").click(function() {
        showDiffcontent(4);
    });
    /*****************点击跳转到第二个页面时的js*******************/
    $("a[href='#page=2']").click(function() {
        showDiffcontent(2);
    });
    /*****************点击跳转到第三个页面时的js*******************/
    $("a[href='#page=3']").click(function() {
        showDiffcontent(3);
    });
    $("#refused").click(function() {
        var refused = 0;
        var dataid = getAgentId();
        if(!dataid) {
            return false;
        }
        var datajson = {
            mids: dataid,
            flag: refused
        };

        audit(datajson, this);

    });
    $("#sure").click(function() {
        var refused = 1;
        var dataid = getAgentId();
        if(!dataid) {
            return false;
        }
        var datajson = {
            mids: dataid,
            flag: refused
        };
        audit(datajson, this);
    });

});
//获取审核代理数据
function getAgentsee(agentseeurl) {
    $.get(agentseeurl, function(data) {
        var items = new Array();
        $.each(data, function(key, val) {
            var str = '<ul class="row col-100 angent-detail3"><li class="col-33">' + val.name + '</li><li class="col-33">' + val.levname + '</li>' +
                '<li class="col-33 agent-select">正常<i class="hide-radio"><input type="radio" data-click="1" data-agentid="' + val.id + '" value="" name="achild" class="achild" class="button-warning"/></i>' +
                '<a href=""><span>查看代理资料</span></a></li></ul>'
            items.push(str);
        });
        $("#agents-list").html("");
        $("#agents-list").append(items);
    });
}

//获取选中代理的id
function getAgentId() {
    var str = "";
    var idarray = $('.agent-select .achild:checked');
    $(idarray).each(function(key, val) {
        var id = $(val).data("agentid");
        str += '|' + id;
    });

    if(str == '') {
        tusi('请选择至少一个代理！');
        return false;
    }
    return str;
}

var sureORrefused = false
//品牌合伙人审核
function audit(data, aim) {
    if(sureORrefused) {
        return
    }
    sureORrefused = true
    $(aim).css({
        background: '#a0a0a0'
    })
    $.post(agentpassurl, {
        pend: data.mids,
        flag: data.flag
    }, function(res) {
        if(res.status) {
            tusi('审核成功！');
            setTimeout(function() {
                window.location.href = changeURLArg(window.location.href, "reloadtime", new Date().valueOf());
            }, 2000);
        } else {
            tusi('审核失败！');
        }
    }, 'json');
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
//实现页面的隐藏
function hideAndshowPage(index) {
    $(".content section").hide();
    var str = "#content" + index;
    $(str).fadeIn();
}

//获取url的参数
function getParam() {
    var hashLocation = location.hash;
    var hashSplit = hashLocation.split("#!/");
    var hashName = hashSplit[1];
    var hash = window.location.hash
    var param = {};
    if(hash.indexOf("#") != -1) {
        var str = hash.substr(1)　 //去掉?号  
        strs = str.split("&");
        for(var i = 0; i < strs.length; i++) {
            param[strs[i].split("=")[0]] = strs[i].split("=")[1];
        }
    }
    return param;
}

//根据url的参数不同显示不同的content
function showDiffcontent(params) {
    //获取正确的title,且菜单栏添加选中状态
    $(".triangle").removeClass("active");
    var num = $(".triangle").length;

    var title = '';
    if(params > num) {
        title = $(".triangle").eq(num - 1).addClass("active").siblings("span").text();
    } else {
        title = $(".triangle").eq(params - 1).addClass("active").siblings("span").text();
    }

    //改变页面的title
    $(document).attr("title", title);
    var content = '#content' + params;
    $(".content section").hide(1);
    console.log($('section'))
    $(content).fadeIn();
}
//根据地址加载相应的显示
function loadHtml() {
    //		$(window).on('load', function() {
    //获取url的参数
    var pageId = getParam();
    console.log(pageId)
    if(pageId.page == undefined) {
        console.log(pageId.page)
        showDiffcontent(1);
    } else {
        showDiffcontent(pageId.page);
    }
    //		});
    //			alert("页面加载完成 ");
}