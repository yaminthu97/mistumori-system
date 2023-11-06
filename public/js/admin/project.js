$(document).ready(function () {
    $(".js_input").on("change", function () {
        var file = $(this).prop("files")[0];
        var file_name = $(this).attr("name").replace("_path", "") + "_file";
        $("input[name=" + file_name + "]").val(file.name ?? "");
    });

    // プロジェクトリスト検索
    $(".icon-btn").on("click", function () {
        $(".icon-btn").toggleClass("open");
        $(".updown-box").toggleClass("open");
    });

    $(".assignee-icon").on("click", function () {
        var login_user = $("#login-user").val();
        $(".select-assignee").val(login_user);
    });

    var isCreate = false;
    // プロジェクト保存にエラーメッセージを表示
    $("#projectForm").submit(function (e) {
        e.preventDefault();
        if (isCreate) {
            return;
        }
        isCreate = true;
        $(".invalid-error").text("");

        var form = new FormData(this);
        var url = $(this).attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "POST",
            cache: false,
            data: form,
            contentType: false, // formdataのコンテンツタイプをfalseに設定
            processData: false, // formdataのProcessDataをfalseに設定
            success: function (data) {
                window.location.href = "/admin/project/detail/" + data.id;
            },
            error: function (data) {
                errors = data.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    $("#" + propName + "_error").text(propVal[0]);
                });
            },
            complete: function () {
                isCreate = false;
            },
        });
    });

    // クリアファイル
    $(".clear-btn").on("click", function () {
        $("input[name='system_file']").val("");
        $("input[name='system_path']").val("");
        $("#system_path_error").text("");
    });
});
