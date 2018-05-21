var tabs=function(){
    function tag(name,elem){
        return (elem||document).getElementsByTagName(name);
    }
    //获得相应ID的元素
    function id(name){
        return document.getElementById(name);
    }
    function first(elem){
        elem=elem.firstChild;
        return elem&&elem.nodeType==1? elem:next(elem);
    }
    function next(elem){
        do{
            elem=elem.nextSibling;  
        }while(
            elem&&elem.nodeType!=1  
        )
        return elem;
    }
    return {
        set:function(elemId,tabId){
            var elem=tag("li",id(elemId));
            var tabs=tag("div",id(tabId));
            var listNum=elem.length;
            var tabNum=tabs.length;
            for(var i=0;i<listNum;i++){
                    elem[i].onclick=(function(i){
                        return function(){
                            for(var j=0;j<tabNum;j++){
                                if(i==j){
                                    tabs[j].style.display="block";
                                    //alert(elem[j].firstChild);
                                    elem[j].firstChild.className="selected";
                                }
                                else{
                                    tabs[j].style.display="none";
                                    elem[j].firstChild.className="";
                                }
                            }
                        }
                    })(i)
            }
        }
    }
}();
tabs.set("nav","menu_con");//执行

  $(document).ready(function(){
            $('#nav li').first().addClass("jd")
            $('#nav li').click(function(){
            var html = $(this).html();
            $(this).addClass("jd").siblings().removeClass("jd") 
            })
      })

 /*index*/
var h = 0;
var arr = [];
 function btm(z,x,v){
     if(z == 8){
         h = h + x;
         k = z - 1;
         arr[k] = v;
         /*ThinkAjax.send('__URL__/checkName','ajax=1&username='+$('username').value,'','result');*/
         $.post("checkName",{arr:arr},function(data, status){
             if(status == 'success'){
                 //alert(data);
                 window.location.href=app+"/Index/Paper/xs/?mark="+h;
             } 
         });
     }else{k = z - 1;
         arr[k] = v;
         $('.mask'+z).fadeIn("slow");
         $('.mask'+k).fadeOut("slow");
         h = h + x;
     }   
}
/*xs*/      
$(function(){
        setTimeout('$(".bgm-btn").hide()', 2000);
        setTimeout('$(".bgm-btn").hide()', 4000);
        });
$(function(){
        setTimeout('$("p").show()', 4000);
        setTimeout('$(".ul").show()', 4000);
        setTimeout('$(".span").show()', 4000);
        setTimeout('$(".span1").show()', 4000);
        setTimeout('$(".bgm-btn").show()', 2000);
        });

$(function(){
        $('.wy_btm').click(function(){
        $('.xs_mask').show();
        })
        $('.xs_mask').click(function(){
        $('.xs_mask').hide();
        })         
})

$(function(){
        $('.zd_btm').click(function(){
        $('.xs_mask').show();
        })
        $('.xs_mask').click(function(){
        $('.xs_mask').hide();
        })         
})        