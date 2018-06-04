<template>
  <div class="coupon">
    <div class="adv-img">
      <img mode="widthFix" src="../../../static/product1.png" alt="" />
    </div>
    <div class="coupon-list" v-show="couponList.length>0" v-for="(item, index) in couponList" :key="index">
      <div class="coupon-item">
        <div class="img-wrapper">
        	<img @click="receiveCoupon(index, item.id)" mode="widthFix" :src="'https://mall.wsxitong.cn'+item.img" alt="" />
        	<div class="mask" v-if="item.is_get">
            <p v-show="item.is_get">已领取</p>
          </div>
        </div>
        <!--<p class="count">剩余：860张</p>-->
      </div>
    </div>
  </div>
</template>

<script type="text/ecmascript">
import api from '@/utils/api'
export default {
  data () {
    return {
      couponList: [],
      page: 1
    }
  },
  onShow () {
    this.getCoupon()
  },
  methods: {
    getCoupon () {
      let openid = wx.getStorageSync('openid')
      if (openid) {
        api.getCoupon(openid, this.page)
          .then(response => {
            if (response.code === 1 && response.list !== null) {
              this.page++
              this.couponList = response.list
            } else {
              wx.showToast({
                title: '暂无更多优惠券',
                icon: 'none',
                duration: 2000
              })
            }
          })
          .catch(error => {
            wx.showToast({
              title: error,
              icon: 'none',
              duration: 2000
            })
          })
      } else {
        wx.navigateTo({
          url: '/pages/index/main'
        })
      }
    },
    receiveCoupon (index, id) {
      let openid = wx.getStorageSync('openid')
      api.recCoupon(openid, id)
        .then(response => {
          if (response.code === 1) {
            this.couponList[index].is_get = true
            console.log(this.couponList)
            wx.showToast({
              title: response.msg,
              icon: 'success',
              duration: 2000
            })
          } else {
            wx.showToast({
              title: response.msg,
              icon: 'none',
              duration: 2000
            })
          }
        })
        .catch(error => {
          wx.showToast({
            title: error,
            icon: 'none',
            duration: 2000
          })
        })
    }
  },
  onShareAppMessage: function (res) {}
}
</script>

<style lang="scss" scoped="" type="text/css">
.coupon{
  background-color:$thame-bgcolor-pink;
  height: 100%;
  overflow: hidden;
  .adv-img{
    margin-top: 5px;
    img{
      width: 100%;
      max-height: 225px;
      box-shadow: 0 0 10px;
    }
  }
  .coupon-list{
    .coupon-item{
      text-align: center;
      padding-bottom:25px;
      .img-wrapper{
        position:relative;
        margin: 12px 10px;
        img{
          box-sizing:border-box;
          width: 100%;
        }
        .mask{
          position: absolute;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: rgba(0,0,0,.6);
          width: 100%;
          height: 100%;
          top: 0;
          right: 0;
          left: 0;
          bottom: 0;
          transition: all ease $time;
          z-index: 10;
          p{
            font-size: 25px;
            color: #FFFFFF;
          }
        }
      }
      .count{
        font-size:12px;
        color:$thame-color-darkpink
      }
    }
  }
}
</style>
