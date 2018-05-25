<template>
  <div class="product">
    <div class="product-list">
      <div class="product-item">
        <img mode="widthFix" src="../../../static/product1.png"/>
        <div class="text-top">
          <p class="title">遇（yu）粉底套装版</p>
          <p class="desc">打造妙龄少女肌粉饰你的美</p>
          <p class="talk">立即资讯</p>
        </div>
        <p class="more">查看更多></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},
  data () {
    return {}
  },
  created () {}
}
</script>

<style lang="scss" scoped="" type="text/css">
.product{
  background-color: $thame-bgcolor-pink;
  .product-list{
    .product-item{
      position:relative;
      margin: 5px 0;
      img{
        width: 100%;
        box-shadow: 0 0 10px;
      }
      .text-top{
        position: absolute;
        top: 0;
        width: 100%;
        text-align:center;
        .title{
          color: $thame-color-brown;
          font-size: 20px;
        }
        .desc{
          color: $thame-color-brown;
          font-size:13px;
          line-height:24px;
        }
        .talk{
          display: inline-block;
          color: #fff;
          font-size: 13px;
          background-color: $thame-color-brown;
          border-radius: 10px;
          padding: 5px;
        }
      }
      .more{
        color:$thame-color-pink;
        font-size:28rpx;
        margin-bottom:10rpx;
        position:absolute;
        bottom:0;
        transform:translateX(-50%);
        left:50%;
      }
    }
  }
}
</style>
