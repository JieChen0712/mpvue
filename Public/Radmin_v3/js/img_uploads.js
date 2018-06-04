layui.use('upload', function() {
  var flag = false;
  var $ = layui.jquery,
    upload = layui.upload;
  upload.render({
    elem: '#uploads_btn',
    url: URL + '/upload/',
//  multiple: true,
    method: 'post',
    size: 3072,
    accept: 'images',
    auto:true,
    data: {
      upload_dir_name: upload_dir_name
    },
    before: function(obj) {
      //预读本地文件示例，不支持ie8
      obj.preview(function(index, file, result) {
        $('.layui-upload-lists').append('<li class="img-item"><img src="' + result + '" alt="' + file.name + '" class="layui-upload-img"><i class="layui-icon delete">&#xe640;</i><input type="hidden" name="'+image_name+'" class="imgUrl"></li>')
        flag = true
      });
    },
    done: function(res) {
      //上传完毕
      if(res.code > 0) {
        
        return layer.msg('上传失败');
      }
      //上传成功
      layer.closeAll('loading'); //关闭loading
      layer.msg(res.msg);
      var timer = setInterval(function(){if(flag){$('.img-item:last').children('.imgUrl').val(res.src);clearInterval(timer)}})
    }
  });
})



$(function() {
if(!!imgList&&img_show==1){
      $.each(imgList2,function(key,value){
        $('.layui-upload-lists').append('<li><img src="' + value + '" class="layui-upload-img"><i class="layui-icon delete">&#xe640;</i><input type="hidden" class="imgUrl" name="'+image_name+'" value="'+imgList[key]+'"></li>')
    });

}
//点击删除图片
$(document).on('click', '.delete', function() {
    $(this).parent().remove();
});

});