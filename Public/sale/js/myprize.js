$(document).ready(function(){
  
  /*模拟数据 */
  Mock.mock( /\.json/,{
    'user': {
      'img': './img/head.jpg',
      'id|10000-99999': 1,  
      'name': '@cname'
    },
    'prizeList': {
      'length|0-20': 1,
      'orderNumber': '@id()',
      'id|': '@range(1,100)',
      'url|': '@url',
      'list|5': {
        'id1': '飞机',
        'id2': '大炮',
        'id3': '火箭',
        'id4': '坦克',
        'id5': '激光炮'
      },
      'date': '@now'
    }
  });

  /*获取后台数据*/
  $.ajax({
    url: 'hgw.json',
    type: 'post',
    dataType: 'json'
  }).done(function (data, status, xhr) {
    //解析后台数据
    var data = JSON.parse(JSON.stringify(data));

    //根据后台数据创建头部元素
    createHeader(data);
    
    //根据后台数据创建商品列表元素
    createPrizeList(data);
  });
  
  //根据后台数据创建头部元素
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
   
  //根据后台数据创建商品列表元素
  function createPrizeList(data) {
    for(var i=0; i < data.prizeList.length; i++){
      var oUl = $('#main .prize-list');
      var iLi = $('<li></li>');
      var num = ((Math.ceil((Math.random()*100)))%5) + 1;
      var oSpan = $('<span>' + data.prizeList.id[i] + '.' + '</span>');
      var oStrong = $('<strong>'+ data.prizeList.list['id'+ num ] +'</strong>');
      var oEm = $('<em>' + data.prizeList.orderNumber + '</em>');
      var oDiv = $('<div>' + data.prizeList.date + '</div>');
      var oA = $('<a href=""></a>');

      oStrong.addClass('prize');
      oEm.addClass('price');
      oDiv.addClass('date');
      oSpan.appendTo(iLi);
      oStrong.appendTo(oA);
      oEm.appendTo(oA);
      oDiv.appendTo(oA);
      oA.attr('href',data.prizeList.url);
      oA.appendTo(iLi);
      iLi.appendTo(oUl);
    }
  }
})