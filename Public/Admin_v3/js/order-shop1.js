$(document).ready(function () {
    var startX = 0,
        startY = 0;
    var page = 1;
    var id = 0;
    var loading = false;
    var isNull = false;
    var scrollflag = 1;

    
    $("a.order").children("img").attr('src', TP_PUBLIC + '/Admin_v3/images/footer3.jpg').siblings(".text").css({
        color: "#1d4a78"
    });

    $(".content").bind("scroll", slidenav);

    $("#search").bind("input", function () {
        $(this).css({
            textAlign: "left"
        });
        $(".icon-search").hide();
    });

    $("#search").bind("input", function () {
        if ($(this).val() == "") {
            $(".icon-search").show();
            $(this).css({
                textAlign: "center"
            });
        }
    });


    //获取导航信息
    $.get(orderNavURL, {
        page_num: 1
    }, function (data) {
        if (data.info.list) {
            var aList = data.info.list
            var html = '';
            $.each(aList, function (k, v) {
                // arr.push(v.name);
                html +=
                    '<li><a href="javascript:void(0);" class="external" data-id=' + v.id + '><p>' +
                    v.name + '</p></a></li>';
            })
            $('.all-nav-list ul').append(html);
            $('.shop-nav-list').append(html);
        }
    });

    //初始化数据
    getGoods(id);
    $.attachInfiniteScroll($('.infinite-scroll'));
    $(document).on('infinite', '.infinite-scroll', function () {
        // 如果正在加载，则退出
        if (loading) return;
        // 设置flag
        loading = true;
        setTimeout(function () {
            loading = false;
            if (isNull) {
                $.detachInfiniteScroll($('.infinite-scroll'));
                $('.infinite-scroll-preloader').remove();
                return;
            }
            // var type = $('.shop-nav-list .external p').eq(0).text();
            id = $('#mask').text();
            page++;
            getGoods(id);
        }, 1000);
    });

    $('.shop-nav-list').on('click', 'li a', function () {
        id = $(this).data('id');
        $('#mask').text(id);
        // var type = $(this).find('p').text();
        var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
        if (!$('.infinite-scroll-preloader').find('div').hasClass('preloader')) {
            $('.shop-items').after(shtml);
        }
        $.attachInfiniteScroll($('.infinite-scroll'));
        page = 1;
        isNull = false;
        $('.shop-nav-list li a').removeClass('active');
        $(this).addClass('active');
        $('#apply-list').html('');
        $('#products').html('');
        getGoods(id);
    });

    $('.all-nav-list').on('click', 'li a', function () {
        id = $(this).data('id');
        $('#mask').text(id);
        // var type = $(this).find('p').text();
        var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
        if (!$('.infinite-scroll-preloader').find('div').hasClass('preloader')) {
            $('.shop-items').after(shtml);
        }
        $.attachInfiniteScroll($('.infinite-scroll'));
        page = 1;
        isNull = false;
        $('.shop-nav-list li a').removeClass('active');
        $('#first-li a').addClass('active');
        $('#first-li a').attr('data-id', id);
        $('#apply-list').html('');
        $('#products').html('');
        getGoods(id);
    });

    $(document).on('click', '.more', function () {
        if ($(".content").scrollTop() > 85) {
            $('.mask').css({
                top: '100px'
            });
        } else if ($(".content").scrollTop() == 0) {
            $('.mask').css({
                top: '177px'
            });
        }
        if ($('.mask').css('display') == 'none') {
            $('.mask').slideDown(100, function () {
                $('.all-nav-list').slideDown();
            })
        } else {
            $('.mask').slideUp();
            $('.all-nav-list').slideUp();
        }

    });
    $(document).on('click', '.mask', function () {
        $('.mask').slideUp();
        $('.all-nav-list').slideUp();
    })
    $(document).on('click', '.all-nav-list', function () {
        $('.mask').slideUp();
        $('.all-nav-list').slideUp();
    })
    $('.all-nav-list').on('click', 'li', function () {
        $('.shop-nav-list .external p').eq(0).text($(this).text());
    })

    function slidenav() {
        if ($(".content").scrollTop() > 85) {
            $(".container").css({
                paddingTop: "7rem"
            });
            $("#kind-head").stop(true, false).hide();
            $(".manager-kind").css({
                position: "fixed"
            });
            $(".hidden-scorll").css({
                position: "fixed",
                top: "4.125rem"
            });
            //			$(".shop-items").css({marginTop:"6.25rem"});
        } else if ($(".content").scrollTop() == 0) {
            $(".container").css({
                paddingTop: "0rem"
            });
            $("#kind-head").slideDown();
            $(".manager-kind").css({
                position: "static"
            }).children(":eq(0)").css({
                display: "flex"
            });
            $(".hidden-scorll").css({
                position: "relative",
                top: "0"
            });
            //			$(".shop-items").css({marginTop:"0"});
        }
    }
    
    function getGoods(id) {
        $.post(orderDedetailURL, {
            page_num: page,
            category: id
        }, function (data) {
            var html = '';
            console.log(data);
            console.log(page);
            console.log(id);
            if (data) {
                if (data.info.list == null || data.info.list == undefined || data.info.list == "") {
                    str = '<li style="font-size:1rem;border:none;text-align:center; line-height: 70px; background: #efeff4;"><span style="margin: 0 auto;">暂无更多数据！</span></li>';
                    $('#apply-list').html(str);
                    $.detachInfiniteScroll($('.infinite-scroll'));
                    $('.infinite-scroll-preloader').remove();
                    isNull = true;
                } else {
                    if ((data.info.list.length) < 4) {
                        $.detachInfiniteScroll($('.infinite-scroll'));
                        $('.infinite-scroll-preloader').remove();
                    }
                    $.each(data.info.list, function (index, val) {
                        html += '<li class="s-items clearfloat">';
                        html += '<a href="goods_detail?id=' + val.id +
                            '" class="external">';
                        html += '<img src="' + val.image + '" alt="" />';
                        html += '<div class="shop-details">';
                        html += '<p class="p-shopneme">' + val.name + '</p>';
                        html += '<p class="p-price"><span>￥</span>' + val.price +
                            '<i class="icon icon-car"></i></p></div>';
                        html += '</a></li>';
                    });      
                }
                $('#products').append(html);
            }
        })
    }
});