global.webpackJsonp([5],{"P8y/":function(t,e,a){"use strict";var i={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"product"},[a("div",{staticClass:"product-list"},t._l(t.product,function(e,i){return a("div",{directives:[{name:"show",rawName:"v-show",value:t.product.length>0,expression:"product.length>0"}],key:i,staticClass:"product-item"},[a("a",{attrs:{href:"/pages/detail/main?id="+e.id}},[a("img",{attrs:{mode:"widthFix",src:"https://mall.wsxitong.cn"+e.image}}),t._v(" "),a("div",{staticClass:"text-top"},[a("p",{staticClass:"title"},[t._v(t._s(e.name))]),t._v(" "),a("p",{staticClass:"talk"},[t._v("立即资讯")])],1),t._v(" "),a("p",{staticClass:"more"},[t._v("查看更多>")])],1)])}))])},staticRenderFns:[]};e.a=i},UqC8:function(t,e){},lHXE:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=a("5nAL"),n=a.n(i),s=a("taF9");new n.a(s.a).$mount(),e.default={config:{navigationBarTitleText:"产品展示"}}},taF9:function(t,e,a){"use strict";var i=a("yaKZ"),n=a("P8y/");var s=function(t){a("UqC8")},o=a("WocH")(i.a,n.a,s,"data-v-732585fa",null);e.a=o.exports},yaKZ:function(t,e,a){"use strict";var i=a("VsUZ");e.a={data:function(){return{product:[],page:1}},created:function(){this.getTemplet()},methods:{getTemplet:function(){var t=this;i.a.getTemplet(0,this.page,1).then(function(e){1===e.code&&null!==e.info.list?(t.page++,t.product=e.info.list):wx.showToast({title:"暂无更多商品",icon:"none",duration:2e3})}).catch(function(t){wx.showToast({title:t,icon:"none",duration:2e3})})}}}}},["lHXE"]);