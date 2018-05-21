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
  $("#datetime-picker4").datetimePicker({
    toolbarTemplate: '<header class="bar bar-nav"><button class="button button-link pull-left close-picker">取消</button><button class="button button-link pull-right close-picker time-picker">确定</button><h1 class="title">选择要跳转的日期</h1></header>'
  });
  $("#datetime-picker4").bind("click", function() {
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
  $("#datetime-picker4").val(dateFormat("yyyy-MM", Date.parse(new Date())));

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
  getAwardData(4, getNowMonth());
});

//开启模块
var count = 1;
var first = true;
$.each(rebatekind, function(key, value) {
  if(key == "OPEN") {
    return;
  }
  if(value && first) {
    if (key == 'ORDER' || key == 'MONEY') {
        $('.award-menu ul').find('li').eq(count - 1).children('a').addClass('active');
    } else {
        $('.award-menu ul').find('li').eq(2).children('a').addClass('active');
    }
    first = !first;
  } else if(!value||value==0) {
    if (key == 'ORDER' || key == 'MONEY') {
        $('.award-menu ul').find('li').eq(count - 1).hide().children('a').removeClass('active');
    } else {
        if (key == 'ORDINARY_TEAM') {
            $('.award-menu ul').find('li').eq(3).hide().children('a').removeClass('active');
        }
    }
  }else if(value&&count==4&&team_rebatekind==null&&personal_rebatekind==null){
        $('.award-menu ul').find('li').eq(count - 1).hide().children('a').removeClass('active');
    }
  count++;
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
  var geturls = rebate;
    if(type==4){
          geturls = team_rebate;
    }
   
  $.post(geturls, {
    type: (type - 1),
    page: page_num,
    month: time
  }, function(data) {
    var page_list_num = data.result.page;
    if(data.code == 1) {
      if(data.result == null) { //为空时
        return;
      }
//      console.log(data);
      var temp1 = "";
      if($('.award-manager' + type).find('.award-num').html() == "") {
        var str1 = data.result.total_money == null ? "0.00" : data.result.total_money;
        var str2 = data.result.month_total_money == null ? "0.00" : data.result.month_total_money;
        var temp1 = '<div class="day-award"><p>当月奖励</p><p><span>￥</span>' + str2 + '</p></div><div class="month-award"><p>总奖励</p>' +
          '<p><span>￥</span>' + str1 + '</p></div>';
      }
      var temp2 = [];
      var count = 0;
      if(type != 4){
          $.each(data.result.list, function(key, value) {
            if(value != null && value.rec_id_info != undefined && value.rec_id_info != null && value.rec_id_info != "") {
              var html = '<dl class="award-list"><dt class="alist-title"><p>支付人：' + value.payer_id_info.name + '</p><p>+￥' + value.money + '</p></dt>' +
                '<dd class="alist-detail"><p>被推荐人：' + value.rec_id_info.name + '</p><p>手机号码：' + value.rec_id_info.phone + '</p><p>等级：' + value.rec_id_info.levname + '</p><p>状态：' + value.status_name + '</p>' +
                '<p>时间：' + value.time + '</p><p>返利类型：' + value.rebate_name + '</p></dd></dl>';
              temp2.push(html);
              count++;
            }
          });
      }else{
          $.each(data.result.list, function(key, value) {
            if(value != null) {
              var html = '<dl class="award-list"><dt class="alist-title"><p>支付人：总部</p><p>+￥' + value.rebate_money + '</p></dt>' +
                '<dd class="alist-detail"><p>业绩：￥' + value.total_money + '</p><p>比例：' + value.ratio + '</p><p>返利类型：' + value.type_name + '</p><p>返利金额：￥' + value.rebate_money + '</p>'+
                '<p>返利月份：' + value.month + '</p><p>返利时间：' + value.time + '</p><p>状态：' + value.status_name + '</p>';
              temp2.push(html);
              count++;
            }
          });
      }
    
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
      $('.award-manager' + type).find('.award-num').append(temp1).siblings('.list-wrapper').append(temp2);
      $('.award-manager' + type).find('.infinite-scroll-preloader').attr('data-load', 'false');
      $('.award-manager' + type).attr('data-page', page_num);
    } else {
      console.log(data.msg);
      $('.award-manager' + type).find('.infinite-scroll-preloader').attr('data-load', 'false');
    }
  })
}
