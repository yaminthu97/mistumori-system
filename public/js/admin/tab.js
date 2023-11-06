$(".tab-link li #comment").click(function (e) {
    clearErrorMessage();
    var current_link_href = $(this).attr("href");
    $(".tab-link li #estimate").removeClass("active-link");
    $(".tab-link li #comment").addClass("active-link");

    $("#estimate-content").removeClass("active-content");
    $("#comment-content").addClass("active-content");
    $(current_link_href).show();

    e.preventDefault();
});
$(".tab-link li #estimate").click(function (e) {
    clearCommentErrorMessage();
    var current_link_href = $(this).attr("href");
    $(".tab-link li #comment").removeClass("active-link");
    $(".tab-link li #estimate").addClass("active-link");

    $("#comment-content").removeClass("active-content");
    $("#estimate-content").addClass("active-content");
    $(current_link_href).show();

    e.preventDefault();
});

$(document).ready(function () {
    $(".sorting a").each(function (i, a) {
        $(a).attr("href", $(a).attr("href") + "#tab");
    });
});

var hashlist = window.location.hash;
var list = window.location.search;
if (hashlist == "#tab") {
    setTimeout(() => {
        history.replaceState(
            "",
            document.title,
            window.location.origin +
                window.location.pathname +
                window.location.search
        );
    }, 1);
}
