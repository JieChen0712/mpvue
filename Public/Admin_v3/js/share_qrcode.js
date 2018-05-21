$().ready(function () {
    $.post(qrurl, {type:"mall"},function (data) {
        console.log(data)
        if (data.code == 1) {
            var aim = $("#qrcodeImg");
            getqrCode(aim, data.link)
        } else {
            $.alert(data.msg);
        }
    })
});

function getqrCode(aim, urls) {
    var images = QRCode.generatePNG(urls, {
        ecclevel: "M",
        format: "html",
        fillcolor: "#FFFFFF",
        textcolor: "#373737",
        margin: 4,
        modulesize: 8
    });
    aim.attr("src", images);
}