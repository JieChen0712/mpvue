<template>
  <div class="sale">
    <h1 class="header-title">最新促销</h1>
    <div class="poster-list">
      <div class="poster-item">
        <img src="../../../static/product1.png" mode="widthFix"/>
      </div>
    </div>
  </div>
</template>

<script type="text/ecmascript">
import api from '@/utils/api'
export default {
  data () {
    return {}
  },
  created () {},
  methods: {}
}
</script>

<style lang="scss" scoped="" type="text/css">
.sale{
  padding-top:8px;
  background-color: $thame-bgcolor-pink;
  overflow:hidden;
  .header-title{
    line-height:40px;
    text-align:center;
    margin-bottom: 22px;
    box-shadow:0 0 15px rgba(0,0,0,.5);
  }
  .poster-list{
    .poster-item{
      margin:0 5px 30px;
      box-shadow: 0 0 15px rgba(0,0,0,.5);
      font-size: 0;
      img{
        width: 100%;
      }
    }
  }
}
</style>
