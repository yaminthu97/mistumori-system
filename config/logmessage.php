<?php

return [
    /*　ログメッセージ
     *　内部で使用するログメッセージの一覧。
     * 
     * 新規に追加するときは、下記形態と同じように追加してください。
     * 'NORMAL_001' => [
     *     'msg'   => '%sを開始しました。',　//メッセージ
     *     'code'  => 'MK000001',　　　　  //メッセージコード
     *     'level' => 'INFO',　　　　　    //ログレベル
     * ],
     * 
     */


    //正常
    //logレベル：INFO
    'NORMAL_001' => [
        'msg'  => '%sを開始しました。',
        'code' => 'MK000001',
        'level' => 'INFO',
    ],
    'NORMAL_002' => [
        'msg'  => '%sを終了しました。',
        'code' => 'MK000002',
        'level' => 'INFO',
    ],

    //システム内部異常
    //logレベル：ERROR
    'SYSTEM_ERROR_001' => [
        'msg'  => 'システムエラーが発生しました。%s',
        'code' => 'MK010001',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_002' => [
        'msg'  => '指定したファイルは存在しません。（ファイル名：%s）',
        'code' => 'MK010002',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_003' => [
        'msg'  => 'ファイルの読み込みに失敗しました。（ファイル名：%s）',
        'code' => 'MK010003',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_004' => [
        'msg'  => 'ファイルの作成に失敗しました。（ファイル名：%s)',
        'code' => 'MK010004',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_005' => [
        'msg'  => 'ファイルの削除に失敗しました。（ファイル名：%s）',
        'code' => 'MK010005',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_006' => [
        'msg'  => 'ファイルのコピーが失敗しました。（ファイル名：%s）',
        'code' => 'MK010006',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_007' => [
        'msg'  => '圧縮ファイルの作成に失敗しました。（ファイル名：%s）',
        'code' => 'MK010007',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_008' => [
        'msg'  => 'コマンド実行エラーが発生しました（コマンド名：%s）',
        'code' => 'MK010008',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_009' => [
        'msg'  => '%sがシステムで採番可能な上限値を超えています。（上限値：%s）',
        'code' => 'MK010009',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_010' => [
        'msg'  => 'ディレクトリの作成に失敗しました。（ディレクトリ名：%s)',
        'code' => 'MK010010',
        'level' => 'ERROR',
    ],
    'SYSTEM_ERROR_011' => [
        'msg'  => 'ディレクトリの削除に失敗しました。（ディレクトリ名：%s)',
        'code' => 'MK010011',
        'level' => 'ERROR',
    ],

    //パラメータエラー
    //logレベル：WARNING
    'PARAM_ERROR_001' => [
        'msg'  => 'バリデーションエラーが発生しました。（項目名：%s,%s）',
        'code' => 'MK020001',
        'level' => 'WARNING',
    ],
    'PARAM_ERROR_002' => [
        'msg'  => '既に登録されているアカウントです。（ユーザーID：%s)',
        'code' => 'MK020002',
        'level' => 'WARNING',
    ],
    'PARAM_ERROR_003' => [
        'msg'  => 'ユーザーIDとパスワードが一致しない、もしくはステータスがロック中か無効です。（ユーザーID：%s）',
        'code' => 'MK020003',
        'level' => 'WARNING',
    ],
    'PARAM_ERROR_004' => [
        'msg'  => '既に登録されている代理店コードです。（代理店コード：%s)',
        'code' => 'MK020004',
        'level' => 'WARNING',
    ],
    'PARAM_ERROR_005' => [
        'msg'  => '既に登録されている団体コードです。（団体コード：%s)',
        'code' => 'MK020005',
        'level' => 'WARNING',
    ],

    //データ無しエラー
    //logレベル：WARNING
    'EMPTY_ERROR_001' => [
        'msg'  => '指定した検索条件に該当する情報が存在しません。（検索条件：%s）',
        'code' => 'MK030001',
        'level' => 'WARNING',
    ],
    'EMPTY_ERROR_002' => [
        'msg'  => '指定した条件に該当する情報は既に削除されています。（条件：%s）',
        'code' => 'MK030002',
        'level' => 'WARNING',
    ],
    'EMPTY_ERROR_003' => [
        'msg'  => '指定したユーザーID、またはパスワードが誤っています。',
        'code' => 'MK030003',
        'level' => 'WARNING',
    ],
    'EMPTY_ERROR_004' => [
        'msg'  => '存在しないURLへアクセスされました。（URL：%s）',
        'code' => 'MK030004',
        'level' => 'WARNING',
    ],
    'EMPTY_ERROR_005' => [
        'msg'  => '指定した情報は存在しない、または閲覧権限がありません。',
        'code' => 'MK030005',
        'level' => 'WARNING',
    ],


    //セキュリティエラー
    //logレベル：WARNING
    'SECURITY_ERROR_001' => [
        'msg'  => 'トークンエラー',
        'code' => 'MK040001',
        'level' => 'WARNING',
    ],
    'SECURITY_ERROR_002' => [
        'msg'  => '不正なページ遷移またはリクエストが改ざんされています。',
        'code' => 'MK040002',
        'level' => 'WARNING',
    ],
    'SECURITY_ERROR_003' => [
        'msg'  => '利用ユーザに登録/更新/参照権限の何れかが付与されていません。',
        'code' => 'MK040003',
        'level' => 'WARNING',
    ],
    'SECURITY_ERROR_004' => [
        'msg'  => '申し込みデータのタイムスタンプが改ざんされています。',
        'code' => 'MK040004',
        'level' => 'WARNING',
    ],
    'SECURITY_ERROR_005' => [
        'msg'  => 'メンテナンス中です。',
        'code' => 'MK040005',
        'level' => 'WARNING',
    ],

    //セッションタイムアウト
    //logレベル：WARNING
    'SESSION_ERROR_001' => [
        'msg'  => 'セッションタイムアウトが発生しました。',
        'code' => 'MK050001',
        'level' => 'WARNING',
    ],

    //外部システム連携異常
    //logレベル：ERROR
    'OUTSIDE_ERROR_001' => [
        'msg'  => '外部API接続に失敗しました。（エラー詳細：%s）',
        'code' => 'MK060001',
        'level' => 'ERROR',
    ],
    'OUTSIDE_ERROR_002' => [
        'msg'  => '外部APIから、期待したレスポンスが取得できませんでした。（レスポンス:%s）',
        'code' => 'MK060002',
        'level' => 'ERROR',
    ],

    //排他制御エラー
    //logレベル：WARNING
    'EXCLUSIVE_ERROR_001' => [
        'msg'  => '対象機能は現在実行中です。（機能名：%s）',
        'code' => 'MK070001',
        'level' => 'WARNING',
    ],

    //ファイルアップロードエラー
    //logレベル：WARNING
    'FILEUPLOAD_ERROR_001' => [
        'msg'  => 'アップロードされたファイルは、php.iniのupload_max_filesize ディレクティブの値を超えています。',
        'code' => 'MK080001',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_002' => [
        'msg'  => 'アップロードされたファイルは、HTMLフォームで指定された MAX_FILE_SIZE を超えています。',
        'code' => 'MK080002',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_003' => [
        'msg'  => 'アップロードされたファイルは一部のみしかアップロードされていません。',
        'code' => 'MK080003',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_004' => [
        'msg'  => 'ファイルがアップロードされませんでした。',
        'code' => 'MK080004',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_005' => [
        'msg'  => 'テンポラリフォルダがありません',
        'code' => 'MK080005',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_006' => [
        'msg'  => 'ディスクへの書き込みに失敗しました。',
        'code' => 'MK080006',
        'level' => 'WARNING',
    ],
    'FILEUPLOAD_ERROR_007' => [
        'msg'  => 'PHPの拡張モジュールがファイルのアップロードを中止しました。',
        'code' => 'MK080007',
        'level' => 'WARNING',
    ],
];
