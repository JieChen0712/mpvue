$(function () {
    var loading = false;
    var isNull = false;
    var page = 1;
    var type = "apply";
    var typeTitle;

    //导航条高亮切换
    $('.nav-wrapper').on('click', 'li', function () {
        $('.nav-wrapper li').removeClass('active');
        $(this).addClass('active');
        switch ($('#picker').text()) {
            // case '全部':
            //     type = 'all';
            //     break;
            case '充值申请':
                type = 'apply';
                break;
            case '提现申请':
                type = 'refund';
                break;
        }
        $.attachInfiniteScroll($('.infinite-scroll'));
        var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
        if (!$('.infinite-scroll-preloader').length>0) {
            $('#apply-list').html("").after(shtml);
        }
        page = 1;
        isNull = false;
        $('.not-string').remove();
        addItems();
        showHide();
    })

    /*选择列表获取内容*/
    selectList();

    function selectList() {
        var oSelect = $('.select');
        var oSelectList = $('.popover .select-list');
        oSelectList.find('li').click(function () {
            var oText = $(this).html();
            //重置参数
            type = $(this).data("type");
            $.attachInfiniteScroll($('.infinite-scroll'));
            var shtml = '<div class="infinite-scroll-preloader"><div class="preloader"></div></div>';
            if (!$('.infinite-scroll-preloader').length>0) {
                $('#apply-list').html("").after(shtml);
            }
            page = 1;
            isNull = false;
            oSelect.find('em').text(oText);
            $('.not-string').remove();
            addItems();
            showHide();
        })
    }
    //滚动加载信息

    function addItems() {
        var html = '';
        $.post(applyurl, {
                type: type,
                page_num: page
            },
            function (data) {
                var len = 0;
                if (type != 'all') {
                    if (data.info != null) {
                        var title = type == 'refund' ? '提现申请' : '充值申请';
                        var status = title.substring(0, 2);
                        if ($('.nav-wrapper .active').text() == '待审核') {
                            $.each(data.info.list, function (key, value) {
                                if (value.status_name == '未审核') {
                                    len++;
                                }
                            })
                        } else if ($('.nav-wrapper .active').text() == '已通过') {
                            $.each(data.info.list, function (key, value) {
                                if (value.status_name == '已审核') {
                                    len++;
                                }
                            })
                        } else if ($('.nav-wrapper .active').text() == '已拒绝') {
                            $.each(data.info.list, function (key, value) {
                                if (value.status_name == '不通过') {
                                    len++;
                                }
                            })
                        }
                        if (len < 5) {
                            if (!$('.not-string').length > 0) {
                                str = '<div class="not-string" style="font-size:1rem;border:none;text-align:center"><span style="margin: 0 auto; padding-bottom: 20px;">暂无更多数据！</span></div>';
                                $('.infinite-scroll').append(str);  
                            }
                            $.detachInfiniteScroll($('.infinite-scroll'));
                            $('.infinite-scroll-preloader').remove();
                        }
                        $.each(data.info.list, function (key, value) {
                            var oclass = '';
                            if (value.status_name == "未审核") {
                                oclass = "unchecked";
                            } else if (value.status_name == "不通过") {
                                oclass = "refuse";
                            } else if (value.status_name == '已审核') {
                                oclass = "pass";
                            }
                            var isclass = '';
                            if (type == 'refund' && value.status_name == "未审核") {
                                isclass = "check_money_refund_apply";
                            } else if (type == 'apply' && value.status_name == "未审核") {
                                isclass = "check_money_apply";
                            }
                            html += '<li class="' + oclass + '"><div class="apply"><dl class="apply-left"><dt>' + title + '</dt><dd>申请人：' + value.dis_info.name + '</dd><dd>申请人等级：' + value.dis_info.levname + '</dd>' +
                                '<dd>' + status + '金额：<span>￥' + value.apply_money + '</span></dd></dl><div class="apply-right"><button class="' + isclass + '" data-id="' + value.id + '">' + value.status_name +
                                '</button></div></div></li>';
                        });
                    } else if (data.info.list == null || data.info.list == undefined || data.info.list == "") {
                        if (!$('.not-string').length > 0) {
                            str = '<div class="not-string" style="font-size:1rem;border:none;text-align:center"><span style="margin: 0 auto; padding-bottom: 20px;">暂无更多数据！</span></div>';
                            $('.infinite-scroll').append(str);  
                        }
                        $.detachInfiniteScroll($('.infinite-scroll'));
                        $('.infinite-scroll-preloader').remove();
                    }
                }
                // else {
                //     if (data.info.list != null || data.info_refund.list != null) {
                //         if ($('.nav-wrapper .active').text() == '待审核') {
                //             $.each(data.info.list, function (key, value) {
                //                 if (value.status_name == '未审核') {
                //                     len++;
                //                 }
                //             })
                //             $.each(data.info_refund.list, function (key, value) {
                //                 if (value.status_name == '未审核') {
                //                     len++;
                //                 }
                //             })
                //         } else if ($('.nav-wrapper .active').text() == '已通过') {
                //             $.each(data.info.list, function (key, value) {
                //                 if (value.status_name == '已审核') {
                //                     len++;
                //                 }
                //             })
                //             $.each(data.info_refund.list, function (key, value) {
                //                 if (value.status_name == '已审核') {
                //                     len++;
                //                 }
                //             })
                //         } else if ($('.nav-wrapper .active').text() == '已拒绝') {
                //             $.each(data.info.list, function (key, value) {
                //                 if (value.status_name == '不通过') {
                //                     len++;
                //                 }
                //             })
                //             $.each(data.info_refund.list, function (key, value) {
                //                 if (value.status_name == '不通过') {
                //                     len++;
                //                 }
                //             })
                //         }
                //         if (len < 5) {
                //             if (!$('.not-string').length > 0) {
                //                 str = '<div class="not-string" style="font-size:1rem;border:none;text-align:center"><span style="margin: 0 auto; padding-bottom: 20px;">暂无更多数据！</span></div>';
                //                 $('.infinite-scroll').append(str);  
                //             }
                //             $.detachInfiniteScroll($('.infinite-scroll'));
                //             $('.infinite-scroll-preloader').remove();
                //         }
                //         $.each(data.info.list, function (key, value) {
                //             var isclass = '';
                //             if (value.status_name == "未审核") {
                //                 isclass = "check_money_apply";
                //             }
                //             var oclass = '';
                //             if (value.status_name == "未审核") {
                //                 oclass = "unchecked";
                //             } else if (value.status_name == "不通过") {
                //                 oclass = "refuse";
                //             } else if (value.status_name == '已审核') {
                //                 oclass = "pass";
                //             }
                //             html += '<li class="' + oclass + '"><div class="apply"><dl class="apply-left"><dt>充值申请</dt><dd>申请人：' + value.dis_info.name + '</dd><dd>申请人等级：' + value.dis_info.levname + '</dd>' +
                //                 '<dd>充值金额：<span>￥' + value.apply_money + '</span></dd></dl><div class="apply-right"><button class="' + isclass + '" data-id="' + value.id + '">' + value.status_name +
                //                 '</button></div></div></li>';
                //         });
                //         $.each(data.info_refund.list, function (key, value) {
                //             var isclass = '';
                //             if (value.status_name == "未审核") {
                //                 isclass = "check_money_refund_apply";
                //             }
                //             var oclass = '';
                //             if (value.status_name == "未审核") {
                //                 oclass = "unchecked";
                //             } else if (value.status_name == "不通过") {
                //                 oclass = "refuse";
                //             } else if (value.status_name == '已审核') {
                //                 oclass = "pass";
                //             }
                //             html += '<li class="' + oclass + '"><div class="apply"><dl class="apply-left"><dt>提现申请</dt><dd>申请人：' + value.dis_info.name + '</dd><dd>申请人等级：' + value.dis_info.levname + '</dd>' +
                //                 '<dd>提现金额：<span>￥' + value.apply_money + '</span></dd></dl><div class="apply-right"><button class="' + isclass + '" data-id="' + value.id + '">' + value.status_name +
                //                 '</button></div></div></li>';
                //         });
                //     } else if (data.info.list == null && data.info_refund.list == null || data.info.list == undefined && data.info_refund.list == undefined || data.info.list == "" && data.info_refund.list == "") {
                //         if (!$('.not-string').length > 0) {
                //             str = '<div class="not-string" style="font-size:1rem;border:none;text-align:center"><span style="margin: 0 auto; padding-bottom: 20px;">暂无更多数据！</span></div>';
                //             $('.infinite-scroll').append(str);  
                //         }
                //         isNull = true;
                //         $.detachInfiniteScroll($('.infinite-scroll'));
                //         $('.infinite-scroll-preloader').remove();
                //     }
                // }
                $('#apply-list').append(html);
                page = page + 1;
                showHide();
            });
    }
    addItems();
    $.attachInfiniteScroll($('.infinite-scroll'));

    $(document).on('infinite', '.infinite-scroll', function () {

        // 如果正在加载，则退出
        if (loading) return;
        // 设置flag
        loading = true;
        setTimeout(function () {
            loading = false;
            addItems();
        }, 1000);
        showHide();
    });

    function showHide() {
        $('#apply-list li').hide();
        if ($('.nav-wrapper .active').text() == '待审核') {
            $('.unchecked').show();
        } else if ($('.nav-wrapper .active').text() == '已通过') {
            $('.pass').show();
        } else if ($('.nav-wrapper .active').text() == '已拒绝') {
            $('.refuse').show();
        }
    }

})