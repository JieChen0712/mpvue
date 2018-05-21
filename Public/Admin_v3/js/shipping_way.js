$().ready(function(){

    $(function(){

        $('.province').mouseover(function(){
            $(this).find('ul').show();
        }).mouseout(function(){
            $(this).find('ul').hide();
        });

        $('.cityall').click(function(){
            var checked = $(this).get(0).checked;
            var citys = $(this).parent().parent().find('.city');
            citys.each(function(){
                $(this).get(0).checked = checked;
            });
            var count = 0;
            if(checked){
                count =  $(this).parent().parent().find('.city:checked').length;
            }
            if(count>0){
                $(this).next().html("(" + count + ")")    ;
            }
            else{
                $(this).next().html("");
            }
        });
        $('.city').click(function(){
            var checked = $(this).get(0).checked;
            var cityall = $(this).parent().parent().parent().parent().find('.cityall');

            if(checked){
                cityall.get(0).checked = true;
            }
            var count = cityall.parent().parent().find('.city:checked').length;
            if(count>0){
                cityall.next().html("(" + count + ")")    ;
            }
            else{
                cityall.next().html("");
            }
        });

    });

    function clearSelects(){
        $('.city').attr('checked',false).removeAttr('disabled');
        $('.cityall').attr('checked',false).removeAttr('disabled');
        $('.citycount').html('');
    }

    function show_type(flag){
        if (flag == 1) {
            $('.show_h').hide();
            $('.show_n').show();
        } else {
            $('.show_h').show();
            $('.show_n').hide();
        }
    }
    $(function(){
        show_type();
        $(':radio[name=calculatetype]').click(function(){
            var val = $(this).val();
            show_type(val);
        })
        $(':radio[name=dispatchtype]').click(function(){
            var val = $(this).val();
            $(".dispatch0,.dispatch1").hide();
            $(".dispatch" + val).show();
        })

        $(':radio[name=isdispatcharea]').click(function(){
            var val = $(this).val();
            var name = '不';
            if(val == 1) {
                name = '只';
            }
            $("#dispatcharea_name").html(name);
        })

        $("select[name=express]").change(function(){
            var obj = $(this);
            var sel = obj.find("option:selected");
            $(":input[name=expressname]").val(sel.data("name"));
        });

        $('.province').mouseover(function(){
            $(this).find('ul').show();
        }).mouseout(function(){
            $(this).find('ul').hide();
        });

        $('.cityall').click(function(){
            var checked = $(this).get(0).checked;
            var citys = $(this).parent().parent().find('.city');
            citys.each(function(){
                $(this).get(0).checked = checked;
            });
            var count = 0;
            if (checked){
                count = $(this).parent().parent().find('.city:checked').length;
            }
            if (count > 0){
                $(this).next().html("(" + count + ")");
            }
            else{
                $(this).next().html("");
            }
        });

        $('.city').click(function(){
            var checked = $(this).get(0).checked;
            var cityall = $(this).parent().parent().parent().parent().find('.cityall');
            if (checked){
                cityall.get(0).checked = true;
            }
            var count = cityall.parent().parent().find('.city:checked').length;
            if (count > 0){
                cityall.next().html("(" + count + ")");
            }
            else{
                cityall.next().html("");
            }
        });
    });
    function getCurrents(withOutRandom){
        var citys = "";
        $('.citys').each(function(){
            var crandom = $(this).prev().val();
            if (withOutRandom && crandom == withOutRandom){
                return true;
            }
            citys += $(this).val();
        });
        return citys;
    }
    function getCurrentsCode(withOutRandom){
        var citys = "";
        $('.citys_code').each(function(){
            var crandom = $(this).prev().prev().prev().val();
            if (withOutRandom && crandom == withOutRandom){
                return true;
            }
            citys += $(this).val();
        });
        return citys;
    }
    var current = '';
    $("#btnSubmitArea").click(function () {
        addArea(btn);
    })
    function addArea(btn){
        $.ajax({
            url:"file:///C:/wamp/www/h5/index/shipping_way.html",
            dataType:'json',
            success:function(json){
                current = json.random;
                $('#tbody-areas').append(json.html);
                $('#tbody-areas tr').last().hide();
                clearSelects();
                $("#modal-areas").modal();

                var citystrs = "";

                var currents = getCurrents();
                currents = currents.split(';');
                $('.city').each(function(){
                    var parentdisabled = false;
                    for (var i in currents){
                        if (currents[i] != '' && currents[i] == $(this).attr('city')){
                            $(this).attr('disabled', true);
                            $(this).parent().parent().parent().parent().find('.cityall').attr('disabled', true);
                        }
                    }
                });
                $('#btnSubmitArea').unbind("click").click(function(){
                    $('.city:checked').each(function(){
                        citystrs += $(this).attr('city') + ";";
                    });
                    console.log(citystrs)
                    $("#area-data").html(citystrs);
                    $('.' + current + ' .citys').val(citystrs);
                    $('#tbody-areas tr').last().show();
                })

                var calculatetype1 = $('input[name="calculatetype"]:checked ').val();
                show_type(calculatetype1);
            }
        })
    }

    $(function () {
        $('.page-content').show();
        $(window).bind('scroll resize', function () {
            var scrolltop = $(window).scrollTop();
            if (scrolltop > 300) {
                $(".page-gotop").fadeIn(300)
            } else {
                $(".page-gotop").fadeOut(300)
            }
            $(".page-gotop").unbind('click').click(function () {
                $('body').animate({scrollTop: "0px"}, 1000)
            })
        });
    });
});