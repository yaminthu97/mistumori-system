<?php

namespace App\Constants;

/**
 * 定数
 */
class GeneralConst
{
    /**
     * 見積管理システムの全般
     */
    public const ENCRYPT_STRING     = 'MK$cSelP0W';
    // アプリ名
    public const FRONT_APP_NAME     = '見積管理システム';
    // ロゴ名
    public const FRONT_LOGO_NAME    = '見積管理システム';

    /**
     * スクリーン
     */
    public const PROJECT_MANAGEMENT     = "プロジェクト管理";
    public const INQUIRY_MANAGEMENT     = "お問い合わせ管理";
    public const CUSTOMER_MANAGEMENT    = "お客様管理";
    public const WIKI                   = "ウィキ";

    /**
     * 管理画面向け全般
     */
    // アプリ名
    public const ADMIN_APP_NAME = "見積もり 管理";
    // アカウント一覧画面の最大表示件数
    public const ADMIN_ACCOUNT_LIST_DISPLAY_MAX_LIMIT = 500;
    // お手続き状況検索一覧画面の最大表示件数
    public const ADMIN_STATUS_LIST_DISPLAY_MAX_LIMIT = 2000;
    // メール送信最大件数
    public const ADMIN_MAIL_LIST_DISPLAY_MAX_LIMIT = 2000;
    // 加入内容詳細画面の情報トグル制限
    public const ADMIN_CONTRACT_INFO_TOGGLE_LIMIT = 3;
    // テンプレートフォルダのパス
    public const FOLDER_TEMPLATE = 'template';
    // Tempフォルダー
    public const FOLDER_TEMP = 'temp';
    // 半角スペース
    public const HALF_WIDTH_SPACE = ' ';
    // 全角スペース
    public const FULL_WIDTH_SPACE = '　';

    /**
     * 権限種別
     */
    public const SALES = 0; //営業
    public const MTM   = 1; //MTM

    public const ROLE_LIST =  [
        self::SALES                 => '営業',
        self::MTM                   => 'MTM'
    ];

    /**
     * 管理サイドバー
     */
    public const ADMIN_MENU_DASHBOARD           = 1;
    public const ADMIN_MENU_PROJECT_MANAGEMENT  = 2;
    public const ADMIN_MENU_INQUIRY_MANAGEMENT  = 3;
    public const ADMIN_MENU_CUSTOMER_MANAGEMENT = 4;
    public const ADMIN_MENU_ACCOUNT_MANAGEMENT  = 5;
    public const ADMIN_MENU_WIKI                = 6;
    public const ADMIN_MENU_LANGUAGE            = 7;

    //エラーメッセージ
    public const ADMIN_ACCOUNT_LIMIT_ERROR      = 'アカウント数が' . GeneralConst::ADMIN_ACCOUNT_LIST_DISPLAY_MAX_LIMIT . '件をこえています。再検索してください。';
    public const STATUS_CONFIRM_LIMIT_ERROR     = 'お手続き状況の件数が' . GeneralConst::ADMIN_STATUS_LIST_DISPLAY_MAX_LIMIT . '件をこえています。再検索してください。';
    public const MAIL_SEND_ERROR                = 'メール送信処理に失敗しました。';
    public const SEND_COUNT_ERROR               = '送信対象者は一人以上選択してください。';
    public const MAIL_ADDRESS_ERROR             = '送信先アドレスに不備があります。';
    public const MAIL_TEMPLATE_ERROR            = 'メールテンプレートの取得に失敗しました。';

    /**
     * アカウント画面用
     */
    // CSVファイル名
    public const ACCOUNT_CSV_NAME = "account.csv";
    // CSVタイトル
    public const ACCOUNT_CSV_HEADER = ['ID', '名前', 'ユーザID（メールアドレス）', '区分'];

    /**
     * プロジェクト詳細画面用
     */
    // ファイル名
    public const SYSTEM_CONTENT_NAME = "system_content.csv";
    // CSVタイトル
    public const SYSTEM_CONTENT_HEADER = ['ID', '名前', 'ユーザID（メールアドレス）', '区分'];
    // ID1のアカウント
    public const ADMIN_ACCOUNT_ID1 = 1;
    // 月の1日
    public const FIRST_DAY_OF_MONTH = '01';

    /**
     * ファイル出力Temp用フォルダーパス
     */
    const TEMP_FOLDER_PATH = 'temp/';

    /**
     * テンプレートフォルダのパス
     */
    const TEMPLATE_FOLDER_PATH = 'template/';

    const ADMIN_ACCOUNT_COLUMN = [
        'role_id'       => 'ロールID',
        'login_id'      => 'ログインID',
        'password'      => 'パスワード',
        'name'          => '名前',
        'email'         => 'メールアドレス'
    ];

    const ADMIN_ACCOUNT_LIST_FOLDER = 'admin_account_list';

    const CSV_OUT_OF_BOUND_ROW = 1;

    const TITLE_ORDER_WRONG = 'X';

    const ADMIN_ACCOUNT_COLUMN_KEYS = ['role_id', 'login_id', 'password', 'name', 'email'];

    // プロジェクト管理: プロジェクトの種類
    public const WEB            = 1;
    public const SYSTEM         = 2;
    public const WEB_AND_SYSTEM = 3;

    public const PROJECT_TYPE = [
        self::WEB                   => 'Web制作',
        self::SYSTEM                => 'システム開発',
        self::WEB_AND_SYSTEM        => 'Web制作＋システム開発'
    ];


    // プロジェクト管理: 優先順位
    public const LOW        = 0;
    public const MEDIUM     = 1;
    public const HIGH       = 2;

    public const PRIORITY = [
        self::LOW               => '低',
        self::MEDIUM            => '中',
        self::HIGH              => '高'
    ];

    // プロジェクト管理: プロジェクトのステータス
    public const NOT_STARTED            = 0;
    public const IN_PROGRESS            = 1;
    public const COMPLETED              = 2;
    public const CONFIRMING             = 3;
    public const REPORT_TO_CUSTOMER     = 4;
    public const NO_RESPONSE_REQUIRED   = 5;

    public const PROJECT_STATUS = [
        self::NOT_STARTED                       => '未着手',
        self::IN_PROGRESS                       => '見積書作成中',
        self::COMPLETED                         => '見積書作成済',
        self::CONFIRMING                        => '営業確認中',
        self::REPORT_TO_CUSTOMER                => 'お客様に報告済',
        self::NO_RESPONSE_REQUIRED              => '対応不要'
    ];

    // お問い合わせ管理: 問い合わせ状況
    public const ANSWERED               = 1;
    public const ADDITIONAL_QUESTION    = 2;
    public const CLOSE                  = 3;
    public const NOT_REQUIRED           = 4;

    public const INQUIRY_STATUS = [
        self::NOT_STARTED                                   => '未着手',
        self::ANSWERED                                      => '回答済み',
        self::ADDITIONAL_QUESTION                           => '追加質問',
        self::CLOSE                                         => 'クローズ',
        self::NOT_REQUIRED                                  => '不要'
    ];

    // 顧客管理: 顧客ステータス
    public const PRIVATE    = 0;
    public const PUBLIC     = 1;

    public const CUSTOMER_STATUS = [
        self::PRIVATE                   => '非公開',
        self::PUBLIC                    => '公開'
    ];

    public const ESTIMATE_FILE  = 'estimate_file/';
    const SYSTEM_FILE_FOLDER    = 'system_file/';
    public const WIKI_FILEPATH  = 'wiki/';

    // 通知カテゴリ
    public const PROJECT_CREATE     = 'project_create';
    public const PROJECT_UPDATE     = 'project_update';
    public const INQUIRY_CREATE     = 'question_create';
    public const INQUIRY_UPDATE     = 'question_update';
    public const COMMENT_CREATE     = 'comment_create';
    public const COMMENT_EDIT       = 'comment_edit';
    public const ESTIMATE_CREATE    = 'estimate_create';
    public const ESTIMATE_UPDATE    = 'estimate_update';
    public const ANSWER_CREATE      = 'answer_create';
    public const ANSWER_UPDATE      = 'answer_update';

    public const CREATE_TEXT = '作成しました。';
    public const UPDATE_TEXT = '更新しました。';

    // オンボーディング
    public const UNDONE_ONBOARD     = 0;
    public const DONE_ONBOARD       = 1;
}
