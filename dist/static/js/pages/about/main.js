<<<<<<< HEAD
global.webpackJsonp([8],{"0G8S":function(t,e,a){"use strict";var s=a("0iJ+"),n=a("junT");var i=function(t){a("ceO6")},o=a("ybqe")(s.a,n.a,i,"data-v-0ca00061",null);e.a=o.exports},"0iJ+":function(t,e,a){"use strict";var s=a("Dd8w"),n=a.n(s),i=a("VsUZ"),o=a("wtEF"),d=a("NYxO");e.a={data:function(){return{latitude:"",longitude:"",address:"",phone:"",markers:[],covers:[],showMap:!1}},created:function(){var t=this;i.a.getCompanyMsg().then(function(e){if(1===e.code&&null!==e.info){var a=e.info.longitude,s=e.info.latitude;t.phone=e.info.phone,t.address=e.info.addres,t.markers=[{id:1,latitude:s,longitude:a,name:e.info.addres,iconPath:""}],t.covers=[{latitude:s,longitude:a,name:e.info.addres}],t.longitude=a,t.latitude=s,t.showMap=!0}else t.phone=t.$store.state.companyPhone,t.address=t.$store.state.companyAddress,t.markers=[{latitude:t.$store.state.companyLongitude,longitude:t.$store.state.companyLatitude,name:t.$store.state.companyAddress,iconPath:""}],t.covers=[{latitude:t.$store.state.companyLongitude,longitude:t.$store.state.companyLatitude,name:t.$store.state.companyAddress}],t.longitude=t.$store.state.companyLongitude,t.latitude=t.$store.state.companyLatitude,t.showMap=!0}).catch(function(t){})},methods:n()({},Object(d.b)(["companyPhone","companyAddress","companyLatitude","companyLongitude"])),store:o.a}},ZYmF:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a("5nAL"),n=a.n(s),i=a("0G8S");new n.a(i.a).$mount(),e.default={config:{navigationBarTitleText:"关于我们"}}},ceO6:function(t,e){},junT:function(t,e,a){"use strict";var s={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"about"},[a("img",{staticClass:"top-banner",attrs:{mode:"widthFix",src:"../../../static/yu_brand.png",alt:""}}),t._v(" "),a("div",{staticClass:"address"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/localtion.png"}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系地址:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.address))]),t._v(" "),t.showMap?a("map",{staticStyle:{width:"100%",height:"170px"},attrs:{id:"myMap",scale:"18",latitude:t.latitude,longitude:t.longitude,markers:"{{markers}}",covers:"{{covers}}","show-location":""}}):t._e()],1),t._v(" "),a("div",{staticClass:"phone"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/phone.png",alt:""}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系电话:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.phone))])],1)])},staticRenderFns:[]};e.a=s}},["ZYmF"]);
=======
global.webpackJsonp([8],{"+Jd9":function(t,e,a){"use strict";var s={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"about"},[a("img",{staticClass:"top-banner",attrs:{mode:"widthFix",src:"../../../static/yu_brand.png",alt:""}}),t._v(" "),a("div",{staticClass:"address"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/localtion.png"}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系地址:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.address))]),t._v(" "),t.showMap?a("map",{staticStyle:{width:"100%",height:"170px"},attrs:{id:"myMap",scale:"18",latitude:t.latitude,longitude:t.longitude,markers:"{{markers}}",covers:"{{covers}}","show-location":""}}):t._e()],1),t._v(" "),a("div",{staticClass:"phone"},[a("img",{staticClass:"icon",attrs:{mode:"widthFix",src:"../../../static/phone.png",alt:""}}),t._v(" "),a("p",{staticClass:"title"},[t._v("联系电话:")]),t._v(" "),a("p",{staticClass:"detail"},[t._v(t._s(t.phone))])],1)])},staticRenderFns:[]};e.a=s},"0G8S":function(t,e,a){"use strict";var s=a("0iJ+"),n=a("+Jd9");var i=function(t){a("sn7e")},o=a("ybqe")(s.a,n.a,i,"data-v-3e52fbd8",null);e.a=o.exports},"0iJ+":function(t,e,a){"use strict";var s=a("Dd8w"),n=a.n(s),i=a("VsUZ"),o=a("wtEF"),d=a("NYxO");e.a={data:function(){return{latitude:"",longitude:"",address:"",phone:"",markers:[],covers:[],showMap:!1}},created:function(){var t=this;i.a.getCompanyMsg().then(function(e){if(1===e.code&&null!==e.info){var a=e.info.longitude,s=e.info.latitude;t.phone=e.info.phone,t.address=e.info.addres,t.markers=[{id:1,latitude:s,longitude:a,name:e.info.addres,iconPath:""}],t.covers=[{latitude:s,longitude:a,name:e.info.addres}],t.longitude=a,t.latitude=s,t.showMap=!0}else t.phone=t.$store.state.companyPhone,t.address=t.$store.state.companyAddress,t.markers=[{latitude:t.$store.state.companyLongitude,longitude:t.$store.state.companyLatitude,name:t.$store.state.companyAddress,iconPath:""}],t.covers=[{latitude:t.$store.state.companyLongitude,longitude:t.$store.state.companyLatitude,name:t.$store.state.companyAddress}],t.longitude=t.$store.state.companyLongitude,t.latitude=t.$store.state.companyLatitude,t.showMap=!0}).catch(function(t){})},methods:n()({},Object(d.b)(["companyPhone","companyAddress","companyLatitude","companyLongitude"])),store:o.a}},ZYmF:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=a("5nAL"),n=a.n(s),i=a("0G8S");new n.a(i.a).$mount(),e.default={config:{navigationBarTitleText:"关于我们"}}},sn7e:function(t,e){}},["ZYmF"]);
>>>>>>> 888231c785b959480ca15f0b0d83ff47e1af73e7
