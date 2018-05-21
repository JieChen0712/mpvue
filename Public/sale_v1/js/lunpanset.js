var countPrize = 0;

$(function() {
    //初始化奖项
    initPrize();
    
    //监听添加奖项按钮
    $('#prize-add').bind('click',function(){
        addPrice();
    });
    
    $(document).on('click','.prize_submit',function(){
       var id = $(this).parent().parent('.layui-form').find('input[name="id"]').val();
       var name = $(this).parent().parent('.layui-form').find('input[name="name"]').val();
       var percent = $(this).parent().parent('.layui-form').find('input[name="percent"]').val();
       var total_num = $(this).parent().parent('.layui-form').find('input[name="total_num"]').val();
       var money  = $(this).parent().parent('.layui-form').find('input[name="money"]').val();
       var img_path = $(this).parent().parent('.layui-form').find('input[name="image_name"]').val();
       
       uploadPrize(this,id,name,percent,total_num,money,img_path);
    });
    
    $(document).on('click','.prize_del',function(){
       var id = $(this).parent().parent('.layui-form').find('input[name="id"]').val();
       deletePrize(id,this);
    });
    
});

//添加
function uploadPrize(aim,id,name,percent,total_num,money,img_path){
    $.post(postlunpan,{id:id,name:name,percent:percent,total_num:total_num,image_name:img_path,money:money},function(data){
        if(data.id!=null&&data.id!=undefined&&data.id!=""){
            $(aim).parent().parent('.layui-form').find('input[name="id"]').val(data.id);
        }
       layer.msg(data.msg);
    });
}

//删除
function deletePrize(id,aim){
    $.post(dellunpan,{id:id},function(data){
       if(data.code==1){
           layer.msg('删除成功！');
           $(aim).parent().parent().parent().remove();
       }else{
           layer.msg(data.msg);
       }
    });
}

//初始化奖品表
function initPrize() {
    $.get(get_lunpan, function(data) {
        if(data.code == 1) {
            if(data.info != null) {
                $.each(data.info, function(key, value) {
                    rand = Math.round(Math.random() * 100);
                    var new_form = '<tr><td><form class="layui-form" onsubmit="return false" >' +
                        '<p class="set"><span class="name">奖品名：</span><input class="input-infs layui-input" type="text"  name="name" lay-verify="" value="' + value.name + '" autocomplete="off" placeholder="奖品名" ></p>' +
                        '<p class="set"><span class="name">中奖概率：</span><input class="input-infs layui-input" type="text"  name="percent" id="prize-pecent" value="' + value.percent + '" lay-verify="" autocomplete="off" placeholder="百分比"></p>' +
                        '<p class="set"><span class="name">奖品数量：</span><input class="input-infs layui-input" type="number"  name="total_num" id="prize-size" value="' + value.total_num + '" lay-verify="" autocomplete="off"  placeholder="数量"></p>' +
                        '<p class="set"><span class="name">运费金额：</span><input class="input-infs layui-input" type="number" name="money"  lay-verify="" value="'+value.money+'" autocomplete="off" placeholder="运费金额"></p>' +
                        '<div class="set"><span class="name">奖品图片：</span><div class="input-infs"><div class="wrapper"><input class="input-inf2" type="text" value="' + value.img + '" lay-verify="title" autocomplete="off" placeholder="请选择上传图片">' +
                        '<button type="button" class="layui-btn orange layui-btn-danger upload-btn' + rand + '"><i class="layui-icon">&#xe67c;</i>上传图片</button>' +
                        '<div class="layui-upload layui-inline"><div class="layui-upload-list" data-show="1" data-url="'+root2 + value.img + '"><img class="layui-upload-img" src="' + root2 + value.img + '">' +
                        '<p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p></div>' +
                        '<input type="hidden" class="image-name" name="image_name" value="' + value.img + '" /></div></div></div></div>' +
                        '<input type="hidden" name="id" value="' + value.id + '"><div class="set"><button class="layui-btn btns prize_submit">提交</button><button class="layui-btn layui-btn-danger prize_del">删除</button></div></form></td></tr>';
                    $("#form-all").append(new_form);
                    resetUpload(rand);
                    form.render();
                    $('.layui-upload-list').each(function(key, value) {
                        if($(this).data('show') == 1) {
                            $(this).fadeIn().find('.layui-upload-img').attr('src', $(this).data('url'))
                        }
                    });
                    countPrize++;
                });
            } else {
                addPrice();
            }

        } else {
            console.log(data.msg);
        }
    });
}

//动态添加奖品项
function addPrice() {
    if(countPrize <= 12) {
        rand = Math.round(Math.random() * 100);
        var new_form = '<tr><td><form class="layui-form" onsubmit="return false" >' +
            '<p class="set"><span class="name">奖品名：</span><input class="input-infs layui-input" type="text"  name="name" lay-verify="" value="" autocomplete="off" placeholder="奖品名" ></p>' +
            '<p class="set"><span class="name">中奖概率：</span><input class="input-infs layui-input" type="text"  name="percent"  value="" lay-verify="" autocomplete="off" placeholder="百分比"></p>' +
            '<p class="set"><span class="name">奖品数量：</span><input class="input-infs layui-input" type="number"  name="total_num"  value="" lay-verify="" autocomplete="off"  placeholder="数量"></p>' +
            '<p class="set"><span class="name">运费金额：</span><input class="input-infs layui-input" type="number" name="money"  lay-verify="" value="" autocomplete="off" placeholder="运费金额"></p>' +
            '<div class="set"><span class="name">奖品图片：</span><div class="input-infs"><div class="wrapper"><input class="input-inf2" type="text" value="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" >' +
            '<button type="button" class="layui-btn orange layui-btn-danger upload-btn' + rand + '"><i class="layui-icon">&#xe67c;</i>上传图片</button>' +
            '<div class="layui-upload layui-inline"><div class="layui-upload-list" data-show="" data-url=""><img class="layui-upload-img" src="">' +
            '<p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p></div>' +
            '<input type="hidden" class="image-name" name="image_name" value="" /></div></div></div></div>' +
            '<input type="hidden" name="id" value=""><div class="set"><button class="layui-btn btns prize_submit">提交</button><button class="layui-btn layui-btn-danger prize_del">删除</button></div></form></td></tr>';
        $("#form-all").append(new_form);
        resetUpload(rand);
        form.render();
        countPrize++;
    } else {
        layer.msg('无法添加更多奖品！');
    }
}

//挂载上传图片按钮
function resetUpload(rand) {
    var index = "";
    var elems = '.upload-btn';
    elems += rand;
    layui.use(['upload'], function() {
        var upload = layui.upload;
        var urls = "";

        //  console.log(urls)
        if(urls == "" || urls == undefined || urls == null) {
            urls = URL + '/upload/';
        }
        //执行实例
        var uploadInst = upload.render({
            elem: elems,
            url: urls,
            method: 'post',
            size: 3072,
            accept: 'images',
            data: {
                upload_dir_name: upload_dir_name
            },
            before: function(obj) {
                //预读本地文件示例，不支持ie8
                var item = this.item;
                obj.preview(function(index, file, result) {
                    $(item).siblings('.layui-upload').find('.layui-upload-list').fadeIn().find('.layui-upload-img').attr('src', result); //图片链接（base64)
                    $(item).siblings('.input-inf2').val(file.name)
                });
            },
            done: function(res, index, upload) {
                //获取当前触发上传的元素，一般用于 elem 绑定 class 的情况，注意：此乃 layui 2.1.0 新增
                //如果上传失败
                if(res.code > 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                layer.closeAll('loading'); //关闭loading
                layer.msg(res.msg);
                var item = this.item;
                $(item).siblings('.layui-upload').find('.image-name').val(res.src)
                
            }
        });
    });
}