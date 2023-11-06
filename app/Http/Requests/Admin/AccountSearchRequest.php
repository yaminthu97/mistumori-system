<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

class AccountSearchRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限を与えられているかどうかを判断する
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * リクエストに適用される検証ルールを取得
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules['name'] = 'nullable|string|max:255';
        $rules['email'] = 'nullable|string|Mailaddress|max:255';

        return $rules;
    }

    /**
     * バリデーターエラーのカスタムメッセージを取得
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        if (App::isLocale('en')) {
            $messages['email.mailaddress'] = 'Specify an email address in the correct format for the user ID (email address).';
        } else {
            $messages['email.mailaddress'] = 'ユーザID(メールアドレス）には正しい形式のメールアドレスを指定してください。';
        }
        return $messages;
    }
}
