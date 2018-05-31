<template>
  <div class="product-detail">
    <div class="header">
      <div class="swiper-wrapper">
        <swiper :imgUrls=product[0].many_image :indicatorDots=indicatorDots :interval=interval :duration=duration :swiperHeight=swiperHeight></swiper>
      </div>
      <div class="price-wrapper">
        <p class="price">￥{{product[0].price}}</p>
        <p class="original_price">价格￥{{product[0].original_price}}</p>
        <p class="name">{{product[0].name}}</p>
        <!--<p class="desc"></p>-->
      </div>
      <div class="other-detail">
        <div class="flex1">
          <p class="text">快递：免运费</p>
        </div>
        <div class="flex1">
          <p class="text">月销：{{product[0].sales}}笔</p>
        </div>
        <div class="flex1">
          <p class="text">广东广州</p>
        </div>
      </div>
    </div>
    <div class="product-wrapper">
      <h1 class="title">—— 商品详情 ——</h1>
      <div class="all_detail">
        <wxParse :content="product[0].disc"/>
      </div>
    </div>
    <div class="btn-wrapper">
      <button class="btn-buy" @click="showQrcode">立即咨询</button>
    </div>
    <div class="mask" @click="hideCode" v-show="showCode">
      <img :src="qrcode" class="qrcode" mode="widthFix"/>
    </div>
  </div>
</template>

<script type="text/ecmascript">
import store from '@/store/store'
import api from '@/utils/api'
import swiper from '@/components/swiper'
import wxParse from 'mpvue-wxparse'
export default {
  data () {
    return {
      indicatorDots: true,
      interval: 5000,
      duration: 1000,
      swiperHeight: '250px',
      productID: '',
      product: [{
        name: '遇（yu）粉底套装版',
        price: '179.00',
        disc: '<p>打造妙龄少女肌肤</p>',
        original_price: '279.00',
        sales: '1045',
        many_image: [
          '../../../static/banner.png'
        ]
      }],
      qrcode:'',
      showCode: false
    }
  },
  onLoad: function(options){
    this.productID = options.id
    this.qrcode = wx.getStorageSync('qrcode')
    if (options.id === undefined || options.id === null || options.id === '') {
      wx.showToast({
        title: '产品id有误',
        icon: 'none',
        duration: 2000
      })
    } else {
      api.getProductDetail(this.productID)
        .then(response => {
          if (response.list !== null) {
            this.product = response.list
            this.product[0].many_image = this.product[0].many_image
            this.product[0].disc = this.product[0].disc.replace(/\<img src\=\"/g,'<img src="' + this.$store.state.baseUrl)
            console.log(this.product[0].disc)
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
  created () {},
  components: {
    swiper,
    wxParse
  },
  methods: {
    showQrcode () {
      this.showCode = true
    },
    hideCode () {
      this.showCode = false
    }
  },
  store
}
</script>

<style lang="scss" scoped="" type="text/css">
@import url("~mpvue-wxparse/src/wxParse.css");
.product-detail{
  background-color: $thame-bgcolor-pink;
  overflow: hidden;
  .header{
    .swiper-wrapper{
      margin-bottom: 5px;
    }
    .price-wrapper{
      background-color:#fff;
      padding:10px 60px 10px 10px;
      .price{
        color: $thame-color;
        font-size: 30px;
      }
      .original_price{
        font-size:12px;
        text-decoration:line-through;
        margin-bottom:12px;
      }
      .name{
        font-size: 17px;
        color: $thame-color-brown;
      }
      .desc{
        font-size: 14px;
        color: $thame-color-brown;
      }
    }
  }
  .other-detail{
    display:flex;
    background-color:#fff;
    margin-bottom:5px;
    font-size:12px;
    padding:0 10px 20px 10px;
    color: $thame-color-grey;
    .flex1{
      flex: 1;
      text-align:center;
      &:last-child{
        text-align: right;
      }
      &:first-child{
        text-align: left;
      }
    }
  }
  .product-wrapper{
    .title{
      text-align: center;
      background-color: #fff;
    }
    .all_detail{
      padding: 10px;
      margin-bottom: 60px;
    }
  }
  .btn-wrapper{
    position: fixed;
    left: 0;
    right: 0;
    bottom: 10px;
    text-align: center;
    z-index: 8;
    .btn-buy{
      height: 45px;
      width: 90%;
      line-height: 45px;
      color: #fff;
      margin: 0 auto;
      font-size: 30px;
      border-radius: 500px;
      background-color: $thame-bgcolor-darkpink2;
      box-sizing: border-box;
    }
  }
  .mask{
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    z-index: 10;
    &.active{
      display: block;
    }
    .qrcode{
      position: absolute;
      width: 60%;
      top: 50%;
      left: 50%;
      transform: translate3d(-50%,-50%,0);
    }
  }
}
</style>
