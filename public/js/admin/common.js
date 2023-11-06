// 変数セット
const width = window.innerWidth;
const scrollPos = $(window).scrollTop();
const breakPoint = 767.9;

var PROJECT = PROJECT || {};
PROJECT.COMMON = {};

// 呼び出し
document.addEventListener(
    "DOMContentLoaded",
    function () {
        // 共通の処理
        PROJECT.COMMON.smoothScroll.const(); // スムーススクロール
        PROJECT.COMMON.tab.const(); // タブ
        PROJECT.COMMON.modal.const(); // モーダル
        PROJECT.COMMON.accordion.const(); // アコーディオン
        PROJECT.COMMON.ripple.const(); // リップル
        PROJECT.COMMON.datepicker.const(); // デイトピッカー
        PROJECT.COMMON.file.const(); // ファイルアップロードのスタイル調整

        PROJECT.COMMON.duplicatePulldown.const();
        PROJECT.COMMON.switchSelect.const();

        PROJECT.COMMON.addItem.const();

        PROJECT.COMMON.infoToggle.const();

        PROJECT.COMMON.inviteNav.const();

        PROJECT.COMMON.selectPlaceholder.const();

        PROJECT.COMMON.disabled.const();

        PROJECT.COMMON.zip.const();
    },
    false
);

// 共通機能
// スムーススクロール
(PROJECT.COMMON.smoothScroll = {
    const: function () {
        // URLのハッシュ値を取得
        var urlHash = location.hash;
        // ハッシュ値があればページ内スクロール
        if (urlHash) {
            // スクロールを0に戻す
            $("body,html").stop().scrollTop(0);
            setTimeout(function () {
                // ロード時の処理を待ち、時間差でスクロール実行
                scrollToAnker(urlHash);
            }, 100);
        }
        // 通常のクリック時
        $("body").on("click", 'a[href^="#"]', function (event) {
            event.preventDefault();
            // ページ内リンク先を取得
            var href = $(this).attr("href");
            //リンク先が#か空だったらhtmlに
            var hash = href == "#" || href == "" ? "html" : href;
            //スクロール実行
            scrollToAnker(hash);
            //リンク無効化
            return false;
        });
        // 関数：スムーススクロール
        // 指定したアンカー(#ID)へアニメーションでスクロール
        function scrollToAnker(hash) {
            var target = $(hash);
            var position = target.offset().top - $("header").innerHeight();
            $("body,html").stop().animate({ scrollTop: position }, 600);
        }
        // IEのガタツキを回避する
        if (
            navigator.userAgent.match(/MSIE 10/i) ||
            navigator.userAgent.match(/Trident\/7\./) ||
            navigator.userAgent.match(/Edge\/12\./)
        ) {
            $("body").on("mousewheel", function () {
                event.preventDefault();
                var wd = event.wheelDelta;
                var csp = window.pageYOffset;
                window.scrollTo(0, csp - wd);
            });
        }
    },
}),
    // 汎用型アコーディオン
    (PROJECT.COMMON.accordion = {
        const: function () {
            $("body").on("click", ".js-multitoggle-btn", function () {
                var toggleID = $(this).attr("data-toggle");
                $("#js-" + toggleID).slideToggle();
                $(this).toggleClass("is-active");
            });
        },
    }),
    // 汎用型タブ
    (PROJECT.COMMON.tab = {
        const: function () {
            if ($(".js-tabs").length > 0) {
                var activeIndex = $(".is-active").index(
                    ".js-tabs .js-tabs-item"
                );
                $(".js-tab-wrap")
                    .children(".js-tab-box")
                    .eq(activeIndex)
                    .css("display", "block");
                $(".js-tabs .js-tabs-item").on("click", function () {
                    var standardEl = $(this).parent(".js-tabs");
                    var index = standardEl
                        .children(".js-tabs-item")
                        .index(this);
                    standardEl
                        .next(".js-tab-wrap")
                        .children(".js-tab-box")
                        .css("display", "none");
                    standardEl
                        .next(".js-tab-wrap")
                        .children(".js-tab-box")
                        .eq(index)
                        .css("display", "block");
                    standardEl
                        .children(".js-tabs-item")
                        .removeClass("is-active");
                    $(this).addClass("is-active");
                });
            }
        },
    }),
    // 汎用型モーダル
    (PROJECT.COMMON.modal = {
        const: function () {
            $(".js-modal-btn--front-close").on("click", function () {
                $(this).parents(".js-modal").removeClass("is-visible");
            });
            $(".js-modal-btn--open").on("click", function () {
                var modalID = $(this).attr("data-modal");
                modalID = modalID.replace("js-modal-btn", "");

                if (modalID == "-middlename") {
                    $("#js-modal" + modalID).addClass("is-visible");
                    $(".js-modal-tooltip-overlay").show();
                } else if (modalID.includes("tooltip")) {
                    $("#js-modal" + modalID).addClass("is-visible");
                    $(".js-modal-tooltip-overlay").show();
                } else {
                    $("#js-modal" + modalID).addClass("is-visible");
                    $(".js-modal-overlay").show();
                }
            });
            // 閉じるボタンで閉じる
            $(".js-modal-btn--close").on("click", function () {
                $(this).parents(".js-modal").removeClass("is-visible");
                $(".js-modal-overlay").hide();
            });

            // 閉じるボタンで閉じる
            $(".js-modal-middlename-btn--close").on("click", function () {
                $(this).parents(".js-modal").removeClass("is-visible");
                $(".js-modal-tooltip-overlay").hide();
            });

            $(".js-modal-tooltip-btn--close").on("click", function () {
                $(this).parents(".js-modal").removeClass("is-visible");
                $(".js-modal-tooltip-overlay").hide();
            });
            // モーダルウィンドウ以外をクリックしたら閉じる
            $(document).click(function (event) {
                var target = $(event.target);
                if (target.hasClass("js-modal-overlay")) {
                    $(".js-modal").removeClass("is-visible");
                    $(".js-modal-overlay").hide();
                } else if (target.hasClass("js-modal-tooltip-overlay")) {
                    $(".js-modal").removeClass("is-visible");
                    $(".js-modal-tooltip-overlay").hide();
                }
            });
        },
    }),
    // リップル
    (PROJECT.COMMON.ripple = {
        const: function () {
            var $clickable = $(".ripple");
            $clickable.append('<span class="ripple__effect"></span>');
            /* mousedownだと直ぐに発動し、clickだとマウスボタンを離した時に発動する */
            $clickable.on("mousedown", function (e) {
                var _self = this;
                var x = e.offsetX;
                var y = e.offsetY;
                var $effect = $(_self).find(".ripple__effect");
                var w = $effect.width();
                var h = $effect.height();
                /* クリックした座標を中心とする */
                $effect.css({
                    left: x - w / 2,
                    top: y - h / 2,
                });
                /* jsではclassの付け替えをするだけ */
                if (!$effect.hasClass("is-show")) {
                    $effect.addClass("is-show");
                    /*
                     * エフェクトアニメーションが終わったらclassを削除する
                     * ここでは、単純にcssで設定するdurationと時間を合わせているだけですが
                     * keyframes終了のイベント(AnimationEnd)が取れるかと思うので、それで対応した方が良いかも
                     */
                    setTimeout(function () {
                        $effect.removeClass("is-show");
                    }, 750);
                }
                return false;
            });
        },
    }),
    (PROJECT.COMMON.datepicker = {
        const: function () {
            if ($(".datepicker").length) {
                $(".datepicker").datepicker({
                    dateFormat: "yy/mm/dd",
                    duration: "fast",
                });
            }
        },
    }),
    (PROJECT.COMMON.file = {
        const: function () {
            if ($(".js-file").length) {
                $(".js-file-input").on("change", function () {
                    var file = $(this).prop("files")[0];
                    $(this)
                        .parents(".js-file")
                        .next(".js-file-label")
                        .text(file.name);
                });
            }
        },
    });

PROJECT.COMMON.duplicatePulldown = {
    const: function () {
        if ($(".js-duplicate-pulldown")) {
            var origin = $(".c-pulldown--origin");
            var originItems = origin.find(".c-pulldown__item");
            var duplicate = $(".c-pulldown--duplicate .simplebar-content");
            this.generate(originItems, duplicate);
            this.delete(
                ".c-pulldown--duplicate .c-pulldown__item",
                originItems
            );
        }
    },
    generate: function (items, duplicate) {
        items.each(function (i, item) {
            $(item).on("click", function () {
                var button =
                    '<button class="c-pulldown__item is-selected" type="button">' +
                    $(this).text() +
                    "</button>";
                duplicate.append(button);
                $(this).hide();
            });
        });
    },
    delete: function (target, origin) {
        $(document).on("click", target, function () {
            var text = $(this).text();
            origin.each(function (i, item) {
                if ($(item).text() === text) $(item).show();
            });
            $(this).remove();
        });
    },
};

PROJECT.COMMON.switchSelect = {
    const: function () {
        if ($(".js-switch-select")) {
            var select = $(".js-switch-select");
            this.init(select);
            this.change(select);
        }
    },
    init: function (select) {
        var value = $(select).val();
        var target = $('[data-switch-select="' + value + '"]');
        $(target).show();
    },
    change: function (select) {
        $(select).change(function () {
            var value = $(this).val();
            var target = $('[data-switch-select="' + value + '"]');
            $("[data-switch-select]").hide();
            $(target).show();
        });
    },
};

(PROJECT.COMMON.addItem = {
    const: function () {
        this.basic();
        // this.limit(); 上限あり
        // this.withDeleteBtn.add();
        this.delete();
    },
    // 無制限
    basic: function () {
        if ($(".js-add")) {
            $(".js-add").on("click", function () {
                var self = $(this);
                var targetAddInput = self.prev(".js-add-wrap");
                var targetAddWrap = self
                    .prev(".js-add-wrap")
                    .find(".js-add-item")
                    .parent();
                self.prev(".js-add-wrap")
                    .find(".js-add-item:last")
                    .clone(true)
                    .appendTo(targetAddWrap);
                var targetItemLength = self
                    .prev(".js-add-wrap")
                    .find(".js-add-item").length;
                if (targetItemLength > 1) {
                    self.prev(".js-add-wrap")
                        .find(".js-delete")
                        .each(function () {
                            $(this).addClass("is-active");
                        });
                }
            });
        }
    },
    // 上限あり
    limit: function () {
        if ($(".js-add-limit")) {
            var targetAddLimitItem = $(this).prev(".js-add-limit-wrap");
            $(".js-add-limit").on("click", function () {
                var addLimitItemLength = $(this)
                    .prev(".js-add-limit-wrap")
                    .children(".js-add-limit-item").length;
                if (addLimitItemLength < 5) {
                    $(this)
                        .prev(".js-add-limit-wrap")
                        .children(".js-add-limit-item:last")
                        .clone(true)
                        .appendTo($(this).prev(".js-add-limit-wrap"));
                }
                if (addLimitItemLength == 4) {
                    $(this).hide();
                }
            });
        }
    },
    // 削除ボタンつきで増やす
    delete: function () {
        $(".js-delete").on("click", function () {
            var self = $(this);
            var addItemLength = $(this)
                .parents(".js-add-wrap")
                .find(".js-add-item").length;

            if (addItemLength < 3) {
                self.parents(".js-add-wrap")
                    .find(".js-delete")
                    .each(function () {
                        $(this).removeClass("is-active");
                    });
            }
            if (addItemLength > 1) {
                self.parents(".js-add-item").remove();
            }
        });
    },
}),
    (PROJECT.COMMON.infoToggle = {
        const: function () {
            if ($(".js-toggle-info-btn").length > 0) {
                $(".js-toggle-info-btn").on("click", function () {
                    var toggleTarget = $(this).prev(".js-toggle-info-wrap");
                    toggleTarget.slideToggle();
                    $(this).toggleClass("is-active");
                    if ($(this).hasClass("is-active")) {
                        $(this).text("一部を非表示に");
                    } else {
                        $(this).text("全てを表示する");
                    }
                });
            }
            if ($(".js-toggle").length > 0) {
                $(".js-toggle").on("click", function () {
                    var toggleTarget = $(
                        '[data-toggle-name="' +
                            $(this).data("toggle-target") +
                            '"]'
                    );
                    if (toggleTarget.is(":visible")) {
                        toggleTarget.hide();
                    } else {
                        console.log(toggleTarget.prop("tagName"));
                        if (toggleTarget.prop("tagName") == "TBODY") {
                            toggleTarget.css("display", "table-row-group");
                        }
                        toggleTarget.show();
                    }
                    $(this).toggleClass("is-active");
                    if ($(this).hasClass("is-active")) {
                        $(this).text("一部を非表示に");
                    } else {
                        $(this).text("全てを表示する");
                    }
                });
            }
        },
    });

PROJECT.COMMON.inviteNav = {
    const: function () {
        if ($(".js-inviteNav-item").length > 0) {
            var inviteNavArray = [];
            $(".js-inviteNav-item").each(function () {
                if ($(this).hasClass("is-active")) {
                    inviteNavArray.push(1);
                } else {
                    inviteNavArray.push(0);
                }
            });
            if (inviteNavArray.indexOf(0) == -1) {
                $(".js-btn-complete").prop("disabled", false);
                $(".js-btn-complete").on("click", function () {
                    $(this).addClass("is-finished");
                    $(".js-btn-publish").prop("disabled", false);
                });
            }
        }
    },
};

PROJECT.COMMON.selectPlaceholder = {
    const: function () {
        if ($(".js-select").length > 0) {
            // 読み込み時
            $(".js-select").each(function () {
                PROJECT.COMMON.selectPlaceholder.function($(this));
            });
            // 選択時
            $(".js-select").on("change", function () {
                PROJECT.COMMON.selectPlaceholder.function($(this));
            });
        }
    },
    function: function (item) {
        if (item.children("option:selected").hasClass("js-option-default")) {
            item.addClass("is-default");
        } else {
            item.removeClass("is-default");
        }
    },
};

PROJECT.COMMON.disabled = {
    const: function () {
        $(".js-disabled").on("click", function () {
            if ($(this).prop("checked")) {
                $(".js-disabled-target").each(function () {
                    $(this).prop("disabled", true);
                });
                $(".js-disabled-target-label").each(function () {
                    $(this).addClass("is-disabled");
                });
            } else {
                $(".js-disabled-target").each(function () {
                    $(this).prop("disabled", false);
                });
                $(".js-disabled-target-label").each(function () {
                    $(this).removeClass("is-disabled");
                });
            }
        });
    },
};

PROJECT.COMMON.zip = {
    const: function () {
        if ($("#js-zip1").length > 0) {
            $("#js-zip1").jpostal({
                click: "#js-zip-btn",
                postcode: ["#js-zip1", "#js-zip2"],
                address: {
                    "#js-zip-address": "%3%4%5",
                    "#js-zip-address-kana": "%ASKV8%ASKV9%ASKV10",
                },
            });
        }
    },
};

// ダブルクリックフォームを制御するには、BTNを送信
$(".js-form-submit").on("click", function (e) {
    if ($(this).data("submitted")) {
        // フォームはすでに提出されています、フォームを再度送信するのを停止
        e.preventDefault();
    } else {
        // レコードに対してデータサブミットされた属性をtrueに設定
        $(this).data("submitted", true);
    }
});
