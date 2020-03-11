$(function() {
    let $twtext = $('#twtext');
    $twtext.on('keyup mousemove', function() {
        twTextCount();
    });
    twTextCount();

    let $twhistEditBtn = $('.twhist-edit-btn');
    let $showClass = 'show';
    $twhistEditBtn.click(function() {
        let editwd = $(this).parent().find('.twhist-edit-window');
        if (editwd.hasClass($showClass)) {
            editwd.removeClass($showClass);
        }else {
            editwd.addClass($showClass);
        }
    });

    $('#twset-message-close').click(function() {
        $('#twset-message').slideUp(500);
    })
});

function twTextCount() {
    let $twtext = $('#twtext');
    let $twTextCount = $('#twTextCount');
    let $autoTwSet = $('#autoTwSet');

    let twlen = $twtext.val().length;
    if (twlen > 140 || twlen == 0) {
        $autoTwSet.attr('disabled', 'true');
        $twTextCount.html('<font color="orange">' + twlen + ' / 140</font>');
    }else {
        $twTextCount.text(twlen + ' / 140');
        $autoTwSet.removeAttr('disabled');
    }
}