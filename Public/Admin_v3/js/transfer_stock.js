$(document).ready(function() {
    var levelArr = [];
    var levelArrSub = [];
    var nameArr = [];
    var nameArrSub = [];
    var pid = 0;
    var tid = 0;

    //获取经销商等级并存入数组
    getLevel();

    //获取产品信息
    getProduct();

    $('.btn').on('click', 'button', function() {
        $('.btn button').attr('disablid', 'true');
        if(parseInt($('.num-select .num').val()) > parseInt($('.text span i').text())) {
            tusi('转移数量大于库存数量');
            setTimeout(function() {
                // window.window.location.href = changeURLArg(window.location.href, "reloadtime", new Date().valueOf());
            }, 1000)
            return false;
        } else if(!parseInt($('.num-select .num').val())) {
            tusi('请输入转移数量');
            setTimeout(function() {
                // window.window.location.href = changeURLArg(window.location.href, "reloadtime", new Date().valueOf());
            }, 1000)
            return false;
        }
        var productNum = $('.num-select .num').val();
        var tid = $('.name-select .name').data('id');
        var productArr = [];
        //        var temp = { p_id: pid ,num:productNum}
        //        productArr.push(temp);

        productArr = {
            p_id: pid,
            num: productNum
        };

        $.post(transferStockURL, {
            info: productArr,
            tid: tid
        }, function(data) {
            console.log(productArr)
            //            console.log(data);
            if(data) {
                if(data.code == 1) {
                    tusi('转移库存成功');
                } else {
                    tusi(data.msg);
                }
            }
            setTimeout(function() {
                window.location.href = changeURLArg(window.location.href, "reloadtime", new Date().valueOf());
            }, 1000)
        })
    })

    //将选择的经销商等级替换上，并同时获取该等级的所有经销商
    $(document).on('click', '#levelBtn', function() {
        $('.grade').text($('.picker-selected').text());
        var text = $('.grade').text();

        var level = 0;
        //获取数组下标并设置ID
        $.each(levelArr, function(idx) {
            if(levelArr[idx] == text) {
                $('.grade').attr('data-id', levelArrSub[idx]);
                level = levelArrSub[idx];
            }
        });
        $.post(myTeamURL, {
            level: level
        }, function(data) {
            nameArr.length = 0;
            if(data.info) {
                tid = data.info
                $.each(data.info, function(index, value) {
                    data.info[index].name
                    nameArr.push(data.info[index].name);
                    nameArrSub.push(data.info[index].id);
                })
            }
        })
    })

    //将选择的经销商名字替换上
    $(document).on('click', '#nameBtn', function() {
        $('.name').text($('.picker-selected').text());
        var text = $('.name').text();
        $.each(nameArr, function(idx) {
            if(nameArr[idx] == text) {
                $('.name').attr('data-id', nameArrSub[idx]);
            }
        });
    })

    //显示经销商等级
    $("#picker1").picker({
        toolbarTemplate: '<header class="bar bar-nav">\
        <button class="button button-link pull-right close-picker" id="levelBtn">确定</button>\
        <h1 class="title">经销商等级</h1>\
        </header>',
        cols: [{
            textAlign: 'center',
            values: levelArr
        }]
    });

    //显示经销商名字
    $("#picker2").picker({
        toolbarTemplate: '<header class="bar bar-nav">\
        <button class="button button-link pull-right close-picker" id="nameBtn">确定</button>\
        <h1 class="title">经销商名字</h1>\
        </header>',
        cols: [{
            textAlign: 'center',
            values: nameArr
        }]
    });

    //获取经销商等级并存入数组
    function getLevel() {
        $.post(levalURL, function(data) {
            levelArr.length = 0;
            if(data.info) {
                $.each(data.info, function(index, value) {
                    levelArr.push(value);
                    levelArrSub.push(index);
                })
            }
        })
    }

    //获取产品信息
    function getProduct() {
        var oURL = window.location.search;
        var idArr = oURL.split('=');
        pid = idArr[1];
        var arr = [];
        $.post(stockInfoURL, {
            pid: pid
        }, function(data) {
            var html = '';
            var info = data.info.list;
            if(info) {
                html +=
                    '<div class="img">' +
                    '<img src="' + info[pid].temp_info.image + '" alt="">' +
                    '</div>' +
                    '<div class="text">' +
                    '<p>' + info[pid].temp_info.name + '</p>' +
                    '<span>库存：<i>' + info[pid].num + '</i>件</span>' +
                    '</div>'
            }
            $('.product').append(html);
        })
    }
})

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