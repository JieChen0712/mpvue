global.webpackJsonp([7],{SGJW:function(t,n,o){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var e=o("5nAL"),i=o.n(e),s=o("o3Bl");new i.a(s.a).$mount(),n.default={config:{navigationBarTitleText:"优惠券"}}},bQGZ:function(t,n){},ljoJ:function(t,n,o){"use strict";var e=o("VsUZ");n.a={data:function(){return{couponList:[],page:1}},onShow:function(){this.getCoupon()},methods:{getCoupon:function(){var t=this,n=wx.getStorageSync("openid");n?e.a.getCoupon(n,this.page).then(function(n){1===n.code&&null!==n.list?(t.page++,t.couponList=n.list):wx.showToast({title:"暂无更多优惠券",icon:"none",duration:2e3})}).catch(function(t){wx.showToast({title:t,icon:"none",duration:2e3})}):wx.navigateTo({url:"/pages/index/main"})},receiveCoupon:function(t,n){var o=this,i=wx.getStorageSync("openid");e.a.recCoupon(i,n).then(function(n){1===n.code?(o.couponList[t].is_get=!0,console.log(o.couponList),wx.showToast({title:n.msg,icon:"success",duration:2e3})):wx.showToast({title:n.msg,icon:"none",duration:2e3})}).catch(function(t){wx.showToast({title:t,icon:"none",duration:2e3})})}}}},o3Bl:function(t,n,o){"use strict";var e=o("ljoJ"),i=o("wIK0");var s=function(t){o("bQGZ")},a=o("ybqe")(e.a,i.a,s,"data-v-7f39b851",null);n.a=a.exports},wIK0:function(t,n,o){"use strict";var e={render:function(){var t=this,n=t.$createElement,o=t._self._c||n;return o("div",{staticClass:"coupon"},[t._m(0),t._v(" "),t._l(t.couponList,function(n,e){return o("div",{directives:[{name:"show",rawName:"v-show",value:t.couponList.length>0,expression:"couponList.length>0"}],key:e,staticClass:"coupon-list"},[o("div",{staticClass:"coupon-item"},[o("div",{staticClass:"img-wrapper"},[o("img",{attrs:{mode:"widthFix",src:"https://mall.wsxitong.cn"+n.img,alt:"",eventid:"0-"+e},on:{click:function(o){t.receiveCoupon(e,n.id)}}}),t._v(" "),n.is_get?o("div",{staticClass:"mask"},[o("p",{directives:[{name:"show",rawName:"v-show",value:n.is_get,expression:"item.is_get"}]},[t._v("已领取")])],1):t._e()])])])})],2)},staticRenderFns:[function(){var t=this.$createElement,n=this._self._c||t;return n("div",{staticClass:"adv-img"},[n("img",{attrs:{mode:"widthFix",src:"../../../static/product1.png",alt:""}})])}]};n.a=e}},["SGJW"]);