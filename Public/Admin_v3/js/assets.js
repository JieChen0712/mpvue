$(function() {
  var moneyChart = echarts.init($('#moneyChart').get(0));
  var enable = $('.enable').find('p').text();
  var disable = $('.disable').find('p').text();
  var total = enable + disable;
//enable = enable - 0.1 * enable;
//disable = disable - 0.5 * disable;

  $(window).resize(function() {
    moneyChart.resize();
  })

  var option = {
    tooltip: {
      show: false
    },
    legend: {
      show: false
    },
    label: {
      normal: {
        show: false
      }
    },
    labelLine: {
      normal: {
        show: false
      }
    },
    series: [{
      name: '可提取金额',
      type: 'pie',
      radius: ['67%', '70%'],
      labelLine: {
        normal: {
          show: false
        }
      },
      data: [{
        value: disable,
        name: '',
        itemStyle: {
          normal: {
            color: '#f7f7f7'
          },
          emphasis: {
            color: '#f7f7f7'
          }
        },
        hoverAnimation: false
      }, {
        value: enable,
        name: '可提取金额',
        itemStyle: {
          normal: {
            color: '#71ebd1',
            borderColor: new echarts.graphic.LinearGradient(0, .2, 1, 1, [{
              offset: 0,
              color: '#71ebd1'
            },{
              offset: 1,
              color: '#0da9ef'
            }]),
            borderWidth: 5
          }
        },
        label: {
          normal: {
            show: false
          }
        },
        clockWise: false,
        hoverAnimation: false
      }]
    }, {
      name: '不可提取金额',
      type: 'pie',
      center: ['50%', '50%'],
      radius: ['47%', '50%'],
      labelLine: {
        normal: {
          show: false
        }
      },
      data: [{
        value: enable,
        name: '',
        itemStyle: {
          normal: {
            color: '#f7f7f7'
          },
          emphasis: {
            color: '#f7f7f7'
          }
        },
        hoverAnimation: false
      }, {
        value: disable,
        name: '不可提取金额',
        itemStyle: {
          normal: {
            color: '#ff806a',
            borderColor: new echarts.graphic.LinearGradient(0, .2, 1, 1, [{
              offset: 0,
              color: '#ff806a'
            }, {
              offset: 1,
              color: '#ff4371'
            }]),
            borderWidth: 5
          }
        },
        label: {
          normal: {
            show: false
          }
        },
        hoverAnimation: false
      }]
    }]
  };

  moneyChart.setOption(option);
})

//{
//    name: '金额',
//    type: 'pie',
//    radius: ['60%', '70%'],
//    data: [{
//      value: 30,
//      name: '占位',
//      itemStyle: {
//        normal: {
//          color: '#f7f7f7'
//        },
//        emphasis: {
//          color: '#f7f7f7'
//        }
//      },
//      hoverAnimation: false
//    },{
//      value: 70,
//      name: '可提取金额',
//      label:{
//        normal:{
//          show:false
//        }
//      }
//      hoverAnimation: false
//    }]
//  }

//
//color: {
//            type: 'linear',
//            x: 0,
//            y: 0,
//            x2: 0,
//            y2: 1,
//            colorStops: [{
//              offset: 0,
//              color: '#71ebd1' // 0% 处的颜色
//            }, {
//              offset: 1,
//              color: '#0da9ef' // 100% 处的颜色
//            }],
//            globalCoord: false // 缺省为 false
//          }