var z=0;
$(function(){
    $(".swiper-button-next").click(function(){
        z++;
    });

    $(".swiper-button-prev").click(function(){
        z--;
    });
    
  });
var x = 0;
function checkForm(imgpp) {
  //alert("AAA");
     if (!parseInt($('#w').val())){
        alert('请裁剪图片！');
        return false;
     }
     $('.mask1_1').show();
     $("#submit").attr('disabled','disabled').die('click');
     //setInterval()
    var count = setTimeout(function(){
           $('.mask1_1').hide();
           $(".jcrop-holder").hide();
           $("#submit").hide();
           $("#reset").hide();
           alert('1、亲，可能网速太慢了！\n2、亲，图片太大了，建议图片大小不超过1.5M\n3、亲，请先用美图秀秀为相机的照片美容瘦身或用美颜相机拍照保存，重新上传！');
            },15000);
    //alert('abc');
    // jquery 表单提交 
    $("#upload_form").ajaxSubmit(function(message) {
      clearTimeout(count);
        var src = message;
        //alert(src);
        $('.mask1_1').hide();
/*        if(imgpp != 0){*/
          var qwe = root+"/"+src;
            $("#img"+imgpp).attr('src',qwe);
/*        }else{
          //$("#cut").attr('src',src);
          var text = '<div class="d-cut" style="position:absolute;left:0px;top:0px;z-index:10;"id="mycutt_'+x+'"><img onclick="sctpbtm('+x+')" id="mycut_'+x+'" class="cut" src="'+root+'/'+src+'" /><img class="imgtp'+x+'" src="'+pub+'/Index/images/sc.png" style="position:absolute;top:-20px;right:-20px;display:none;z-index:11;"></div>'
          $(".swiper-slide"+z).append(text);
          $("#mycutt_"+x+"").die().draggable({containment:"parent"});
          x=x+1;
        }*/
        $(".jcrop-holder").hide();
        $("#submit").hide();
        $("#reset").hide();
    // 对于表单提交成功后处理，message为提交页面saveReport.htm的返回内容 
    }); 
    return false; // 必须返回false，否则表单会自己再做一次提交操作，并且页面跳转 
    }
$(function(){
  $('#reset').click(function(){
    $.post(clearUrl,{},function(data){
      $("#preview").removeAttr('src');
      $('.img').hide();
      $('#submit').hide();
      $('#reset').hide();
    })
  });
})
/*var n = 0;
function font(){
var content=$("#edit").val().replace(/\n/g,"<br/>");
var edit =  $("#edit").val();
var elem = document.getElementById('edit');
var FW = $( ".ui-widget-header" ).width();
var FH = $( '.ui-widget-header' ).height();
var obj = $( '#edit' );
var color = $("#color").val();
var size = $("#fontsize").val();
var family = $("#fontfamily").val();
obj.css( {
    left : parseInt(FW - obj.width()) + 'px',
    top : parseInt(FH - obj.height()) + 'px',
} );
var objStyle = elem.style;
var left = objStyle['left'];
var top = objStyle['top'];
var text = '<div class="d-ui-widget-header uio" style="padding:0px;margin:0px;position:absolute;left:100px;top:100px;z-index:20;" id="divliae_'+n+'"><span id="divlia_'+n+'" onclick="scbtm('+n+')" class="ui-widget-header" style="color:'+color+';font-size:'+size+';font-family:'+family+';">'+content+'</span><img class="tp'+n+'" src="'+pub+'/Index/images/sc.png" style="position:absolute;top:-30px;right:-35px;display:none;z-index:20;"></div>';

$(".swiper-slide"+z).append(text);
    
$("#divliae_"+n+"").die().draggable({containment:"parent"});
n=n+1;

$("#divliae_"+n+"").css( {
    left : 0,
    top : 0,
    color : color,
} );

$("#divlia_"+n+"").css("font-size", size); 
$("#divlia_"+n+"").css("font-family", family);

$('#add').hide();   
}*/
 $(function(){
     $(".yl_btm").click(function(){
      var name= confirm("亲，祝福已经编辑完，确定要发布吗？");
      if(name != false){
      $('.mask2_2').show();
      setTimeout(function(){
      //var h = document.getElementById("vv").getElementsByClassName("swiper-slide").length-1;
      var oo = Array();
      var yy = Array();
      var kk = Array();
      var jj = Array();
      var txt,img,backimg,mbtext;
      var ii = 0;
      var pp = 0;
      var rr = 0;
      var r = 0;
/*      for(var w = 0;w <= h;w++){
        var bb = Array();
        var tt = Array();
        var cc,gg;
        var p=0;
        var i=0;
          $(".swiper-slide"+w+" .uio").each(function(){
                     var top = $(this).css('top');
                     var left = $(this).css('left');
                     var edit = $(".swiper-slide"+w+" #divlia_"+ii).html();
                     var color = $(".swiper-slide"+w+" #divlia_"+ii).css('color');
                     var size = $(".swiper-slide"+w+" #divlia_"+ii).css('font-size');
                     var family = $(".swiper-slide"+w+" #divlia_"+ii).css('font-family');
                     var aa = new Array(edit,left,top,color,size,family);
                        cc = aa.join("^"); 
                        bb[i] = cc;
                        i++;
                        ii++;
          });
        txt = bb.join("@");
        oo[w] = txt;
          $(".swiper-slide"+w+" .d-cut").each(function(){
                      var imgleft = $(this).css('left');
                      var imgtop = $(this).css('top');  
                      var src = $("#mycut_"+pp).attr('src');
                      var ee = new Array(imgleft,imgtop,src);
                      gg = ee.join("^");
                      tt[p] = gg;
                      p++;
                      pp++;
            });
        img = tt.join("@");
        yy[w] = img;
      }*/
      $(".swiper-wrapper .imgh").each(function(){
              var src = $(this).attr('src');
                  kk[rr] = src;
                  rr++;
      });
      $(".swiper-wrapper .spanh").each(function(){
              var edith = $(this).html();
                  jj[r] = edith;
                  r++;
      });
       backimg = kk.join("`");
       mbtext = jj.join("`");
       /*txt = oo.join("`");
       img = yy.join("`");
       var music = $('#mm').val();*/
         $.post(app+"/Index/Edit/hand",{/*txt:txt,img:img,*/backimg:backimg,mbtext:mbtext,/*music:music,*/cl:cl},function(data){
             window.location.href = app+"/Index/Edit/"+cl+"/product_id/"+data;
         });
      },3000);
     	}
     });
 });
 $(document).ready(function(){
     $(".wb_btm").click(function(){
    	 $("#edit").val(''); 
     $('#add').show();
     });
     /*$("#bn").bigColorpicker("f3");*/
     });
/*$(function(){
     $('.yy_btm').click(function(){
        $('#music_op').show();
     })
     $('.sysbtm').click(function(){
        $('.mask').show();
        $('#music_op').hide();
     })
     $('.cancel').click(function(){
        $('#music_op').hide();
     })
     $('.music_btm').click(function(){
      for (var i = 1; i <= 6; i++) {
             document.getElementById('pp'+i).src=pub+"/Index/images/lb.png";
             document.getElementById('bgm'+i).pause();
         };
     $('.mask').hide();
     })
})*/
$(function(){
     $('.yy_btm').click(function(){
     $('.mask').show();
     })
     $('.music_btm').click(function(){
      $(".bgm-btn1").removeClass("mut");
     	for (var i = 1; i <= 8; i++) {
             document.getElementById('pp'+i).src=pub+"/Index/images/lb.png";
             document.getElementById('bgm'+i).pause();
         };
     $('.mask').hide();
     })
     })
//删除文本JS
function scbtm(a){
$('#divlia_'+a).click(function(){
$('.tp'+a).show();
});
$('body').bind('click', function(event) { 
var evt = event.srcElement ? event.srcElement : event.target;    
if(evt.id == 'divlia_'+a)return; // 如果是元素本身，则返回
else {
    $('.tp'+a).hide(); // 如不是则隐藏元素
}   
});
$('.tp'+a).click(function(){
$('#divlia_'+a).remove();
});
}
//删除图片JS
function sctpbtm(b){
	$('#mycut_'+b).click(function(){
		$('.imgtp'+b).show();
		});
		$('body').bind('click', function(event) { 
		var evt = event.srcElement ? event.srcElement : event.target;    
		if(evt.id == 'mycut_'+b)return; // 如果是元素本身，则返回
		else {
		    $('.imgtp'+b).hide(); // 如不是则隐藏元素
		}   
		});
		$('.imgtp'+b).click(function(){
			$('#mycut_'+b).remove();
	});
}
function selectMusic(id){
        document.getElementById('bgm').pause();
    for (var i = 1; i <= 8; i++) {
        document.getElementById('pp'+i).src=pub+"/Index/images/lb.png";
        document.getElementById('bgm'+i).pause();
    };
    $("#mm").val(id);
    rty = root+'/music/'+id+'.mp3';
    $("#bgm").attr('src',rty);
    $(".bgm-btn1").addClass("mut");
    var audio = $("#bgm"+id)[0];
    var pp = document.getElementById('pp'+id)
    pp.src=pub+"/Index/images/lb1.png";
    audio.play();
    
}
$(function(){
    $('.mb_btm').click(function(){
      $('.mb_mask').show();  
  })
    $(".swiper-wrapper").click(function(){
      $('.mb_mask').hide(); 
    })
      /* /\ */
  $(".tp_btm").click(function(){
    var division = 'return checkForm(0)';
    $("#upload_form").attr('onSubmit',division);
    $('#show').show();
  })
  $(".btm").click(function(){
    $('#show').hide();
  }) 
})