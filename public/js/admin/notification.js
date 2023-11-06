/**
 * Open sidebar when click notification
 */
function openNav() {
    $("#noti-side-panel").toggle();
}

/**
 * Close sidebar when click notification
 */
function closeNav() {
    $("#noti-side-panel").css("display", "none");
}

/**
 * Close sidebar when click any place
 */
$(document).on("click", ".c-sidebar, .c-main", function () {
    closeNav();
});

var data_exists = false;

$(document).ready(function () {
    $(".noti-img").on("click", function () {
        data_exists ? openNav() : closeNav();
    });
    notificationList();
    // プッシャーJSライブラリを開始
    var pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_APP_CLUSTER,
        encrypted: true,
    });

    // イベントに関数をバインドしますLaravelクラスの完全なクラス
    var channel = pusher.subscribe("notification-channel");

    channel.bind("notification-event", function (data) {
        notificationList();
    });
    /**
     * IDを備えたプロジェクトの詳細ページと問い合わせの詳細ページを表示
     */
    $(document).on("click", ".notification-list", function () {
        let noti_id = $(this).attr("data-id");
        var readAt_url = "/admin/markAsRead/" + noti_id;
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: readAt_url,
            type: "GET",
            cache: false,
            dataType: "json",
            success: function (response) {
                routeNotification(response);
            },
            error: function (error) {
                console.log(error);
            },
        });
    });

    // プッシャーJSライブラリを開始
    var pusher = new Pusher(PUSHER_KEY, {
        cluster: PUSHER_APP_CLUSTER,
        encrypted: true,
    });

    // イベントに関数をバインドしますLaravelクラスの完全なクラス
    var channel = pusher.subscribe("notification-channel");

    channel.bind("notification-event", function (data) {
        notificationList();
    });
});

/**
 * サイドバーに通知リストを表示
 *
 * @param res
 */
function notificationDataList(res) {
    var notification_id = res["id"];
    let parsed_data = JSON.parse(res.data);
    var background_color = res.read_at === null ? "#cbcbcb" : "#f5f5f5";
    var darkmode = document.documentElement.classList.contains("dark");
    if (darkmode) {
        var background_color = res.read_at === null ? "#0f172a" : "#1E293B";
    }
    var html_view = `
    <div class="notification-list" data-id="${notification_id}" style="background-color: ${background_color}">
      <div class="notification-list-col1">
        <img src="${"/img/admin/common/icons8-male-user.png"}" class="user-icon" alt="Notification_Bell">
      </div>
      <div class="notification-list-col2">
        <p class="noti-span">
          <span id="created_user_name"><strong>${
              res.category === PROJECT_CREATE || res.category === PROJECT_UPDATE
                  ? res.category === PROJECT_CREATE
                      ? parsed_data.created_user_name
                      : parsed_data.updated_user_name
                  : parsed_data.created_user_name
          }</strong></span>
          <span>が</span>
          <span>${showTest(res.category)}</span>
        </p>
        <p>${
            res.category === PROJECT_CREATE || res.category === PROJECT_UPDATE
                ? parsed_data.project_name
                : ""
        }</p>
        <p class="p-mention-user" id="assignee_name"><span>@</span>${
            parsed_data.assignee_name
        }</p>
        <p class="noti-content">${
            parsed_data.comment_content ? parsed_data.comment_content : ""
        }</p>
      </div>
      <div class="notification-list-col3">
        <div class="created-time"><span>${timeAgo(res.created_at)}</span></div>
        <div>
          <button class="proj-progress-btn">${
              STATUS[parsed_data.status]
          }</button>
        </div>
      </div>
    </div>`;
    // html_viewコンテンツを通知リスト要素に追加
    $("#notification-list").append(html_view);
}

/**
 * 入力DateTimeをローカルデータタイムに変更し、どのタイミアゴを表示
 *
 * @param date_string
 */
function timeAgo(date_string) {
    const current_time = luxon.DateTime.local();
    const input_time = luxon.DateTime.fromSQL(date_string, {
        zone: "Asia/Tokyo",
    }).setZone(TIMEZONE["time_zone"]);

    const time_difference = current_time.diff(input_time);
    var minutes = time_difference.as("minutes");
    var hours = time_difference.as("hours");
    var days = time_difference.as("days");
    minutes = `${Math.floor(minutes)}`;
    hours = `${Math.floor(hours)}`;
    days = `${Math.floor(days)}`;

    if (minutes < 1) {
        return "Just now";
    } else if (minutes == 1) {
        return "1 min ago";
    } else if (minutes < 60) {
        return `${minutes} mins ago`;
    } else if (hours == 1) {
        return "1 hr ago";
    } else if (hours < 24) {
        return `${hours} hrs ago`;
    } else if (days == 1) {
        return "1 day ago";
    } else if (days <= 5) {
        return `${days} days ago`;
    } else {
        return input_time.toFormat("MMM dd yyyy");
    }
}

/**
 * すべての通知データを取得
 */
function notificationList() {
    var noti_url = "/admin/notification";
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: noti_url,
        type: "GET",
        cache: false,
        dataType: "json",
        success: function (response) {
            $("#notification-list").empty();
            response[0].forEach((res) => {
                notificationDataList(res);
            });
            if (response[0].length) data_exists = true;
            var count = response[1].length;
            if (count > 0) {
                var noti_icon = $(".notification-icon .noti");
                if (noti_icon.length === 0) {
                    noti_icon = $(`<div class="noti">
                        <span id="noti"></span>
                      </div>`);
                    $(".notification-icon").append(noti_icon);
                }
                data_exists = true;
                count > 99 ? noti_icon.text("99+") : noti_icon.text(count);
            }
        },
        error: function (error) {
            console.log(error);
        },
    });
}

function showTest(category) {
    let text = "";

    switch (category) {
        case PROJECT_CREATE:
            text = CREATE_TEXT;
            break;
        case PROJECT_UPDATE:
            text = UPDATE_TEXT;
            break;
        case QUESTION_CREATE:
            text = "質問を" + CREATE_TEXT;
            break;
        case QUESTION_UPDATE:
            text = "質問を" + UPDATE_TEXT;
            break;
        case COMMENT_CREATE:
            text = "コメントを" + CREATE_TEXT;
            break;
        case COMMENT_EDIT:
            text = "コメントを" + UPDATE_TEXT;
            break;
        case ESTIMATE_CREATE:
            text = "見積内容を" + CREATE_TEXT;
            break;
        case ESTIMATE_UPDATE:
            text = "見積内容を" + UPDATE_TEXT;
            break;
        case ANSWER_CREATE:
            text = "回答を" + CREATE_TEXT;
            break;
        case ANSWER_UPDATE:
            text = "回答を" + UPDATE_TEXT;
            break;
    }

    return text;
}

function routeNotification(response) {
    if (
        response.category === PROJECT_CREATE ||
        response.category === PROJECT_UPDATE
    ) {
        window.location.href = "/admin/project/detail/" + response.category_id;
    } else if (
        response.category === QUESTION_CREATE ||
        response.category === QUESTION_UPDATE
    ) {
        window.location.href = "/admin/inquiry/detail/" + response.category_id;
    } else if (
        response.category === COMMENT_CREATE ||
        response.category === COMMENT_EDIT
    ) {
        let commentId = `comment-${response.category_id}`;
        let currentUrl = window.location.href;

        if (
            currentUrl.includes(
                `/admin/project/detail/${response.data.project_id}`
            )
        ) {
            let nextUrl = `/admin/project/detail/${response.data.project_id}#${commentId}`;
            const nextState = {
                additionalInformation: "Updated the URL with JS",
            };
            window.history.pushState(nextState, "", nextUrl);
            window.location.reload();
        } else {
            window.location.href = `/admin/project/detail/${response.data.project_id}#${commentId}`;
        }
    } else if (
        response.category === ESTIMATE_CREATE ||
        response.category === ESTIMATE_UPDATE
    ) {
        let estimateId = `estimate-${response.category_id}`;
        let currentUrl = window.location.href;

        if (
            currentUrl.includes(
                `/admin/project/detail/${response.data.project_id}`
            )
        ) {
            let nextUrl = `/admin/project/detail/${response.data.project_id}?fromEstimateNoti=true#${estimateId}`;
            const nextState = {
                additionalInformation: "Updated the URL with JS",
            };
            window.history.pushState(nextState, "", nextUrl);
            window.location.reload();
        } else {
            window.location.href = `/admin/project/detail/${response.data.project_id}?fromEstimateNoti=true#${estimateId}`;
        }
    } else if (
        response.category === ANSWER_CREATE ||
        response.category === ANSWER_UPDATE
    ) {
        let answerId = `answer-${response.category_id}`;
        let currentUrl = window.location.href;

        if (
            currentUrl.includes(
                `/admin/inquiry/detail/${response.data.inquiry_id}`
            )
        ) {
            let nextUrl = `/admin/inquiry/detail/${response.data.inquiry_id}#${answerId}`;
            const nextState = {
                additionalInformation: "Updated the URL with JS",
            };
            window.history.pushState(nextState, "", nextUrl);
            window.location.reload();
        } else {
            window.location.href = `/admin/inquiry/detail/${response.data.inquiry_id}#${answerId}`;
        }
    }
}
