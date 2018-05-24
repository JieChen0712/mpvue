<template>
  <div class="coupon">
    <div class="adv-img">
      <img mode="widthFix" src="../../../static/product1.png" alt="" />
    </div>
    <div class="coupon-list">
      <div class="coupon-item">
        <div class="img-wrapper">
        	<img mode="widthFix" src="../../../static/product1.png" alt="" />
        	<div class="mask">
            <p>已领取完，下次再来哦</p>
          </div>
        </div>
        <p class="count">剩余：860张</p>
      </div>
    </div>
  </div>
</template>

<script type="text/ecmascript">
export default {
  data () {
    return {}
  },
  created () {},
  mounted () {},
  methods: {}
}
</script>

<style lang="scss" scoped="" type="text/css">
.coupon{
  background-color:$thame-bgcolor-pink;
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
          &.active{
            display: flex;
          }
          position: absolute;
          display: none;
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
