$(function () {
    completeLoading();

    $("#check_all").click(function () {
        accountDeleteLink($(this).prop("checked"));
        if (this.checked) {
            $("input[name='row_checkbox[]']")
                .prop("checked", false)
                .trigger("change");
            $("input[name='row_checkbox[]']")
                .prop("checked", true)
                .trigger("change");
            $("#check_all").prop("checked", true);
        } else {
            $("input[name='row_checkbox[]']")
                .prop("checked", false)
                .trigger("change");
            $("#check_all").prop("checked", false);
        }
    });

    if ($("#account").val()) {
        var ids = JSON.parse("[" + $("#account_data").val() + "]");

        $(".row_checkbox").change(function () {
            var item = $(this).val();

            if (this.checked) {
                ids.push(item);
            } else {
                var index = ids.indexOf(item);
                if (index !== -1) {
                    ids.splice(index, 1);
                }
            }
            $("#account_data").val(ids.toString());

            if (
                $("input[name='row_checkbox[]']:checked").length ==
                $("input[name='row_checkbox[]']").length
            ) {
                $("#check_all").prop("checked", true);
            } else {
                $("#check_all").prop("checked", false);
            }
            accountDeleteLink($(this).prop("checked"));
        });
    }

    // アカウント作成
    $("#js-account-save").on("click", function () {
        $(".js-form-submit").unbind("click");
        $("#account_info_form").submit();
    });

    // アカウント戻る
    $("#js-account-cancel").click(function () {
        var url = $("#complete_flg").data("url");
        window.location = url;
    });

    // アカウント情報作成
    $("#js-modal-account-save-btn").on("click", function () {
        $("#js-account-save").unbind("click");
        $("#js-account-cancel").unbind("click");
        $("#account_info_form").submit();
        setTimeout(function () {
            $("#js-modal-account-save-btn").off("click");
        }, 200);
    });

    $(".js-modal-btn--close").on("click", function () {
        $("input[name='confirm']").val("0");
    });

    $(document).click(function (event) {
        var target = $(event.target);
        if (target.hasClass("js-modal-overlay")) {
            $("input[name='confirm']").val("0");
        }
    });

    // アカウントCSVダウンロード
    $(".js-account-csv-download").on("click", function (e) {
        if ($(".row_checkbox").is(":checked", true)) {
            $("#js-modal-csv-download").show();
            $(".js-modal-csv-download-overlay").show();
        }

        e.preventDefault();

        var account_data_id = $("#account_data").val();
        if (account_data_id == "") {
            $("#error-message").show();
            return;
        } else {
            $("#error-message").hide();
        }

        var downloadCSV = $(this).data("url");
        var request = $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: downloadCSV,
            type: "POST",
            cache: false,
            data: {
                account_data_id: account_data_id,
            },
            xhrFields: {
                responseType: "blob",
            },
        });

        request.done(function (response) {
            var url = window.URL.createObjectURL(response);
            var link = document.createElement("a");
            document.body.appendChild(link);
            link.href = url;
            link.download = "account.csv";
            link.click();
            // ダウンロード後
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
            $("#js-modal-csv-download").hide();
            $(".js-modal-csv-download-overlay").hide();
        });
        request.fail(function (response) {
            console.log("error");
            $("#js-modal-csv-download").hide();
            $(".js-modal-csv-download-overlay").hide();
        });
    });
});

// アカウント削除モーダル
$("#js-account-delete-modal-open").on("click", function () {
    if ($(".row_checkbox").is(":checked", true)) {
        $("#js-account-delete-modal").addClass("is-visible");
        $(".js-modal-overlay").show();
    }
});

// 1つのアカウントがモーダルを削除
$("#js-one-account-delete-modal-open").on("click", function () {
    $("#js-account-delete-modal").addClass("is-visible");
    $(".js-modal-overlay").show();
});

// アカウント削除確認確認
$("#js-account-delete-modal-btn").on("click", function () {
    $("#account_list_form").submit();
    $(".js-modal").removeClass("is-visible");
    $(".js-modal-over-lay").hide();
    $("#js-modal-save").removeClass("is-visible");
});

// 削除のため、アカウントの選択するかしない
function accountDeleteLink(checked) {
    if (checked) {
        $(".u-link.--delete").removeClass("delete-link-disabled");
    } else {
        $(".u-link.--delete").addClass("delete-link-disabled");
    }
}

// モーダルボックスの表示
function completeLoading() {
    var complete_flg = $("#complete_flg").val();
    var mail_failure = $("#mail_failure").val();
    var url = $("#complete_flg").data("url");

    //  完了モーダルボックスの表示
    if (complete_flg) {
        $("#js-modal-complete").addClass("is-visible");
        $(".js-modal-complete-overlay").show();
        $(".js-complete-btn").click(function () {
            window.location = url;
        });
    }

    // メール失敗モーダルボックスの表示
    if (mail_failure) {
        $("#js-modal-save").removeClass("is-visible");
        $(".js-modal-overlay").hide();
        $("#js-modal-mail-failure").addClass("is-visible");
        $(".js-modal-mail-failure-overlay").show();

        $(".js-mail-failure-btn").click(function () {
            $("#js-modal-mail-failure").removeClass("is-visible");
            $(".js-modal-mail-failure-overlay").hide();
            $("#js-modal-save").removeClass("is-visible");
            $(".js-modal-overlay").hide();
        });
    }

    // アカウント登録確認モーダル開く
    var confirm = $("input[name='confirm']").val();
    if (confirm) {
        $("#js-modal-save").addClass("is-visible");
        $(".js-modal-overlay").show();
    }
}
