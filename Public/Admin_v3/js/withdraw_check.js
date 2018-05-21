$(function () {
    $(document).on('click', '.alert-text', function () {
        $.alert('下级申请提现，对方给你转了现金，你的虚拟币增加了。');
        $('.modal-buttons .modal-button').text('知道了');
    });
})  