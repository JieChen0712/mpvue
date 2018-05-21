$(document).ready(function(){
  
  /*模拟数据 */
  Mock.mock( /\.json/,{
    'user': {
      'img': './img/head.jpg',
      'id|10000-99999': 1,  
      'name': '@cname'
    },
    'prizeInformation': {
      'QRCode': '@id',
      'orderNumber': '@id',
      'prize|5': {
        'id1': '飞机',
        'id2': '大炮',
        'id3': '火箭',
        'id4': '坦克',
        'id5': '激光炮'
      },
      'consignee': '@cname',
      'phoneNumber|18000000000-18099999999': 1,
      'date': '@now',
      'address': '@county'
    }
  });


  //获取后台数据
  $.ajax({
    url: 'hgw.json',
    type: 'post',
    dataType: 'json'
  }).done(function (data, status, xhr) {
    //解析后台数据
    var data = JSON.parse(JSON.stringify(data));
    
    //根据后台数据创建头部模块
    createHeader(data);
    
    //根据后台数据创建商品信息模块
    createPrizeInformation(data);
  });
  

  function createHeader(data){
    var oText = $('#header .text');
    var oImg = $('#header img');
    var oId = $('<div><span>ID</span>:'+ data.user.id +'</div>');
    var oName = $('<div><span>Name</span>:' + data.user.name + '</div>');
    
    oImg.attr('src', data.user.img)
    oId.addClass('id');
    oName.addClass('username');
    oId.appendTo(oText);
    oName.appendTo(oText);
  }
   
  function createPrizeInformation(data) {
    var oInformation = data.prizeInformation;
    var count = 0;
    for (var i in oInformation) {
      if (oInformation[i] instanceof Object) {
        var iLi = $('#main .prize-information li');
        var num = ((Math.ceil((Math.random() * 100))) % 5) + 1;
        var oStrong = $('<strong>' + oInformation[i]['id'+ num ] + '</strong>');
        oStrong.appendTo(iLi[count]);
      } else {
        var iLi = $('#main .prize-information li');
        var oStrong = $('<strong>' + oInformation[i] + '</strong>');
        oStrong.appendTo(iLi[count]);
      }
      count++;
    }
  }
})