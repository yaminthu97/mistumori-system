// トグル
function togglePermissionAccess(e) {
    e.preventDefault();
    var $this = $(this);
    $this.toggleClass("active").next().toggle();

    $("body").on("click", function (event) {
        if (!$this.is(event.target) && $this.has(event.target).length === 0) {
            // ダイアログが開いている場合は閉じする
            if ($this.hasClass("active")) {
                $this.removeClass("active").next().toggle();
            }
        }
    });
}

$(function () {
    $(".permission-access-estimate").click(togglePermissionAccess);
});

$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);

    const fromEstimateNoti = urlParams.get("fromEstimateNoti");
    if (fromEstimateNoti === "true") {
        $(".tab-link li #estimate").click();
    }

    const isEstimate = urlParams.get("isEstimate");
    if (isEstimate === "true") {
        // URL には ?isEstimate=true
        $(".tab-link li #comment").removeClass("active-link");
        $(".tab-link li #estimate").addClass("active-link");
        $("#comment-content").removeClass("active-content");
        $("#estimate-content").addClass("active-content");
    }

    // 見積もり/コメントを含むプロジェクトの URL を設定する
    function setEstimateUrl() {
        let url = window.location.href;
        const urlParams = new URLSearchParams(window.location.search);
        const isEstimate = urlParams.get("isEstimate");
        if (isEstimate === "true") {
            // URL には ?isEstimate=true
            window.location.replace(url);
        } else {
            // URL に ?isEstimate=true
            url = fromEstimateNoti ? url.split("#")[0].split("?")[0] : url;
            window.location.replace(url + "?isEstimate=true");
        }
    }

    const dateFormatOptions = {
        weekday: undefined,
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        hour12: true,
    };

    function formatEstimateContent(estimateContent) {
        // 通常のスペースを非改行スペースに置き換える
        var contentWithNbsp = estimateContent.replace(/ /g, "\u00A0");

        // 改行文字を <br> タグに変換する
        var contentWithBr = contentWithNbsp.replace(/\n/g, "<br>");

        return contentWithBr;
    }

    /**
     * 見積データを見積リストに設定する
     *
     * @param data
     */
    function estimateList(data) {
        let htmlView = "";
        var str = data.estimates.estimate_creator_name;
        var firstChar = str.substring(0, 1);

        var bg;
        if (data.estimates.estimate_creator_role == 0) {
            bg = "bg01";
        } else {
            bg = "bg02";
        }

        // ファイル名を抽出する
        const filename = data.estimates.estimation_file_path
            ? data.estimates.estimation_file_path.split("/").pop()
            : "";
        const estimateTexts = {
            en: {
                record: " recorded the estimation",
                estimate: ".",
                PG: "PG man hours",
                BSE: "BSE man hours",
                editLink: "Edit",
                deleteLink: "Delete",
            },
            ja: {
                record: "が",
                estimate: "に見積内容を記録しました。",
                PG: "PG工数（人月）",
                BSE: "BSE工数（人月）",
                editLink: "編集",
                deleteLink: "削除",
            },
        };

        const localeText = estimateTexts[currentLocale];
        htmlView += `
            <div class="estimate-list content-tile auth-user clearfix">
                <div class="avatar ${bg}">
                    <span>${firstChar}</span>
                </div>
                <div class="content-slide">
                    <p class="content-msg"><span class="fc-01">${
                        data.estimates.estimate_creator_name
                    }</span>${localeText.record}<span class="fc-01">${new Date(
            data.estimates.created_at
        ).toLocaleDateString("en-US", dateFormatOptions)}</span>${
            localeText.estimate
        }</p>
                    <div class="content-box">
                        <a class="permission-access-estimate-js"></a>
                        <div class="permission--dialog" id="js-permission-modal">
                            <a class="edit-link estimate-edit-link" data-toggle="c-modal" href="/admin/estimate/get-estimate-data/${
                                data.estimates.id
                            }" data-id=${data.estimates.id}>${
            localeText.editLink
        }</a>
                            <a href="{{ route('admin.estimate.delete', ['id' => ${
                                data.estimates.id
                            }]) }}" class="delete-link estimate-delete-link" data-id="${
            data.estimates.id
        }}">${localeText.deleteLink}</a>
                        </div>
                        <p>${formatEstimateContent(
                            data.estimates.estimation_content
                        )}</p>
                        <p>${localeText.PG} - ${
            data.estimates.PG_man_months
        }</p>
                        <p class="mb-0">${localeText.BSE} - ${
            data.estimates.BSE_man_months
        }</p>
                        <a href="/admin/estimate/${
                            data.estimates.id
                        }/download/${filename}" class="a-link js-system_overview-download">${filename}</a>
                    </div>
                </div>
        </div>`;
        $("#estimate-list").append(htmlView);
        $(".permission-access-estimate-js").click(togglePermissionAccess);

        $("#assignee_name_detail").text(data.estimates.estimate_assignee_name);
        $("#status_detail").text(PROJECT_STATUS[data.estimates.status]);
    }

    $(".js_input_edit").on("change", function () {
        var file = $(this).prop("files")[0];
        file
            ? $(this).siblings("input").val(file.name)
            : $(this).siblings("input").val("");
    });

    $(".js_input").on("change", function () {
        var file = $(this).prop("files")[0];
        file
            ? $(this).siblings("input").val(file.name)
            : $(this).siblings("input").val("");
    });

    // 見積を作成する
    var isCreate = false;
    $("#estimate-form-data").on("submit", function (e) {
        e.preventDefault();
        clearErrorMessage();

        if (isCreate) {
            return;
        }
        isCreate = true;

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
            mimeType: "multipart/form-data",
            processData: false,
            contentType: false,
            success: function (data) {
                // 検証エラーメッセージをクリアする
                $("#estimate-content-error").text("");
                $("#estimation-file-error").text("");
                $("#PG-man-months-error").text("");
                $("#BSE-man-months-error").text("");
                $("#estimate-assignee-error").text("");
                $("#status-error").text("");
                $("#estimate-form-data")[0].reset();
                estimateList(JSON.parse(data));
            },
            error: function (data) {
                var errors = JSON.parse(data.responseText).errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "estimation_content") {
                        $("#estimate-content-error").text(propVal[0]);
                    } else if (propName == "PG_man_months") {
                        $("#PG-man-months-error").text(propVal[0]);
                    } else if (propName == "BSE_man_months") {
                        $("#BSE-man-months-error").text(propVal[0]);
                    } else if (propName == "estimate_assignee") {
                        $("#estimate-assignee-error").text(propVal[0]);
                    } else if (propName == "status") {
                        $("#status-error").text(propVal[0]);
                    }
                });
            },
            complete: function () {
                isCreate = false;
            },
        });
    });

    // 「キャンセル」をクリックするとフォームがリセットされる
    $(document).on("click", "#js-cancel", function (e) {
        e.preventDefault();
        clearInputValues();
        clearErrorMessage();
    });

    // ファイルをクリアする
    $(document).on("click", ".clear-btn", function (e) {
        e.preventDefault();
        clearFile();
    });

    // 見積編集モーダルを開く
    $(document).on("click", ".estimate-edit-link", function (e) {
        e.preventDefault();
        clearInputValues();
        clearErrorMessage();

        let id = $(this).data("id");
        $("#estimate-id-edit").val(id);

        var url = $(this).attr("href");
        var form = $(this).serialize();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "GET",
            data: form,
            cache: false,
            dataType: "json",
            success: function (data) {
                if (!data.success) {
                    return false;
                }
                var estimationFileEdit = data.estimates.estimation_file_path;
                $("#estimate-id-edit").val(data.estimates.id);
                $("#estimate-content-edit").val(
                    data.estimates.estimation_content
                );
                $("#estimation-file-edit").val(
                    estimationFileEdit
                        ? estimationFileEdit.split("/").pop()
                        : ""
                );
                $("#PG-man-months-edit").val(data.estimates.PG_man_months);
                $("#BSE-man-months-edit").val(data.estimates.BSE_man_months);
                $("#estimate-assignee-edit").val(data.estimates.user_id);
                $("#status-edit").val(data.estimates.status);

                $("#js-estimate-edit-modal").addClass("is-visible");
                $(".js-modal-overlay").show();

                $(".js-modal-overlay").on("click", function () {
                    $("#js-estimate-edit-modal").removeClass("is-visible");
                });

                $(".js-modal-btn--close").on("click", function () {
                    $("#js-estimate-edit-modal").removeClass("is-visible");
                });
            },
        });
    });

    // 見積を編集する
    $("#estimate-edit-form-data").on("submit", function (e) {
        e.preventDefault();

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
            mimeType: "multipart/form-data",
            processData: false,
            contentType: false,
            success: function (res) {
                res = JSON.parse(res);
                if (!res.success) document.write(res.message);
                else setEstimateUrl();
            },
            error: function (res) {
                var errors = JSON.parse(res.responseText).errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "estimation_content") {
                        $("#estimate-content-edit-error").text(propVal[0]);
                    } else if (propName == "PG_man_months") {
                        $("#PG-man-months-edit-error").text(propVal[0]);
                    } else if (propName == "BSE_man_months") {
                        $("#BSE-man-months-edit-error").text(propVal[0]);
                    } else if (propName == "estimate_assignee") {
                        $("#estimate-assignee-edit-error").text(propVal[0]);
                    } else if (propName == "status") {
                        $("#status-edit-error").text(propVal[0]);
                    }
                });
            },
        });
    });

    // 見積削除モーダルを開く
    $(document).on("click", ".estimate-delete-link", function (e) {
        e.preventDefault();
        clearInputValues();
        clearErrorMessage();

        var id = $(this).data("id");
        $("#estimate_id").val(id);

        $("#js-estimate-delete-modal-btn").val(id);
        $("#js-estimate-delete-modal").addClass("is-visible");
        $(".js-modal-overlay").show();

        $(".js-modal-overlay").on("click", function () {
            $("#js-estimate-delete-modal").removeClass("is-visible");
        });

        $(".js-modal-btn--close").on("click", function () {
            $("#js-estimate-delete-modal").removeClass("is-visible");
        });
    });

    // 見積を削除する
    var isDelete = false;
    $("#estimate-delete-form-data").on("submit", function (e) {
        e.preventDefault();

        if (isDelete) {
            return;
        }

        isDelete = true;

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
                if (!res.success) document.write(res.message);
                else setEstimateUrl();
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

// エラーメッセージをクリアする
function clearErrorMessage() {
    $("#estimate-content-error").text("");
    $("#estimation-file-error").text("");
    $("#PG-man-months-error").text("");
    $("#BSE-man-months-error").text("");
    $("#estimate-assignee-error").text("");
    $("#status-error").text("");

    $("#estimate-content-edit-error").text("");
    $("#estimation-file-edit-error").text("");
    $("#PG-man-months-edit-error").text("");
    $("#BSE-man-months-edit-error").text("");
    $("#estimate-assignee-edit-error").text("");
    $("#status-edit-error").text("");
}

// 入力値をクリアする
function clearInputValues() {
    $("#estimate-content-field").val("");
    $("#estimation-file").val("");
    $("#estimation-file-path").val("");
    $("#PG-man-months-field").val("");
    $("#BSE-man-months-field").val("");
    $("#estimate-assignee-field").val("");
    $("#status-field").val("");
}

// ファイルの価値クリアする
function clearFile() {
    $("#estimation-file").val("");
    $("#estimation-file-path").val("");
    $("#estimation-file-edit").val("");
    $("#estimation-file-path_edit").val("");
}

// 現在の URL を取得する
var currentUrl = window.location.href;
var urlParams = new URLSearchParams(new URL(currentUrl).search);
var sortingActive = urlParams.get("sort") !== null;
var moreNum = 3;

if (!sortingActive) {
    $(
        "#estimate-list .content-tile:nth-last-child(n + " + (moreNum + 1) + ")"
    ).addClass("is-hidden");
    $(".more-btn").on("click", function () {
        var hiddenComments = $("#estimate-list .content-tile.is-hidden");
        var numToShow = Math.min(moreNum, hiddenComments.length);
        hiddenComments.slice(-numToShow).removeClass("is-hidden");
        if ($("#estimate-list .content-tile.is-hidden").length === 0) {
            $(".more-btn").fadeOut();
        }
    });
}
