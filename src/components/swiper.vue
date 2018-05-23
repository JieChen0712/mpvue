<template>
  <div class="swiper">
    <swiper indicator-dots="{{indicatorDots}}" autoplay="{{autoplay}}" interval="{{interval}}" duration="{{duration}}" style="height:{{swiperHeight}}">
      <block wx:for="{{imgUrls}}">
        <swiper-item>
          <image src="{{item}}" class="slide-image" />
        </swiper-item>
      </block>
    </swiper>
  </div>
</template>

<script type="text/ecmascript">
  export default {
    props: {
      imgUrls: Array, // 图片数组
      indicatorDots: Boolean, // 是否显示下标
      interval: Number, // 切换时间
      duration: Number, // 过渡时间
      swiperHeight: String // 为空的话默认150
    },
    data () {
      return {
        autoplay: true // 自动播放
      }
    },
    created () {},
    mounted () {},
    methods: {}
  }
</script>

<style lang="scss" scoped="">
.swiper{
  .slide-image{
    width: 100%;
    height: 100%;
  }
}
</style>
