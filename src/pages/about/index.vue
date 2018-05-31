<template>
  <div class="about">
    <img class="top-banner" mode="widthFix" src="../../../static/yu_brand.png" alt="" />
    <div class="address">
      <img class="icon" mode="widthFix" src="../../../static/localtion.png">
      <p class="title">联系地址:</p>
      <p class="detail">{{address}}</p>
      <map id="myMap" scale="18" style="width: 100%; height: 170px;" :latitude="latitude" :longitude="longitude" markers="{{markers}}" covers="{{covers}}" show-location v-if="showMap"></map>
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
        latitude: '',
        longitude: '',
        address: '',
        phone: '',
        markers: [],
        covers: [],
        showMap: false
      }
    },
    created () {
      api.getCompanyMsg()
        .then(response => {
          if (response.code === 1 && response.info !== null) {
            let _longitude = response.info.longitude
            let _latitude = response.info.latitude
            this.phone = response.info.phone
            this.address = response.info.addres
            this.markers = [{
              id: 1,
              latitude: _latitude,
              longitude: _longitude,
              name: response.info.addres,
              iconPath: ''
            }]
            this.covers = [{
              latitude: _latitude,
              longitude: _longitude,
              name: response.info.addres
            }] 
            this.longitude = _longitude
            this.latitude = _latitude
            this.showMap = true
          } else {
            this.phone = this.$store.state.companyPhone
            this.address = this.$store.state.companyAddress
            this.markers = [{
              latitude: this.$store.state.companyLongitude,
              longitude: this.$store.state.companyLatitude,
              name: this.$store.state.companyAddress,
              iconPath: ''
            }]
            this.covers = [{
              latitude: this.$store.state.companyLongitude,
              longitude: this.$store.state.companyLatitude,
              name: this.$store.state.companyAddress
            }]
            this.longitude = this.$store.state.companyLongitude
            this.latitude = this.$store.state.companyLatitude
            this.showMap = true
          }
        })
        .catch(error => {})
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