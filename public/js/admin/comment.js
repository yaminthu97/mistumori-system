// トグル
function togglePermissionAccess(e) {
    e.preventDefault();
    var $this = $(this);
    $this.toggleClass("active").next().toggle();

    $("body").on("click", function (event) {
        if (!$this.is(event.target) && $this.has(event.target).length === 0) {
            // ダイアログが開いている場合は閉じる
            if ($this.hasClass("active")) {
                $this.removeClass("active").next().toggle();
            }
        }
    });
}

$(function () {
    $(".permission-access").click(togglePermissionAccess);
});

// Ajaxを使用したテーブル行
function commentList(data) {
    const dateFormatOptions = {
        weekday: undefined,
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        hour12: true,
    };
    function formatCommentContent(commentContent) {
        // 通常のスペースを非改行スペースに置き換える
        var contentWithNbsp = commentContent.replace(/ /g, "\u00A0");

        // 改行文字を <br> タグに変換する
        var contentWithBr = contentWithNbsp.replace(/\n/g, "<br>");

        return contentWithBr;
    }

    var str = data.comments.comment_creator_name;
    var firstChar = str.substring(0, 1);

    var bg;
    if (data.comments.comment_creator_role == 0) {
        bg = "bg01";
    } else {
        bg = "bg02";
    }

    const commentTexts = {
        en: {
            comment: "commented",
            commented: ".",
            editLink: "Edit",
            deleteLink: "Delete",
        },
        ja: {
            comment: "が",
            commented: "にコメントしました。",
            editLink: "編集",
            deleteLink: "削除",
        },
    };

    const localeText = commentTexts[currentLocale];

    let htmlView = "";
    htmlView +=
        `
        <div class="comment-list content-tile auth-user clearfix" id="comment-${data.comments.id}">
            <div class="avatar ${bg}"><span>` +
        firstChar +
        `</span></div>
            <div class="content-slide">
                <p class="content-msg"><span class="fc-01"> ` +
        data.comments.comment_creator_name +
        `</span>${localeText.comment}<span class="fc-01"> ` +
        new Date(data.comments.created_at).toLocaleDateString(
            "en-US",
            dateFormatOptions
        ) +
        ` </span>${localeText.commented}</p>
                <div class="content-box">
                    <a class="permission-access-js">
                    </a>
                    <div class="permission--dialog" id="js-permission-modal">
                        <a href="{{ route('admin.comment.edit', ['id' => ` +
        data.comments.id +
        `]) }} " class="edit-link comment-edit-link"
                        data-toggle="c-modal"
                        data-target="#js-comment-edit-modal"
                        data-id="${data.comments.id}"
                        data-comment_assignee="${data.comments.comment_assignee}"
                        data-comment_assignee_name="${data.comments.comment_assignee_name}"
                        data-comment_content="${data.comments.comment_content}">${localeText.editLink}</a>
                        <a href="{{ route('admin.comment.delete', ['id' => ` +
        data.comments.id +
        `]) }} " class="delete-link comment-delete-link" data-id="${data.comments.id}">${localeText.deleteLink}</a>
                    </div>
                    <p class="mentioned-person"><span>@</span>` +
        data.comments.comment_assignee_name +
        `</p>
                    <p>` +
        formatCommentContent(data.comments.comment_content) +
        `</p>
                </div>
            </div>
        </div>`;
    $("#comment-list").append(htmlView);
    $(".permission-access-js").click(togglePermissionAccess);
}

$(document).ready(function () {
    // コメントを作成する
    var isCommentCreate = false;
    $("#comment-form-data").on("submit", function (e) {
        e.preventDefault();
        clearCommentErrorMessage();

        if (isCommentCreate) {
            return;
        }

        isCommentCreate = true;

        var form = $(this).serialize();
        var url = $(this).attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "POST",
            cache: false,
            data: form,
            dataType: "json",
            success: function (data) {
                // 検証エラーメッセージをクリアする
                $("#comment-assignee-error").text("");
                $("#comment-content-error").text("");
                $("#comment-form-data")[0].reset();
                commentList(data);
            },
            error: function (data) {
                var errors = data.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "comment_assignee") {
                        $("#comment-assignee-error").text(propVal[0]);
                    } else {
                        $("#comment-content-error").text(propVal[0]);
                    }
                });
            },
            complete: function () {
                isCommentCreate = false;
            },
        });
    });

    // 「キャンセル」をクリックするとフォームがリセットされる
    $(document).on("click", "#js-cancel", function (e) {
        e.preventDefault();
        clearCommentInputValues();
        clearCommentErrorMessage();
    });

    // コメント編集モーダルを開く
    $(document).on("click", ".comment-edit-link", function (e) {
        e.preventDefault();
        clearCommentErrorMessage();
        clearCommentErrorMessage();

        let id = $(this).data("id");

        var commentAssigneeName = $(this).data("comment_assignee");
        var commentContent = $(this).data("comment_content");

        let dataAction = $(this).data("action");
        $("#comment-edit-form-data").attr("action", dataAction);

        $("#comment-assignee-edit").val(commentAssigneeName);
        $("#comment-id-edit").val(id);
        $("#comment-content-edit").val(commentContent);

        $("#js-comment-edit-modal").addClass("is-visible");
        $(".js-modal-overlay").show();

        $(".js-modal-overlay").on("click", function () {
            $("#js-comment-edit-modal").removeClass("is-visible");
        });

        $(".js-modal-btn--close").on("click", function () {
            $("#js-comment-edit-modal").removeClass("is-visible");
        });
    });

    // コメントを編集する
    $(document).on("click", "#js-comment-edit-submit", function (e) {
        var form = $("#comment-edit-form-data").serialize();
        var url = $(this).data("url");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: url,
            data: form,
            success: function (res) {
                if (!res.success) {
                    document.write(res.message);
                    return false;
                }
                const url = window.location.href;
                const urlParams = new URLSearchParams(window.location.search);
                const isEstimate = urlParams.get("isEstimate");

                if (isEstimate === "true") {
                    const uRL = new URL(url);
                    const baseUrl = uRL.origin + uRL.pathname;
                    window.location.replace(baseUrl);
                } else {
                    location.reload();
                }
            },
            error: function (res) {
                var errors = res.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "comment_assignee") {
                        $("#comment-assignee-edit-error").text(propVal[0]);
                    } else {
                        $("#comment-content-edit-error").text(propVal[0]);
                    }
                });
            },
        });
    });

    //　コメントを削除する
    $(document).on("click", ".comment-delete-link", function (e) {
        e.preventDefault();
        clearCommentInputValues();
        clearCommentErrorMessage();

        var id = $(this).data("id");
        $("#comment-id").val(id);

        $("#js-comment-delete-modal-btn").val(id);
        $("#js-comment-delete-modal").addClass("is-visible");
        $(".js-modal-overlay").show();

        $(".js-modal-overlay").on("click", function () {
            $("#js-comment-delete-modal").removeClass("is-visible");
        });

        $(".js-modal-btn--close").on("click", function () {
            $("#js-comment-delete-modal").removeClass("is-visible");
        });
    });
});

function clearCommentErrorMessage() {
    $("#comment-assignee-error").text("");
    $("#comment-content-error").text("");
    $("#comment-assignee-edit-error").text("");
    $("#comment-content-edit-error").text("");
}

// 入力値をクリアする
function clearCommentInputValues() {
    $("#comment-assignee-field").val("");
    $("#comment-content-field").val("");
}

// 現在の URL を取得する
var currentUrl = window.location.href;
var urlParams = new URLSearchParams(new URL(currentUrl).search);
var sortingActive = urlParams.get("sort") !== null;
var moreNum = 3;

if (!sortingActive) {
    $(
        "#comment-list .content-tile:nth-last-child(n + " + (moreNum + 1) + ")"
    ).addClass("is-hidden");
    $(".more").on("click", function () {
        var hiddenComments = $("#comment-list .content-tile.is-hidden");
        var numToShow = Math.min(moreNum, hiddenComments.length);
        hiddenComments.slice(-numToShow).removeClass("is-hidden");
        if ($("#comment-list .content-tile.is-hidden").length === 0) {
            $(".more").fadeOut();
        }
    });
}
