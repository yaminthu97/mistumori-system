<?php

use App\Constants\GeneralConst;

return [
    'ADMIN_APP_NAME'                        => '見積もり 管理',
    'FRONT_APP_NAME'                        => '見積管理システム',
    'PROJECT_MANAGEMENT'                    => 'プロジェクト管理',
    'INQUIRY_MANAGEMENT'                    => 'お問い合わせ管理',
    'CUSTOMER_MANAGEMENT'                   => 'お客様管理',
    'ACCOUNT_MANAGEMENT'                    => 'アカウント管理',
    'WIKI'                                  => 'ウィキ',

    'customer_status' => [
        GeneralConst::PRIVATE               => '非公開',
        GeneralConst::PUBLIC                => '公開'
    ],
    'inquiry_status' => [
        GeneralConst::NOT_STARTED           => '未着手',
        GeneralConst::ANSWERED              => '回答済み',
        GeneralConst::ADDITIONAL_QUESTION   => '追加質問',
        GeneralConst::CLOSE                 => 'クローズ',
        GeneralConst::NOT_REQUIRED          => '不要'
    ],
    'priority' => [
        GeneralConst::LOW                   => '低',
        GeneralConst::MEDIUM                => '中',
        GeneralConst::HIGH                  => '高'
    ],
    'role_list' =>  [
        GeneralConst::SALES                 => '営業',
        GeneralConst::MTM                   => 'MTM'
    ],
    'project_status' => [
        GeneralConst::NOT_STARTED           => '未着手',
        GeneralConst::IN_PROGRESS           => '見積書作成中',
        GeneralConst::COMPLETED             => '見積書作成済',
        GeneralConst::CONFIRMING            => '営業確認中',
        GeneralConst::REPORT_TO_CUSTOMER    => 'お客様に報告済',
        GeneralConst::NO_RESPONSE_REQUIRED  => '対応不要'
    ],
    'project_type'=> [
        GeneralConst::WEB                   => 'Web制作',
        GeneralConst::SYSTEM                => 'システム開発',
        GeneralConst::WEB_AND_SYSTEM        => 'Web制作＋システム開発'
    ]
];
