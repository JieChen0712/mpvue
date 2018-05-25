import Vue from 'vue'
import App from './App'
// import store from './store/store'

Vue.config.productionTip = false
App.mpType = 'app'

// Vue.prototype.$store = store

const app = new Vue(App)
app.$mount()

export default {
  // 这个字段走 app.json
  config: {
    // 页面前带有 ^ 符号的，会被编译成首页，其他页面可以选填，我们会自动把 webpack entry 里面的入口页面加进去
    pages: ['^pages/index/main', 'pages/product/main', 'pages/coupon/main', 'pages/logs/main'],
    window: {
      backgroundTextStyle: 'light',
      navigationBarBackgroundColor: '#fff',
      navigationBarTitleText: 'WeChat',
      navigationBarTextStyle: 'black'
    },
    tabBar: {
      color: '#6d6d6d',
      selectedColor: '#fe785a',
      backgroundColor: '#fff',
      borderStyle: 'black',
      list: [{
        pagePath: 'pages/index/main',
        text: '首页',
        iconPath: 'static/index.png',
        selectedIconPath: 'static/index_active.png'
      }, {
        pagePath: 'pages/product/main',
        text: '产品展示',
        iconPath: 'static/product.png',
        selectedIconPath: 'static/product_active.png'
      }, {
        pagePath: 'pages/index/main',
        text: '促销',
        iconPath: 'static/sell.png',
        selectedIconPath: 'static/sell_active.png'
      }, {
        pagePath: 'pages/index/main',
        text: '联系我们',
        iconPath: 'static/about.png',
        selectedIconPath: 'static/about_active.png'
      }]
    }
  }
}
