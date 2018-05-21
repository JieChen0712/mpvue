var new_countPrize = 0;

$(document).ready(function () {
    $(".file-input-new").addClass("col-md-9");
    createPrizeModule();
    //根据抽奖数生成相应的信息模块
    var countPrize = 0;
    var countid = 0;
    var oAddbtn = $("#prize-add");

    oAddbtn.click(function () {
        
        countid++;
        var countPrize = $("#lunpancount").val();
        console.log($("#lunpancount").val())
        countPrize++;
        if( new_countPrize == 0 ){
            new_countPrize = countPrize;
        }
        else{
            new_countPrize++;
        }
        
        createPrize(countid, new_countPrize);
//		alert(countid+" "+countPrize);
    });
//	删除按钮绑定
    var oDelbtn = $(".delete-form");
//	oDelbtn.click(function(){
//		deleteform();
//	});

});

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
    for (var i = 0; i < oCount; i++) {
//      oPrizeInformation.eq(i).removeClass('hidden');
        new_prize_content += prize_content_hidden;
    }

    $("#prize_content").html(new_prize_content);
}

function createPrize(countid, countPrize) {
    var forms = $("#form-all>form");
    
    if (countPrize <= 12) {
        var new_form = $("#new_form").html();
        $("#form-all").append(new_form);
    }
    else{
        alert('无法添加更多奖品！');
    }

}

function deletePrize() {
//		aim.parent().parent().parent().addClass("hidden").slideUp();
    window.location.reload();
}
function saveReport() {
    return true;
}
function submitform(id) {
    var name = $("#name"+id).val();
    var percent = $("#percent"+id).val();
    var total_num = $("#total_num"+id).val();
    
    $.ajax({
        type: "POST",
        url: lunpanset_addurl,
        data: {
            id: id,
          },
        success: function (data) {
            if (data.code == 1) {
                alert(data.msg);
            } else {
                alert(data.msg);
            }

        }
    });
    //return false;	
}
function deleteform(id) {
    if( !confirm('删除不可回退，确认删除吗？') ){
        return false;
    }
    $.ajax({
        url: lunpanset_delurl,
        data:{id:id},
        success: function (data) {
            if (data.code == 1) {
                alert(data.msg);
                location.reload();
            } else {
                alert(data.msg);
            }
        }
    });
    return false;
}
