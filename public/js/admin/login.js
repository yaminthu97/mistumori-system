$("#submit-login").on("click", function (e) {
    var clientTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    $("#time-zone").val(clientTimeZone);
});

//　ダブルクリックフォームを制御するには、BTNを送信
$(".js-form-submit").on("click", function (e) {
    if ($(this).data("submitted")) {
        // フォームはすでに提出されています、フォームを再度送信するのを停止
        e.preventDefault();
    } else {
        // レコードに対してデータサブミットされた属性をtrueに設定
        $(this).data("submitted", true);
    }
});
