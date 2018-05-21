$(document).ready(function () {
    var loading = false;
    var isNull = false;
    var page = 1;
    $("#datetime-picker").datetimePicker({
        toolbarTemplate: '<header class="bar bar-nav">\
    <button class="button button-link pull-left close-picker">取消</button>\
    <button class="button button-link pull-right close-picker" id="choose">确定</button>\
    <h1 class="title">选择日期</h1>\
    </header>'
    });

    $("#datetime-picker").bind("click", function () {
        $(".picker-items-col").eq(2).hide();
        $(".picker-items-col-divider").hide().next().hide().next().hide();
        $(".picker-modal").css({
            background: "white"
        });
        $(".picker-items-col").css({
            margin: "0 2rem"
        });
        subDate(this);
        $(this).change(function () {
            subDate(this);
            //自动更新title的月份
            getMonth();
        });
    });

    $.attachInfiniteScroll($('.infinite-scroll'));

    $(document).on('infinite', '.infinite-scroll', function() {
        // 如果正在加载，则退出
        if(loading) return;
        // 设置flag
        loading = true;
        setTimeout(function() {
            loading = false;
            if(isNull) {
                $.detachInfiniteScroll($('.infinite-scroll'));
                $('.infinite-scroll-preloader').remove();
                return;
            }
            page++;
            upData(page);
        }, 1000);
    });
    // 初始化数据
    initString();

    function initString() {
        //自动更新title的月份
        getMonth();
        subDate($("#datetime-picker"));
        var date = new Date();
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        if (month < 10) {
            month = '0' + month;
        }
        var str = year.toString() + month.toString();
        getData(str, 'recharge', 1);

    }

    /* 改变账单类型时重新渲染数据 */
    $(document).on('click', '.list-button', function () {
        page = 1;
        isNull = false;
        $.attachInfiniteScroll($('.infinite-scroll'));
        var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
        if($('.infinite-scroll-preloader')){
            $('.month-detail').after(shtml);
        }
        $('.detail-list').html('');
        upData(page);
    });

    /* 改变月份时重新渲染数据 */
    $(document).on('click', '#choose', function () {
        page = 1;
        isNull = false;
        $.attachInfiniteScroll($('.infinite-scroll'));
        var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
        if($('.infinite-scroll-preloader')){
            $('.month-detail').after(shtml);
        }
        $('.detail-list').html('');
        upData(page);
    });

    /*选择列表获取内容*/
    selectList();

})

function subDate(aim) {
    var str = $(aim).val();
    $(aim).val(str.substring(0, 7));
}

function getMonth() {
    //获取要更新月份的Dom
    var oText = $('.bill-title .text')
    //获取用户选取的年月
    var inputValue = $('#datetime-picker').val();
    //截取月份
    var month = inputValue.substring(5, 7);
    //当月份不于10，截取一位数
    if (month < 10) {
        month = inputValue.substring(6, 7);
    }
    //将截取的数据更新到页面上
    oText.html(month + '月份账单');
}

function selectList() {
    var oSelect = $('.select-wrapper .select');
    var oSelectList = $('.popover .select-list');
    oSelectList.find('li a').click(function () {
        var oText = $(this).html();
        oSelect.find('em').text(oText);
    })
}


/*用Ajax获取数据 */
function getData(month, type, page) {
    $.get(billurll, {
        month: month,
        type: type,
        page_num: page
    }, function (data) {
        var html = '';
        console.log(data);
        if (data.info.list == null || data.info.list == undefined || data.info.list == "") {
            // html = '<include file="Public/empty"/>';
            // $('.detail-list').html("").append(html);
            $.detachInfiniteScroll($('.infinite-scroll'));
            $('.infinite-scroll-preloader').remove();
            isNull = true;
        } else {
            var len = data.info.list.length;
            if(data.info.list.length < 5) {
                $.detachInfiniteScroll($('.infinite-scroll'));
                $('.infinite-scroll-preloader').remove();
            }
            for (var i = 0; i < len; i++) {
                html += '<li class="detail">' +
                    '<div class="date">' +
                    '<p class="week">' + getWeek(data.info.list[i].created_format.substring(0, 11)) +
                    '</p>' +
                    '<p class="month">' + data.info.list[i].created_format.substring(5, 11) + '</p>' +
                    '</div>' +
                    '<div class="avatar">' +
                    // <img src="__PUBLIC__/Admin_v3/images/information/avatar.png" height="40" alt="">
                    '</div>' +
                    '<div class="description-wrapper">' +
                    '<p class="money">+' + data.info.list[i].money + '</p>' +
                    '<span class="user">' + data.info.list[i].dis_name + '</span>' +
                    '<span class="description">-' + data.info.list[i].type_name + '</span>' +
                    '</div>' +
                    '</li>'
            }
            $('.detail-list').append(html);
        } 
    });
}

/* 重新渲染数据 */
function upData(page) {
    var month = $('#datetime-picker').val().toString().slice(0, 4) + $('#datetime-picker').val().toString().slice(
        5, 7);
    var text = $('.select-wrapper .select em').text();
    var type = '';
    switch (text) {
        case '充值':
            type = 'recharge';
            break;
        case '扣费':
            type = 'charge';
            break;
        case '提现':
            type = 'refund';
            break;
    }
    getData(month, type, page);
}

function getWeek(str) {
    var temp = new Date(str);
    var week;
    switch (temp.getDay()) {
        case 0:
            week = "周日";
            break;
        case 1:
            week = "周一";
            break;
        case 2:
            week = "周二";
            break;
        case 3:
            week = "周三";
            break;
        case 4:
            week = "周四";
            break;
        case 5:
            week = "周五";
            break;
        case 6:
            week = "周六";
            break;
        default:
            week: "获取日期失败！";
    }
    return week;
}