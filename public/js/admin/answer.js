$(document).ready(function () {
    const dateFormatOptions = {
        weekday: undefined,
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        hour12: true,
    };
    function formatAnswerContent(answerContent) {
        // 通常のスペースを非改行スペースに置き換える
        var contentWithNbsp = answerContent.replace(/ /g, "\u00A0");

        // 改行文字を <br> タグに変換する
        var contentWithBr = contentWithNbsp.replace(/\n/g, "<br>");

        return contentWithBr;
    }

    function answerList(data) {
        var editLink = $("#js-btn-submit");
        if (data.answers.inquiry_status != INQUIRY_NOT_STARTED) {
            editLink.addClass("edit-link-disable");
        } else {
            editLink.removeClass("edit-link-disable");
        }

        const answerTexts = {
            en: {
                answer: "answered",
                answered: ".",
                editLink: "Edit",
                deleteLink: "Delete",
            },
            ja: {
                answer: "が",
                answered: "に回答しました。",
                editLink: "編集",
                deleteLink: "削除",
            },
        };

        const localeText = answerTexts[currentLocale];

        let htmlView = `
					<div class="comment-list clearfix" id="comment-each-list" data-answer-id=${
                        data.answers.id
                    }>
                        <a id="answer-${data.answers.id}"></a>
						<div class="comment-list-col1">
							<p>${data.answers.answer_creator}
								<span>${localeText.answer}</span>
								<span id="create-edit-time">
									${new Date(data.answers.created_at).toLocaleDateString(
                                        "en-US",
                                        dateFormatOptions
                                    )}
								</span>
								<span>${localeText.answered}</span>
							</p>
                            <p id="answer-content" class="txt-overview u-mr20">${formatAnswerContent(
                                data.answers.answer_content
                            )}</p>
						</div>
						<div class="comment-list-col2">
							<a class="answer-edit-link" data-toggle="c-modal"
                                href="/admin/answer/get-answer-data/${
                                    data.answers.id
                                }"
                                data-id=${data.answers.id}
							>${localeText.editLink}</a>
							<a class="answer-delete-link"
                                data-id=${data.answers.id}
                                data-created_user_id=${
                                    data.answers.created_user_id
                                }>${localeText.deleteLink}</a>
						</div>
					</div>
				`;
        $("#comment-list").append(htmlView);
        $("#question_assignee_name_detail").text(
            data.answers.question_assignee_name
        );
        $(".answer-assignee-field").val(data.answers.question_assignee);
        $("#inquiry_status").text(data.answers.inquiry_status_text);
    }

    // 答えを作成する
    var isSubmit = false;
    $("#answer-form-data").on("submit", function (e) {
        e.preventDefault();

        if (isSubmit) {
            return;
        }

        isSubmit = true;

        let answerContent = $("#ac").val().length < 1000 ? $("#ac").val() : "";
        let inquiryStatus = $("#is").val();
        let questionAssignee = $("#aa").val();

        if (answerContent) {
            $("#answer-content-error").text("");
        }

        if (inquiryStatus) {
            $("#inquiry-status-error").text("");
        }

        if (questionAssignee) {
            $("#question-assignee-error").text("");
        }

        var answerData = $(this).serialize();
        var url = $(this).attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "POST",
            cache: false,
            data: answerData,
            dataType: "json",
            success: function (data) {
                $("#answer-content-error").text("");
                $("#answer-form-data")[0].reset();
                answerList(data);
                console.log("success");
                $(this).data("submitted", true);
            },
            error: function (data) {
                errors = data.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "answer_content") {
                        $("#answer-content-error").text(propVal[0]);
                    } else if (propName == "inquiry_status") {
                        $("#inquiry-status-error").text(propVal[0]);
                    } else if (propName == "question_assignee") {
                        $("#question-assignee-error").text(propVal[0]);
                    } else {
                        $("#question-assignee-error").text(propVal[0]);
                    }
                });
            },
            complete: function () {
                isSubmit = false;
            },
        });
    });

    function clearErrorMessage() {
        $("#answer-content-error").text("");
        $("#inquiry-status-error").text("");
        $("#question-assignee-error").text("");
        $("#answer_content_edit_error").text("");
        $("#inquiry_status_edit_error").text("");
        $("#question_assignee_edit_error").text("");
    }

    // 回答編集モーダルを開く
    $(document).on("click", ".answer-edit-link", function (e) {
        e.preventDefault();
        clearErrorMessage();

        var url = $(this).attr("href");
        const id = $(this).data("id");

        $("#answer_id").val(id);

        var answerId = $(this).serialize();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            type: "GET",
            data: answerId,
            cache: false,
            dataType: "json",
            success: function (data) {
                $("#answer_id").val(data.answers.id);
                $("#inquiry_id").val(data.answers.question_id);
                $("#created_user_id").val(data.answers.created_user_id);
                $("#answer_content_edit").val(data.answers.answer_content);
                $("#inquiry_status_edit").val(data.answers.inquiry_status);
                $("#question_assignee_edit").val(
                    data.answers.question_assignee_number
                );

                $("#js-answer-edit-modal").addClass("is-visible");
                $(".js-modal-overlay").show();

                $(".js-modal-overlay").on("click", function () {
                    $("#js-answer-edit-modal").removeClass("is-visible");
                });

                $(".js-modal-btn--close").on("click", function () {
                    $("#js-answer-edit-modal").removeClass("is-visible");
                });
            },
        });
    });

    // 答えを編集する
    $(document).on("click", "#js-answer-edit-submit", function (e) {
        e.preventDefault();

        let answerContentEdit =
            $("#answer_content_edit").val().length < 1000
                ? $("#answer_content_edit").val()
                : "";
        let inquiryStatusEdit = $("#inquiry_status_edit").val();
        let questionAssigneeEdit = $("#question_assignee_edit").val();

        if (answerContentEdit) {
            $("#answer_content_edit_error").text("");
        }

        if (inquiryStatusEdit) {
            $("#inquiry_status_edit_error").text("");
        }

        if (questionAssigneeEdit) {
            $("#question_assignee_edit_error").text("");
        }

        var answerData = $("#answer-edit-form-data").serialize();
        var url = $("#answer-edit-form-data").attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: url,
            data: answerData,
            success: function (res) {
                if (!res.success) {
                    document.write(res.message);
                    return false;
                }
                onEditSuccess(res);
                closeModal();
            },
            error: function (res) {
                var errors = res.responseJSON.errors;
                $.each(errors, function (propName, propVal) {
                    if (propName == "answer_content") {
                        $("#answer_content_edit_error").text(propVal[0]);
                    } else if (propName == "inquiry_status") {
                        $("#inquiry_status_edit_error").text(propVal[0]);
                    } else {
                        $("#question_assignee_edit_error").text(propVal[0]);
                    }
                });
            },
        });
    });

    // 回答削除モーダルを開く
    $(document).on("click", ".answer-delete-link", function (e) {
        e.preventDefault();

        const id = $(this).data("id");
        const createdUserId = $(this).data("created_user_id");

        $("#aID").val(id);
        $("#cuID").val(createdUserId);

        $("#js-answer-delete-modal").addClass("is-visible");
        $(".js-modal-overlay").show();

        $(".js-modal-overlay").on("click", function () {
            $("#js-answer-delete-modal").removeClass("is-visible");
        });

        $(".js-modal-btn--close").on("click", function () {
            $("#js-answer-delete-modal").removeClass("is-visible");
        });
    });

    // 答えを削除する
    var isDelete = false;
    $(document).on("click", "#js-answer-delete-modal-btn", function (e) {
        e.preventDefault();

        if (isDelete) {
            return;
        }

        isDelete = true;

        var answerData = $("#answer-delete-form-data").serialize();
        var url = $("#answer-delete-form-data").attr("action");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: url,
            data: answerData,
            success: function (res) {
                if (!res.success) {
                    document.write(res.message);
                    return false;
                }
                $(
                    '#comment-each-list[data-answer-id="' + res.id + '"]'
                ).remove();
                closeModal();
            },
            complete: function () {
                isDelete = false;
            },
        });
    });

    // モーダルボックスを閉じる
    function closeModal() {
        $(".js-modal").removeClass("is-visible");
        $(".js-modal-overlay").hide();
    }

    function onEditSuccess(data) {
        var editLink = $("#js-btn-submit");
        if (data.answers.inquiry_status != INQUIRY_NOT_STARTED) {
            editLink.addClass("edit-link-disable");
        } else {
            editLink.removeClass("edit-link-disable");
        }

        var contentElement = $(
            '#comment-each-list[data-answer-id="' +
                data.answers.id +
                '"] #answer-content'
        );
        contentElement.html(formatAnswerContent(data.answers.answer_content));

        var timeElement = $(
            '#comment-each-list[data-answer-id="' +
                data.answers.id +
                '"] #create-edit-time'
        );
        timeElement.text(
            new Date(data.answers.created_at).toLocaleDateString(
                "en-US",
                dateFormatOptions
            )
        );

        $("#question_assignee_name_detail").text(
            data.answers.question_assignee_name
        );
        $(".answer-assignee-field").val(data.answers.question_assignee);
        $("#inquiry_status").text(data.answers.inquiry_status_text);
    }

    // キャンセルボタンでフォームをクリアする
    $(document).on("click", "#js-cancel", function (e) {
        e.preventDefault();
        $("#answer-form-data")[0].reset();
        clearErrorMessage();
    });
});
