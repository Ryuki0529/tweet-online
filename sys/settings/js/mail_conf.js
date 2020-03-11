$(function () {
    $('#mail').keyup(function() { mail_conf() });
    $('#mail, #mail_submit').mousemove(function() { mail_conf() });

    // パスワード文字の表示・非表示
    let $showBtn = $('#show_pass');
    let $passValue = $('#pass');
    let $passSwitch = true;
    $showBtn.click(function() {
        if ($passSwitch == true) {
            $passValue.attr('type', 'text');
            $showBtn.text('パスワードを隠す');
            $passSwitch = false;
        }else {
            $passValue.attr('type', 'password');
            $showBtn.text('パスワードを表示');
            $passSwitch = true;
        }
    });
});

// バリデーション結果に基づくステータス表示
function mail_conf() {
    let $mail = $('#mail').val();
    let $forms = [$('#mail'), $('#pass')];
    let $showMg = $('#mail_validation');
    let $submitBtn = $('#mail_submit');
    let $possibleStyle = '<font color="greeeen">';
    let $impossibleStyle = '<font color="red">';
    if (Mail_check($mail)) {
        $.ajax({
            url: "../settings/db_mail_conf.php",
            type: "POST",
            data: {'mail_check': '', 'mail': $mail},
            //processData: false,
            //contentType: false
        }).done(function(data) {
            if (data == "success") {
                console.log(data);
                $showMg.html($possibleStyle + '✔使用可能</font>');
                $submitBtn.removeAttr('disabled');
                blank_check($forms, $submitBtn);
            }else {
                $showMg.html($impossibleStyle + '❌既に使用されています</font>');
                $submitBtn.attr('disabled', 'true');
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log("jqXHR          : " + jqXHR.status); // HTTPステータスが取得
            console.log("textStatus     : " + textStatus);    // タイムアウト、パースエラー
            console.log("errorThrown    : " + errorThrown.message); // 例外情報
        });
    }else {
        $showMg.html($impossibleStyle + '❌形式が不正</font>');
        $submitBtn.attr('disabled', 'true');
    }
}

// 空欄チェック
function blank_check( objArray, submitObj ) {
    objArray.forEach(function( obj ) {
        if (obj.val() == "") {
            //console.log(obj.val() + '空欄');
            submitObj.attr('disabled', 'true');
        }
    });
}

// メールアドレスのバリデーション
function Mail_check( mail ) {
    var mail_regex1 = new RegExp( '(?:[-!#-\'*+/-9=?A-Z^-~]+\.?(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*|"(?:[!#-\[\]-~]|\\\\[\x09 -~])*")@[-!#-\'*+/-9=?A-Z^-~]+(?:\.[-!#-\'*+/-9=?A-Z^-~]+)*' );
    var mail_regex2 = new RegExp( '^[^\@]+\@[^\@]+$' );
    if( mail.match( mail_regex1 ) && mail.match( mail_regex2 ) ) {
        // 全角チェック
        if( mail.match( /[^a-zA-Z0-9\!\"\#\$\%\&\'\(\)\=\~\|\-\^\\\@\[\;\:\]\,\.\/\\\<\>\?\_\`\{\+\*\} ]/ ) ) { return false; }
        // 末尾TLDチェック（?.co,jpなどの末尾ミスチェック用）
        if( !mail.match( /\.[a-z]+$/ ) ) { return false; }
        return true;
    } else {
        return false;
    }
}