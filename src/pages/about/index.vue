<template>
  <div class="about">
    <img class="top-banner" mode="widthFix" src="../../../static/yu_brand.png" alt="" />
    <div class="address">
      <img class="icon" mode="widthFix" src="../../../static/localtion.png">
      <p class="title">联系地址:</p>
      <p class="detail">{{address}}</p>
      <map id="myMap" scale="18" style="width: 100%; height: 170px;" :latitude="latitude" :longitude="longitude" markers="{{markers}}" covers="{{covers}}" show-location></map>
    </div>
    <div class="phone">
      <img class="icon" mode="widthFix" src="../../../static/phone.png" alt="" />
      <p class="title">联系电话:</p>
      <p class="detail">{{phone}}</p>
    </div>
  </div>
</template>

<script type="text/ecmascript">
  import api from '@/utils/api'
  import store from '@/store/store'
  import { mapMutations } from 'vuex'
  export default {
    data () {
      return {
        latitude: this.$store.state.companyLatitude,
        longitude: this.$store.state.companyLongitude,
        address: this.$store.state.companyAddress,
        phone: this.$store.state.companyPhone,
        markers: [{
          id: 1,
          latitude: this.$store.state.companyLatitude,
          longitude: this.$store.state.companyLongitude,
          name: this.$store.state.companyAddress
        }],
        covers: [{
          latitude: this.$store.state.companyLatitude,
          longitude: this.$store.state.companyLongitude,
          iconPath: '',
          name: this.$store.state.companyAddress
        }]
      }
    },
    methods: {
      ...mapMutations([  
        'companyPhone',
        'companyAddress',
        'companyLatitude',
        'companyLongitude'
      ]),
    },
    store
  }
</script>

<style lang="scss" scoped="" type="text/css">
page{
  height: 100%;
}
.about{
  background-color: $thame-bgcolor-pink;
  font-size: 0;
  height: 100%;
  overflow: hidden;
  .top-banner{
    width: 100%;
    box-shadow:-5px -5px 5px rgba(0,0,0,.2) inset;
  }
  .address,.phone{
    font-size: 20px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 0 15px rgba(0,0,0,.5);
    background-color: #fff;
    .title{
      color: $thame-color-pink;
    }
    .icon{
      width: 25px;
      height: 25px;
      margin: 5px auto 0;
    }
    .detail{
      box-shadow:0 5px 5px rgba(0,0,0,.2);
      margin-bottom:5px;
    }
  }
}
</style>