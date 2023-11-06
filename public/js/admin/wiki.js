$(document).ready(function () {
    var toolbar_options = {
        container: [
            [{ header: [1, 2, 3, 4, 5, false] }],
            ["bold", "italic", "strike"],
            [{ list: "ordered" }, { list: "bullet" }],
            [{ align: [] }],
            ["blockquote"],
            [{ color: [] }, { background: [] }],
            ["link"],
        ],
    };

    // クイル構成
    var quill = new Quill("#editor", {
        modules: {
            toolbar: toolbar_options,
        },
        theme: "snow",
    });

    // Wikiを入手して、Quill HTMLに追加
    if ($('input[name="wiki_content"]').val()) {
        quill.root.innerHTML = $('input[name="wiki_content"]').val();
    }

    // [参照]ボタンをクリックすると、入力ボックスにファイル名を追加
    $(".js_input").on("change", function () {
        var files = $(this).prop("files");
        var file_name = $(this).attr("id").replace("_path", "") + "_file";
        var file_arr = (combined_file_arr = []);

        $.each(files, function (key, file) {
            if (file) {
                file_arr.push(file.name);
            }
        });

        if (file_arr.length > 1) {
            // 複数のファイルがある場合は、コンマと結合
            var combined_file_arr = file_arr.join(", ");
        } else {
            // コンマなしで単一のファイル名を使用
            var combined_file_arr = file_arr[0];
        }

        $("input[name=" + file_name + "]").val(combined_file_arr);
    });

    var is_create = false;
    // Wiki Saveでエラーメッセージを表示
    $(".js-form-submit").on("click", function (e) {
        e.preventDefault();

        // ダブルクリックを防ぐ
        if (is_create) {
            return;
        }
        is_create = true;

        // Quill HTMLを入手して、Wikiに追加
        var wiki_content =
            quill.root.innerHTML == "<p><br></p>"
                ? $("input[name='wiki_content']").val("")
                : $("input[name='wiki_content']").val(quill.root.innerHTML);

        // クリアエラー
        if ($("#wiki_title").val()) {
            $("#wiki_title_error").text("");
        }

        if (wiki_content[0].value.length > 0) {
            $("#wiki_content_error").text("");
        }

        var myForm = $("#wiki-form")[0];
        var form = new FormData(myForm);
        var url = $("#wiki-form").attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "POST",
            cache: false,
            data: form,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (response) {
                window.location.href = "/admin/wiki/detail/" + response.id;
            },
            error: function (data) {
                errors = data.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    $("#" + propName + "_error").text(propVal[0]);
                    if (propName == "wiki_path.0") {
                        $("#wiki_path_error").text(propVal[0]);
                    }
                });
            },
            complete: function () {
                is_create = false;
            },
        });
    });

    // ファイルとエラーメッセージをクリア
    $(".clear-btn").on("click", function () {
        $("input[name='wiki_file']").val("");
        $("input[name='wiki_path[]']").val("");
        $("#wiki_path_error").text("");
    });

    // wikiを削除するモーダルを開く
    $(document).on("click", ".wiki-delete-link", function (e) {
        e.preventDefault();

        var id = $(this).data("id");
        $("#wiki_id").val(id);

        $("#js-wiki-delete-modal-btn").val(id);
        $("#js-wiki-delete-modal").addClass("is-visible");
        $(".js-modal-overlay").show();

        $(".js-modal-overlay").on("click", function () {
            $("#js-wiki-delete-modal").removeClass("is-visible");
        });

        $(".js-modal-btn--close").on("click", function () {
            $("#js-wiki-delete-modal").removeClass("is-visible");
        });
    });

    // Wiki 削除
    var is_delete = false;
    $("#wiki-delete-form-data").on("submit", function (e) {
        e.preventDefault();

        // ダブルクリックを防ぐ
        if (is_delete) {
            return;
        }
        is_delete = true;

        var form = new FormData(this);
        var url = $(this).attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: url,
            cache: false,
            data: form,
            processData: false,
            contentType: false,
            success: function (res) {
                window.location.href = "/admin/wiki";
            },
            error: function (res) {
                document.write(res.status);
            },
            complete: function () {
                isDelete = false;
            },
        });
    });
});
