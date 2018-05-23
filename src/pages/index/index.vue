<template>
  <div class="mall">
    <div class="swiper-wrapper">
      <swiper :imgUrls=imgUrls :indicatorDots=indicatorDots :interval=interval :duration=duration :swiperHeight=swiperHeight ></swiper>
    </div>
    <div class="entry">
      <ul class="entry-list">
      	<li class="list-item"><img src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg" alt="" /><p>产品展示</p></li>
      	<li class="list-item"><img src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg" alt="" /><p>产品展示</p></li>
      	<li class="list-item"><img src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg" alt="" /><p>产品展示</p></li>
      </ul>
    </div>
    <div class="seckill">
      <p class="title">-限时秒杀-</p>
      <scroll-view class="product-list" scroll-x="true" style="100%">
          <view class="product-item">
            <div class="product-img">
              <img mode="widthFix" src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg"/>
              <span class="sec-title">剩余：0件</span>
            </div>
            <div class="product-detail">
              <p class="name"><span class="hot">HOT</span>遇yu粉底套装</p>
              <p class="time">倒计时：00：00：00</p>
              <progress percent="80" active activeColor="#fe0100" backgroundColor="transparent" class="progress"/>
              <p class="price">秒杀价：<span class="red">￥19.9</span><span class="del">179.00</span></p>
            </div>
          </view>
          <view class="product-item">
            <div class="product-img">
              <img mode="widthFix" src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg"/>
              <span class="sec-title">剩余：0件</span>
            </div>
            <div class="product-detail">
              <p class="name"><span class="hot">HOT</span>遇yu粉底套装</p>
              <p class="time">倒计时：00：00：00</p>
              <progress percent="80" active activeColor="#fe0100" backgroundColor="transparent" class="progress"/>
              <p class="price">秒杀价：<span class="red">￥19.9</span><span class="del">179.00</span></p>
            </div>
          </view>
          <view class="product-item">
            <div class="product-img">
              <img mode="widthFix" src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg"/>
              <span class="sec-title">剩余：0件</span>
            </div>
            <div class="product-detail">
              <p class="name"><span class="hot">HOT</span>遇yu粉底套装</p>
              <p class="time">倒计时：00：00：00</p>
              <progress percent="80" active activeColor="#fe0100" backgroundColor="transparent" class="progress"/>
              <p class="price">秒杀价：<span class="red">￥19.9</span><span class="del">179.00</span></p>
            </div>
          </view>
          <view class="product-item">
            <div class="product-img">
              <img mode="widthFix" src="http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg"/>
              <span class="sec-title">剩余：0件</span>
            </div>
            <div class="product-detail">
              <p class="name"><span class="hot">HOT</span>遇yu粉底套装</p>
              <p class="time">倒计时：00：00：00</p>
              <progress percent="80" active activeColor="#fe0100" backgroundColor="transparent" class="progress"/>
              <p class="price">秒杀价：<span class="red">￥19.9</span><span class="del">179.00</span></p>
            </div>
          </view>
      </scroll-view>
    </div>
    <div class="team-buy">
      <p class="title">-热门团购-</p>
      <div class="team-product">
        <img mode="widthFix" src="../../../static/product.png"/>
        <div class="text-left">
          <p class="title2">遇（yu）粉底套装版</p>
          <p class="desc">打造妙龄少女肌粉饰你的美</p>
          <p class="price"><span class="hot">拼团价</span>￥179.00</p>
          <p class="red">立即拼团</p>
        </div>
        <div class="text-right">
          <p>1,504<span class="desc">件已售</span></p>
        </div>
      </div>
      <p class="more">查看更多></p>
    </div>
  </div>
</template>

<script>
  import swiper from '@/components/swiper'
  export default {
    data () {
      return {
        imgUrls: [
          'http://img02.tooopen.com/images/20150928/tooopen_sy_143912755726.jpg',
          'http://img06.tooopen.com/images/20160818/tooopen_sy_175866434296.jpg',
          'http://img06.tooopen.com/images/20160818/tooopen_sy_175833047715.jpg'
        ],
        indicatorDots: true,
        interval: 5000,
        duration: 1000,
        swiperHeight: '200px'
      }
    },
    components: {
      swiper
    },
    methods: {},
    onPullDownRefresh () {
      wx.showToast({
        title: '下拉',
        icon: 'none',
        duration: 600
      })
      // doing some thing
      // 下拉刷新执行完毕要停止当前页面下拉刷新
      setTimeout(function () {
        wx.stopPullDownRefresh()
      }, 1000)
    },
    onReachBottom () {
      wx.showToast({
        title: 'onReachBottom',
        icon: 'none',
        duration: 600
      })
    }
  }
</script>

<style scoped lang="scss" type="text/css">
.mall{
  .entry{
    .entry-list{
      width: 100%;
      .list-item{
        display: inline-block;
        width: 33%;
        padding-top:16px;
        padding-bottom:12px;
        text-align: center;
        img{
          width: 40px;
          height: 40px;
          border-radius: 50%;
          overflow: hidden;
        }
        p{
          font-size: 12px;
          line-height: 16px;
          color: $thame-color-grey;
        }
      }
    }
  }
  .seckill{
    padding-top: 12px;
    background-color: $thame-bgcolor-pink;
    .title{
      color: $thame-color-orange;
      font-size: 15px;
      line-height: 20px;
      background-color: #ffffff;
      text-align: center;
    }
    .product-list{
      white-space: nowrap;
      display: flex;
      width: 100%;
      .product-item{
        display: inline-block;
        width: 50%;
        padding: 12px 10px;
        box-sizing: border-box;
        .product-img{
          box-shadow:0 0 5px;
          display:block;
          position:relative;
          border-radius:5px;
          overflow:hidden;
          img{
            width: 100%;
          }
          .sec-title{
            background-color: $thame-color;
            color: #fff;
            position:absolute;
            bottom:0;
            width:100%;
            left:0;
            z-index:10;
            text-align:center;
            font-size:12px;
            border-radius:0 0 5px 5px;
          }
        }
        .product-detail{
          margin-top: 5px;
          .name{
            font-size:16px;
            word-wrap:break-word;
            word-break:break-all;
            white-space:pre-line;
            .hot{
              background-color:$thame-color;
              color:#fff;
              border-radius:3px;
              font-weight:normal;
              font-size:12px;
              padding:3px 3px;
              margin-right:5px;
            }
          }
          .time{
            text-align:center;
            font-size:14px;
            color:#ff3836;
          }
          .progress{
            border-radius:50px;
            border:solid 1rpx $red-border;
            margin-bottom: 5px;
          }
          .price{
            font-size:16px;
            word-wrap:break-word;
            word-break:break-all;
            white-space:pre-line;
            .red{
              color: $thame-color;
              font-size: 18px;
              vertical-align: sub;
            }
            .del{
              color: $thame-color-grey;
              margin-left: 5px;
              text-decoration:line-through;
              display:inline;
              font-size:16px;
              vertical-align:sub;
            }
          }
        }
      }
    }
  }
  .team-buy{
    .title{
      color: $thame-color-orange;
      font-size: 15px;
      line-height: 20px;
      background-color: #ffffff;
      text-align: center;
    }
    .team-product{
      position: relative;
      img{
        width: 100%;
      }
      .text-left{
        position:absolute;
        top:0;
        left:0;
        width:60%;
        overflow:hidden;
        padding:2% 5%;
        .title2{
          color: #fff;
          font-size: 20px;
        }
        .desc{
          color:#fff;
          font-size:13px;
          line-height:24px;
        }
        .price{
          color:$thame-color;
          font-size:16px;
          .hot{
            background-color:$thame-color;
            color:#fff;
            border-radius:3px;
            padding:0 3px;
            font-size:14px;
          }
        }
        .red{
          color:$thame-color-pink;
          font-size:12px;
        }
      }
      .text-right{
        position: absolute;
        right: 0;
        top: 0;
        font-size: 13px;
        width: 30%;
        padding:2% 3%;
        overflow: hidden;
        color: $thame-color;
        text-align: right;
        p{
          display: inline-block;
          background-color: white;
          border-radius:3px;
          padding:0 3px;
          .desc{
            font-size: 10px;
          }
        }
      }
    }
    .more{
      text-align: center;
      color:$thame-color-pink;
      font-size: 14px;
      margin-bottom: 5px;
    }
  }
}
</style>