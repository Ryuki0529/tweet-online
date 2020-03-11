$(function() {
    // 共通オブジェクト
    const message_modal_mask = $('#message_modal_mask'); // メッセージモーダル用
    const message_modal_wrapper = $('#message_modal_wrapper');
    const message_modal_close = $('#message_modal_close');
    const message_modal_content = $('#message_modal_content'); // END メッセージモーダル用

    //  背景スライドの設定
    /*$('#slide').vegas({
        slides: [
        { src: './img/band_1-min.JPG' },
        { src: './img/band_2-min.JPG' },
        { src: './img/band_3-min.JPG' },
        { src: './img/cs1-min.JPG' },
        { src: './img/gokurakudou-min.JPG' },
        { src: './img/radio-min.JPG' },
        { src: './img/yacpe-min.JPG' },
        { src: './img/yatai_2-min.JPG' }
        ],
        transition: 'slideRight2',
        transitionDuration: 2000,
        shuffle: true,
        delay: 7000
    });*/

    // アカウント切り替えメニュー
    let myaccounts_menu = $('#myaccounts_menu');
    let myaccounts_menu_show = $('#myaccounts_menu_show');
    let myaccounts_menu_close = $('#myaccounts_menu_close');
    let myaccounts_menu_mask = $('#myaccounts_menu_mask');
    myaccounts_menu_close.click(function() {
        myaccounts_menu.css('transform', 'translateX(-100%)');
        myaccounts_menu_mask.css('transform', 'translateX(-100%)');
    });
    myaccounts_menu_show.click(function() {
        myaccounts_menu_mask.css('transform', 'translateX(0%)');
        myaccounts_menu.css('transform', 'translateX(0%)');
    });

    // アカウント追加処理(ログインリンク取得)
    $('#add_myaccount').click(function() {
        message_modal_mask.attr('data-messagemodale', 'true');
        $.ajax({
            url: "load_oarth_link.php",
            type: "POST",
            data: "oarthlink",
            /*processData: false,
            contentType: false*/
        }).done(function(data) {
            $('#message_modal_content').html(data);
        }).fail(function(data) {
            $('#message_modal_content').html('通信エラーが発生しました。');
        });
        show_message_modal();
    });

    // メッセージモーダルを閉じる
    message_modal_close.click(function() {
        message_modal_mask.removeAttr('style');
        message_modal_wrapper.removeAttr('style');
    });
    message_modal_mask.click(function() {
        message_modal_mask.removeAttr('style');
        message_modal_wrapper.removeAttr('style');
    });

    show_message_modal();

    changeTwitterWidgetDesign();

    function changeTwitterWidgetDesign(){
        var $twitter_widget = $('iframe.twitter-timeline');
        var $twitter_widget_contents = $twitter_widget.contents();

        if ($twitter_widget.length > 0 && $twitter_widget[0].contentWindow.document.body.innerHTML !== ""){
            $twitter_widget_contents
                .find('.timeline-Tweet-text')
                .css({
                    'font-size' : '12pt',
                    'line-height' : '17pt'
                });
            $twitter_widget_contents
                .find('.timeline-Viewport')
                .css({
                    'scrollbar-width' : 'thin',
                    'scrollbar-color' : '#7a8093 #3b3d44'
                })
        }else {
            setTimeout(function(){
            changeTwitterWidgetDesign();
        }, 350);
        }
    }

    function show_message_modal() {
        if (message_modal_mask.attr('data-messagemodale') == "true") {
            message_modal_mask.css('transform', 'scale(1)');
            message_modal_wrapper.css('transform', 'translate(-50%, -50%) scale(1)');
            message_modal_mask.attr('data-messagemodale', 'false');
        }
    }
});

//new WOW().init();;