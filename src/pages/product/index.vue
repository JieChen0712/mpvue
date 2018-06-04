<template>
  <div class="product">
    <div class="product-list">
      <p v-show="product.length<=0" class="empty">暂无商品</p>
      <div class="product-item" v-for="(item, index) in product" v-show="product.length>0" :key="index">
        <a :href="'/pages/detail/main?id='+item.id">
          <img mode="widthFix" :src="'https://mall.wsxitong.cn'+item.image" />
          <div class="text-top">
            <p class="title">{{item.name}}</p>
            <!--<p class="desc">{{item.disc}}</p>-->
            <p class="talk">立即资讯</p>
          </div>
          <p class="more">查看更多></p>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '@/utils/api'
  export default {
    data () {
      return {
        product: [],
        page: 1
      }
    },
    created () {
      this.getTemplet()
    },
    methods: {
      getTemplet () {
        api.getTemplet(0, this.page, 1)
          .then(response => {
            if (response.code === 1 && response.info.list !== null) {
              this.page++
              this.product = response.info.list
            } else {
              wx.showToast({
                title: '暂无更多商品',
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
    onReachBottom () {
      this.getTemplet()
    },
    onShareAppMessage: function (res) {}
  }
</script>

<style lang="scss" scoped="" type="text/css">
  .product {
    background-color: $thame-bgcolor-pink;
    overflow:hidden;
    .product-list {
      .empty{
        text-align: center;
      }
      .product-item {
        position: relative;
        margin: 5px 0;
        img {
          width: 100%;
          box-shadow: 0 0 10px;
        }
        .text-top {
          position: absolute;
          top: 0;
          width: 100%;
          text-align: center;
          .title {
            color: $thame-color-brown;
            font-size: 20px;
          }
          .desc {
            color: $thame-color-brown;
            font-size: 13px;
            line-height: 24px;
          }
          .talk {
            display: inline-block;
            color: #fff;
            font-size: 13px;
            background-color: $thame-color-brown;
            border-radius: 10px;
            padding: 5px 10px;
          }
        }
        .more {
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