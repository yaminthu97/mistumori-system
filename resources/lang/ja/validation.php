<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'first_day_of_month'    => ':attributeには1日を指定してください。',
    'accepted'              => ':attributeを承認してください。',
    'active_url'            => ':attributeには有効なURLを指定してください。',
    'after'                 => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal'        => ':attributeには:date以降の日付を指定してください。',
    'alpha'                 => ':attributeには英字のみからなる文字列を指定してください。',
    'alpha_dash'            => ':attributeには英数字・ハイフン・アンダースコアのみからなる文字列を指定してください。',
    'alpha_num'             => ':attributeには、英数字のみ使用してください。',
    'array'                 => ':attributeには配列を指定してください。',
    'before'                => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal'       => ':attributeには:date以前の日付を指定してください。',
    'between'               => [
        'numeric'           => ':attributeには:min〜:maxまでの数値を指定してください。',
        'file'              => ':attributeには:min〜:max KBのファイルを指定してください。',
        'string'            => ':attributeには:min〜:max文字の文字列を指定してください。',
        'array'             => ':attributeには:min〜:max個の要素を持つ配列を指定してください。'
    ],
    'boolean'               => ':attributeには真偽値を指定してください。',
    'confirmed'             => ':attributeが確認用の値と一致しません。',
    'date'                  => ':attributeには正しい日付を指定してください。',
    'date_format'           => ':attribute は日付（YYYY/MM/DD）で指定してください。',
    'different'             => ':attributeには:otherとは異なる値を指定してください。',
    'digits'                => ':attributeには:digits桁の数値を指定してください。',
    'digits_between'        => ':attributeには:min〜:max桁の数値を指定してください。',
    'dimensions'            => ':attributeの画像サイズが不正です。',
    'distinct'              => '指定された:attributeが重複しています。',
    'email'                 => ':attributeには正しい形式のメールアドレスを指定してください。',
    'exists'                => '指定された:attributeは存在しません。',
    'file'                  => ':attributeにはファイルを指定してください。',
    'filled'                => ':attributeには空でない値を指定してください。',
    'image'                 => ':attributeには画像ファイルを指定してください。',
    'in'                    => ':attributeには:valuesのうちいずれかの値を指定してください。',
    'in_array'              => ':attributeが:otherに含まれていません。',
    'integer'               => ':attributeには整数を指定してください。',
    'ip'                    => ':attributeには正しい形式のIPアドレスを指定してください。',
    'ip4'                   => ':attributeにはIPv4形式のIPアドレスを指定してください。',
    'ip6'                   => ':attributeにはIPv6形式のIPアドレスを指定してください。',
    'json'                  => ':attributeには正しい形式のJSON文字列を指定してください。',
    'lt'                    => [
        'numeric'           => ':attributeには、:valueより小さな値を指定してください。',
        'file'              => ':attributeには、:value kBより小さなファイルを指定してください。',
        'string'            => ':attributeは、:value文字より短く指定してください。',
        'array'             => ':attributeには、:value個より少ないアイテムを指定してください。'
    ],
    'lte'                   => [
        'numeric'           => ':attributeには、:value以下の値を指定してください。',
        'file'              => ':attributeには、:value kB以下のファイルを指定してください。',
        'string'            => ':attributeは、:value文字以下で指定してください。',
        'array'             => ':attributeには、:value個以下のアイテムを指定してください。'
    ],
    'max'                   => [
    'numeric'               => ':attributeには:max以下の数値を指定してください。',
        'file'              => ':attributeには:max KB以下のファイルを指定してください。',
        'string'            => ':attributeには:max文字以下の文字列を指定してください。',
        'array'             => ':attributeには:max個以下の要素を持つ配列を指定してください。'
    ],
    'mimes'                 => ':attributeには:valuesのうちいずれかの形式のファイルを指定してください。',
    'mimetypes'             => ':attributeには:valuesのうちいずれかの形式のファイルを指定してください。',
    'min'                   => [
        'numeric'           => ':attributeには:min以上の数値を指定してください。',
        'file'              => ':attributeには:min KB以上のファイルを指定してください。',
        'string'            => ':attributeには:min文字以上の文字列を指定してください。',
        'array'             => ':attributeには:min個以上の要素を持つ配列を指定してください。'
    ],
    'not_in'                => ':attributeには:valuesのうちいずれとも異なる値を指定してください。',
    'numeric'               => ':attributeには数値を指定してください。',
    'present'               => ':attributeには現在時刻を指定してください。',
    'regex'                 => '正しい形式の:attributeを指定してください。',
    'required'              => ':attributeは必須です。',
    'required_if'           => ':otherが:valueの時:attributeは必須です。',
    'required_unless'       => ':otherが:values以外の時:attributeは必須です。',
    'required_with'         => ':valuesのうちいずれかが指定された時:attributeは必須です。',
    'required_with_all'     => ':valuesのうちすべてが指定された時:attributeは必須です。',
    'required_without'      => ':valuesのうちいずれかがが指定されなかった時:attributeは必須です。',
    'required_without_all'  => ':valuesのうちすべてが指定されなかった時:attributeは必須です。',
    'same'                  => ':attributeが:otherと一致しません。',
    'size'                  => [
        'numeric'                       => ':attributeには:sizeを指定してください。',
        'file'                          => ':attributeには:size KBのファイルを指定してください。',
        'string'                        => ':attributeには:size文字の文字列を指定してください。',
        'array'                         => ':attributeには:size個の要素を持つ配列を指定してください。'
    ],
    'string'                            => ':attributeには文字列を指定してください。',
    'timezone'                          => ':attributeには正しい形式のタイムゾーンを指定してください。',
    'unique'                            => 'その:attributeはすでに使われています。',
    'uploaded'                          => ':attributeのアップロードに失敗しました。',
    'url'                               => ':attributeには正しい形式のURLを指定してください。',
    'mailaddress'                       => ':attributeには正しい形式のメールアドレスを指定してください。',
    'loginFailed'                       => 'ユーザIDまたはパスワードが正しくありません。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name'                 => 'custom-message'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'login_id'                      => 'ユーザID',
        'password'                      => 'パスワード',
        'email'                         => 'メールアドレス',
        'role'                          => '区分',
        'name'                          => '氏名',
        'address'                       => 'ユーザー名(メールアカウント)',


        //inquiry management
        'response_date'                 => '対応希望日',
        'project_name'                  => 'プロジェクト名',
        'comment_content'               => 'お問い合わせ内容',
        'question_assignee'             => '担当者',
        'expected_answer_date'          => '対応希望日',
        'priority'                      => '優先',
        'answer_content'                => '回答内容',
        'inquiry_status'                => 'ステータス',

        // project management
        'project_name'                  => 'プロジェクト名',
        'submit_date'                   => '見積提出予定日',
        'customer_name'                 => 'お客様名',
        'project_type'                  => 'プロジェクト種類',
        'system_content'                => 'システム内容',
        'system_path'                   => 'システム内容添付ファイルパス',
        'development_process'           => '開発工程',
        'development_language'          => '開発言語',
        'server_environment'            => 'サーバー環境',
        'development_start_date'        => '開発開始予定日',
        'development_end_date'          => '開発完了予定日',
        'priority'                      => '優先',
        'assignee'                      => '担当者',

        // customer management
        'customer_name'                 => 'お客様名',
        'customer_id'                   => 'お客様名',
        'description'                   => 'お客様概要',
        'status'                        => 'ステータス',

        // wiki
        'wiki_title'                    => 'タイトル',
        'wiki_content'                  => 'コンテンツ',
        'wiki_path'                     => 'ファイル'
    ]
];
