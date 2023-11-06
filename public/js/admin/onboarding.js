function runOnboarding() {
    introJs().setOptions({
        nextLabel: '次に',
        prevLabel: '戻る',
        doneLabel: '完了',
        showProgress: true,
        showBullets: false,
        exitOnOverlayClick: false,
        exitOnEsc: false,
        steps: [
            {
                title: "見積管理",
                intro: '<b>ようこそ！</b><br><span> ユーザーガイドを始めましょう。</span>',
                tooltipClass: 'introjs-tooltip-step1',
            },
            {
                title: "ヒント 1",
                element: document.querySelector('.u-heading-h1'),
                intro: "この<b>プロジェクト管理</b>では<br><span> プロジェクトの検索とを管理をします。</span>",
            },
            {
                title: "ヒント 2",
                element: document.querySelector('.c-breadcrumb-onboard'),
                intro: "この<b>パンくずリスト</b>を使用して <br><span> 画面チェンジできます。</span>",
                tooltipClass: 'introjs-tooltip-step3',
            },
            {
                title: "ヒント 3",
                element: document.querySelector('.project-search-frame'),
                intro: 'ここでプロジェクトを検索できます。',
                tooltipClass: 'introjs-tooltip-step4',
            },
            {
                title: "ヒント 4",
                element: document.querySelector('.p-MK02013'),
                intro: "ここではプロジェクト検索の<br> <b>結果</b>が見られます。",
                position: 'right',
            },
            {
                title: "ヒント 5",
                element: document.querySelector('.project-add'),
                intro: 'ここをクリックすると<br> 新しいプロジェクトを作成できます。',
                tooltipClass: 'introjs-tooltip-step6',
                position: 'left'
            },
            {
                title: "ヒント 6",
                element: document.querySelector('.MKCUSTOMER'),
                intro: 'このお<b>客様管理</b>では <br> お客様の情報を管理します。',
                tooltipClass: 'introjs-tooltip-step-7-8-9-10',
                position: 'right',
            },
            {
                title: "ヒント 7",
                element: document.querySelector('.MKINQUIRY'),
                intro: 'このお<b>問い合わせ管理</b>では <br> お問い合わせの情報を管理します。',
                tooltipClass: 'introjs-tooltip-step-7-8-9-10',
                position: 'right',
            },
            {
                title: "ヒント 8",
                element: document.querySelector('.MKACCOUNT'),
                intro: 'この<b>アカウント管理</b>では <br>ユーザーアカウントの情報を管理します。',
                tooltipClass: 'introjs-tooltip-step-7-8-9-10',
                position: 'right',
            },
            {
                title: "ヒント 9",
                element: document.querySelector('.MKWIKI'),
                intro: 'この<b>ウィキ</b>では <br> 手軽にウェブページを作成・編集できます。',
                tooltipClass: 'introjs-tooltip-step-7-8-9-10',
                position: 'right',
            },
            {
                title: "ヒント 10",
                element: document.querySelector('.dark-mode-toggle'),
                intro: 'この<b>ダークモードボタン</b>を切り替えると <br> 黒を基調とした配色の画面に設定できます。',
                tooltipClass: 'introjs-tooltip-step-7-8-9-10',
                position: 'right',
            },
        ],
    }).oncomplete(function () {
        const path = window.location.pathname;
        if (path === '/admin/project') {
            if (onboarded === UNDONE_ONBOARD) {
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    url: `/admin/onboarding/${userId}`,
                    type: "POST",
                    dataType: "json",
                    data: { complete: true },
                    success: function (res) {
                        console.log(res);
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            }
            const currentUrl = window.location.href;
            const newUrl = currentUrl.replace('?onboard=true', '');
            window.history.replaceState({}, "", newUrl);
        }
    }).start();
}

$(document).ready(function () {

    const path = window.location.pathname;
    if (path === '/admin/project') {

        const params = new URLSearchParams(document.location.search);
        const onboardParam = JSON.parse(params.get("onboard"));

        if (onboardParam === true) {
            setTimeout(runOnboarding, 1000);
        }

        if (onboarded === UNDONE_ONBOARD) {
            setTimeout(runOnboarding, 1600);
        }
    }

    $('.icon--user-guide').on('click', function (e) {
        e.preventDefault();
        $(".js-modal").removeClass("is-visible");
        $(".js-modal-overlay").hide();
        if (path !== '/admin/project') {
            window.location.href = '/admin/project' + `?onboard=${JSON.stringify(true)}`;
        } else {
            setTimeout(runOnboarding, 1000);
        }
    })
});
