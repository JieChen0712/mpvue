$().ready(function() {

  subDate(this);
  $("#datetime-picker1").datetimePicker({
    toolbarTemplate: '<header class="bar bar-nav"><button class="button button-link pull-left close-picker">取消</button><button class="button button-link pull-right close-picker time-picker">确定</button><h1 class="title">选择要跳转的日期</h1></header>'
  });
  $("#datetime-picker1").bind("click", function() {
    $(".picker-items-col-divider").prev().hide()
    $(".picker-items-col-divider").hide().next().hide().next().hide();
    $(".picker-modal").css({
      background: "white"
    });
    $(".picker-items-col").css({
      margin: "0 2rem"
    });
    subDate(this);
    $(this).change(function() {
      console.log(1)
      subDate(this);
    });
  });
  $("#datetime-picker2").datetimePicker({
    toolbarTemplate: '<header class="bar bar-nav"><button class="button button-link pull-left close-picker">取消</button><button class="button button-link pull-right close-picker time-picker">确定</button><h1 class="title">选择要跳转的日期</h1></header>'
  });
  $("#datetime-picker2").bind("click", function() {
    $(".picker-items-col-divider").prev().hide()
    $(".picker-items-col-divider").hide().next().hide().next().hide();
    $(".picker-modal").css({
      background: "white"
    });
    $(".picker-items-col").css({
      margin: "0 2rem"
    });
    subDate(this);
    $(this).change(function() {
      subDate(this);
    });
  });
  $("#datetime-picker3").datetimePicker({
    toolbarTemplate: '<header class="bar bar-nav"><button class="button button-link pull-left close-picker">取消</button><button class="button button-link pull-right close-picker time-picker">确定</button><h1 class="title">选择要跳转的日期</h1></header>'
  });
  $("#datetime-picker3").bind("click", function() {
    $(".picker-items-col-divider").prev().hide()
    $(".picker-items-col-divider").hide().next().hide().next().hide();
    $(".picker-modal").css({
      background: "white"
    });
    $(".picker-items-col").css({
      margin: "0 2rem"
    });
    subDate(this);
    $(this).change(function() {
      subDate(this);
    });
  });

  //初始化时间
  $("#datetime-picker1").val(dateFormat("yyyy-MM", Date.parse(new Date())));
  $("#datetime-picker2").val(dateFormat("yyyy-MM", Date.parse(new Date())));
  $("#datetime-picker3").val(dateFormat("yyyy-MM", Date.parse(new Date())));

  //监听日期选择
  $(document).on('click', '.time-picker', function() {
    var str = $(".award-menu").find('a.active').eq(0).attr("href");
    $('.award-manager' + str).find(".award-num").empty();
    $('.award-manager' + str).find(".list-wrapper").empty();
    if($('.award-manager' + str + " .award-show").find(".infinite-scroll-preloader").length < 1) {
      $('.award-manager' + str + " .award-show").append('<div class="infinite-scroll-preloader" data-load="false"><div class="preloader"></div></div>');
    }
    console.log($("#datetime-picker" + str).val())
    $('.award-manager' + str).attr('data-page','1');
    getAwardData(str, $("#datetime-picker" + str).val().replace(/-/g, '').trim());
  })
  //Tab切换监听
  $(".award-menu a").bind("click", function() {
    var str = $(this).attr("href");
    $('a[href=' + str + ']').addClass("active").parent().siblings().children("a").removeClass("active");
    $(".content section").hide();

    var content = '.award-manager' + str;
    $(content).fadeIn();
    if($(content).data('empty') == 'false') {
      $.attachInfiniteScroll($('.infinite-scroll'));
    } else {
      $.detachInfiniteScroll($('.infinite-scroll'));
      $(content).find('.infinite-scroll-preloader').remove();
    }
    return false;
  });

  //获取奖励的数据

  //滚动加载
  $(document).on('infinite', '.infinite-scroll', function() {
    var str = $(".award-menu").find('a.active').eq(0).attr("href");

    if($('.award-manager' + str).find('.infinite-scroll-preloader').data('load') == "true") {
      return;
    }
    $('.award-manager' + str).find('.infinite-scroll-preloader').attr('data-load', 'true');
//    setTimeout(function() {
      getAwardData(str, getNowMonth());
//    }, 1000)
  });

  getAwardData(1, getNowMonth());
  getAwardData(2, getNowMonth());
  getAwardData(3, getNowMonth());
});


if($(".award-menu").find('a.active').length != 0) {
  var str = $(".award-menu").find('a.active').eq(0).attr("href");
  $(".content section").hide();
  var content = '.award-manager' + str;
  $(content).fadeIn();
}

function subDate(aim) {
  var str = $(aim).val();
  $(aim).val(str.substring(0, 7));
}
//获取当前年月时间
function getNowMonth() {
  var time = new Date();
  return time.getFullYear().toString() + ((time.getMonth() + 1) > 9 ? "" : "0") + (time.getMonth() + 1).toString();
}

//转换日期格式
function dateFormat(format, dates) {
  _this = new Date(dates);
  var o = {
    "M+": _this.getMonth() + 1, //月份
    "d+": _this.getDate(), //日
    "H+": _this.getHours(), //小时
    "m+": _this.getMinutes(), //分
    "s+": _this.getSeconds(), //秒
    "q+": Math.floor((_this.getMonth() + 3) / 3), //季度
    "f+": _this.getMilliseconds() //毫秒
  };
  if(/(y+)/.test(format))
    format = format.replace(RegExp.$1, (_this.getFullYear() + "").substr(4 - RegExp.$1.length));
  for(var k in o)
    if(new RegExp("(" + k + ")").test(format))
      format = format.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k])
        .substr(("" + o[k]).length)));
  return format;
};

//获取奖励的数据
function getAwardData(type, time) {
  var page_num = $('.award-manager' + type).data('page');
   
  $.post(geturls, {
    type: typeList[type],
    page: page_num,
    month: time,
    pid:getUrlParam('pid')
  }, function(data) {
    var page_list_num = 10;
    if(data.list != null) {
      if(data.list == null) { //为空时
        return;
      }
//      console.log(data);
      var temp1 = "";
      var mark = "+";
      if($('.award-manager' + type).find('.award-num').html() == "") {
        var str1 = data.total_num == null ? "0" : data.total_num;
        var str2 = data.total_month_num == null ? "0" : data.total_month_num;
        if(typeList[type]=="recharge"){
            temp1 = '<div class="day-award"><p>当月充入</p><p>' + str2 + '</p></div><div class="month-award"><p>总充入</p>' +
            '<p>' + str1 + '</p></div>';
        }else if(typeList[type]=="audit_charge"){
            temp1 = '<div class="day-award"><p>当月审核扣除</p><p>' + str2 + '</p></div><div class="month-award"><p>总审核扣除</p>' +
            '<p>' + str1 + '</p></div>';
            mark = "-";
        }else if(typeList[type]=="charge"){
            temp1 = '<div class="day-award"><p>当月提货扣除</p><p>' + str2 + '</p></div><div class="month-award"><p>总提货扣除</p>' +
            '<p>' + str1 + '</p></div>';
            mark = "-";
        }
      }
      console.log(temp1);
      var temp2 = [];
      var count = 0;
//    if(type != 4){
          $.each(data.list, function(key, value) {
            if(value != null && value.dis_info != undefined && value.dis_info != null && value.dis_info != "") {
              var html = '<dl class="award-list"><dt class="alist-title"><p>订单号：' + value.order_num + '</p><p>'+mark + value.point + '</p></dt>' +
                '<dd class="alist-detail"><p>类型：' + value.type_name + '</p><p>代理名称：' + value.dis_info.name + '</p><p>代理等级：' + value.dis_info.levname + '</p>' +
                '<p>代理电话：' + value.dis_info.phone + '</p><p>云仓产品：' + value.temp_info.name + '</p><p>时间：' + value.created_format + '</p></dd></dl>';
              temp2.push(html);
              count++;
            }
          });
//    }else{
//        $.each(data.list, function(key, value) {
//          if(value != null) {
//            var html = '<dl class="award-list"><dt class="alist-title"><p>支付人：总部</p><p>+￥' + value.rebate_money + '</p></dt>' +
//              '<dd class="alist-detail"><p>业绩：￥' + value.total_money + '</p><p>比例：' + value.ratio + '</p><p>返利类型：' + value.type_name + '</p><p>返利金额：￥' + value.rebate_money + '</p>'+
//              '<p>返利月份：' + value.month + '</p><p>返利时间：' + value.time + '</p><p>状态：' + value.status_name + '</p>';
//            temp2.push(html);
//            count++;
//          }
//        });
//    }
    
      if(count < page_list_num) { //不满一页时
        $('.award-manager' + type).find('.infinite-scroll-preloader').remove();
        $.detachInfiniteScroll($('.infinite-scroll'));
        $('.award-manager' + type).attr('data-empty', "true");
      }
      //    }else if(type == 2){
      //      $.each(data.result, function(key,value) {
      //        if(value.rec_id_info!=undefined&&value.rec_id_info!=null&&value.rec_id_info!=""){
      //        var html = '<dl class="award-list"><dt class="alist-title"><p>推荐人：{$manager.name}</p><p>+￥{$vo.money}</p></dt><dd class="alist-detail">'+
      //                  '<p>被推荐人：{$vo.result.name}</p><p>手机号码：{$vo.result.phone}</p><p>等级：{$vo.result.levname}</p><p>状态：<eq name='vo.status' value='0'>未结算<else/>已结算</eq>'+
      //                  '</p><p>日期：{$vo.time|date='Y-m-d H:i:s',###}</p></dd></dl>';
      //                  temp2.push(html);
      //        }
      //      });
      //    }else if(type == 3){
      //      $.each(data.result, function(key,value) {
      //        var html = '<dl class="award-list"><dt class="alist-title"><p>充值奖励</p><p>+￥{$m.money}</p></dt><dd class="alist-detail">'+
      //                   '<p>充值者：{$m.distributor_name}</p><p>充值者手机号码：{$m.phone}</p><p>充值金额：<span>￥</span>{$m.apply_money}</p>'+
      //                   '<p>日期：{$m.time|date="Y-m-d H:i:s",###}</p></dd></dl>';
      //      });
      //      temp2.push(html);
      //    }
      //    
      page_num++;
      console.log('.award-manager' + type);
      $('.award-manager' + type).find('.award-num').append(temp1).siblings('.list-wrapper').append(temp2);
      $('.award-manager' + type).find('.infinite-scroll-preloader').attr('data-load', 'false');
      $('.award-manager' + type).attr('data-page', page_num);
    } else {
      console.log(1)
      $('.award-manager' + type).find('.infinite-scroll-preloader').attr('data-load', 'false');
    }
  })
}


//取参
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg); //匹配目标参数
    if(r != null) return unescape(r[2]);
    return null; //返回参数值
}