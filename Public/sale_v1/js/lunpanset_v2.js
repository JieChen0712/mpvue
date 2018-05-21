var new_countPrize = 0;

$(document).ready(function() {
    initPrize();

    $(".file-input-new").addClass("col-md-9");
    createPrizeModule();
    //根据抽奖数生成相应的信息模块
    var countPrize = 0;
//  var countid = 0;
    var oAddbtn = $("#prize-add");

    createPrize(new_countPrize);

    
    $(document).on('click', '.demoText', function() {
        if($(this).find('.delete').length > 0) {
            $(this).siblings('img').attr('src', '').parent().hide().siblings('.image-name').val('');
        }
    });

//  oAddbtn.click(function() {
//
//      
//      //		alert(countid+" "+countPrize);
//  });
    //	删除按钮绑定
    var oDelbtn = $(".delete-form");
    //	oDelbtn.click(function(){
    //		deleteform();
    //	});

});

    $(document).on('click','#prize-add',function(){
//      var countPrize = $("#lunpancount").val();
//      countPrize++;
//      if(new_countPrize == 0) {
//          new_countPrize = countPrize;
//      } else {
//          new_countPrize++;
//      }

        initPrize()
        form.render();
//      $("#lunpancount").val(countPrize);
    });
    
    
   form.on("submit",function(data){
       console.log(data);
   })

function createPrizeModule() {

    var oSelect = $('.select');
    var oCount = oSelect.val();

    var oPrizeWrapper = $('.prize-wrapper');
    var oPrizeInformation = $('.prize-information');
    //    oPrizeInformation.addClass('hidden');

    var prize_content_hidden = $("#prize_content_hidden").html();
    //    alert(prize_content_hidden);
    var new_prize_content = '';
    $("#prize_content").html('');
    for(var i = 0; i < oCount; i++) {
        //      oPrizeInformation.eq(i).removeClass('hidden');
        new_prize_content += prize_content_hidden;
    }

    $("#prize_content").html(new_prize_content);
}

function createPrize(countPrize) {
    if(countPrize <= 12) {
        //  var new_form = $("#new_form").html();
        rand = Math.round(Math.random() * 100);
        var new_form = '<tr><td>' +
                        '<p class="set"><span class="name">奖品名：</span><input class="input-infs layui-input" type="text"  name="name" lay-verify="" value="" autocomplete="off" placeholder="奖品名" ></p>' +
                        '<p class="set"><span class="name">中奖概率：</span><input class="input-infs layui-input" type="text"  name="percent"  value="" lay-verify="" autocomplete="off" placeholder="百分比"></p>' +
                        '<p class="set"><span class="name">奖品数量：</span><input class="input-infs layui-input" type="number"  name="total_num"  value="" lay-verify="" autocomplete="off"  placeholder="数量"></p>' +
                        '<p class="set"><span class="name">运费金额：</span><input class="input-infs layui-input" type="number" name="money"  lay-verify="" value="" autocomplete="off" placeholder="运费金额"></p>' +
                        '<div class="set"><span class="name">奖品图片：</span><div class="input-infs"><div class="wrapper"><input class="input-inf2" type="text" value="" lay-verify="title" autocomplete="off" placeholder="请选择上传图片" >' +
                        '<button type="button" class="layui-btn orange layui-btn-danger upload-btn' + rand + '"><i class="layui-icon">&#xe67c;</i>上传图片</button>' +
                        '<div class="layui-upload layui-inline"><div class="layui-upload-list" data-show="" data-url=""><img class="layui-upload-img" src="">' +
                        '<p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p></div>' +
                        '<input type="hidden" class="image-name" name="image_name" value="" /></div></div></div></div>' +
                        '<input type="hidden" name="id" value=""><div class="set"><button class="layui-btn btns">提交</button></div></td></tr>';
        $("#form-all").append(new_form);
        resetUpload(rand);
        form.render();
    } else {
        layer.msg('无法添加更多奖品！');
    }

}

function initPrize() {
    $.get(get_lunpan, function(data) {
        if(data.code == 1) {
            if(data.info!=null){
                $.each(data.info, function(key, value) {
                    new_countPrize++;
                    rand = Math.round(Math.random() * 100);
                    var new_form = '<tr><td><form class="layui-form"  action="' + urls + '/lunpanset_post" data-auto="false" >' +
                        '<p class="set"><span class="name">奖品名：</span><input class="input-infs layui-input" type="text"  name="name" lay-verify="" value="' + value.name + '" autocomplete="off" placeholder="奖品名" ></p>' +
                        '<p class="set"><span class="name">中奖概率：</span><input class="input-infs layui-input" type="text"  name="percent" id="prize-pecent" value="' + value.percent + '" lay-verify="" autocomplete="off" placeholder="百分比"></p>' +
                        '<p class="set"><span class="name">奖品数量：</span><input class="input-infs layui-input" type="number"  name="total_num" id="prize-size" value="' + value.total_num + '" lay-verify="" autocomplete="off"  placeholder="数量"></p>' +
                        '<p class="set"><span class="name">运费金额：</span><input class="input-infs layui-input" type="number" name="money"  lay-verify="" value="" autocomplete="off" placeholder="运费金额"></p>' +
                        '<div class="set"><span class="name">奖品图片：</span><div class="input-infs"><div class="wrapper"><input class="input-inf2" type="text" value="' + value.img + '" lay-verify="title" autocomplete="off" placeholder="请选择上传图片">' +
                        '<button type="button" class="layui-btn orange layui-btn-danger upload-btn' + rand + '"><i class="layui-icon">&#xe67c;</i>上传图片</button>' +
                        '<div class="layui-upload layui-inline"><div class="layui-upload-list" data-show="1" data-url="' + value.img + '"><img class="layui-upload-img" src="'+root2 + value.img + '">' +
                        '<p class="demoText"><i class="layui-icon delete" style="font-size: 26px;color: white;line-height: 27px;">&#xe640;</i></p></div>' +
                        '<input type="hidden" class="image-name" name="image_name" value="' + value.img + '" /></div></div></div></div>' +
                        '<input type="hidden" name="id" value="'+value.id+'"><div class="set"><button class="layui-btn btns">提交</button></div></form></td></tr>';
                    $("#form-all").append(new_form);
                    resetUpload(rand);
                    form.render();
                    $('.layui-upload-list').each(function(key, value) {
                        if($(this).data('show') == 1) {
                            $(this).fadeIn().find('.layui-upload-img').attr('src', $(this).data('url'))
                        }
                    });
                    new_countPrize++;
                });
            }else{
                createPrize( new_countPrize);
                new_countPrize++;
            }
            
        } else {
            console.log(data.msg);
        }
    });
}

function deletePrize() {
    //		aim.parent().parent().parent().addClass("hidden").slideUp();
    window.location.reload();
}

function saveReport() {
    return true;
}

function submitform(id) {
    var name = $("#name" + id).val();
    var percent = $("#percent" + id).val();
    var total_num = $("#total_num" + id).val();

    $.ajax({
        type: "POST",
        url: lunpanset_addurl,
        data: {
            id: id,
        },
        success: function(data) {
            if(data.code == 1) {
                layui.msg(data.msg);
            } else {
                layui.msg(data.msg);
            }

        }
    });
    //return false;	
}

function deleteform(id) {
    layui.confirm('删除不可回退，确认删除吗？', function(index) {
        layer.close(index);
        $.ajax({
            url: lunpanset_delurl,
            data: {
                id: id
            },
            success: function(data) {
                if(data.code == 1) {
                    alert(data.msg);
                    location.reload();
                } else {
                    alert(data.msg);
                }
            }
        });
    })
}

function resetUpload(rand) {
    var index = "";
    var elems = '.upload-btn';
    elems += rand;
    layui.use(['upload'], function() {
        var upload = layui.upload;
        var urls = "";

        try {
            urls = logo_upload;
        } catch(e) {
//          console.log(e)
            //TODO handle the exception
        }
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
                //网站配置的额外配置方法
                try {
                    if(upload_done && typeof(upload_done) == "function") {
                        console.log(2)
                        $(item).siblings('.layui-upload').find('.image-name').val(res.data.src)
                        upload_done(res.data.src);
                    } else {
                        console.log(1)
                    }
                } catch(e) {
                    console.log(e);
                    //TODO handle the exception
                }
            }
        });
    });
}