global.webpackJsonp([7],{"+qNO":function(t,n,e){"use strict";var o=e("VsUZ");n.a={data:function(){return{couponList:[],page:1}},created:function(){this.getCoupon()},methods:{getCoupon:function(){var t=this,n=wx.getStorageSync("openid");n?o.a.getCoupon(n,this.page).then(function(n){1===n.code&&null!==n.list?(t.page++,t.couponList=n.list):wx.showToast({title:"暂无更多优惠券",icon:"none",duration:2e3})}).catch(function(t){wx.showToast({title:t,icon:"none",duration:2e3})}):wx.navigateTo({url:"/pages/index/main"})},receiveCoupon:function(t,n){var e=this,i=wx.getStorageSync("openid");o.a.recCoupon(i,n).then(function(n){1===n.code?(e.couponList[t].is_get=!0,console.log(e.couponList),wx.showToast({title:n.msg,icon:"success",duration:2e3})):wx.showToast({title:n.msg,icon:"none",duration:2e3})}).catch(function(t){wx.showToast({title:t,icon:"none",duration:2e3})})}}}},"2IRp":function(t,n){},SGJW:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var o=e("5nAL"),i=e.n(o),s=e("o3Bl");new i.a(s.a).$mount()},o3Bl:function(t,n,e){"use strict";var o=e("+qNO"),i=e("ytu5");var s=function(t){e("2IRp")},a=e("WocH")(o.a,i.a,s,"data-v-7a8d44e8",null);n.a=a.exports},ytu5:function(t,n,e){"use strict";var o={render:function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("div",{staticClass:"coupon"},[t._m(0),t._v(" "),t._l(t.couponList,function(n,o){return e("div",{directives:[{name:"show",rawName:"v-show",value:t.couponList.length>0,expression:"couponList.length>0"}],key:o,staticClass:"coupon-list"},[e("div",{staticClass:"coupon-item"},[e("div",{staticClass:"img-wrapper"},[e("img",{attrs:{mode:"widthFix",src:"https://mall.wsxitong.cn"+n.img,alt:"",eventid:"0-"+o},on:{click:function(e){t.receiveCoupon(o,n.id)}}}),t._v(" "),n.is_get?e("div",{staticClass:"mask"},[e("p",{directives:[{name:"show",rawName:"v-show",value:n.is_get,expression:"item.is_get"}]},[t._v("已领取")])],1):t._e()])])])})],2)},staticRenderFns:[function(){var t=this.$createElement,n=this._self._c||t;return n("div",{staticClass:"adv-img"},[n("img",{attrs:{mode:"widthFix",src:"../../../static/product1.png",alt:""}})])}]};n.a=o}},["SGJW"]);