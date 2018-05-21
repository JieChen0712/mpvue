$(function(){
$(document).bind('touchmove',function(e){
    e.preventDefault();
    e.stopPropagation();
    e.cancelable = false;
    return false;
})
  

  if(!(guide=="true")){
    $('.mask').hide(1).css('backgroundColor','rgba(0, 0, 0, .7)');
    $('.overlay_hole').hide(1);
    $('.guide-wrapper').hide(1);
    $(document).off('touchmove');
  }
  
  $('.mask .btn-close').bind('click',function(){
    $(document).off('touchmove');
    $('.mask').fadeOut().css('backgroundColor','rgba(0, 0, 0, .7)');
    $('.overlay_hole').fadeOut();
    $('.guide-wrapper').fadeOut();
    return false;
  })
  $('.mask').bind('click',function(){
    if($('.guide-wrapper').is(":visible")){
      return false;
    }else{
      $(this).slideToggle();
    }
  })
});
