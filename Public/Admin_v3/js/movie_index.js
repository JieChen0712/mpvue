$(document).ready(function () {

    /*视频中心收藏图片的切换 */
    collect();
    function collect() {
        var oCollectBtn = $('.detail-wrapper .title-wrapper button');
        oCollectBtn.click(function () {
            $(this).siblings('h3').toggleClass('toggle');
        }) 
    }
})