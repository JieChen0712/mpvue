$(document).ready(function () {

    //设置商品列表左边的高度
    setListHeight();
    function setListHeight() {
        var iHeight = $(document).height();
        var searchHeight = $('.bar').height();
        var ListHeight = iHeight - searchHeight;
        $('.goods-list').css('height', ListHeight);
    }

    
    $('.goods-title').on('click', 'li', function () {
        var aList = $('.goods-wrapper');
        var iHeight = 0;

        //点击商品列表高亮状态切换
        $(this).addClass('active').siblings().removeClass('active');

        //获取点击列表后对应模块的相对于顶部距离
        for (var i = 0; i < $(this).index(); i++){
            iHeight += aList[i].offsetHeight;
        }

        //点击后让相应模块滚动
        $('.goods-list').animate({
            scrollTop: iHeight + 'px'
        });
    })
})