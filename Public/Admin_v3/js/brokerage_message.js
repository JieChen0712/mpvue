var loading = false;
var isload = true;
var rpage = 1;
var bpage = 1;
var rflag = false;
var bflag = false;
var type = 'refund';
var page = 1;

$(document).ready(function() {
    $(".message-list2").hide(1);

    $('.type').on('click', function(e) {
        var X = $(document).width() - 115;
        var Y = e.pageY + 30;
        $('.mask-select .list').css({
            'top': Y + 'px',
            'left': X + 'px'
        })
        $('.mask-select').fadeIn();
    })
    $('.mask-select').on('click', function() {
        $(this).fadeOut();
    })
    $('.mask-select').on('click', '.list dd', function() {
        var text = $(this).text();
        $('.type span:first-child').text(text);
    })

    $("#select-btn dd").bind("click", function() {
        //  	清空内容
        loading = false;
        $.attachInfiniteScroll($('.infinite-scroll'));
        $(".message-list1").html("");
        $(".message-list2").html("");
        $(".infinite-scroll-preloader").show();
        //  	console.log($(this))
        type = $(this).attr("data-type");
        if(type == 'refund') {
            $(".message-list1").show(1);
            $(".message-list2").hide(1);
        } else if(type == 'bonus') {
            $(".message-list2").show(1);
            $(".message-list1").hide(1);
        }
        page = 1;
        addItems();
    });

    addItems();

    //滚动加载
    $.attachInfiniteScroll($('.infinite-scroll'));
    $(document).on('infinite', '.infinite-scroll', function() {
        // 如果正在加载，则退出
        if(loading) return;

        // 设置flag
        loading = true;

        setTimeout(function() {
            addItems();
            //          if (lastIndex >= maxItems) {
            //              $.detachInfiniteScroll($('.infinite-scroll'));
            //              $('.infinite-scroll-preloader').remove();
            //              return;
            //          }

            //          addItems(itemsPerLoad, lastIndex);
        }, 1);
    });
})

function addItems() {
    //	var pege=1;
    var Ourl = '';
    var html = '';
    var temp = [];
    var name = '';
    var aim = '';

    if(isload == false) {
        return;
    }
    isload = false;

    if(type == 'bonus') {
        //		page=bpage;
        Ourl = refundurl;
        name = '订单佣金';
        aim = $(".message-list2");
        //		bpage++;
    } else if(type == 'refund') {
        //		page=rpage;
        aim = $(".message-list1");
        Ourl = bonusurl;
        name = '佣金提现';
        //		rpage++;
    }
    $.post(Ourl, {
            page_num: page
        },
        function(data) {
            if(data.code == 1) {
                if(data.info.list == null || data.info.list == "" || data.info.list == undefined) {
                    stopLoad(aim);
                    return;
                }
                if(type == 'bonus') {

                    $.each(data.info.list, function(key, value) {
                        var time = new Date(value.time * 1000);
                        html = '<div class="message ' + type + '" data-id="' + value.id + '"><div class="type"><p>' + name + '</p><span>' + value.money + '</span></div><div class="status">' +
                            '<p>' + time.toLocaleDateString() + '</p></div></div>';
                        temp.push(html);
                    });
                } else if(type == 'refund') {
                    $.each(data.info.list, function(key, value) {
                        var time = new Date(value.created * 1000);
                        html = '<div class="message ' + type + '"  data-id="' + value.id + '"><div class="type"><p>' + name + '</p><span>-' + value.apply_money + '</span></div><div class="status">' +
                            '<p>' + time.toLocaleDateString() + '</p><span>' + value.status_name + '</span></div></div>';
                        temp.push(html);
                    });
                }
                aim.append(temp);
                loading = false;
                if(data.info.list.length < 10) {
                    stopLoad(aim);
                    return;
                }
                page++;
            } else {
                console.log(data.msg)
            }
        });
    isload = true;
}

function stopLoad(aim) {
    var str = ' <p style="text-align: center;">暂无更多数据</p>';
    $.detachInfiniteScroll($('.infinite-scroll'));
    $(".infinite-scroll-preloader").hide();
    aim.append(str);
}