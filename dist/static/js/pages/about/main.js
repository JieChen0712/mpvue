global.webpackJsonp([8],{"0G8S":function(t,e,a){"use strict";var s=a("0iJ+"),i=a("junT");var n=function(t){a("ceO6")},o=a("ybqe")(s.a,i.a,n,"data-v-0ca00061",null);e.a=o.exports},"0iJ+":function(t,e,a){"use strict";var s=a("Dd8w"),i=a.n(s),n=a("VsUZ"),o=a("wtEF"),d=a("NYxO");e.a={data:function(){return{latitude:this.$store.state.companyLatitude,longitude:this.$store.state.companyLongitude,address:this.$store.state.companyAddress,phone:this.$store.state.companyPhone,markers:[{id:1,latitude:this.$store.state.companyLatitude,longitude:this.$store.state.companyLongitude,name:this.$store.state.companyAddress}],covers:[{latitude:this.$store.state.companyLatitude,longitude:this.$store.state.companyLongitude,iconPath:"",name:this.$store.state.companyAddress}]}},onLoad:function(t){var e=this;n.a.getCompanyMsg().then(function(t){1===t.code&&(e.phone=t.info.phone,e.address=t.info.addres,e.longitude=t.info.latitude,e.latitude=t.info.longitude,e.markers[0].latitude=t.info.latitude,e.markers[0].longitude=t.info.longitude,e.markers[0].name=t.info.addres,e.covers[0].latitude=t.info.latitude,e.covers[0].longitude=t.info.longitude,e.covers[0].name=t.info.addres,e.companyPhone(t.info.phone),e.companyAddress(t.info.addres),e.companyLatitude(t.info.latitude),e.companyLongitude(t.info.longitude))}).catch(function(t){wx.showToast({icon:"none",title:t,duration:2e3})})},methods:i()({},Object(d.b)(["companyPhone","companyAddress","companyLatitude","companyLongitude"])),store:o.a}},ZYmF:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a("5nAL"),i=a.n(s),n=a("0G8S");new i.a(n.a).$mount(),e.default={config:{navigationBarTitleText:"关于我们"}}},ceO6:function(t,e){},junT:function(t,e,a){"use strict";var s={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"about"},[a("img",{staticClass:"top-banner",attrs:{mode:"widthFix",src:"../../../static/yu_brand.png",alt:""}}),t._v(" "),a("div",{staticClass:"address"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/localtion.png"}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系地址:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.address))]),t._v(" "),a("map",{staticStyle:{width:"100%",height:"170px"},attrs:{id:"myMap",scale:"18",latitude:t.latitude,longitude:t.longitude,markers:"{{markers}}",covers:"{{covers}}","show-location":""}})],1),t._v(" "),a("div",{staticClass:"phone"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/phone.png",alt:""}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系电话:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.phone))])],1)])},staticRenderFns:[]};e.a=s}},["ZYmF"]);