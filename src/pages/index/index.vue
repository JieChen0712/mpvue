<template>
  <div class="mall">
    <div class="swiper-wrapper">
      <swiper :imgUrls=imgUrls :indicatorDots=indicatorDots :interval=interval :duration=duration :swiperHeight=swiperHeight></swiper>
    </div>
    <div class="entry">
      <ul class="entry-list">
        <li class="list-item"><img src="../../../static/product_icon.png" alt="" />
          <p>产品展示</p>
        </li>
        <li class="list-item"><img src="../../../static/sale_icon.png" alt="" />
          <p>促销</p>
        </li>
        <li class="list-item">
          <a @click="checkLogin"><img src="../../../static/coupon_icon.png" alt="" />
            <p>优惠券</p>
          </a>
        </li>
      </ul>
    </div>
    <div class="seckill">
      <p class="title">-限时秒杀-</p>
      <scroll-view class="product-list" scroll-x="true" style="100%">
        <view class="product-item" v-for="(item, index) in secTemplet">
          <div class="product-img">
            <img mode="widthFix" :src="'https://mall.wsxitong.cn'+item.image" />
            <span class="sec-title">剩余：{{item.quantity}}件</span>
          </div>
          <div class="product-detail">
            <p class="name" @click="showTarget"><span class="hot">HOT</span>{{item.name}}</p>
            <p class="time">倒计时：00：00：00</p>
            <progress percent="80" active activeColor="#fe0100" backgroundColor="transparent" class="progress" />
            <p class="price">秒杀价：<span class="red">￥{{item.price}}</span><span class="del">￥{{item.original_price}}</span></p>
          </div>
        </view>
      </scroll-view>
    </div>
    <div class="team-buy">
      <p class="title">-热门团购-</p>
      <div class="team-product">
        <img mode="widthFix" src="../../../static/product1.png" />
        <div class="text-left">
          <p class="title2">遇（yu）粉底套装版</p>
          <p class="desc">打造妙龄少女肌粉饰你的美</p>
          <p class="price"><span class="hot">拼团价</span>￥179.00</p>
          <p class="red">立即拼团</p>
        </div>
        <div class="text-right">
          <p>1,504<span class="desc">件已售</span></p>
        </div>
        <p class="more">查看更多></p>
      </div>
    </div>
  </div>
</template>

<script>
  // import store from '@/store/store'
  // import { mapMutations } from 'vuex'
  import api from '@/utils/api'
  import swiper from '@/components/swiper'
  import countDown from '@/components/countDown'
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
        swiperHeight: '200px',
        secPage: 1,
        couponPage: 1,
        secTemplet: [],
        couponTemplet: []
      }
    },
    components: {
      swiper,
      countDown
    },
    created () {
      api.getStore()
        .then(response => {
          if (response.code === 1) {
            wx.setTopBarText({text: response.info.name})
          } else {
            wx.showToast({
              title: response.msg,
              icon: 'none',
              duration: 2000
            })
          }
        })
        .catch(error => {
          console.log(error)
        })
    },
    mounted () {
      this.getSecTemplets()
      // let res = api.getStore()
      // console.log(res)
    },
    methods: {
      // ...mapMutations([
      // 'nickName',
      // 'avatar',
      // 'openID'
      // ]),
      getSecTemplets () {
        api.getTemplet(1, this.secPage, 1)
          .then(response => {
            if (response.code === 1) {
              this.secPage++
              this.secTemplet = response.info.list
              console.log(this.secTemplet)
            } else {
              wx.showToast({
                title: response.msg,
                icon: 'none',
                duration: 2000
              })
            }
          })
          .catch(error => {
            console.log(error)
          })
      },
      getCouTemplets () {
        let _templet = api.getTemplet(2, this.couponPage)
        if (_templet.code === 1) {
          this.couponPage++
          this.couponTemplet = _templet.list
        }
      },
      checkLogin () {
        wx.checkSession({
          success: () => {
            let openid = wx.getStorageSync('openid')
            if (!openid) {
              this.$options.methods.Login()
            }
          },
          fail: () => {
            this.$options.methods.Login()
          }
        })
      },
      Login () {
        // 调用登录接口
        wx.login({
          success: (res) => {
            let resCode = res.code
            wx.setStorageSync('resCode', resCode)
            wx.getUserInfo({
              success: (res) => {
                console.log(res)
                api.checkLogin(resCode, res.userInfo.nickName, res.userInfo.avatarUrl, res.userInfo.province, res.userInfo.city, res.userInfo.country)
                  .then(response => {
                    console.log(response)
                    if (response.code === 1) {
                      wx.setStorageSync('openid', response.info.openid)
                      wx.showToast({
                        title: '登录成功',
                        icon: 'success',
                        duration: 2000,
                        complete: () => {
                          wx.navigateTo({
                            url: '/pages/coupon/main'
                          })
                        }
                      })
                    } else {
                      wx.showToast({
                        title: '登录失败',
                        icon: 'none',
                        duration: 2000
                      })
                      return false
                    }
                  })
                  .catch(error => {
                    console.log(error)
                  })
                // this.nickName(res.userInfo.nickName)
                // this.avatar(res.userInfo.avatarUrl)
                // this.nickName(res.userInfo.nickName)
                // console.log(this.$store.state.nickName)
              },
              fail: () => {}
            })
          },
          fail: () => {}
        })
      }
    },
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
        title: 'bottom',
        icon: 'none',
        duration: 600
      })
    }
  }
</script>

<style scoped lang="scss" type="text/css">
  .mall {
    .entry {
      .entry-list {
        width: 100%;
        .list-item {
          display: inline-block;
          width: 33%;
          padding-top: 16px;
          padding-bottom: 12px;
          text-align: center;
          img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
          }
          p {
            font-size: 12px;
            line-height: 16px;
            color: $thame-color-grey;
          }
        }
      }
    }
    .seckill {
      padding-top: 12px;
      background-color: $thame-bgcolor-darkpink;
      .title {
        color: $thame-color-orange;
        font-size: 15px;
        line-height: 20px;
        background-color: #ffffff;
        text-align: center;
      }
      .product-list {
        white-space: nowrap;
        display: flex;
        width: 100%;
        .product-item {
          display: inline-block;
          width: 50%;
          padding: 12px 10px;
          box-sizing: border-box;
          .product-img {
            box-shadow: 0 0 5px;
            display: block;
            position: relative;
            border-radius: 5px;
            overflow: hidden;
            img {
              width: 100%;
            }
            .sec-title {
              background-color: $thame-color;
              color: #fff;
              position: absolute;
              bottom: 0;
              width: 100%;
              left: 0;
              z-index: 10;
              text-align: center;
              font-size: 12px;
              border-radius: 0 0 5px 5px;
            }
          }
          .product-detail {
            margin-top: 5px;
            .name {
              font-size: 16px;
              word-wrap: break-word;
              word-break: break-all;
              white-space: pre-line;
              .hot {
                background-color: $thame-color;
                color: #fff;
                border-radius: 3px;
                font-weight: normal;
                font-size: 12px;
                padding: 3px 3px;
                margin-right: 5px;
              }
            }
            .time {
              text-align: center;
              font-size: 14px;
              color: $thame-color;
            }
            .progress {
              border-radius: 50px;
              border: solid 1rpx $red-border;
              margin-bottom: 5px;
            }
            .price {
              font-size: 16px;
              word-wrap: break-word;
              word-break: break-all;
              white-space: pre-line;
              .red {
                color: $thame-color;
                font-size: 18px;
                vertical-align: sub;
              }
              .del {
                color: $thame-color-grey;
                margin-left: 5px;
                text-decoration: line-through;
                display: inline-block;
                font-size: 16px;
                vertical-align: sub;
              }
            }
          }
        }
      }
    }
    .team-buy {
      .title {
        color: $thame-color-orange;
        font-size: 15px;
        line-height: 20px;
        background-color: #ffffff;
        text-align: center;
      }
      .team-product {
        position: relative;
        img {
          width: 100%;
        }
        .text-left {
          position: absolute;
          top: 0;
          left: 0;
          width: 60%;
          overflow: hidden;
          padding: 2% 5%;
          .title2 {
            color: #fff;
            font-size: 20px;
          }
          .desc {
            color: #fff;
            font-size: 13px;
            line-height: 24px;
          }
          .price {
            color: $thame-color;
            font-size: 16px;
            .hot {
              background-color: $thame-color;
              color: #fff;
              border-radius: 3px;
              padding: 0 3px;
              font-size: 14px;
            }
          }
          .red {
            color: $thame-color-pink;
            font-size: 12px;
          }
        }
        .text-right {
          position: absolute;
          right: 0;
          top: 0;
          font-size: 13px;
          width: 30%;
          padding: 2% 3%;
          overflow: hidden;
          color: $thame-color;
          text-align: right;
          p {
            display: inline-block;
            background-color: white;
            border-radius: 3px;
            padding: 0 3px;
            .desc {
              font-size: 10px;
            }
          }
        }
        .more {
          text-align: center;
          color: $thame-color-pink;
          font-size: 28rpx;
          margin-bottom: 10rpx;
          position: absolute;
          bottom: 0;
          transform: translateX(-50%);
          left: 50%;
        }
      }
    }
  }
</style>