/**
 *
 * HTML5 Image uploader with Jcrop
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2012, Script Tutorials
 * http://www.script-tutorials.com/
 */

//    用于压缩图片的canvas 
var canvas = document.createElement("canvas"); 
var ctx = canvas.getContext('2d'); 

 //    瓦片canvas 
var tCanvas = document.createElement("canvas"); 

var tctx = tCanvas.getContext("2d"); 
var maxsize = 500 * 1024;

// convert bytes into friendly format
function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};

// check for selected crop region
function checkForm() {
    if (parseInt($('#w').val())) return true;
    $('.error').html('Please select a crop region and then press Upload').show();
    return false;
};

// update info by cropping (onChange and onSelect events handler)
function updateInfo(e) {
    $('#x1').val(e.x);
    $('#y1').val(e.y);
    $('#x2').val(e.x2);
    $('#y2').val(e.y2);
    $('#w').val(e.w);
    $('#h').val(e.h);
};

// clear info by cropping (onRelease event handler)
function clearInfo() {
    $('.info #w').val('');
    $('.info #h').val('');
};

// Create variables (in this scope) to hold the Jcrop API and image size
var jcrop_api, boundx, boundy;

function fileSelectHandler() {
	
	$('#preview').remove();
	var text = '<img id="preview" />';
	$('.img').append(text);
    // get selected file
    var oFile = $('#image_file')[0].files[0];
    var Orientation = null;  
    // var URL = URL || webkitURL;  
    //获取照片方向角属性，用户旋转控制  
    EXIF.getData(oFile, function() {  
       // alert(EXIF.pretty(this));  
        EXIF.getAllTags(oFile);   
        //alert(EXIF.getTag(this, 'Orientation'));   
        Orientation = EXIF.getTag(oFile, 'Orientation');
        //return;  
    });
    // hide all errors
    $('.error').hide();
    $('#show').hide();
    // check for image type (jpg and png are allowed)
    var rFilter = /^(image\/jpg|image\/jpeg|image\/png|image\/bmp|image\/gif)$/i;
    if (! rFilter.test(oFile.type)) {
        alert('不支持图片格式');
        $('#submit').hide();
        $('.error').hide();
        $('#show').hide();
        //$('.error').html('Please select a valid image file (jpg and png are allowed)').show();
        return;
    }

    // check for file size
    if (oFile.size > 1024 * 1024 * 10) {
        alert('图片大小不能超过10M');  
        $('#submit').hide();
        $('.error').hide();
        $('#show').hide();
        //$('.error').html('You have selected too big file, please select a one smaller image file').show();
        return;
    }

    // preview element
    var oImage = document.getElementById('preview');
    $('.step2').show();

    
    // prepare HTML5 FileReader
    var oReader = new FileReader();
        oReader.onload = function(e) {
    if (navigator.userAgent.match(/iphone/i)) {  
    console.log('iphone');
    //alert("1");
    //alert(expectWidth + ',' + expectHeight);  
    //如果方向角不为1，都需要进行旋转 added by lzk  
        if(Orientation == 6){
        alert('旋转处理');
        }
    }
        //var res= this.result; 
        // e.target.result contains the DataURL which we can use as a source of the image
        oImage.src = e.target.result;

                oImage.onload = function () { // onload event handler
                var sResultFileSize = bytesToSize(oFile.size);
                $('#filesize').val(sResultFileSize);
                $('#filetype').val(oFile.type);
                $('#filedim').val(oImage.naturalWidth + ' x ' + oImage.naturalHeight);
                // destroy Jcrop if it is existed
                if (typeof jcrop_api != 'undefined') {
                    jcrop_api.destroy();
                    jcrop_api = null;
                }
                $('#submit').fadeIn(100);
                $("#reset").fadeIn(100);
                $('.img').show();
                $("#submit").removeAttr('disabled');
                //$("#submit").attr('enabled','enabled');
                // display step 2
                $('.step2').fadeIn(100);
                setTimeout(function(){
                    // initialize Jcrop
                    $('#preview').Jcrop({
                        minSize: [32, 32], // min crop size
                        aspectRatio : 0, // keep aspect ratio 1:1
                        bgFade: true, // use fade effect
                        bgOpacity: .3, // fade opacity
                        onChange: updateInfo,
                        onSelect: updateInfo,
                        onRelease: clearInfo,
                        setSelect:[ 5, 5, 595 , 595 ]
                    }, function(){

                        // use the Jcrop API to get the real image size
                        var bounds = this.getBounds();
                        boundx = bounds[0];
                        boundy = bounds[1];

                        // Store the Jcrop API in the jcrop_api variable
                        jcrop_api = this;
                    });
                },500);

                myfunction(oFile,oImage,Orientation);

                };
            
        
    };

    // read selected file as DataURL
    oReader.readAsDataURL(oFile);
}


function myfunction(oFile,oImage,Orientation){
    // alert(oImage.src);
    var data = "";
    //如果图片大小小于100kb，则直接上传 
     if (oFile.size <= maxsize) {//alert('ddd');
         /*img = null; 
         upload(result, file.type, $(li)); 
         return; */
         data = oImage.src;
     }else{
         // 图片加载完毕之后进行压缩，然后上传 
         if (oImage.complete) {
             data = compress(oImage);

         } else {
             oImage.onload = compress(oImage);
         } 
     }
      $.post(uploadUrl,{imgData:data,Orientation:Orientation},function(data){
        //alert(data);
      });
}

//    使用canvas对大图片进行压缩 
     function compress(img) { 
        //$("#myimg").attr('src',img.src);
         var initSize = img.src.length; 
         var width = img.width; 
         var height = img.height; 
         /*alert(width);
         alert(height);*/
  
         //如果图片大于四百万像素，计算压缩比并将大小压至400万以下 
         var ratio; 
         if ((ratio = width * height / 4000000)>1) { 
             ratio = Math.sqrt(ratio); 
             width /= ratio; 
             height /= ratio; 
         }else { 
             ratio = 1; 
         } 
         canvas.width = width; 
         canvas.height = height; 
  
 //        铺底色 
         ctx.fillStyle = "#fff"; 
         ctx.fillRect(0, 0, canvas.width, canvas.height); 
  
         //如果图片像素大于100万则使用瓦片绘制 
         var count; 
         if ((count = width * height / 1000000) > 1) {
             count = ~~(Math.sqrt(count)+1); //计算要分成多少块瓦片 
  
 //            计算每块瓦片的宽和高 
             var nw = ~~(width / count); 
             var nh = ~~(height / count); 
  
             tCanvas.width = nw; 
             tCanvas.height = nh; 
  
             for (var i = 0; i < count; i++) { 
                 for (var j = 0; j < count; j++) { 
                     tctx.drawImage(img, i * nw * ratio, j * nh * ratio, nw * ratio, nh * ratio, 0, 0, nw, nh); 
  
                     ctx.drawImage(tCanvas, i * nw, j * nh, nw, nh); 
                 } 
             } 
         } else { 
             ctx.drawImage(img, 0, 0, width, height); 
         } 
         
  
         //进行最小压缩 
         var ndata = canvas.toDataURL('image/jpeg', 0.5); 
  
         /*console.log('压缩前：' + initSize); 
         console.log('压缩后：' + ndata.length); 
         console.log('压缩率：' + ~~(100 * (initSize - ndata.length) / initSize) + "%"); */
  
         tCanvas.width = tCanvas.height = canvas.width = canvas.height = 0; 
         return ndata; 
     }