
function genRandNumber(startNum, endNum) {
    var randomNumber;
    randomNumber = Math.round(Math.random() * (endNum - startNum)) + startNum;
    return randomNumber;
}

var UNPOST_KEY = '_J_P_';
var GET_KEY_PREFIX = '_J_';

/**
 * 缓存从服务端获取的数据
 * @param key
 * @param value
 */
var _save = function (key, value) {
    var data = {
        data: value,
        cacheTime: new Date()
    }
    window.localStorage.setItem(GET_KEY_PREFIX + key, JSON.stringify(data));
}
/**
 * 获取本地已缓存的数据
 */
var _get = function (key) {
    return JSON.parse(window.localStorage.getItem(GET_KEY_PREFIX + key));
}

/**
 * 删除本地已缓存的数据
 */
var _clear = function (key) {
    return  window.localStorage.removeItem(GET_KEY_PREFIX + key);
}

/**
 * 清空本地缓存
 */
var clear = function () {
    var storage = window.localStorage;
    for (var key in storage) {
        if (key.indexOf(GET_KEY_PREFIX) == 0) {
            storage.removeItem(key);
        }
    }
    storage.removeItem(UNPOST_KEY);
}

var TipLoad = {};

TipLoad.loading = function (text) {
    var tip = text ? text : '加载中...';

    $('#jingle_popup_mask').show();
    $('#jingle_popup').show();
    $('#jingle_popup p').text(tip);

}
TipLoad.close = function () {
    $('#jingle_popup_mask').hide();
    $('#jingle_popup').hide();
}
/* 

//取参数
*/ 
function GetQueryString(name, url) {

    if (url && url.indexOf('?') != -1) {
        var args = new Object(); //声明一个空对象 

        //获取URL中全部参数列表数据  
        var query = "&" + url.split("?")[1];

        if (query.indexOf('#') != -1) {
            query = query.split("#")[0];
        }

        var pairs = query.split("&"); // 以 & 符分开成数组 
        for (var i = 0; i < pairs.length; i++) {
            var pos = pairs[i].indexOf('='); // 查找 "name=value" 对 
            if (pos == -1) continue; // 若不成对，则跳出循环继续下一对 
            var argname = pairs[i].substring(0, pos); // 取参数名 
            var value = pairs[i].substring(pos + 1); // 取参数值 
            value = decodeURIComponent(value); // 若需要，则解码 
            args[argname] = value; // 存成对象的一个属性 
        }


        return args[name];

    } else {
        return null;
    }
}

/*  */
//截取字符串 包含中文处理 
//(串,长度,增加...) 
function subString(str, len, hasDot) {
    var newLength = 0;
    var newStr = "";
    var chineseRegex = /[^\x00-\xff]/g;
    var singleChar = "";
    var strLength = str.replace(chineseRegex, "**").length;
    for (var i = 0; i < strLength; i++) {
        singleChar = str.charAt(i).toString();
        if (singleChar.match(chineseRegex) != null) {
            newLength += 2;
        }
        else {
            newLength++;
        }
        if (newLength > len) {
            break;
        }
        newStr += singleChar;
    }

    if (hasDot && strLength > len) {
        newStr += "...";
    }
    return newStr;
}
 

////
//$(function () {
//    //自动设置高度
//    $('html').height($(window).height());
//    $('#index_section').height($(window).height());
//    $(window).resize(function () {
//        $('html').height($(window).height());
//        $('#index_section').height($(window).height());
//    });

    
//});
 

var  settings = {
   
    //page默认动画效果
    transitionType : 'slide',
    //自定义动画时的默认动画时间(非page转场动画时间)
    transitionTime : 250,
    //自定义动画时的默认动画函数(非page转场动画函数)
    transitionTimingFunc : 'ease-in',
    //toast 持续时间,默认为3s
    toastDuration : 3000  
}
/*
* alias func
* 简化一些常用方法的写法
** /
/**
* 完善zepto的动画函数,让参数变为可选
*/
var Janim = function (el, animName, duration, ease, callback) {
    var d, e, c;
    var len = arguments.length;
    for (var i = 2; i < len; i++) {
        var a = arguments[i];
        var t = $.type(a);
        t == 'number' ? (d = a) : (t == 'string' ? (e = a) : (t == 'function') ? (c = a) : null);
    }
    $(el).animate(animName, d || settings.transitionTime, e || settings.transitionTimingFunc, c);
}

/*
*  
*  附加返回方法
*/
window.history.goback= function(url) {
    if (history.length >= 2) {
        history.back();
    } else {
        try {
            //如果有值
            if (url) {
                location.href = url;
            } else {
                location.href = "/a/";
            }

            TipLoad.loading();
        } catch (e) {
            alert(e);
        } 
        
    }
}

/*
*  
*  动态加载script 文件
*/
function LoadScript(url, callback) {
    callback = callback || function () { };
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.onload = script.onreadystatechange = function () {
        if (!this.readyState || this.readyState === "loaded" || this.readyState === "complete") {
            //加载完成回调
             callback();  
            // Handle memory leak in IE 
            script.onload = script.onreadystatechange = null;
        }
    };
    script.src = url;
    head.appendChild(script);
}




function CheckEmail(email) {
    return /^[\w\.\-\+]+@([\w\-]+\.)+[a-z]{2,4}$/ig.test(email)
}
function CheckPhone(phone) {
    return /^1[3|4|5|7|8][0-9]\d{8}$/.test(phone);
}
function CheckTel(tel) {
    return /(^[0-9]{3,4}\-[0-9]{7,8}$)|(^[0-9]{7,8}$)|(^\([0-9]{3,4}\)[0-9]{3,8}$)/.test(tel);
}
function CheckSite(site) {
    return /^http:\/\/+[a-z]{2,4}/.test(site);
}


//运行代码
function doRun(cod1) {
    cod = document.getElementById(cod1)
    var code = cod.value;
    if (code != "") {
        var newwin = window.open('', '', '');
        newwin.opener = null
        newwin.document.write(code);
        newwin.document.close();
    }
}


//添加查看图片 神童
/*
自动选择宽或高为标准
showGallery('url');
以宽度为标准
showGallery('url',true);
*/
$(function() {
    $('head').append('<style>.fs_gallery{background:rgba(0,0,0,0.9);position:fixed;left:0;top:0;right:0;bottom:0;z-index:99999}.fs_gallery_close{position:absolute;top:20px;right:20px;width:35px;height:35px;color:#ccc;font-size:34px;line-height:23px;text-align:center;cursor:pointer;z-index:102}.fs_gallery_close:before{content:"×"}.fs_gallery_close:hover{color:#fff}.fs_gallery_prev,.fs_gallery_next{position:absolute;width:80px;color:#888;font-size:30px;cursor:pointer;z-index:101}.fs_gallery_prev:hover,.fs_gallery_next:hover{background:rgba(0,0,0,0.1);color:#fff}.fs_gallery_prev{left:0;top:0;bottom:0}.fs_gallery_next{right:0;top:0;bottom:0}.fs_gallery_prev:before{content:"‹";position:absolute;height:30px;margin-top:-30px;top:50%;left:35px}.fs_gallery_next:before{content:"›";position:absolute;height:30px;margin-top:-30px;top:50%;left:35px}.fs_gallery_shuft{position:relative;width:9999999px}.fs_gallery_shuft:after{clear:both;content:"";display:block}.fs_gallery_shuft_item{float:left;position:relative;background-image:url(data:image/gif;base64,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);background-position:center center;background-repeat:no-repeat}.fs_gallery_shuft_item img{box-shadow:0 0 8px rgba(0,0,0,0.8);position:absolute;top:50%;left:50%}</style>');
});

function showGallery(img, isWidth) {
    var image = new Image();
    image.src = img;
    var bWidth = $(window).width();        //设置最大宽度
    var bHeight = $(window).height();
    var sHeight = image.height;
    var sWidth = image.width;


    if (image.width > bWidth || image.height > bHeight) {
        var scaling = 1;
        var wScaling = 1 - (image.width - bWidth) / image.width;
        var hScaling = 1 - (image.height - bHeight) / image.height;

        //取最小比例
        //是否指定以宽度为标准
        if (isWidth) {
            scaling = wScaling;
        } else {
            if (wScaling > hScaling) {
                scaling = hScaling;
            } else {
                scaling = wScaling;
            }
        }


        //计算缩小比例
        sWidth = image.width * scaling;
        sHeight = image.height * scaling;    //img元素设置高度时需进行等比例缩小
    }

    var margintop = sHeight / 2;
    var marginleft = sWidth / 2;

    var obj = { src: image.src, width: image.width, height: image.height, sHeight: sHeight, sWidth: sWidth, bHeight: bHeight, bWidth: bWidth, margintop: margintop, marginleft: marginleft };
    console.log(obj);

    var fs_gallery = '<div class="fs_gallery"> <div class="fs_gallery_close" onclick="closeGallery();"></div><div class="fs_gallery_shuft" style="transition-duration: 0ms; transform: translate(0px, 0px);"><div class="fs_gallery_shuft_item" style="width:{{bWidth}}px; height:{{bHeight}}px;overflow: auto;overflow-x: hidden;"><img src="{{src}}" alt="" style="width:{{sWidth}}px;height:{{sHeight}}px;  margin-top: -{{margintop}}px;margin-left: -{{marginleft}}px; display: inline;"></div></div><div class="fs_gallery_thumbs"><div class="fs_gallery_thumbs_list"></div></div></div>';
    // $("body").append(template('fs_gallery',obj ));
    $("body").append(template.compile(fs_gallery)(obj));
}

function closeGallery() {
    $(".fs_gallery").remove();
}

//添加查看图片end