$(document).ready(function () {
    $('.submit-button').click(function () {
        var sSelectValue = $('.select').val() || '';
        var sText = $('.textarea').text() || '';
        var flimg =  $('#flimg').val();

        $.ajax({
            url: addUpgradeUrl,
            data: {
                apply_level: sSelectValue,
                note: sText,
                flimg:flimg,
            },
            type: 'post',
            dataType: 'json',
            success: function (data) {
                console.log(data);
                tusi(data.msg);
                if (data.code == 1) {
                    setTimeout(function() {
                        window.location.href = return_url
                    },2000);
                } else {
                     setTimeout(function() {
                        window.location.reload()
                    },2000);
                }
            }
        })
    })
})