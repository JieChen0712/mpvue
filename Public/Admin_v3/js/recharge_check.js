$(document).ready(function () {
    /* 弹窗提示 */
    $(document).on('click', '.alert-text', function () {
        $.alert('下级申请充值，对方给你转了现金，你的虚拟币减少了。');
        $('.modal-buttons .modal-button').text('知道了');
    });
    $(document).on('click', '.pay-screenshot .img-wrapper img', function () {
        $('.mask-img').fadeIn();     
    });
    $(document).on('click', '.mask-img', function () {
        $(this).fadeOut();
    });   

})  