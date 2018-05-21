$(document).ready(function () {

    /*导航栏点击时切换三角形*/
    toggleTriangle();
    function toggleTriangle() {
        var aLi = $('#main .nav li');
        var oTriangle = aLi.find('.triangle');
            aLi.click(function () {
            oTriangle.removeClass('active');
           oTriangle.eq($(this).index()).addClass('active');
        })
    }


    /*点击footer导航栏时切换图片*/
    toggleBackground();
    function toggleBackground() {
        var aWrapper = $('#footer .wrapper');
        var aImg = aWrapper.find('.img');
        var aText = aWrapper.find('.text');
        aWrapper.click(function () {
            for (var i = 0; i < aWrapper.length; i++){
                aImg.eq(i).attr('src', './img/footer-' + (i + 1) + '.jpg')
                aText.css('color', '#B3B3B3');
            }
            aImg.eq($(this).index()).attr('src', './img/footer' + ($(this).index() + 1) + '.jpg')
            aText.eq($(this).index()).css('color', '#0E3E6E');
        })
    }
})